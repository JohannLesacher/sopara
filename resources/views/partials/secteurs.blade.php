@if($shouldDisplay && ($images || $titre))
  <div class="block-secteurs">
    @if($images)
      <div class="block-secteurs__images">
        @foreach($images as $index => $imageId)
          <a href="{!! get_the_permalink(pll_get_post($pages[$index] ?? 0)) !!}">
            {!! wp_get_attachment_image($imageId, 'thumbnail', false, [
              'class' => 'block-secteurs__img',
              'alt' => get_the_title($pages[$index]),
            ]) !!}
          </a>
        @endforeach
      </div>
    @endif
    @if($titre)
      <p class="block-secteurs__titre">{!! $titre !!}</p>
    @endif
  </div>
@endif
