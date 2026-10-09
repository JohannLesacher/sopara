@extends('layouts.app')

@section('content')
  @include('partials.banner')
  {!! $blogPageContent !!}
@endsection
