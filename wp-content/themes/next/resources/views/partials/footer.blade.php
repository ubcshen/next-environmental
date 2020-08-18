<footer class="content-info">
  <div class="container white-font">
    <?php $logo = get_field('logo', 'option');  ?>
    <div class="row">
      <div class="col-12 col-md-3 col-lg-4"><a class="brand" href="{{ home_url('/') }}"><?php echo output_inline_svg_file($logo); ?></a></div>
      <div class="col-12 col-md-9 col-lg-8">
        <div class="row">
          <div class="col-12 col-md-2">
            @if (has_nav_menu('footerlink1'))
              {!! wp_nav_menu(['theme_location' => 'footerlink1', 'menu_class' => 'list']) !!}
            @endif
          </div>
          <div class="col-12 col-md-3">
            @if (has_nav_menu('footerlink2'))
              {!! wp_nav_menu(['theme_location' => 'footerlink2', 'menu_class' => 'list']) !!}
            @endif
          </div>
          <div class="col-12 col-md-3 pl-md-0">
            <div class="f16 bold">CONTACT US</div>
            @if (has_nav_menu('contact'))
              {!! wp_nav_menu(['theme_location' => 'contact', 'menu_class' => 'list f16']) !!}
            @endif
          </div>
          <div class="col-12 col-md-4 container-fluid">
            <div class="row">
              <?php
                while(has_sub_field('social_media','option')):
                  $image = get_sub_field('icon');
                  $link = get_sub_field('link');
              ?>
              <div class="col social-icon">
                  <a class="sm-icon" href="<?php echo $link; ?>" target="_blank"><?php echo output_inline_svg_file($image); ?></a>
              </div>
              <?php endwhile; ?>
            </div>
            <div class="f16">&copy;<?php echo date('Y'); ?> <?php echo get_field("company", 'option'); ?></div>
            <div class="f16"><?php echo get_field("copyright", 'option'); ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>
