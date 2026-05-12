@php
  $splideData = [
      'perPage' => $attributes['perPage'] ?? 3,
      'type' => ($attributes['loop'] ?? false) ? 'loop' : 'slide',
      'autoplay' => $attributes['autoplay'] ?? false,
  ];
  if (isset($attributes['perPageTablet'])) {
      $splideData['perPageTablet'] = $attributes['perPageTablet'];
  }
  if (isset($attributes['perPageMobile'])) {
      $splideData['perPageMobile'] = $attributes['perPageMobile'];
  }
@endphp
<div
  class="block-slider splide {{ $attributes['className'] ?? '' }} {{ isset($attributes['align']) ? 'align' . $attributes['align'] : '' }}"
  data-splide='{{ json_encode($splideData) }}'>
  <div class="splide__track">
    <ul class="splide__list">
      {!! $content !!}
    </ul>
  </div>
</div>
