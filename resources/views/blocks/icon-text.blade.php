<section class="block-icon-text is-animated {{ $attributes['className'] ?? '' }} align{!! $attributes['align'] ?? 'normal' !!}">
    @if($icone())
        <div class="block-icon-text__icon">
            {!! wp_get_attachment_image($icone(), 'full', false, [
                'class' => 'block-icon-text__icon-img'
            ]) !!}
        </div>
    @endif

    <div class="block-icon-text__content">
        <InnerBlocks />
    </div>
</section>
