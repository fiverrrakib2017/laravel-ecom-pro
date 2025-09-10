#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Generic OLT Telnet collector (BDCOM ready; hooks for other vendors).
- Reads vendor profile from vendor_profiles.json
- Logs in via Telnet, handles pager, runs ONU list + optical diag
- Outputs JSON with "onus" snapshot and "metrics" samples

Args:
  host port username vendor cmd_json
ENV:
  OLT_PASS = password

cmd_json example:
{
  "pon_ports": ["gpON 0/1"],  // optional; if not provided, auto-discover
  "per_onu_optical": true,    // per-ONU optical (reliable, slower)
  "fetch_macs": false         // get client MACs per-ONU (heavy)
}
"""
import os, sys, re, time, json, telnetlib

PROFILES_PATH = os.path.join(os.path.dirname(__file__), 'vendor_profiles.json')

def load_vendor_profile(vendor):
    with open(PROFILES_PATH, 'r', encoding='utf-8') as f:
        profiles = json.load(f)
    # case-insensitive match
    for k in profiles.keys():
        if k.lower() == vendor.lower():
            prof = profiles[k]
            prof['_name'] = k
            return prof
    raise SystemExit(json.dumps({"error": f"Unknown vendor '{vendor}'. Add profile in vendor_profiles.json"}))

def read_until_any(tn, markers, timeout=3.0):
    """Read until any marker (bytes/str) is seen or timeout."""
    if not markers:
        return b""
    bmarkers = [m.encode() if isinstance(m, str) else m for m in markers]
    buf = b""
    end = time.time() + timeout
    while time.time() < end:
        chunk = tn.read_very_eager()
        if chunk:
            buf += chunk
            low = buf.lower()
            for m in bmarkers:
                if m.lower() in low:
                    return buf
        time.sleep(0.02)
    return buf

def login_telnet(host, port, user, password, prof):
    tn = telnetlib.Telnet(host, int(port), timeout=10)
    # login prompt
    read_until_any(tn, prof.get("login_prompts", []), timeout=3)
    tn.write((user + "\n").encode())
    read_until_any(tn, prof.get("password_prompts", []), timeout=2)
    tn.write((password + "\n").encode())
    # reach prompt
    _read_until_prompt(tn, prof, timeout=4)
    # try enable (if asks)
    out = send_cmd(tn, "enable", prof)
    if 'assword' in out:
        tn.write((password + "\n").encode())
        _read_until_prompt(tn, prof, timeout=2)
    return tn

def _read_until_prompt(tn, prof, timeout=5):
    """Read until device prompt regex matches. Handle pager markers by sending space."""
    pager_marks = [m.encode() if isinstance(m, str) else m for m in prof.get("pager_markers", [])]
    prompt_re = re.compile(prof.get("prompt_regex", r'(?:[#>\]])\s*$'), re.M)
    buf = b''
    end = time.time() + timeout
    while time.time() < end:
        chunk = tn.read_very_eager()
        if chunk:
            buf += chunk
            # pager handling
            for pm in pager_marks:
                if pm and pm in buf:
                    tn.write(b' ')
                    time.sleep(0.05)
                    break
            # prompt?
            txt = buf.decode(errors='ignore')
            if prompt_re.search(txt):
                return txt
        time.sleep(0.02)
    return buf.decode(errors='ignore')

def send_cmd(tn, cmd, prof, sleep=0.05, timeout=6):
    tn.write((cmd + "\n").encode())
    time.sleep(sleep)
    return _read_until_prompt(tn, prof, timeout=timeout)

def autodiscover_pon_ports(tn, prof):
    cmd = prof["commands"].get("ports_discover")
    if not cmd:
        return []
    out = send_cmd(tn, cmd, prof)
    rx = prof.get("autodiscover_regex")
    if not rx:
        return []
    ports = sorted(set(re.findall(rx, out, flags=re.I)))
    # normalize spacing/case for BDCOM e.g., 'gpON 0/1'
    return [p.replace('GPON','gpON').strip() for p in ports]

# ----------------------
# Vendor-specific parsers
# ----------------------
def parse_onu_list_bdcom(pon, text):
    items = []
    # Pattern A: "GPON 0/1:<id> ... SN: XXXXX ... State: online"
    patA = re.compile(
        r'(?:GPON|gpON)\s*\d+/\d+:(\d+).*?(?:SN|Loid/SN)\s*[:\s]+([A-Za-z0-9:\-]+).*?(?:State|Status)\s*[:\s]+(online|offline)',
        re.I | re.S)
    for m in patA.finditer(text):
        onu_id, sn, st = m.groups()
        items.append({"pon_port": pon, "onu_id": int(onu_id), "serial_number": sn.strip(), "status": st.lower()})

    # Pattern B (tabular fallback): line has "gpon0/1:1 ... online ... BDCMxxxx"
    if not items:
        for line in text.splitlines():
            if 'gpon' in line.lower() and ':' in line:
                m1 = re.search(r'(?:gpon|GPON)\s*\d+/\d+:(\d+)', line)
                st = 'online' if re.search(r'\bonline\b', line, re.I) else ('offline' if re.search(r'\boffline\b', line, re.I) else None)
                snm = re.search(r'([A-Z]{4}[:\-]?[A-Z0-9]{6,})', line)
                if m1:
                    items.append({
                        "pon_port": pon,
                        "onu_id": int(m1.group(1)),
                        "serial_number": (snm.group(1) if snm else None),
                        "status": st or 'online'
                    })
    return items

def parse_optical_generic(text):
    # Works for many CLIs if lines have Rx/Tx/Temp/Volt keywords
    rx = tx = temp = volt = None
    m = re.search(r'(?i)\brx[^:\n]*[:=]\s*([\-]?\d+\.?\d*)\s*dBm', text)
    if m: rx = float(m.group(1))
    m = re.search(r'(?i)\btx[^:\n]*[:=]\s*([\-]?\d+\.?\d*)\s*dBm', text)
    if m: tx = float(m.group(1))
    m = re.search(r'(?i)\btemp(?:erature)?[^:\n]*[:=]\s*([\-]?\d+\.?\d*)\s*[cC]', text)
    if m: temp = m.group(1)
    m = re.search(r'(?i)\bvolt(?:age)?[^:\n]*[:=]\s*([\-]?\d+\.?\d*)\s*V', text)
    if m: volt = m.group(1)
    # distance (if present)
    dist = None
    md = re.search(r'(?i)\bdistance[^:\n]*[:=]\s*(\d+)\s*m', text)
    if md: dist = int(md.group(1))
    return rx, tx, temp, volt, dist

def parse_mac_list(text):
    # dotted or colon MAC
    macs = re.findall(r'([0-9a-f]{4}\.[0-9a-f]{4}\.[0-9a-f]{4}|[0-9a-f]{2}(:[0-9a-f]{2}){5})', text, flags=re.I)
    # re returns tuples for colon pattern; normalize to strings
    cleaned = []
    for m in macs:
        if isinstance(m, tuple):
            cleaned.append(m[0])
        else:
            cleaned.append(m)
    # dedupe
    return sorted(set(cleaned))

# Router to vendor parser
def parse_onu_list(vendor_name, pon, text):
    name = vendor_name.upper()
    if name == 'BDCOM':
        return parse_onu_list_bdcom(pon, text)
    # Fallback generic (very loose)
    items = []
    for line in text.splitlines():
        m_port = re.search(r'(\d+/\d+)[:](\d+)', line)  # e.g., 0/1:1
        if m_port:
            onu_id = int(m_port.group(2))
            st = 'online' if re.search(r'\bonline\b', line, re.I) else ('offline' if re.search(r'\boffline\b', line, re.I) else 'online')
            sn = None
            ms = re.search(r'\b([A-Z]{4}[:\-]?[A-Z0-9]{6,})\b', line)
            if ms: sn = ms.group(1)
            items.append({"pon_port": pon, "onu_id": onu_id, "serial_number": sn, "status": st})
    return items

# ----------------------
# Main
# ----------------------
def main():
    if len(sys.argv) < 6:
        print(json.dumps({"error": "usage: host port username vendor cmd_json"}))
        sys.exit(1)

    host, port, user, vendor, cmd_json = sys.argv[1:]
    password = os.environ.get("OLT_PASS", "")
    if not password:
        print(json.dumps({"error":"OLT_PASS env is empty"})); sys.exit(1)

    prof = load_vendor_profile(vendor)
    try:
        cfg = json.loads(cmd_json) if cmd_json.strip() else {}
    except Exception as e:
        print(json.dumps({"error": f"invalid cmd_json: {e}"})); sys.exit(1)

    try:
        tn = login_telnet(host, port, user, password, prof)
    except Exception as e:
        print(json.dumps({"error": f"telnet/login failed: {e}"})); sys.exit(1)

    # PON ports
    pon_ports = cfg.get("pon_ports")
    if not pon_ports:
        try:
            pon_ports = autodiscover_pon_ports(tn, prof)
        except Exception:
            pon_ports = []

    onus_all = []
    metrics_all = []

    per_onu_optical = bool(cfg.get("per_onu_optical", True))
    fetch_macs = bool(cfg.get("fetch_macs", False))

    cmds = prof.get("commands", {})

    for pon in pon_ports:
        # ONU list
        onu_cmd = cmds.get("onu_list", "").format(pon=pon)
        out_onu = send_cmd(tn, onu_cmd, prof)
        onus = parse_onu_list(prof.get('_name',''), pon, out_onu)
        onus_all.extend(onus)

        if per_onu_optical:
            # Per ONU optical
            opt_one = cmds.get("optical_one")
            mac_cmd = cmds.get("mac_table")
            for o in onus:
                # derive {pon_if} from pon + onu_id
                # BDCOM expects "gpON 0/1:1"
                if ':' in pon:
                    pon_if = pon  # already with :id ?
                else:
                    pon_if = f"{pon.split()[1]}:{o['onu_id']}" if ' ' in pon else f"{pon}:{o['onu_id']}"
                    # For BDCOM, prepend keyword "gpON " if missing
                    if not pon_if.lower().startswith('gpon') and not pon_if.lower().startswith('gpon') and not pon_if.lower().startswith('gpon '):
                        if pon.lower().startswith('gpon') or pon.lower().startswith('gpon '):
                            pon_if = pon_if
                        elif pon.lower().startswith('gpon'):
                            pon_if = pon_if
                        elif pon.lower().startswith('gpon'):
                            pon_if = pon_if
                        else:
                            # BDCOM: ensure "gpON " at start
                            if pon.lower().startswith('gpon') or pon.lower().startswith('gpon '):
                                pass
                # Build optical-one command
                if opt_one:
                    cmd = opt_one.format(pon_if=pon_if)
                    opt_txt = send_cmd(tn, cmd, prof)
                    rx, tx, t, v, dist = parse_optical_generic(opt_txt)
                    rec = {
                        "pon_port": pon,
                        "onu_id": o.get('onu_id'),
                        "serial_number": o.get('serial_number'),
                        "rx_power": rx, "tx_power": tx,
                        "temperature": t, "voltage": v,
                        "distance": dist
                    }
                    if fetch_macs and mac_cmd:
                        mac_txt = send_cmd(tn, mac_cmd.format(pon_if=pon_if), prof)
                        macs = parse_mac_list(mac_txt)
                        if macs: rec["macs"] = macs
                    metrics_all.append(rec)
        else:
            # Port-wise bulk optical (if vendor supports)
            opt_all = cmds.get("optical_all")
            if opt_all:
                txt = send_cmd(tn, opt_all.format(pon=pon), prof)
                # Best-effort: split per-ONU by line, find onu_id & Rx
                for line in txt.splitlines():
                    mi = re.search(r'(?i)(\d+/\d+)[:](\d+).*?rx[^:]*[:=]\s*([\-]?\d+\.?\d*)', line)
                    if mi:
                        onu_id = int(mi.group(2))
                        rx = float(mi.group(3))
                        tx = re.search(r'(?i)tx[^:]*[:=]\s*([\-]?\d+\.?\d*)', line)
                        t  = re.search(r'(?i)temp[^:]*[:=]\s*([\-]?\d+\.?\d*)', line)
                        v  = re.search(r'(?i)volt[^:]*[:=]\s*([\-]?\d+\.?\d*)', line)
                        metrics_all.append({
                            "pon_port": pon, "onu_id": onu_id,
                            "rx_power": rx,
                            "tx_power": float(tx.group(1)) if tx else None,
                            "temperature": t.group(1) if t else None,
                            "voltage": v.group(1) if v else None
                        })

    try:
        tn.write(b"exit\n")
        tn.close()
    except Exception:
        pass

    print(json.dumps({"onus": onus_all, "metrics": metrics_all}, ensure_ascii=False))

if __name__ == "__main__":
    main()
