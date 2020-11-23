<footer class="d-flex">
  <div class="container d-flex align-items-center justify-content-between">
    <div><img src="{{ \App\asset_path('images/COLBY_logotype_white.png') }}" /></div>
    <div class="colby-base-theme-footer-menu">@if (has_nav_menu('footer_navigation'))

      {!! wp_nav_menu(['theme_location' => 'footer_navigation', 'container'=> false, 'menu_class' => 'navbar-nav ml-auto']) !!}
    @endif
  </div>
</footer>
