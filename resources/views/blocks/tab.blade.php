<div class="block-tab-wrapper">
  <header
    class="block-tab__header has-{!! $attributes['backgroundColor'] ?? '' !!}-background-color has-{!! $attributes['textColor'] ?? '' !!}-color">
    @isset($attributes['title'])
      <h3 class="block-tab__header-title">{!! $attributes['title'] !!}</h3>
    @endisset
  </header>
  <article
    class="block-tab__content has-{!! $attributes['backgroundColor'] ?? '' !!}-background-color has-{!! $attributes['textColor'] ?? '' !!}-color">
    {!! $content !!}
  </article>
</div>
