<section class="block-frise-chronologique block-frise-chronologique--{{ $type() }} {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'full' !!}">
    @if ($type() === 'slider')
        <div class="block-frise-chronologique__splide splide">
            <div class="splide__track">
                <ul class="splide__list">
                    @foreach ($etapes() as $i => $etape)
                        <li class="splide__slide">
                            <div class="block-frise-chronologique__etape is-animated" data-animation="fade">
                                <span class="block-frise-chronologique__date">{!! $etape['date'] ?? '' !!}</span>
                                @if (! empty($etape['image']))
                                    <div class="block-frise-chronologique__image">
                                        {!! wp_get_attachment_image($etape['image'], 'large') !!}
                                    </div>
                                @endif
                                @if (! empty($etape['titre']))
                                    <h3 class="block-frise-chronologique__titre">{!! $etape['titre'] !!}</h3>
                                @endif
                                @if (! empty($etape['texte']))
                                    <div class="block-frise-chronologique__texte">{!! $etape['texte'] !!}</div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="splide__arrows">
                <button class="splide__arrow splide__arrow--prev" type="button" aria-label="Previous slide">
                    @svg('arrow')
                </button>
                <button class="splide__arrow splide__arrow--next" type="button" aria-label="Next slide">
                    @svg('arrow')
                </button>
            </div>
        </div>
    @else
        <div class="horizontal-scroll" js-horizontal-scroll>
            <div class="horizontal-scroll__wrapper" js-horizontal-scroll_wrapper>
                <div class="horizontal-scroll__view sync-element-heights" js-horizontal-scroll_view>
                    @foreach ($etapes() as $i => $etape)
                        <div class="block-frise-chronologique__etape is-animated" data-animation="fade">
                            <span class="block-frise-chronologique__date">{!! $etape['date'] ?? '' !!}</span>
                            @if (! empty($etape['image']))
                                <div class="block-frise-chronologique__image">
                                    {!! wp_get_attachment_image($etape['image'], 'large') !!}
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
            </div>
            <div class="horizontal-scroll__scroller" js-horizontal-scroll_scroller></div>
        </div>
    @endif
</section>
