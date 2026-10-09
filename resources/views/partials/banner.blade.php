<section class="page-banner alignfull">
  @if($bannerImageId)
    {!! wp_get_attachment_image($bannerImageId, 'very-large', false, ['class' => 'page-banner__image', 'alt' => '', 'sizes' => '100vw', 'loading' => 'eager', 'fetchpriority' => 'high']) !!}
  @endif
  <div class="page-banner__inner">
    <h1 class="page-banner__title">{!! $bannerTitle !!}</h1>
  </div>
</section>
