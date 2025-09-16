<!-- Available SMS -->
                    <div class="d-flex align-items-center bg-light p-2 rounded m-1">
                        <i class="fas fa-comment-dots text-info me-2"></i>
                        <span class="text-dark">
                            <strong> Available SMS Balance:</strong>
                            <strong class="text-danger fw-bold counter-value">

                                @php
    //                             use GuzzleHttp\Client as sms_api;
    //                               $client = new sms_api();

    // $response = $client->get('https://api.bulksmsbd.net/api/v1/get-balance', [
    //     'headers' => [
    //         'Authorization' => 'Bearer SOkFEJZipnBeQ6g2YhvR',
    //     ],
    // ]);

    // $data = json_decode($response->getBody()->getContents(), true);

    // $smsBalance = $data['balance'] ?? 0;
                                @endphp
                                520
                            </strong>
                        </span>
                    </div>
