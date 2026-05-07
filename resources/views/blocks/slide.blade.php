<li
  class="{{ implode(' ', $classeNames()) }}"
  @if($animated()) data-animation="{{ $animationType() }}" @endif
>
  {!! $content !!}
</li>
