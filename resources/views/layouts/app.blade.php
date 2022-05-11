<a class="sr-only focus:not-sr-only focus:absolute focus:text-colby-blue z-30 top-0 focus:w-auto focus:h-auto focus:p-0 focus:m-0 focus:overflow-visible focus:whitespace-normal" style="clip:auto;" href="#main">

  {{ __('Skip to content') }}
</a>

@include('sections.header')

<main id="main" class="main">
  <div class="mx-auto container px-4">
    @yield('content')
  </div>
</main>
<div id="vue-menu"></div>


@include('sections.footer')
