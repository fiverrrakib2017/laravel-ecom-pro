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
    // $locations = [];
    // foreach ($customers as $customer) {
    //     $locations[] = [
    //         'name' => $customer->name,
    //         'lat' => $customer->latitude,
    //         'lng' => $customer->longitude,
    //         'address' => $customer->address,
    //     ];
    // }

    use Faker\Factory;

    $faker = Factory::create();
    $locations = [];

    for ($i = 0; $i < 500; $i++) {
        $locations[] = [
            'name' => $faker->name,
            'lat' => $faker->randomFloat(6, 23.4200, 23.4700), // কুমিল্লা latitude
            'lng' => $faker->randomFloat(6, 91.1000, 91.2000), // কুমিল্লা longitude
            'address' => $faker->streetAddress . ', Cumilla',
            'status' => $faker->boolean ? 'online' : 'offline',
        ];
    }


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

