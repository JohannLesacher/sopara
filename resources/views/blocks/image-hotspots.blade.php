<section
  class="block-image-hotspots {!! $attributes['className'] ?? '' !!}"
>
  @if($image())
    <div class="block-image-hotspots__wrapper">
      {!! wp_get_attachment_image($image(), 'very-large', false, [
        'class' => 'block-image-hotspots__image'
      ]) !!}

      @foreach($points() as $index => $point)
        @php
          $title    = trim($point['title'] ?? '');
          $text     = trim($point['text']  ?? '');
          $x        = (float) ($point['pos_x'] ?? 50);
          $y        = (float) ($point['pos_y'] ?? 50);
          $id       = 'hotspot-' . ($attributes['id'] ?? uniqid()) . '-' . $index;
          $cta_text = $point['cta_text'] ?? '';
          $cta_url  = $point['cta_url'] ?? '';
        @endphp

        <div class="block-image-hotspots__point" data-index="{{ $index }}" data-x="{{ $x }}" data-y="{{ $y }}">
          <button
            type="button"
            class="block-image-hotspots__marker"
            aria-expanded="false"
            aria-controls="{!! $id !!}"
            aria-label="{!! $title ?: 'Point ' . ($index + 1) !!}"
          >
          </button>

          <div id="{!! $id !!}" class="block-image-hotspots__tooltip" role="tooltip">
            <button
              type="button"
              class="block-image-hotspots__tooltip-close"
              aria-label="Fermer"
            >
              <span aria-hidden="true">&times;</span>
            </button>
            @if($title)
              <p class="block-image-hotspots__tooltip-title">{!! $title !!}</p>
            @endif
            @if($text)
              <p class="block-image-hotspots__tooltip-text">{!! nl2br(e($text)) !!}</p>
            @endif
            @if($cta_url && $cta_text)
              <div class="wp-block-buttons">
                <div class="wp-block-button is-style-outline">
                  <a
                    href="{{ esc_url($cta_url) }}"
                    class="wp-block-button__link has-secondary-0-color"
                  >{!! $cta_text !!}</a>
                </div>
                @endif
              </div>
          </div>
        </div>
      @endforeach
    </div>

    <ul class="block-image-hotspots__mobile-list">
      @foreach($points() as $index => $point)
        @php
          $title    = trim($point['title'] ?? '');
          $text     = trim($point['text']  ?? '');
          $cta      = $point['cta'] ?? [];
          $cta_url  = $cta['url']    ?? '';
          $cta_text = $cta['title']  ?? '';
          $cta_tgt  = $cta['target'] ?? '';
        @endphp
        <li class="block-image-hotspots__mobile-item">
          <button
            type="button"
            class="block-image-hotspots__mobile-card"
            data-index="{{ $index }}"
            aria-expanded="false"
          >
            @if($title)
              <h3 class="block-image-hotspots__mobile-title">{!! $title !!}</h3>
            @endif
            @if($text)
              <p class="block-image-hotspots__mobile-text">{!! nl2br(e($text)) !!}</p>
            @endif
          </button>
          @if($cta_url && $cta_text)
            <a
              href="{{ esc_url($cta_url) }}"
              class="block-image-hotspots__mobile-cta"
              @if($cta_tgt) target="{{ $cta_tgt }}" rel="noopener noreferrer" @endif
            >{!! $cta_text !!}</a>
          @endif
        </li>
      @endforeach
    </ul>
  @elseif($is_preview)
    <p>Sélectionnez une image pour commencer.</p>
  @endif
  @if ($is_preview)
    <style>
      @foreach($points() as $index => $point)
    .block-image-hotspots__point[data-index="{{ $index }}"] {
        --x: {{ (float) ($point['pos_x'] ?? 50) }}%;
        --y: {{ (float) ($point['pos_y'] ?? 50) }}%;
      }
      @endforeach
    </style>
  @endif
</section>
