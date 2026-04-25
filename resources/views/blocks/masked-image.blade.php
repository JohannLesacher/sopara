<section
    class="block-masked-image {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}"
    @if($inlineStyles()) style="{!! $inlineStyles() !!}" @endif
>
    @if($imageId())
        {!! wp_get_attachment_image($imageId(), 'large', false, ['class' => 'block-masked-image__img']) !!}
    @endif
</section>
