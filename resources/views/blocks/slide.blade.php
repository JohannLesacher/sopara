<div
  class="{{ implode(' ', $classeNames()) }}"
  @if($animated()) data-animation="{{ $animationType() }}" @endif
>
  {!! $content !!}
</div>
