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
        aria-label="{{ pll__('Diapositive précédente') }}"
        aria-controls="splide01-track"
      >
        @svg('arrow')
      </button>
      <button
        class="splide__arrow splide__arrow--next"
        type="button"
        aria-label="{{ pll__('Diapositive suivante') }}"
        aria-controls="splide01-track"
      > @svg('arrow')
      </button>
    </div>
  @endif
</div>
