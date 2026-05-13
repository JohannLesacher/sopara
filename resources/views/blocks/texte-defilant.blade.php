@php
    $allowedTags = ['div', 'p', 'h1', 'h2', 'h3'];
    $tag = $data['tag'] ?? 'div';
    $tag = in_array($tag, $allowedTags, true) ? $tag : 'div';
    $texte = $data['texte'] ?? 'Texte défilant';
@endphp
<div class="block-texte-defilant {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'full' !!}"
     data-vitesse="{!! $data['vitesse'] ?? 'normal' !!}"
     data-direction="{!! $data['direction'] ?? 'gauche' !!}"
     data-tag="{{ $tag }}"
     role="marquee"
     aria-label="{{ strip_tags($texte) }}">
    <div class="block-texte-defilant__track">
        <{{ $tag }} class="block-texte-defilant__text">{!! $texte !!}</{{ $tag }}>
    </div>
</div>
