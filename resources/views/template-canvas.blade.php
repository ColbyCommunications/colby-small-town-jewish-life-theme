{{--
    Template Name: Colby Base Theme: Canvas
--}}

@extends('layouts.canvas')

@section('content')
    @while(have_posts()) @php the_post() @endphp
        @include('partials.content-page')
    @endwhile
@endsection
