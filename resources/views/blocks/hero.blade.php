<section class="block-hero {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}">
    @if($imageFond())
        @if($imageFixe())
            @php
                $sources = $imageFondSources();
                $styleVars = collect($sources)
                    ->map(fn($url, $size) => "--bg-{$size}:url('{$url}')")
                    ->implode(';');
            @endphp
            <div class="block-hero__bg block-hero__bg--fixed" style="{!! $styleVars !!}" aria-hidden="true"></div>
        @else
            <img class="block-hero__bg" src="{!! $imageFond() !!}" alt="" aria-hidden="true" loading="eager">
        @endif
    @elseif($is_preview)
        <div class="block-hero__bg block-hero__bg--placeholder"></div>
    @endif

    <div class="block-hero__content innergrid">
        <div class="block-hero__center">
            @if($titre())
                <h1 class="block-hero__title">{!! $titre() !!}</h1>
            @endif
        </div>

        <div class="block-hero__bottom">
            <InnerBlocks />
        </div>
    </div>
</section>
