@extends('layouts.app')

@section('content')
  @include('partials.banner')
  @include('partials.breadcrumb')

  @if($intro)
    <div class="archive__intro">{!! $intro !!}</div>
  @endif

  <div class="archive__posts">
    @include('partials.post-grid', ['posts' => $posts, 'paginate' => true])
  </div>
@endsection
