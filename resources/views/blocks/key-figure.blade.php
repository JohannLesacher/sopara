<div class="block-key-figure {!! $blockClass() !!} {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}" style="{!! $blockStyle() !!}">
  <p class="block-key-figure__value {!! $valueColorClass !!}" @if($valueColorStyle) style="{!! $valueColorStyle !!}" @endif>
    {{ $valeur }}
    @if($unite)
      <span class="block-key-figure__unit">{{ $unite }}</span>
    @endif
  </p>
  @if($libelle)
    <p class="block-key-figure__label">{{ $libelle }}</p>
  @endif
</div>
