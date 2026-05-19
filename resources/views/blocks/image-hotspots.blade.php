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
          $title = trim($point['title'] ?? '');
          $text  = trim($point['text']  ?? '');
          $x     = (float) ($point['pos_x'] ?? 50);
          $y     = (float) ($point['pos_y'] ?? 50);
          $id    = 'hotspot-' . ($attributes['id'] ?? uniqid()) . '-' . $index;
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
            @if($title)
              <h3 class="block-image-hotspots__tooltip-title">{!! $title !!}</h3>
            @endif
            @if($text)
              <p class="block-image-hotspots__tooltip-text">{!! nl2br(e($text)) !!}</p>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    <ul class="block-image-hotspots__mobile-list">
      @foreach($points() as $index => $point)
        @php
          $title = trim($point['title'] ?? '');
          $text  = trim($point['text']  ?? '');
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
