<section
  @if(! empty($attributes['anchor'])) id="{!! $attributes['anchor'] !!}" @endif
class="block-etapes-circulaires {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}"
  data-count="{{ $count() }}"
>
  @if($count() < 2)
    @if($is_preview)
      <p class="block-etapes-circulaires__placeholder">Ajoutez au moins 2 étapes.</p>
    @endif
  @else
    <div class="block-etapes-circulaires__stage" style="{!! $gridStyle() !!}">
      <div class="block-etapes-circulaires__center" style="{!! $imageStyle() !!}">
        <svg class="block-etapes-circulaires__ring" viewBox="0 0 100 100" aria-hidden="true">
          <circle class="block-etapes-circulaires__ring-base" cx="50" cy="50" r="48" pathLength="100"/>
          <circle class="block-etapes-circulaires__ring-arc" cx="50" cy="50" r="48" pathLength="100"/>
        </svg>

        @if($image())
          <img class="block-etapes-circulaires__image" src="{!! $image() !!}" alt="{!! get_the_title() !!}"
               loading="lazy">
        @endif

        @foreach($items() as $item)
          <button
            type="button"
            class="block-etapes-circulaires__num"
            data-index="{{ $item['index'] }}"
            style="--nx: {{ $item['nx'] }}; --ny: {{ $item['ny'] }};"
            aria-label="{{ sprintf(pll__('Étape %d'), $item['index'] + 1) }}"
            @if($item['index'] === 0) data-active @endif
          >{!! $item['num'] !!}</button>
        @endforeach
      </div>

      @foreach($items() as $item)
        <button
          type="button"
          class="block-etapes-circulaires__box"
          data-index="{{ $item['index'] }}"
          data-side="{!! $item['side'] !!}"
          style="{!! $item['boxStyle'] !!}"
          @if($item['index'] === 0) data-active @endif
        >
          <span class="block-etapes-circulaires__titre">{!! $item['titre'] !!}</span>
          <span class="block-etapes-circulaires__texte">{!! $item['texte'] !!}</span>
        </button>
      @endforeach
    </div>

    <div class="block-etapes-circulaires__mobile-panel" aria-live="polite">
      @foreach($items() as $item)
        <h3 class="block-etapes-circulaires__mobile-titre">{!! $item['titre'] !!}</h3>
        <div class="block-etapes-circulaires__mobile-texte">{!! $item['texte'] !!}</div>
        @break
      @endforeach
    </div>
  @endif
</section>
