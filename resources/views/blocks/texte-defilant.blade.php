<div class="block-texte-defilant {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'full' !!}" data-vitesse="{!! $data['vitesse'] ?? 'normal' !!}" data-direction="{!! $data['direction'] ?? 'gauche' !!}">
    <div class="block-texte-defilant__track">
        <span class="block-texte-defilant__text">{!! $data['texte'] ?? 'Texte défilant' !!}</span>
        <span class="block-texte-defilant__text" aria-hidden="true">{!! $data['texte'] ?? 'Texte défilant' !!}</span>
    </div>
</div>
