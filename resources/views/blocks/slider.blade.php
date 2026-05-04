<div class="block-slider splide {{ $attributes['className'] ?? '' }} {{ isset($attributes['align']) ? 'align' . $attributes['align'] : '' }}">
    <div class="splide__track">
        <ul class="splide__list">
            {!! $content !!}
        </ul>
    </div>
</div>
