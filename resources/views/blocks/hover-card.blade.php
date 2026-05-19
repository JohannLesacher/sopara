<article
  class="block-hover-card {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}{{ $is_preview ? ' is-preview' : '' }} is-animated">
  <div class="block-hover-card__face block-hover-card__face--front">
    @if($imageSrc())
      <div class="block-hover-card__media">
        <img src="{!! $imageSrc() !!}" alt="" />
      </div>
    @endif
    @if($titre())
      <h3 class="block-hover-card__titre">{!! $titre() !!}</h3>
    @endif
  </div>
  <div class="block-hover-card__face block-hover-card__face--back">
    <p class="block-hover-card__texte">{!! $texte() !!}</p>
  </div>
</article>
