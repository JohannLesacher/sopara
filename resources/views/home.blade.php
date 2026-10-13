@extends('layouts.app')

@section('content')
  @include('partials.banner')
  @include('partials.breadcrumb')
  {!! $blogPageContent !!}
@endsection
