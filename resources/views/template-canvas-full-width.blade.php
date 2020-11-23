{{--
    Template Name: Colby Base Theme: Canvas Full Width
--}}

@extends('layouts.canvas-full-width')

@section('content')
    @while(have_posts()) @php the_post() @endphp
        @include('partials.content-page')
    @endwhile
@endsection
