<header class="banner {{ $is_fixed ? 'is-fixed' : '' }} innergrid" data-header>
  <div class="banner__wrapper">
    <a class="brand" href="{{ home_url('/') }}">
      @svg('logo', 'logo', [
      'role' => 'img',
      'aria-label' => 'Sopara',
      ])
    </a>

    @if($navigation)
      <nav class="banner__nav banner__nav--{{ $align }}" aria-label="Main">
        <ul class="nav-primary">
          @foreach($navigation as $item)
            <li class="menu-item {{ $item->active ? 'is-active' : '' }} {{ $item->children ? 'has-children' : '' }}"
                @if($item->children)
                  data-megamenu-trigger
                data-megamenu-image="{{ $item->megamenu_image_id ? wp_get_attachment_image($item->megamenu_image_id, 'large') : '' }}"
                data-megamenu-children="{{ json_encode($item->children) }}"
              @endif>
              <a href="{{ $item->url }}">{!! $item->label !!}</a>
            </li>
          @endforeach
        </ul>
      </nav>
    @endif

    <div class="banner__actions">
      @if($has_lang && $languages)
        <div class="banner__langswitcher">
          @php(
             $langs = pll_the_languages([
               'dropdown' => true,
               'show_flags' => true,
               'display_names_as' => 'slug',
               'hide_current' => false,
               'raw' => true,
             ])
          )
      
          {{-- Conteneur principal du sélecteur personnalisé --}}
          <div class="langswitcher">
            {{-- Bouton affichant la langue actuelle --}}
            <button class="langswitcher-trigger" aria-expanded="false" aria-controls="lang-options">
              @foreach($langs as $lang)
                @if($lang['current_lang'])
                  {!! $lang['flag'] !!}
                  <span class="lang-slug">{{ $lang['slug'] }}</span>
                  <span class="arrow-down"></span>
                @endif
              @endforeach
            </button>
      
            {{-- Liste déroulante des options --}}
            <ul class="langswitcher-options" id="lang-options" data-hidden="true">
              @foreach($langs as $lang)
                <li
                  class="custom-option @if($lang['current_lang']) is-current @endif"
                  data-url="{{ $lang['url'] }}"
                  tabindex="0"
                >
                  {!! $lang['flag'] !!}
                  <span class="lang-slug">{{ $lang['slug'] }}</span>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      <button class="burger" aria-label="{{ pll__('Menu') }}">
        <span class="text">{!! pll__('MENU') !!}</span>
        <span class="close">X</span>
      </button>

      @if($navigationCTA)
        <nav class="banner__cta banner__nav--{{ $align }}" aria-label="CTA">
          <ul class="nav-primary">
            @foreach($navigationCTA as $item)
              <li class="menu-item {{ $item->active ? 'is-active' : '' }}">
                <a href="{{ $item->url }}">{!! $item->label !!}</a>
              </li>
            @endforeach
          </ul>
        </nav>
      @endif
    </div>
  </div>
  <div class="megamenu-container" data-megamenu-container></div>
</header>
