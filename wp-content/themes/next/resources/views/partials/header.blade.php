<header class="banner header-banner desktop-show">
  <div class="container">
    <?php 
    if(!is_page("home")) {
      $logo = get_field('logo', 'option');  
    } else {
      $logo = get_field('home_logo', 'option'); 
      $logofixed = get_field('logo', 'option');  
    }
    ?>
    <div class="row justify-content-end">
      <?php 
      if(!is_page("home")) {
      ?>
      <a class="brand col" href="{{ home_url('/') }}"><?php echo output_inline_svg_file($logo); ?></a>
      <?php } else { ?>
      <a class="brand col home-head-logo" href="{{ home_url('/') }}"><?php echo output_inline_svg_file($logo); ?></a>
      <a class="brand col home-head-logo-fixed" href="{{ home_url('/') }}"><?php echo output_inline_svg_file($logofixed); ?></a>
      <?php } ?>
      <nav class="nav-primary col-auto">
        @if (has_nav_menu('topmenu2'))
          {!! wp_nav_menu(['theme_location' => 'topmenu2', 'menu_class' => 'nav nav-small ml-auto']) !!}
        @endif
        <hr class="hide-scroll">
        <div class="row">
          <div class="col">
            @if (has_nav_menu('primary_navigation'))
              {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']) !!}
            @endif
          </div>
          <div class="col-auto d-flex align-items-center justify-content-center">
            <div class="hamburger" data-toggle="collapse" data-target="#navbarAdditionalContent"><span></span><span></span><span></span></div>
          </div>
        </div>
      </nav>
    </div>
  </div>
  <div class="additional-nav navbar-collapse collapse" id="navbarAdditionalContent">
    <div class="container">
      <div class="row">
        <div class="w20 col-auto additional-nav-title">CASE STUDIES</div>
      </div>
      <div class="row justify-content-end">
        <div class="col">
          <div class="row px-0">
            <?php
              $post_objects = get_field('header_case_study_selector', 'option');
              if( $post_objects ): 
                foreach( $post_objects as $post):
                    $headline = get_field("headline", $post);
                    $content = get_field("brief", $post);
                    $image = get_field('image_for_case_study',$post);
            ?>
                    <div class="casestudy-container col-6">
                      <?php output_acf_img($image,'casestudy-image'); ?>
                      <a href="<?php echo get_post_permalink($post); ?>"><h5><?php echo $headline; ?></h5></a>
                      <div class="casestudy-content"><?php echo $content; ?></div>
                    </div>
            <?php $i++; endforeach; endif; ?>
          </div>
          </div>
          <div class="col-auto">
            @if (has_nav_menu('additional'))
              {!! wp_nav_menu(['theme_location' => 'additional', 'menu_class' => 'nav flex-column']) !!}
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
<header class="banner header-banner mobile-show">
  <div class="container">
    <?php 
    if(!is_page("home")) {
      $logo = get_field('logo', 'option');  
    } else {
      $logo = get_field('home_logo', 'option'); 
      $logofixed = get_field('logo', 'option');  
    }
    ?>
    <div class="row justify-content-end">
      <?php 
      if(!is_page("home")) {
      ?>
      <a class="brand col" href="{{ home_url('/') }}"><?php echo output_inline_svg_file($logo); ?></a>
      <?php } else { ?>
      <a class="brand col home-head-logo" href="{{ home_url('/') }}"><?php echo output_inline_svg_file($logo); ?></a>
      <a class="brand col home-head-logo-fixed" href="{{ home_url('/') }}"><?php echo output_inline_svg_file($logofixed); ?></a>
      <?php } ?>
      <div class="col-auto d-flex align-items-start justify-content-center mobile-nav-768">
        <div class="hamburger" data-toggle="collapse" data-target="#navbarAdditionalContent"><span></span><span></span><span></span></div>
      </div>
    </div>
  </div>
  <div class="additional-nav navbar-collapse collapse" id="navbarAdditionalContent">
    <nav class="nav-primary col-auto">
      @if (has_nav_menu('mobile'))
        {!! wp_nav_menu(['theme_location' => 'mobile', 'menu_class' => 'nav']) !!}
      @endif
    </nav>
  </div>
</header>
