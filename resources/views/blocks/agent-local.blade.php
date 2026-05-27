<section
  class="block-agent-local {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}{{ $is_preview ? ' is-preview' : '' }}">
  <div class="block-agent-local__media">
    @if($imageId())
      {!! wp_get_attachment_image($imageId(), 'large') !!}
    @endif
  </div>

  <div class="block-agent-local__card">
    <div class="block-agent-local__row block-agent-local__row--head">
      <div class="block-agent-local__logo">
        @if($logoId())
          {!! wp_get_attachment_image($logoId(), 'medium') !!}
        @endif
      </div>

      <div class="block-agent-local__identity">
        @if($title())
          <p class="block-agent-local__title">{!! $title() !!}</p>
        @endif
        @if($locationCity() || $locationCountry())
          <p class="block-agent-local__location">
            @svg('pin')
            {!! trim($locationCity() . ($locationCity() && $locationCountry() ? ', ' : '') . $locationCountry()) !!}
          </p>
        @endif
      </div>
      @if($websiteUrl())
        <div class="block-agent-local__cta">
          <div class="wp-block-buttons is-content-justification-right is-layout-flex">
            <div class="wp-block-button is-style-with-icon has-icon-left">
              <a class="wp-block-button__link has-primary-0-background-color has-white-color has-text-color has-link-color has-regular-font-size" href="{{ esc_url($websiteUrl()) }}" target="_blank" rel="noopener">
                {!! $websiteName() !!}
                @svg('buttonIcon', [
                  'fill'=> 'currentColor'
                ])
              </a>
            </div>
          </div>
        </div>
      @endif
    </div>

    @if(count($tags()) > 0)
      <div class="block-agent-local__row block-agent-local__row--tags">
        <ul class="block-agent-local__tags">
          @foreach($tags() as $tag)
            <li class="block-agent-local__tag">{!! $tag !!}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if($contactName() || $contactEmail() || $contactPhone())
      <div class="block-agent-local__row block-agent-local__row--contact">
        <div class="block-agent-local__contact-item">
          @if($contactName())
            <span class="block-agent-local__contact-value">{!! pll__('Contact :') !!} {!! $contactName() !!}</span>
          @endif
        </div>
        <div class="block-agent-local__contact-item">
          @if($contactEmail())
            <a class="block-agent-local__contact-value" href="mailto:{{ $contactEmail() }}">{!! $contactEmail() !!}</a>
          @endif
        </div>
        <div class="block-agent-local__contact-item">
          @if($contactPhone())
            <a class="block-agent-local__contact-value"
               href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone()) }}">{!! $contactPhone() !!}</a>
          @endif
        </div>
      </div>
    @endif
  </div>
</section>
