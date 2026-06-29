<article
  class="block-hover-card {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}{{ $is_preview ? ' is-preview' : '' }} is-animated">
  <div class="block-hover-card__face block-hover-card__face--front">
    @if($imageId())
      <div class="block-hover-card__media">
        {!! wp_get_attachment_image($imageId(), 'large', false, [
          'alt' => $imageAlt()
        ]) !!}
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
