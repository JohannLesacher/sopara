<section class="block-frise-chronologique {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'full' !!}"
    data-frise data-frise-type="{{ $type() }}">
    <div class="block-frise-chronologique__etapes" data-frise-etapes>
        @foreach ($etapes() as $etape)
            <div class="block-frise-chronologique__etape is-animated" data-animation="fade">
                <span class="block-frise-chronologique__date">{!! $etape['date'] ?? '' !!}</span>
                @if (! empty($etape['image']))
                    <div class="block-frise-chronologique__image">
                      {!! wp_get_attachment_image($etape['image'], 'large', false, [
                        'alt' => $etape['imageAlt']
                      ]) !!}
                    </div>
                @endif
                @if (! empty($etape['titre']))
                    <h3 class="block-frise-chronologique__titre">{!! $etape['titre'] !!}</h3>
                @endif
                @if (! empty($etape['texte']))
                    <div class="block-frise-chronologique__texte">{!! $etape['texte'] !!}</div>
                @endif
            </div>
        @endforeach
    </div>

    <template data-frise-arrows>
        <div class="splide__arrows">
            <button class="splide__arrow splide__arrow--prev" type="button" aria-label="{{ pll__('Diapositive précédente') }}">
                @svg('arrow')
            </button>
            <button class="splide__arrow splide__arrow--next" type="button" aria-label="{{ pll__('Diapositive suivante') }}">
                @svg('arrow')
            </button>
        </div>
    </template>
</section>
