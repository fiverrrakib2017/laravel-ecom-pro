@php
    $website_info = App\Models\Website_information::first();
@endphp

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Employee ID Card Generate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        #wrapp {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            width: 100%;
            max-width: 1200px;
            padding: 20px;
        }

        .id-card {
            width: 60.452mm;
            height: 93.856mm;

            background: url('{{ asset('/Backend/images/id_card.png') }}') no-repeat center center/cover;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            padding: 15px;
            position: relative;
            background-color: white;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .school-header {
            font-size: 12px;
            color: #fff;
            text-align: center;
        }

        .school-header h1 {
            font-size: 12px;
            color: #ffffff;
            margin: 0;
        }

        .school-header p {
            font-size: 10px;
            margin: 5px 0;
            color: #ffffff;
        }

        .photo {
            width: 131px;
            height: 130px;
            border-radius: 50%;
            overflow: hidden;
            margin: 15px auto;
            border: 3px solid #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 39px;
        }

        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info {
            font-size: 12px;
            color: #000;
            border-radius: 6px;
            line-height: 1.3;
            text-align: left;
            margin-left: 37px;
        }

        .info p {
            margin: 3px 0;
        }

        /* ID CARD BACK SIDE**/
        .back-side {
            width: 60.452mm;
            height: 93.856mm;
            background: url('{{ asset('/Backend/images/login_photo.jpg') }}') no-repeat center center/cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            color: #000;
            text-align: center;
            position: relative;
            page-break-inside: avoid;
            font-family: Arial, sans-serif;
        }

        .back-inner {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .back-side h3 {
            font-size: 14px;
            margin-top: 8px;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .photo-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #fff;
            margin: 0 auto 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .photo-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-block p {
            font-size: 11px;
            margin: 2px 0;
            line-height: 1.4;
            color: #ffffff;
        }

        .qr-code {
            margin: 10px auto;
            width: 90px;
            height: 90px;
            color: #ffffff;
            background: white;
        }

        .qr-code img {
            width: 100%;
            height: auto;
        }

        .divider {
            width: 60%;
            margin: 8px auto;
            border: none;
            border-top: 1px solid #ffffff;
        }

        .contact-info p {
            font-size: 11px;
            margin: 2px 0;
            color: #ffffff;
        }
    </style>
</head>

<body>
    <div id="wrapp">
        @foreach ($employees as $item)
            {{-- ========== FRONT SIDE ========== --}}
            <div class="id-card">
                <div class="school-header">
                    {{-- <h1>{{ $website_info->name ?? 'Future ICT School' }}</h1>
                    <p>{{ $website_info->address ?? 'Purnota Plaza,Gouripur,Daudkandi,cumilla,Bangladesh' }}</p> --}}
                    <h1>Future ICT </h1>
                    <p>Purnota Plaza,Gouripur,Daudkandi,Cumilla</p>
                </div>
                <div class="photo">
                    <img src="{{ asset(!empty($item->photo) ? 'uploads/photos/' . $item->photo : 'uploads/photos/avatar.png') }}"
                        alt="image">
                    {{-- <img src="{{ asset('/Backend/images/avatar.png') }}"
                        alt="image"> --}}
                </div>
                <div class="info" style="margin-top: 5px;">
                    <p><strong>Name:</strong> {{ $item->name }}</p>
                    <p><strong>F. Name:</strong> {{ $item->father_name }}</p>
                    <p><strong>M. Name:</strong> {{ $item->mother_name }}</p>
                    <p style="color: red;"><strong>Blood Group:</strong> {{ $item->blood_group }}</p>
                    <p><strong>Mobile:</strong> <span style="color: red;">{{ $item->phone }}</span></p>
                </div>
            </div>

            {{-- ========== BACK SIDE ========== --}}
            <div class="id-card back-side">
                <div class="back-inner">
                    <h3>If found, please return to</h3>

                    <div class="photo-circle">
                        {{-- <img src="{{ asset(!empty($website_info->logo) ? 'uploads/photos/' . $website_info->logo : 'uploads/photos/avatar.png') }}"
                            alt="Student Photo"> --}}
                        <img src="https://futureictbd.com/wp-content/uploads/elementor/thumbs/cropped-cropped-Logo_PNG-qfck3vzd7fexozzunwtuizr6gqqp3zs34uq1fu6kgw.png"
                            alt="Student Photo">
                    </div>

                    <div class="info-block">
                        <p><strong>{{ $website_info->school_name ?? 'Future ICT ' }}</strong></p>
                        <p>{{ $website_info->address ?? 'Purnota Plaza, Gouripur, Daudkandi, Cumilla, Bangladesh' }}
                        </p>
                    </div>

                    <div class="qr-code">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg"
                            alt="QR Code">
                    </div>

                    <hr class="divider">

                    <div class="contact-info">
                        <p><strong>Mobile:</strong></p>
                        <p>01757967432</p>
                        <p>01757967432</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        window.addEventListener("load", function() {
            window.print();
        });
    </script>
</body>

</html>
