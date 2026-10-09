<section class="block-post-listing {!! $blockClass() !!} {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}" style="{!! $blockStyle() !!}">
  @if($featured)
    @include('partials.post-card', ['post' => $featured, 'isFeatured' => true])
  @endif

  @include('partials.post-grid', ['posts' => $posts, 'paginate' => $paginate])
</section>
