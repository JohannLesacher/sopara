<section class="block-hero {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}">
    @if($imageFond())
        <img class="block-hero__bg" src="{!! $imageFond() !!}" alt="" aria-hidden="true" loading="eager">
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

@if($imagesSecteurs() || $titreSecteurs())
  <div class="block-hero__secteurs">
    @if($imagesSecteurs())
      <div class="block-hero__secteurs-images">
        @foreach($imagesSecteurs() as $imageId)
          {!! wp_get_attachment_image($imageId, 'thumbnail', false, ['class' => 'block-hero__secteurs-img']) !!}
        @endforeach
      </div>
    @endif
    @if($titreSecteurs())
      <p class="block-hero__secteurs-titre">{!! $titreSecteurs() !!}</p>
    @endif
  </div>
@endif
