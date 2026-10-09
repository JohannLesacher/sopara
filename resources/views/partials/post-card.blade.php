<article class="post-card {!! $cardModifier !!}">
  <a class="post-card__link" href="{!! esc_url($cardPermalink) !!}">
    <div class="post-card__media">
      @if($cardImageId)
        {!! wp_get_attachment_image($cardImageId, $cardImageSize, false, ['class' => 'post-card__image']) !!}
      @endif
    </div>

    <div class="post-card__body">
      @if($cardCategory)
        <span class="post-card__category">{!! $cardCategory !!}</span>
      @endif

      <{!! $cardHeadingTag !!} class="post-card__title">{!! $cardTitle !!}</{!! $cardHeadingTag !!}>

      <ul class="post-card__meta">
        <li><time datetime="{!! $cardDateIso !!}">{!! $cardDate !!}</time></li>
        @if($cardReadingTime)
          <li>{!! sprintf(pll__('%d min de lecture'), $cardReadingTime) !!}</li>
        @endif
        @if($cardExperts)
          <li>{!! $cardExperts !!}</li>
        @endif
      </ul>
    </div>
  </a>
</article>
