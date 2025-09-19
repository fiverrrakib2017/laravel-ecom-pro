<div class="col-lg-3 col-6 card-item wow animate__animated animate__fadeInUp"
     data-id="{{ $card['id'] }}" data-wow-delay="0.{{ $card['id'] }}s">
    <a href="{{ $card['url'] ?? '#' }}" style="text-decoration: none;">
        <div class="small-box bg-{{ $card['bg'] }}">
            <div class="inner">
                <h3>{{ $card['value'] }}</h3>
                <p>{{ $card['title'] }}</p>
            </div>
            <div class="icon">
                <i class="{{ $card['icon'] }} fa-2x text-gray-300"></i>
            </div>
        </div>
    </a>
</div>
