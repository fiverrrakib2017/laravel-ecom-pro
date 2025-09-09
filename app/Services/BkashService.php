<?php
namespace App\Services;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\Payment_method;
class BkashService
{
    private string $base     = '';
    private string $username = '';
    private string $password = '';
    private string $appKey   = '';
    private string $appSecret= '';
    private string $callback = '';

    public function __construct()
    {
        /*----Auth check----*/
        // $customer = auth('customer')->user()->id;
        // if (!$customer) {
        //     throw new \RuntimeException('No authenticated customer found for bKash initialization.');
        // }

        // $popId = auth()->guard('customer')->user()->pop_id;
        // $data = Payment_method::where(function ($q) use ($popId) {
        //         $q->where('pop_id', $popId)
        //         ->orWhereNull('pop_id');
        //     })
        //     ->orderByRaw('CASE WHEN pop_id = ? THEN 0 ELSE 1 END', [$popId])
        //     ->latest()
        //     ->first();
        $data = Payment_method::where('pop_id',NULL)
            ->latest()
            ->first();
        if (!$data) {
            throw new \RuntimeException('No Payment_method configured');
        }

        $this->base         = rtrim((string)$data->url, '/');
        $this->username     = (string)$data->username;
        $this->password     = (string)$data->password;
        $this->api_key      = (string)$data->api_key;
        $this->api_secret   = (string)$data->api_secret;
        $this->callback     = (string) ($data->callback ?? route('customer.portal', [], true));
        /*------Some Validation---------**/
        foreach (['base','username','password','api_key','api_secret'] as $k) {
            if ($this->{$k} === '') {
                throw new \RuntimeException("bKash credential '{$k}' is missing.");
            }
        }
    }

    private function token(): string
    {
        $cacheKey = 'bkash_token_'.md5($this->username.'|'.$this->api_key.'|'.$this->base);

        return Cache::remember($cacheKey, now()->addMinutes(50), function(){
            $resp = Http::withHeaders([
                'username'     => $this->username,
                'password'     => $this->password,
                'Content-Type' => 'application/json'
            ])->post($this->base.'/tokenized/checkout/token/grant', [
                'app_key'    => $this->api_key,
                'app_secret' => $this->api_secret,
            ]);

            if(!$resp->ok() || empty($resp['id_token'])){
                throw new \RuntimeException($resp['statusMessage'] ?? 'bKash token fetch failed');
            }
            return $resp['id_token'];
        });
    }

    public function createPayment(array $payload): array
    {
        $token = $this->token();
        $customer  = auth()->guard('customer')->user();
        $payerRef  = $this->normalizeMsisdn($customer->mobile ?? $customer->phone ?? null)
                 ?? (string)($customer->id ?? 'GUEST');
        $body = [
            'payerReference'        => $payerRef,
            'mode'                  => '0011',
            'amount'                => number_format((float)$payload['amount'], 2, '.', ''),
            'currency'              => 'BDT',
            'intent'                => 'sale',
            'merchantInvoiceNumber' => $payload['invoice'],
            'callbackURL'           => $this->callback,
        ];


        $resp = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key'     => $this->api_key, 
            'Content-Type'  => 'application/json',
        ])->post($this->base . '/tokenized/checkout/create', $body);

        if (!$resp->ok()) {
            throw new \RuntimeException($resp['statusMessage'] ?? $resp->body() ?? 'bKash create failed');
        }

        $data = $resp->json();
        if (empty($data['bkashURL']) || empty($data['paymentID'])) {
            throw new \RuntimeException('Unexpected bKash create response: ' . json_encode($data));
        }

        return $data;
    }

    public function executePayment(string $paymentID): array
    {
        $token = $this->token();

        $resp = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key'     => $this->api_key,
            'Content-Type'  => 'application/json',
        ])->post($this->base.'/tokenized/checkout/execute', [
            'paymentID' => $paymentID
        ]);

        if(!$resp->ok()){
            throw new \RuntimeException('bKash execute failed');
        }
        return $resp->json();
    }

    public function queryPayment(string $paymentID): array
    {
        $token = $this->token();

        $resp = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key'     => $this->api_key,
            'Content-Type'  => 'application/json',
        ])->get($this->base.'/tokenized/checkout/payment/status', [
            'paymentID' => $paymentID
        ]);

        if(!$resp->ok()){
            throw new \RuntimeException('bKash query failed');
        }
        return $resp->json();
    }
    private function normalizeMsisdn(?string $msisdn): ?string
    {
        if (!$msisdn) return null;
        $msisdn = preg_replace('/\D/', '', $msisdn);
        if (str_starts_with($msisdn, '880')) {
            $msisdn = '0' . substr($msisdn, 3);
        }
        if (strlen($msisdn) === 11 && str_starts_with($msisdn, '01')) {
            return $msisdn;
        }
        return null;
    }
}
