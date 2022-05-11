<footer class="flex">
  <div class="container mx-auto flex items-center my-8 sm:my-16 sm:justify-between flex-col sm:flex-row">
    <div><img src="@asset('images/COLBY_logotype_white.png')" /></div>
    <div class="colby-base-theme-footer-menu" >
      @if (has_nav_menu('primary_navigation'))
        <nav class="nav-primary" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
          {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav', 'echo' => false]) !!}
        </nav>
      @endif
  </div>
</footer>