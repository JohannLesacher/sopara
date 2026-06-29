<article
  class="block-image-card {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}{{ $is_preview ? ' is-preview' : '' }}">
  <div class="block-image-card__media">
    {!! wp_get_attachment_image($imageId(), 'large', false, [
      'alt' => $imageAlt()
    ]) !!}
  </div>
  <div class="block-image-card__overlay">
    <div class="block-image-card__content">
      <InnerBlocks/>
    </div>
    @if($ctaLabel() && $ctaUrl())
      <div class="wp-block-buttons">
        <div class="wp-block-button is-style-outline">
          <a class="wp-block-button__link has-primary-0-border-color has-white-color has-text-color has-link-color" href="{{ esc_url($ctaUrl()) }}">
            {{ $ctaLabel() }}
          </a>
        </div>
      </div>
    @endif
    <div class="block-image-card__icon">
      @svg('buttonIcon')
    </div>
  </div>
</article>
