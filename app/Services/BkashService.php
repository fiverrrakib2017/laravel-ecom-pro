<?php
namespace App\Services;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\Payment_method;
class BkashService
{
    private string $base;
    private string $username;
    private string $password;
    private string $appKey;
    private string $appSecret;
    private string $callback;

    public function __construct()
    {
        if (auth()->guard('admin')->user()->pop_id == null) {
            $data = Payment_method::latest()->where('pop_id',null)->first();
        }else{
            $data = Payment_method::where('pop_id',auth()->guard('admin')->user()->pop_id)->latest()->first();
        }
        $this->base     = $data['base_url'];
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->appKey   = $data['app_key'];
        $this->appSecret= $data['app_secret'];
        $this->callback = $data['callback'];
    }

    private function token(): string
    {
        return Cache::remember('bkash_token', now()->addMinutes(50), function(){
            $resp = Http::withHeaders([
                'username'  => $this->username,
                'password'  => $this->password,
                'Content-Type' => 'application/json'
            ])->post($this->base.'/tokenized/checkout/token/grant', [
                'app_key'    => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

            if(!$resp->ok() || empty($resp['id_token'])){
                throw new \RuntimeException('bKash token fetch failed');
            }
            return $resp['id_token'];
        });
    }

    public function createPayment(array $payload): array
    {
        $token = $this->token();

        $body = [
            'mode'                  => '0011',         // standard checkout
            'amount'                => number_format($payload['amount'], 2, '.', ''),
            'currency'              => 'BDT',
            'intent'                => 'sale',
            'merchantInvoiceNumber' => $payload['invoice'],
            'callbackURL'           => $this->callback,
        ];

        $resp = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key'     => $this->appKey,
            'Content-Type'  => 'application/json',
        ])->post($this->base.'/tokenized/checkout/create', $body);

        if(!$resp->ok() || empty($resp['bkashURL']) || empty($resp['paymentID'])){
            throw new \RuntimeException($resp['statusMessage'] ?? 'bKash create failed');
        }

        return $resp->json();
    }

    public function executePayment(string $paymentID): array
    {
        $token = $this->token();

        $resp = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key'     => $this->appKey,
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
            'X-APP-Key'     => $this->appKey,
            'Content-Type'  => 'application/json',
        ])->get($this->base.'/tokenized/checkout/payment/status', [
            'paymentID' => $paymentID
        ]);

        if(!$resp->ok()){
            throw new \RuntimeException('bKash query failed');
        }
        return $resp->json();
    }
}
