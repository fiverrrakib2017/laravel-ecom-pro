<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="rakibas375">
    <!-- Site Title -->
    <title>Sales Billing Invoice</title>
    <link rel="stylesheet" href="{{asset('Backend/assets/css/style.css')}}">
    <style>
        .tm_invoice.tm_style1 .tm_logo img {
            max-height: 100px;
        }
    </style>
</head>

<body style="margin-top: 1%">
    <div class="tm_container">
        <div class="tm_invoice_wrap">
            <div class="tm_invoice tm_style1" id="tm_download_section">
                <div class="tm_invoice_in">
                    <div class="tm_invoice_head tm_mb20">
                        <div class="tm_invoice_left">
                            <div class="tm_logo"><img src="{{ asset('Backend/assets/images/it-fast.png') }}" alt="Logo"><br> </div>
                        </div>
                        <div class="tm_invoice_right tm_text_right">
                            <div class="tm_primary_color tm_f50 tm_text_uppercase">Invoice</div>
                        </div>
                    </div>
                    <div class="tm_invoice_info tm_mb20">
                        <div class="tm_invoice_seperator tm_gray_bg"></div>
                        <div class="tm_invoice_info_list">
                            <p class="tm_invoice_number tm_m0">Invoice No: <b class="tm_primary_color">{{$data->id}}</b></p>
                            <p class="tm_invoice_date tm_m0">Date:
                            {{ \Carbon\Carbon::parse($data->invoice_date)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="tm_invoice_head tm_mb10">
                        <div class="tm_invoice_left">
                            <p class="tm_mb2 tm_f16"><b class="tm_primary_color tm_text_uppercase">STAR COMMUNICATION</b></p>
                            <p>
                                Sarkar super market 2nd floor <br>Gouripur Bazar, Daudkandi, Cumilla <br>
                                www.sr-communication.com <br>
                                hello@sr-cummunication.com<br>
                                01580-651309
                            </p>
                        </div>
                        <div class="tm_invoice_right" style="width:69% !important">
                            <div class="tm_grid_row tm_col_2  tm_col_2_sm tm_invoice_table tm_round_border">

                                <div>
                                    <p class="tm_m0">Client ID:</p>
                                    <b class="tm_primary_color">{{$data->client->id}}</b>
                                </div>
                                <div>
                                    <p class="tm_m0">Client Name:</p>
                                    <b class="tm_primary_color">{{$data->client->fullname}}</b>
                                </div>
                                <div>
                                    <p class="tm_m0">Phone Number:</p>
                                    <b class="tm_primary_color">{{$data->client->phone_number}}</b>
                                </div>


                                <div>
                                    <p class="tm_m0">Client Create Date</p>
                                    <b class="tm_primary_color">
                                    {{ \Carbon\Carbon::parse($data->client->created_at)->format('d M Y') }}

                                    </b>
                                </div>




                                <div>
                                    <p class="tm_m0">Address</p>
                                    <b class="tm_primary_color">
                                        {{$data->client->address}}
                                    </b>
                                </div>



                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="tm_table tm_style1">
                        <div class="tm_round_border">
                            <div class="tm_table_responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="tm_width_4 tm_semi_bold tm_primary_color">Item Name</th>

                                            <th class="tm_width_2 tm_semi_bold tm_primary_color">Quantity</th>

                                            <th class="tm_width_2 tm_semi_bold tm_primary_color">Amount</th>

                                            <th class="tm_width_2 tm_semi_bold tm_primary_color tm_text_right">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data->items as $item)
                                            <tr class="tm_gray_bg">
                                            <td class="tm_width_4">
                                                {{ $item->product->name }}</td>

                                            <td class="tm_width_2 "><b>{{ $item->qty}}</b></td>

                                            <td class="tm_width_2 "><b>{{ $item->price  }} ৳</b></td>

                                            <td class="tm_width_2 tm_text_right"><b> {{ $item->total_price }} ৳</b></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tm_invoice_footer">
                            <div class="tm_left_footer">

                            </div>
                            <div class="tm_right_footer">
                                <table>
                                    <tbody>
                                    <tr class="table_footer_row">
                                        <td colspan="3"><strong>Total Amount</strong></td>
                                        <td><strong>{{ isset($data->sub_total) ? number_format(floatval($data->sub_total), 0, '.', '') : '0' }}৳</strong> </td>
                                    </tr>
                                    <tr class="table_footer_row">
                                        <td colspan="3"><strong>Paid Amount</strong></td>
                                        <td><strong>{{ isset($data->paid_amount) ? number_format(floatval($data->paid_amount), 0, '.', '') : '0' }}৳</strong></td>
                                    </tr>



                                    <tr class="table_footer_row">
                                        <td colspan="3"><strong>Due Amount</strong></td>
                                        <td><strong>{{ isset($data->due_amount) ? number_format(floatval($data->due_amount), 0, '.', '') : '0' }}৳</strong></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tm_note tm_text_center tm_m0_md">
                        <p class="tm_m0" align=""><br> <br>Authorization signature and seal. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;
                            Prepared By:  {{ Auth::guard('admin')->user()->name ?? 'N/A' }}
                        </p>
                    </div><!-- .tm_note -->
                </div>
            </div>
            <div class="tm_invoice_btns tm_hide_print">
                <a href="javascript:window.print()" class="tm_invoice_btn tm_color1">
                    <span class="tm_btn_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                            <path d="M384 368h24a40.12 40.12 0 0040-40V168a40.12 40.12 0 00-40-40H104a40.12 40.12 0 00-40 40v160a40.12 40.12 0 0040 40h24" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                            <rect x="128" y="240" width="256" height="208" rx="24.32" ry="24.32" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                            <path d="M384 128v-24a40.12 40.12 0 00-40-40H168a40.12 40.12 0 00-40 40v24" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                            <circle cx="392" cy="184" r="24" fill='currentColor' />
                        </svg>
                    </span>
                    <span class="tm_btn_text">Print</span>
                </a>
                <button id="tm_download_btn" class="tm_invoice_btn tm_color2">
                    <span class="tm_btn_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                            <path d="M320 336h76c55 0 100-21.21 100-75.6s-53-73.47-96-75.6C391.11 99.74 329 48 256 48c-69 0-113.44 45.79-128 91.2-60 5.7-112 35.88-112 98.4S70 336 136 336h56M192 400.1l64 63.9 64-63.9M256 224v224.03" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" />
                        </svg>
                    </span>
                    <span class="tm_btn_text">Download</span>
                </button>
                <button id="tm_download_btn" class="tm_invoice_btn tm_color3" onclick="window.history.back()">
                    <span class="tm_btn_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                            <path d="M328 112L184 256l144 144" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48"/>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
</body>

</html>
