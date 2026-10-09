@extends('layouts.app')

@section('content')
  @include('partials.banner')

  <article class="expert innergrid alignfull">
    @include('partials.breadcrumb')

    <header class="expert__header">
      @if($photoId)
        <div class="expert__photo">
          {!! wp_get_attachment_image($photoId, 'medium', false, ['class' => 'expert__image']) !!}
        </div>
      @endif

      <div class="expert__identity">
        @if($fonction)
          <p class="expert__fonction">{!! $fonction !!}</p>
        @endif

        <dl class="expert__details">
          @if($expertise)
            <div>
              <dt>{!! pll__('Expertise') !!}</dt>
              <dd>{!! $expertise !!}</dd>
            </div>
          @endif
          @if($experience)
            <div>
              <dt>{!! pll__('Expérience') !!}</dt>
              <dd>{!! sprintf(pll__('%d ans'), $experience) !!}</dd>
            </div>
          @endif
        </dl>

        @if($linkedin)
          <a class="expert__linkedin" href="{!! esc_url($linkedin) !!}" target="_blank" rel="me noopener">LinkedIn</a>
        @endif
      </div>
    </header>

    @if($content)
      <div class="expert__content innergrid alignfull">
        {!! $content !!}
      </div>
    @endif
  </article>
@endsection
