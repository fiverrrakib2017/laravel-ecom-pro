#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
OLT CLI (Telnet) — single file
- Supports BDCOM & Huawei out of the box (easily extendable)
- Actions: discover_ports, onu_list, optical_one, optical_all, mac_table, raw
- Output: JSON (default) or plain text (for raw)

Usage examples:
  # BDCOM: ONU list on gpON 0/1
  python3 olt_cli.py --host 10.10.10.18 --port 23 --user admin --vendor BDCOM \
    --pon "gpON 0/1" --action onu_list

  # Huawei: optical info for a specific ONU id=5 on 0/1
  python3 olt_cli.py --host 10.10.10.20 --port 23 --user admin --vendor Huawei \
    --pon "0/1" --onu-id 5 --action optical_one

  # Run any raw command (prints raw text)
  OLT_PASS='secret' python3 olt_cli.py --host 10.10.10.18 --user admin --vendor BDCOM \
    --action raw --cmd "show gpon onu-information interface gpON 0/1"
"""
import argparse, os, sys, telnetlib, time, re, json, getpass

# -------- Vendor Profiles (inline) --------
PROFILES = {
    "BDCOM": {
        "login_prompts": ["login:", "Username:"],
        "password_prompts": ["Password:", "password:"],
        "prompt_regex": r"(?:[#>\]])\s*$",
        "pager_markers": [b"--More--", b"--- More ---", b"More:", b"Press any key"],
        "commands": {
            "ports_discover": "show interface brief",
            "onu_list":       "show gpon onu-information interface {pon}",
            "optical_one":    "show gpon interface {pon_if} onu optical-transceiver-diagnosis",
            "optical_all":    "show gpon optical-transceiver-diagnosis interface {pon}",
            "mac_table":      "show mac address-table interface {pon_if}",
        },
        "autodiscover_regex": r"\bgpON\s+\d+/\d+",
        "pon_if_fmt": "{pon_if_bdcom}",  # computed: "gpON 0/1:5"
    },
    "HUAWEI": {
        "login_prompts": ["Username:", "Login:"],
        "password_prompts": ["Password:", "password:"],
        "prompt_regex": r"(?:[#>\]])\s*$",
        "pager_markers": [b"---- More ----", b"More:"],
        "commands": {
            "ports_discover": "display interface brief | include GPON",
            "onu_list":       "display ont info summary {pon}",
            "optical_one":    "display ont optical-info {pon} ontid {onu_id}",
            "optical_all":    "display ont optical-info {pon} all",
            "mac_table":      "display mac-address port {pon_if}",  # rarely needed
        },
        "autodiscover_regex": r"\b\d+/\d+\b",
        "pon_if_fmt": "{pon} {onu_id}",  # e.g. "0/1 5"
    },
}

# ---------- Telnet helpers ----------
def _read_until_prompt(tn, prompt_re, pager_marks, timeout=6.0):
    buf = b""
    end = time.time() + timeout
    while time.time() < end:
        chunk = tn.read_very_eager()
        if chunk:
            buf += chunk
            # pager
            for pm in pager_marks:
                if pm in buf:
                    tn.write(b" ")
                    time.sleep(0.05)
                    break
            # prompt?
            try:
                txt = buf.decode(errors="ignore")
            except Exception:
                txt = ""
            if re.search(prompt_re, txt, flags=re.M):
                return txt
        time.sleep(0.02)
    try:
        return buf.decode(errors="ignore")
    except Exception:
        return ""

def telnet_login(host, port, user, password, prof):
    tn = telnetlib.Telnet(host, int(port), timeout=10)
    # login prompts
    lp = prof.get("login_prompts", [])
    pp = prof.get("password_prompts", [])
    if lp:
        tn.read_until(lp[0].encode(), timeout=3)  # best-effort
    tn.write((user + "\n").encode())
    if pp:
        tn.read_until(pp[0].encode(), timeout=3)
    tn.write((password + "\n").encode())
    # reach prompt
    prompt_re = prof.get("prompt_regex", r"(?:[#>\]])\s*$")
    pager_marks = [m if isinstance(m, bytes) else m.encode() for m in prof.get("pager_markers", [])]
    _read_until_prompt(tn, prompt_re, pager_marks, timeout=4)
    # try enable (if asked)
    out = telnet_cmd(tn, "enable", prof, timeout=3)
    if "assword" in out:
        tn.write((password + "\n").encode())
        _read_until_prompt(tn, prompt_re, pager_marks, timeout=2)
    return tn

def telnet_cmd(tn, cmd, prof, sleep=0.05, timeout=6):
    tn.write((cmd + "\n").encode())
    time.sleep(sleep)
    prompt_re = prof.get("prompt_regex", r"(?:[#>\]])\s*$")
    pager_marks = [m if isinstance(m, bytes) else m.encode() for m in prof.get("pager_markers", [])]
    return _read_until_prompt(tn, prompt_re, pager_marks, timeout=timeout)

# ---------- Parsers ----------
def parse_onu_list_bdcom(pon, text):
    items = []
    patA = re.compile(
        r"(?:GPON|gpON)\s*\d+/\d+:(\d+).*?(?:SN|Loid/SN)\s*[:\s]+([A-Za-z0-9:\-]+).*?(?:State|Status)\s*[:\s]+(online|offline)",
        re.I | re.S
    )
    for m in patA.finditer(text):
        onu_id, sn, st = m.groups()
        items.append({"pon_port": pon, "onu_id": int(onu_id), "serial_number": sn.strip(), "status": st.lower()})
    if not items:
        for line in text.splitlines():
            if "gpon" in line.lower() and ":" in line:
                mid = re.search(r"(?:gpon|GPON)\s*\d+/\d+:(\d+)", line)
                st = "online" if re.search(r"\bonline\b", line, re.I) else ("offline" if re.search(r"\boffline\b", line, re.I) else None)
                snm = re.search(r"([A-Z]{4}[:\-]?[A-Z0-9]{6,})", line)
                if mid:
                    items.append({
                        "pon_port": pon, "onu_id": int(mid.group(1)),
                        "serial_number": (snm.group(1) if snm else None),
                        "status": st or "online"
                    })
    # dedupe by onu_id
    uniq = {i["onu_id"]: i for i in items}
    return list(uniq.values())

def parse_onu_list_huawei(pon, text):
    items = []
    for line in text.splitlines():
        m_id = re.search(r"\b(\d+)\b", line)
        if not m_id: continue
        onu_id = int(m_id.group(1))
        st = "online" if re.search(r"\bonline\b", line, re.I) else ("offline" if re.search(r"\boffline\b", line, re.I) else None)
        if st:
            items.append({"pon_port": pon, "onu_id": onu_id, "serial_number": None, "status": st})
    uniq = {i["onu_id"]: i for i in items}
    return list(uniq.values())

def parse_optical_generic(text):
    rx = tx = temp = volt = dist = sn = None
    m = re.search(r"(?i)\brx[^:\n]*[:=]\s*([\-]?\d+\.?\d*)\s*dBm", text)
    if m: rx = float(m.group(1))
    m = re.search(r"(?i)\btx[^:\n]*[:=]\s*([\-]?\d+\.?\d*)\s*dBm", text)
    if m: tx = float(m.group(1))
    m = re.search(r"(?i)\btemp(?:erature)?[^:\n]*[:=]\s*([\-]?\d+\.?\d*)\s*[cC]", text)
    if m: temp = m.group(1)
    m = re.search(r"(?i)\bvolt(?:age)?[^:\n]*[:=]\s*([\-]?\d+\.?\d*)\s*V", text)
    if m: volt = m.group(1)
    m = re.search(r"(?i)\bdistance[^:\n]*[:=]\s*(\d+)\s*m", text)
    if m: dist = int(m.group(1))
    m = re.search(r"(?i)\bSN\s*[:=]\s*([A-Za-z0-9:\-]+)", text)
    if m: sn = m.group(1)
    return rx, tx, temp, volt, dist, sn

# ---------- Action runners ----------
def build_pon_if(vendor, pon, onu_id):
    v = vendor.upper()
    if v == "BDCOM":
        # Expect "gpON 0/1:5"
        if ":" in pon:
            return pon
        if " " in pon:
            return f"{pon.split()[0]} {pon.split()[1]}:{onu_id}"
        return f"{pon}:{onu_id}"
    elif v == "HUAWEI":
        # "0/1 5"
        return f"{pon} {onu_id}"
    else:
        return f"{pon}:{onu_id}"

def act_discover_ports(tn, prof):
    txt = telnet_cmd(tn, prof["commands"]["ports_discover"], prof, timeout=6)
    rx = prof.get("autodiscover_regex")
    ports = sorted(set(re.findall(rx, txt, flags=re.I))) if rx else []
    # Normalize BDCOM gpON case
    ports = [p.replace("GPON","gpON") for p in ports]
    return {"ports": ports, "raw": txt}

def act_onu_list(tn, vendor, prof, pon):
    cmd = prof["commands"]["onu_list"].format(pon=pon)
    txt = telnet_cmd(tn, cmd, prof, timeout=8)
    if vendor.upper() == "BDCOM":
        items = parse_onu_list_bdcom(pon, txt)
    elif vendor.upper() == "HUAWEI":
        items = parse_onu_list_huawei(pon, txt)
    else:
        items = []
    return {"onus": items, "raw": txt}

def act_optical_one(tn, vendor, prof, pon, onu_id):
    pon_if = build_pon_if(vendor, pon, onu_id)
    cmd = prof["commands"]["optical_one"].format(pon=pon, onu_id=onu_id, pon_if=pon_if, pon_if_bdcom=f"gpON {pon.split()[1]}:{onu_id}" if " " in pon else f"{pon}:{onu_id}")
    txt = telnet_cmd(tn, cmd, prof, timeout=8)
    rx, tx, t, v, d, sn = parse_optical_generic(txt)
    rec = {"pon_port": pon, "onu_id": onu_id, "serial_number": sn, "rx_power": rx, "tx_power": tx, "temperature": t, "voltage": v, "distance": d}
    return {"metric": rec, "raw": txt}

def act_optical_all(tn, vendor, prof, pon):
    cmd = prof["commands"]["optical_all"].format(pon=pon)
    txt = telnet_cmd(tn, cmd, prof, timeout=10)
    # best-effort per-line parse
    items = []
    for line in txt.splitlines():
        mi = re.search(r"(?i)(\d+/\d+)[:](\d+).*?rx[^:]*[:=]\s*([\-]?\d+\.?\d*)", line)
        if mi:
            onu_id = int(mi.group(2)); rx = float(mi.group(3))
            tx = re.search(r"(?i)tx[^:]*[:=]\s*([\-]?\d+\.?\d*)", line)
            t  = re.search(r"(?i)temp[^:]*[:=]\s*([\-]?\d+\.?\d*)", line)
            v  = re.search(r"(?i)volt[^:]*[:=]\s*([\-]?\d+\.?\d*)", line)
            items.append({
                "pon_port": pon, "onu_id": onu_id, "rx_power": rx,
                "tx_power": float(tx.group(1)) if tx else None,
                "temperature": t.group(1) if t else None,
                "voltage": v.group(1) if v else None
            })
    return {"metrics": items, "raw": txt}

def act_mac_table(tn, vendor, prof, pon, onu_id):
    pon_if = build_pon_if(vendor, pon, onu_id)
    cmd = prof["commands"]["mac_table"].format(pon_if=pon_if, pon_if_bdcom=f"gpON {pon.split()[1]}:{onu_id}" if " " in pon else f"{pon}:{onu_id}")
    txt = telnet_cmd(tn, cmd, prof, timeout=8)
    macs = re.findall(r"([0-9a-f]{4}\.[0-9a-f]{4}\.[0-9a-f]{4}|[0-9a-f]{2}(?:[:\-][0-9a-f]{2}){5})", txt, flags=re.I)
    # normalize to dotted or colon—just return raw matches
    return {"macs": sorted(set([m[0] if isinstance(m, tuple) else m for m in macs])), "raw": txt}

# ---------- Main ----------
def main():
    ap = argparse.ArgumentParser(description="OLT CLI (Telnet)")
    ap.add_argument("--host", required=True, help="OLT IP/Host (reachable from your machine)")
    ap.add_argument("--port", default=23, type=int, help="Telnet port (default 23)")
    ap.add_argument("--user", required=True, help="Username")
    ap.add_argument("--password", help="Password (or use env OLT_PASS or prompt)")
    ap.add_argument("--vendor", required=True, choices=["BDCOM","Huawei","HUAWEI"], help="Vendor")
    ap.add_argument("--pon", help="PON (e.g., 'gpON 0/1' or '0/1')")
    ap.add_argument("--onu-id", type=int, help="ONU ID (integer)")
    ap.add_argument("--action", required=True, choices=[
        "discover_ports","onu_list","optical_one","optical_all","mac_table","raw"
    ])
    ap.add_argument("--cmd", help="raw command (when --action raw)")
    ap.add_argument("--plain", action="store_true", help="print raw text only (for action=raw)")
    args = ap.parse_args()

    vendor = args.vendor.upper()
    prof = PROFILES.get(vendor)
    if not prof:
        print(json.dumps({"error": f"unsupported vendor {args.vendor}"})); sys.exit(1)

    password = args.password or os.environ.get("OLT_PASS") or getpass.getpass("OLT password: ")

    try:
        tn = telnet_login(args.host, args.port, args.user, password, prof)
    except Exception as e:
        print(json.dumps({"error": f"telnet/login failed: {e}"})); sys.exit(1)

    try:
        if args.action == "discover_ports":
            res = act_discover_ports(tn, prof)

        elif args.action == "onu_list":
            if not args.pon: print(json.dumps({"error":"--pon required"})); sys.exit(1)
            res = act_onu_list(tn, vendor, prof, args.pon)

        elif args.action == "optical_one":
            if not args.pon or not args.onu_id:
                print(json.dumps({"error":"--pon and --onu-id required"})); sys.exit(1)
            res = act_optical_one(tn, vendor, prof, args.pon, args.onu_id)

        elif args.action == "optical_all":
            if not args.pon: print(json.dumps({"error":"--pon required"})); sys.exit(1)
            res = act_optical_all(tn, vendor, prof, args.pon)

        elif args.action == "mac_table":
            if not args.pon or not args.onu_id:
                print(json.dumps({"error":"--pon and --onu-id required"})); sys.exit(1)
            res = act_mac_table(tn, vendor, prof, args.pon, args.onu_id)

        elif args.action == "raw":
            if not args.cmd: print(json.dumps({"error":"--cmd required for action=raw"})); sys.exit(1)
            txt = telnet_cmd(tn, args.cmd, prof, timeout=10)
            if args.plain:
                print(txt)
                tn.write(b"exit\n"); tn.close()
                return
            res = {"raw": txt}

        else:
            res = {"error":"unknown action"}

        print(json.dumps(res, ensure_ascii=False))
    finally:
        try:
            tn.write(b"exit\n"); tn.close()
        except Exception:
            pass

if __name__ == "__main__":
    main()
