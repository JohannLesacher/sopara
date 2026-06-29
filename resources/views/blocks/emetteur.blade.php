<article class="block-emetteur {!! $blockClass() !!} {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}" style="{!! $blockStyle() !!}" >
  @if($imageId())
    <div class="block-emetteur__image">
      {!! wp_get_attachment_image($imageId(), 'medium', false, [
        'alt' => $imageAlt(),
        'fetchpriority' => 'auto'
      ]) !!}
    </div>
  @endif

  @if($titre())
    <p class="block-emetteur__titre">{!! $titre() !!}</p>
  @endif

  @if($sousTitre())
    <p class="block-emetteur__sous-titre is-style-f1_125-6-1_5">{!! $sousTitre() !!}</p>
  @endif

  @if($texte())
    <p class="block-emetteur__texte is-style-f1-4-1_5">{!! $texte() !!}</p>
  @endif

  @if($lien()['url'])
    <div class="block-emetteur__lien wp-block-buttons is-layout-flex wp-block-buttons-is-layout-flex">
      <div class="wp-block-button is-style-with-icon has-icon-left">
        <a href="{!! $lien()['url'] !!}" target="{!! $lien()['target'] !!}"
           class="wp-block-button__link has-primary-0-background-color has-background wp-element-button has-text-color has-white-color">
          {!! $lien()['title'] ?: $lien()['url'] !!}
          @svg('buttonIcon', [
            'fill' => '#fff',
          ])
        </a>
      </div>
    </div>
  @endif
</article>
