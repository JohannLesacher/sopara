<header class="banner {{ $is_fixed ? 'is-fixed' : '' }} innergrid" data-header>
  <div class="banner__wrapper">
    <a class="brand" href="{{ home_url('/') }}">
      @svg('logo')
    </a>

    @if($navigation)
      <nav class="banner__nav banner__nav--{{ $align }}" aria-label="Main">
        <ul class="nav-primary">
          @foreach($navigation as $item)
            <li class="menu-item {{ $item->active ? 'is-active' : '' }} {{ $item->children ? 'has-children' : '' }}">
              <a href="{{ $item->url }}">{{ $item->label }}</a>
            </li>
          @endforeach
        </ul>
      </nav>
    @endif

    <div class="banner__actions">
      @if($has_lang && $languages)
        <nav class="banner__lang">
          @foreach($languages as $lang)
            <a href="{{ $lang['url'] }}" class="{{ $lang['current_lang'] ? 'active' : '' }}">
              {{ strtoupper($lang['slug']) }}
            </a>
          @endforeach
        </nav>
      @endif

      @if($navigationCTA)
        <nav class="banner__cta banner__nav banner__nav--{{ $align }}" aria-label="Main">
          <ul class="nav-primary">
            @foreach($navigationCTA as $item)
              <li class="menu-item {{ $item->active ? 'is-active' : '' }}">
                <a href="{{ $item->url }}">{{ $item->label }}</a>
              </li>
            @endforeach
          </ul>
        </nav>
      @endif

      <button class="burger" aria-label="Menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </div>
</header>
