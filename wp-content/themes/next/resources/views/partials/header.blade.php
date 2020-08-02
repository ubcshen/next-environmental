<header class="banner header-banner">
  <div class="container">
    <?php $logo = get_field('logo', 'option');  ?>
    <div class="row justify-content-end">
      <a class="brand col" href="{{ home_url('/') }}"><?php echo output_inline_svg_file($logo); ?></a>
      <nav class="nav-primary col-auto">
        @if (has_nav_menu('topmenu2'))
          {!! wp_nav_menu(['theme_location' => 'topmenu2', 'menu_class' => 'nav nav-small ml-auto']) !!}
        @endif
        <hr>
        <div class="row">
          <div class="col">
            @if (has_nav_menu('primary_navigation'))
              {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']) !!}
            @endif
          </div>
          <div class="col-auto">
            <div class="hamburger"><span></span><span></span><span></span></div>
          </div>
        </div>
      </nav>
    </div>
  </div>
</header>
