@extends('layouts.app')

@section('content')
  @include('partials.banner')
  @include('partials.breadcrumb')

  @if($intro)
    <div class="archive-intro">{!! $intro !!}</div>
  @endif

  @include('partials.post-grid', ['posts' => $posts, 'paginate' => true])
@endsection
