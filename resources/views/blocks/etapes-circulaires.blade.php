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
        <div class="block-etapes-circulaires__stage">
            <svg class="block-etapes-circulaires__ring" viewBox="0 0 100 100" aria-hidden="true">
                <circle class="block-etapes-circulaires__ring-base" cx="50" cy="50" r="48" pathLength="100" />
                <circle class="block-etapes-circulaires__ring-arc" cx="50" cy="50" r="48" pathLength="100" />
            </svg>

            @if($image())
                <div class="block-etapes-circulaires__center">
                    <img src="{!! $image() !!}" alt="" loading="lazy">
                </div>
            @endif

            <div class="block-etapes-circulaires__items">
                @foreach($etapes() as $i => $etape)
                    <button
                        type="button"
                        class="block-etapes-circulaires__item"
                        data-index="{{ $i }}"
                        @if($i === 0) data-active @endif
                    >
                        <span class="block-etapes-circulaires__num">{!! str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) !!}</span>
                        <span class="block-etapes-circulaires__box">
                            <span class="block-etapes-circulaires__titre">{!! $etape['titre'] ?? '' !!}</span>
                            <span class="block-etapes-circulaires__texte">{!! $etape['texte'] ?? '' !!}</span>
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="block-etapes-circulaires__mobile-panel" aria-live="polite">
            <h3 class="block-etapes-circulaires__mobile-titre"></h3>
            <div class="block-etapes-circulaires__mobile-texte"></div>
        </div>
    @endif
</section>
