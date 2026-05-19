<div class="{{ implode(' ', $classeNames()) }}" @foreach ($dataAttributes() as $key => $value) {{ $key }}="{{ $value }}" @endforeach>
  <div class="splide__track">
    <ul class="splide__list">
      {!! $content !!}
    </ul>
  </div>
  @if ($showArrows())
    <div class="splide__arrows">
      <button
        class="splide__arrow splide__arrow--prev"
        type="button"
        aria-label="Previous slide"
        aria-controls="splide01-track"
      >
        @svg('arrow')
      </button>
      <button
        class="splide__arrow splide__arrow--next"
        type="button"
        aria-label="Next slide"
        aria-controls="splide01-track"
      > @svg('arrow')
      </button>
    </div>
  @endif
</div>
