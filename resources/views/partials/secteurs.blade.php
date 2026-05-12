@if($shouldDisplay && ($images || $titre))
  <div class="block-secteurs">
    @if($images)
      <div class="block-secteurs__images">
        @foreach($images as $imageId)
          {!! wp_get_attachment_image($imageId, 'thumbnail', false, ['class' => 'block-secteurs__img']) !!}
        @endforeach
      </div>
    @endif
    @if($titre)
      <p class="block-secteurs__titre">{!! $titre !!}</p>
    @endif
  </div>
@endif
