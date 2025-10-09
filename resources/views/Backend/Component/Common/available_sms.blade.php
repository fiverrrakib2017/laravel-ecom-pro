<!-- Available SMS -->
<div class="d-flex align-items-center bg-light p-2 rounded m-1">
    <i class="fas fa-comment-dots text-info me-2"></i>
    <span class="text-dark">
        <strong>SMS Balance: ৳</strong>
        <strong class="text-danger fw-bold counter-value">
            @php
                try {
                    $config = App\Models\Sms_configuration::latest()->first();

                    if ($config) {
                        $url = 'http://bulksmsbd.net/api/getBalanceApi';
                        $data = [
                            'api_key' => $config->api_key,
                        ];

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                        $response = curl_exec($ch);
                        curl_close($ch);

                        $responseData = json_decode($response, true);

                        $smsBalance = $responseData['balance'] ?? 'N/A';
                    } else {
                        $smsBalance = 'N/A';
                    }
                } catch (Exception $e) {
                    $smsBalance = 'N/A'; 
                }
            @endphp
            {{ $smsBalance }}
        </strong>
    </span>
</div>
