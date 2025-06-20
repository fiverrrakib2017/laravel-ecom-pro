<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Online Offline Customer Map</h5>
            </div>
            <div class="card-body">
                <div id="customer_googleMap" style="height: 400px; width: 100%;"></div>
            </div>
        </div>

    </div>
</div>

@php


    $locations = [
        [
            'name' => 'Customer 1',
            'lat' => 23.456789,
            'lng' => 91.123456,
            'address' => 'Shasangacha, Cumilla',
            'status' => 'online',
        ],
        [
            'name' => 'Customer 2',
            'lat' => 23.460000,
            'lng' => 91.130000,
            'address' => 'Kandirpar, Cumilla',
            'status' => 'offline',
        ],
        [
            'name' => 'Customer 3',
            'lat' => 23.465000,
            'lng' => 91.135000,
            'address' => 'Court Road, Cumilla',
            'status' => 'online',
        ],
        [
            'name' => 'Customer 4',
            'lat' => 23.470000,
            'lng' => 91.140000,
            'address' => 'Racecourse, Cumilla',
            'status' => 'offline',
        ],
    ];


@endphp
<script>
    function initMap() {
        const center = { lat: {{ $locations[0]['lat'] ?? 23.8103 }}, lng: {{ $locations[0]['lng'] ?? 90.4125 }} };

        const map = new google.maps.Map(document.getElementById("customer_googleMap"), {
            zoom: 15,
            center: center,
        });

        const customers = @json($locations);

        customers.forEach(c => {
    const iconUrl = c.status === 'online'
        ? "{{ asset('Backend/images/wifi_green_icon.png') }}"  // সবুজ WiFi
        : "{{ asset('Backend/images/wifi_red_icon.png') }}" ; // লাল WiFi

    const marker = new google.maps.Marker({
        position: { lat: parseFloat(c.lat), lng: parseFloat(c.lng) },
        map: map,
        title: c.name,
        icon: {
            url: iconUrl,
            scaledSize: new google.maps.Size(32, 32), // আইকনের সাইজ
        },
    });

    const infowindow = new google.maps.InfoWindow({
        content: `<strong>${c.name}</strong><br>${c.address}<br>Status: ${c.status}`,
    });

    marker.addListener("click", () => {
        infowindow.open(map, marker);
    });
});

    }
</script>

<script async
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap">
</script>

