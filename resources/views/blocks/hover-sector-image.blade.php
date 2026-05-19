<article
  class="block-hover-sector-image {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}{{ $is_preview ? ' is-preview' : '' }}">
  @if($imageSrc())
    <div class="block-hover-sector-image__media">
      <img src="{!! $imageSrc() !!}" alt="" />
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
