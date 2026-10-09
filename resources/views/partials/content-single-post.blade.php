@include('partials.banner')

<article class="single-post innergrid alignfull">
  <header class="single-post__header">
    @include('partials.breadcrumb')

    <div class="single-post__glance">
      @if($tags || $category)
        <ul class="single-post__tags">
          @if($category)
            <li><a class="single-post__category" href="{!! esc_url($category['url']) !!}">{!! $category['name'] !!}</a></li>
          @endif

          @foreach($tags as $tag)
            <li><a href="{!! esc_url($tag['url']) !!}">{!! $tag['name'] !!}</a></li>
          @endforeach
        </ul>
      @endif

      @if($summary)
        <p class="single-post__summary">{!! $summary !!}</p>
      @endif

      <div class="single-post__meta">
        @if($experts)
          <ul class="single-post__experts">
            @foreach($experts as $expert)
              <li>
                <a class="single-post__expert" href="{!! esc_url($expert['url']) !!}">
                  @if($expert['imageId'])
                    {!! wp_get_attachment_image($expert['imageId'], 'thumbnail', false, ['class' => 'single-post__expert-photo', 'alt' => '']) !!}
                  @endif
                  <span>{!! $expert['name'] !!}</span>
                </a>
              </li>
            @endforeach
          </ul>
        @endif

        <ul class="single-post__dates">
          <li>{!! pll__('Publié le') !!}
            <time datetime="{!! $publishedIso !!}">{!! $published !!}</time>
          </li>
          @if($isUpdated)
            <li>{!! pll__('Mis à jour le') !!}
              <time datetime="{!! $modifiedIso !!}">{!! $modified !!}</time>
            </li>
          @endif
          @if($readingTime)
            <li>{!! sprintf(pll__('%d min de lecture'), $readingTime) !!}</li>
          @endif
        </ul>
      </div>
    </div>
  </header>

  <div class="single-post__content innergrid alignfull">
    {!! $content !!}
  </div>

  @if($relatedPosts)
    <section class="single-post__related">
      <h2>{!! pll__('Articles liés') !!}</h2>
      @include('partials.post-grid', ['posts' => $relatedPosts, 'paginate' => false, 'heading' => 'h3'])
    </section>
  @endif
</article>
