<section
  class="block-image-hotspots {!! $attributes['className'] ?? '' !!}"
>
  @if($imageId())
    <div class="block-image-hotspots__wrapper">
      <div class="block-image-hotspots__image-area">
        {!! wp_get_attachment_image($imageId(), 'very-large', false, [
          'class' => 'block-image-hotspots__image',
          'alt' => $imageAlt(),
        ]) !!}

        @foreach($points() as $index => $point)
          @php
            $title = trim($point['title'] ?? '');
            $x     = (float) ($point['pos_x'] ?? 50);
            $y     = (float) ($point['pos_y'] ?? 50);
            $id    = 'hotspot-' . ($attributes['id'] ?? uniqid()) . '-' . $index;
          @endphp
          <button
            type="button"
            class="block-image-hotspots__marker"
            data-index="{{ $index }}"
            data-x="{{ $x }}"
            data-y="{{ $y }}"
            aria-controls="{!! $id !!}"
            aria-expanded="false"
            aria-label="{!! $title ?: sprintf(pll__('Point %d'), $index + 1) !!}"
          ></button>
        @endforeach
      </div>

      <div class="block-image-hotspots__points">
        @foreach($points() as $index => $point)
          @php
            $title    = trim($point['title'] ?? '');
            $text     = trim($point['text']  ?? '');
            $x        = (float) ($point['pos_x'] ?? 50);
            $y        = (float) ($point['pos_y'] ?? 50);
            $id       = 'hotspot-' . ($attributes['id'] ?? uniqid()) . '-' . $index;
            $cta_text = trim($point['cta_text'] ?? '');
            $cta_url  = trim($point['cta_url']  ?? '');
          @endphp

          <div
            class="block-image-hotspots__point"
            data-index="{{ $index }}"
            data-x="{{ $x }}"
            data-y="{{ $y }}"
          >
            <div id="{!! $id !!}" class="block-image-hotspots__panel" role="region">
              <button
                type="button"
                class="block-image-hotspots__close"
                aria-label="{{ pll__('Fermer') }}"
              >
                <span aria-hidden="true">&times;</span>
              </button>

              <button
                type="button"
                class="block-image-hotspots__panel-toggle"
                aria-expanded="false"
              >
                @if($title)
                  <h3 class="block-image-hotspots__title">{!! $title !!}</h3>
                @endif
              </button>

              <div class="block-image-hotspots__body">
                @if($text)
                  <p class="block-image-hotspots__text">{!! nl2br(e($text)) !!}</p>
                @endif
                @if($cta_url && $cta_text)
                  <div class="wp-block-buttons block-image-hotspots__cta">
                    <div class="wp-block-button is-style-outline">
                      <a
                        href="{{ esc_url($cta_url) }}"
                        class="wp-block-button__link has-secondary-0-color"
                      >{!! $cta_text !!}</a>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @elseif($is_preview)
    <p>Sélectionnez une image pour commencer.</p>
  @endif
  @if ($is_preview)
    <style>
      @foreach($points() as $index => $point)
    .block-image-hotspots__marker[data-index="{{ $index }}"],
    .block-image-hotspots__point[data-index="{{ $index }}"] {
        --x: {{ (float) ($point['pos_x'] ?? 50) }}%;
        --y: {{ (float) ($point['pos_y'] ?? 50) }}%;
      }
      @endforeach
    </style>
  @endif
</section>
