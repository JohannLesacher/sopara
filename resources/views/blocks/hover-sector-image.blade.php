<article
  class="block-hover-sector-image {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}{{ $is_preview ? ' is-preview' : '' }} is-animated">
  @if($imageId())
    <div class="block-hover-sector-image__media">
      {!! wp_get_attachment_image($imageId(), 'large', false, [
        'alt' => $imageAlt()
      ]) !!}
    </div>
  @endif

  <div class="block-hover-sector-image__overlay">
    @if($texte())
      <p class="block-hover-sector-image__texte">{!! $texte() !!}</p>
    @endif
  </div>

  @if($tag())
    @if($url())
      <a class="block-hover-sector-image__tag" href="{{ esc_url($url()) }}">{{ $tag() }}</a>
    @else
      <span class="block-hover-sector-image__tag">{{ $tag() }}</span>
    @endif
  @endif
</article>
