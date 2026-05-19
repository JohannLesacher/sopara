<div class="{{ implode(' ', $classeNames()) }}"
     data-vitesse="{!! $vitesse() !!}"
     data-direction="{!! $direction() !!}"
     data-tag="{{ $tag() }}"
     role="marquee"
     aria-label="{{ $ariaLabel() }}">
    <div class="block-texte-defilant__track">
        <{{ $tag() }} class="block-texte-defilant__text">{!! $texte() !!}</{{ $tag() }}>
    </div>
</div>
