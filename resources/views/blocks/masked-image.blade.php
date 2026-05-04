<section
    class="block-masked-image {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}"
    @if($inlineStyles()) style="{!! $inlineStyles() !!}" @endif
>
    @if($imageId())
        <div style="background-image: url('{!! wp_get_attachment_image_url($imageId(), 'very-large') !!}'); @if ($imageFixe()) background-attachment: fixed; @endif" class="block-masked-image__img"></div>
    @endif
</section>
