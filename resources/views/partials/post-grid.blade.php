@if($posts)
  <div class="post-grid">
    @foreach($posts as $post)
      @include('partials.post-card', ['post' => $post, 'isFeatured' => false, 'heading' => $heading ?? 'h2'])
    @endforeach
  </div>
@endif

@if($gridPagination)
  <nav class="post-pagination" aria-label="{!! esc_attr(pll__('Pagination')) !!}">
    {!! $gridPagination !!}
  </nav>
@endif
