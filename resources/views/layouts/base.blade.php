<!doctype html>
<html {!! get_language_attributes() !!}>
  @include('partials.head')
  <body @php body_class() @endphp>
    @yield('fullbody')
    <script src="{{ get_theme_file_uri() . '/libraries/fslightbox-basic-3.2.1/fslightbox.js' }}"></script>
  </body>
</html>
