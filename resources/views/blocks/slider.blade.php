<div
  class="block-slider splide {{ $attributes['className'] ?? '' }} {{ isset($attributes['align']) ? 'align' . $attributes['align'] : '' }}"
  data-splide='{"perPage": {{ $attributes['perPage'] ?? 3 }}, "type": "{{ ($attributes['loop'] ?? false) ? 'loop' : 'slide' }}", "autoplay": {{ ($attributes['autoplay'] ?? false) ? 'true' : 'false' }}}'>
  <div class="splide__track">
    <ul class="splide__list">
      {!! $content !!}
    </ul>
  </div>
</div>
