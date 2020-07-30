<?php

/**
 * Custom functions
 */

// Remove Open Sans that WP adds from frontend
if (!function_exists('remove_wp_open_sans')) :
function remove_wp_open_sans() {
wp_deregister_style( 'open-sans' );
wp_register_style( 'open-sans', false );
}
add_action('wp_enqueue_scripts', 'remove_wp_open_sans');
endif;

add_filter( 'rest_endpoints', function( $endpoints ){
    if ( isset( $endpoints['/wp/v2/users'] ) ) {
        unset( $endpoints['/wp/v2/users'] );
    }
    if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
        unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    return $endpoints;
});

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

add_action( 'init', function() {
  // Remove the REST API endpoint.
  remove_action('rest_api_init', 'wp_oembed_register_route');
  // Turn off oEmbed auto discovery.
  // Don't filter oEmbed results.
  remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
  // Remove oEmbed discovery links.
  remove_action('wp_head', 'wp_oembed_add_discovery_links');
  // Remove oEmbed-specific JavaScript from the front-end and back-end.
  remove_action('wp_head', 'wp_oembed_add_host_js');
}, PHP_INT_MAX - 1 );  // remove the wp-embed.min.js file from the frontend completely

function multiexplode ($delimiters,$string) {

    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

//remove wordpress dns-prefetch
function remove_dns_prefetch( $hints, $relation_type ) {
    if ( 'dns-prefetch' === $relation_type ) {
        return array_diff( wp_dependencies_unique_hosts(), $hints );
    }

    return $hints;
}

add_filter( 'wp_resource_hints', 'remove_dns_prefetch', 10, 2 );


if( !defined('NEXT_PAGE_PATH') ){
    define('NEXT_PAGE_PATH', get_template_directory() .'/' );
}
require_once NEXT_PAGE_PATH . 'Mobile-Detect/Mobile_Detect.php';

/*if( function_exists('acf_add_options_page') ) {
  // add parent
  $parent = acf_add_options_page(array(
    'page_title'  => 'N’kmip Campground Settings',
    'menu_title'  => 'N’kmip Campground Settings',
    'redirect'    => false
  ));

  acf_add_options_sub_page(array(
    'page_title'  => 'Footer CopyRight',
    'menu_title'  => 'Footer CopyRight',
    'menu_slug'   => 'footer-copyright',
    'parent_slug'   => $parent['menu_slug'],
    'capability'  => 'activate_plugins',
    'redirect'    => false
  ));

  acf_add_options_sub_page(array(
    'page_title'  => 'Header Setting',
    'menu_title'  => 'Header Setting',
    'menu_slug'   => 'header_setting',
    'parent_slug'   => $parent['menu_slug'],
    'capability'  => 'activate_plugins',
    'redirect'    => false
  ));
}*/

if ( function_exists( 'add_theme_support' ) ) {
  add_theme_support( 'post-thumbnails' );
  set_post_thumbnail_size( 150, 150 ); // default Post Thumbnail dimensions
}

if ( function_exists( 'add_image_size' ) ) {
  add_image_size('team', 424, 520,  array( 'left', 'top' ));
}

//get primary category name in Wordpress
if ( ! function_exists( 'get_primary_taxonomy_id' ) ) {
  function get_primary_taxonomy_id( $post_id, $taxonomy ) {
      $prm_term = '';
      if (class_exists('WPSEO_Primary_Term')) {
          $wpseo_primary_term = new WPSEO_Primary_Term( $taxonomy, $post_id );
          $prm_term = $wpseo_primary_term->get_primary_term();
      }
      if ( !is_object($wpseo_primary_term) && empty( $prm_term ) ) {
          $term = wp_get_post_terms( $post_id, $taxonomy );
          if (isset( $term ) && !empty( $term ) ) {
              return wp_get_post_terms( $post_id, $taxonomy )[0]->term_id;
          } else {
              return '';
          }
      }
      return $wpseo_primary_term->get_primary_term();
  }
}

function load_Img($className, $fieldName) { ?>

    <!--[if lt IE 9]>
    <script>
        $(document).ready(function() {
            $("<?php print $className ?>").backstretch("<?php $img=wp_get_attachment_image_src(get_sub_field($fieldName), "full"); echo $img[0];  ?>");
        });
    </script>
    <![endif]-->

  <style scoped>
  <?php echo $className; ?> {
    background-image: url(<?php $img=wp_get_attachment_image_src(get_sub_field($fieldName), "full"); echo $img[0];  ?>);
        background-repeat:no-repeat;
        background-position: center center;
        background-size: cover;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
  }
  @media only screen and (max-width: 1024px) {
    <?php echo $className; ?> {
      background-image: url(<?php $img=wp_get_attachment_image_src(get_sub_field($fieldName), "large"); echo $img[0];  ?>);
    }
  }
  </style>
  <?php
    $detect = new Mobile_Detect;
    $css_code = "<style scoped>";
    if ( $detect->isMobile() )
    {
      $css_code .= $className . ' {background-attachment: scroll;}';
    }
    $css_code .= "</style>";
    echo $css_code;
}

function load_Tax_Img($className, $fieldName, $hasTerm) { ?>

    <!--[if lt IE 9]>
    <script>
        $(document).ready(function() {
            $("<?php print $className ?>").backstretch("<?php $img=wp_get_attachment_image_src(get_field($fieldName, $hasTerm), "full"); echo $img[0];  ?>");
        });
    </script>
    <![endif]-->

  <style scoped>
  <?php echo $className; ?> {
    background-image: url(<?php $img=wp_get_attachment_image_src(get_field($fieldName, $hasTerm), "full"); echo $img[0];  ?>);
        background-repeat:no-repeat;
        background-position: center center;
        background-size: cover;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
  }
  @media only screen and (max-width: 1024px) {
    <?php echo $className; ?> {
      background-image: url(<?php $img=wp_get_attachment_image_src(get_field($fieldName, $hasTerm), "large"); echo $img[0];  ?>);
    }
  }
  </style>
  <?php
    $detect = new Mobile_Detect;
    $css_code = "<style scoped>";
    if ( $detect->isMobile() )
    {
      $css_code .= $className . ' {background-attachment: scroll;}';
    }
    $css_code .= "</style>";
    echo $css_code;
}

function load_Feature_Img($className, $fieldName) { ?>

  <style scoped>
  <?php echo $className; ?> {
    background-image: url(<?php $img=wp_get_attachment_image_src( get_post_thumbnail_id( $fieldName ), "full" ); echo $img[0];  ?>);
        background-repeat:no-repeat;
        background-position: center center;
        background-size: cover;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
  }
  @media only screen and (max-width: 1024px) {
    <?php echo $className; ?> {
      background-image: url(<?php $img=wp_get_attachment_image_src( get_post_thumbnail_id( $fieldName ),  "large"); echo $img[0];  ?>);
    }
  }
  </style>
  <?php
    $detect = new Mobile_Detect;
    $css_code = "<style scoped>";
    if ( $detect->isMobile() )
    {
      $css_code .= $className . ' {background-attachment: scroll;}';
    }
    $css_code .= "</style>";
    echo $css_code;
}

function load_Feature_Img_Item($className, $fieldName, $size) { ?>

  <style scoped>
  <?php echo $className; ?> {
    background-image: url(<?php $img=wp_get_attachment_image_src( get_post_thumbnail_id( $fieldName ), $size ); echo $img[0];  ?>);
        background-repeat:no-repeat;
        background-position: center center;
        background-size: cover;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
  }
  </style>
  <?php
    $detect = new Mobile_Detect;
    $css_code = "<style scoped>";
    if ( $detect->isMobile() )
    {
      $css_code .= $className . ' {background-attachment: scroll;}';
    }
    $css_code .= "</style>";
    echo $css_code;
}

function load_Img_no_mobile_not_sub($className, $fieldName) { ?>
  <style>
  <?php echo $className; ?> {
    background-image: url(<?php $img=wp_get_attachment_image_src(get_field($fieldName), "full"); echo $img[0];  ?>);
  }
  @media only screen and (max-width: 1024px) {
    <?php echo $className; ?> {
      background-image: url(<?php $img=wp_get_attachment_image_src(get_field($fieldName), "large"); echo $img[0];  ?>);
    }
  }
  @media only screen and (max-width: 640px) {
    <?php echo $className; ?> {
      background: none;
    }
  }
  </style>
  <?php
}

//display image on next theme
function output_acf_img($field, $class = '') {
  $detect = new Mobile_Detect;

  if ( $detect->isMobile() && !$detect->isTablet() ) {
    $size = 'large';
    $large = $field['sizes'][ $size ];
    $width = $field['sizes'][ $size . '-width' ];
    $height = $field['sizes'][ $size . '-height' ];

    echo get_lazy_load_img($large, $field['alt'], $class, $width, $height);
  } else {
    echo get_lazy_load_img($field['url'], $field['alt'], $class, $field['width'], $field['height']);
  }
}

//display an svg image on next theme
function output_inline_svg_file($fileURL) {
  //Get the SVG file contents
  $svg = file_get_contents($fileURL);

  //SVG's will always use the same st# class names when defining styles, so we'll take the filename and append it as part of the class name.
  //We'll do the same for masks and other elements in the SVG file.
  $svgClass = basename(strtolower($fileURL), ".svg");
  $svgClass = sanitize_title($svgClass);

  $svg = str_replace('.st', '.st'.$svgClass, $svg);
  $svg = str_replace('class="st', 'class="st'.$svgClass, $svg);
  $svg = str_replace('#mask', '#mask'.$svgClass, $svg);
  $svg = str_replace('id="mask', 'id="mask'.$svgClass, $svg);
  $svg = str_replace('#Adobe', '#Adobe'.$svgClass, $svg);
  $svg = str_replace('id="Adobe', 'id="Adobe'.$svgClass, $svg);

  //Remove the height and width from the SVG tag of the file.
  $svg = preg_replace('/(.*<svg[^>]*) (height)="\w*"/', '$1', $svg);
  $svg = preg_replace('/(.*<svg[^>]*) (width)="\w*"/', '$1', $svg);

  echo $svg;
}

//return image on next theme
function return_acf_img($field, $class = '') {
  return get_lazy_load_img($field['url'], $field['alt'], $class, $field['width'], $field['height']);
}

function get_lazy_load_img($src, $alt, $class = '', $width = '', $height = '') {
  //Set up width and height attributes if they're defined.
  $tagAttributes = '';

  if (!empty($width)) {
    $tagAttributes .= ' width="'.$width.'" ';
  }

  if (!empty($height)) {
    $tagAttributes .= ' width="'.$height.'" ';
  }

  //Clean any tags or quotes from the alt text if they exist.
  $alt = strip_tags($alt);
  $alt = str_replace_first('"', '', $alt);

  return '<img '.get_lazy_load_img_src($src).' alt="'.$alt.'" class="'.$class.'"  '.$tagAttributes.' />';
}

function get_lazy_load_img_src($src) {
  return  'src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="'.$src.'"';
}

// GET SECTION BUILDER
function build_sections()
{
    $question_count = 1;
    $detect = new Mobile_Detect;
    if( get_field('section_builder') )
    {

        while( has_sub_field("section_builder") )
        {
            if( get_row_layout() == "section_html" ) // layout: Section Html
            {
              $fullWidth = get_sub_field("enable_full_width");
              $cssClass = get_sub_field("section_html_class");
            ?>
                <section class="<?php if(!$fullWidth && !preg_match('/\bnoMargin\b/',$cssClass)) { echo 'container'; } ?> section-html <?php echo $cssClass; ?>">
                    <?php if(!$fullWidth && !preg_match('/\bnoMargin\b/',$cssClass)) { ?><div class="container"><?php } ?>
                        <?php echo get_sub_field("html_field"); ?>
                    <?php if(!$fullWidth && !preg_match('/\bnoMargin\b/',$cssClass)) { ?></div><?php } ?>
                </section>
            <?php }
            elseif( get_row_layout() == "chat_section") // layout: Chat Section
            {
                $image = get_sub_field("section_hero_image");
                $link = get_sub_field('section_button');
                if( $link ):
                    $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                endif
            ?>
                <section class="section-chat container" style="background-color: <?php echo get_sub_field('section_background_color'); ?>">
                  <div class="row align-items-center d-flex justify-content-center">
                    <div class="col-12 col-md-7 col-lg-6 section-chat-content">
                        <h3><?php echo get_sub_field("section_headerline"); ?></h3>
                        <?php echo get_sub_field("section_content"); ?>
                        <div class="section-chat-btn align-items-center d-flex justify-content-left">
                          <a class="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                        </div>
                    </div>
                    <div class="col-4 col-md-5 col-lg-6 ml-auto section-chat-image">
                        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-fluid img-responsive img-outof-container"  />
                    </div>
                  </div>
                </section>
            <?php }
            elseif( get_row_layout() == "cta_section") // layout: CTA Section
            {
                $link = get_sub_field('section_button');
                if( $link ):
                    $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                endif
            ?>
                <section class="section-cta container">
                  <div class="row align-items-center d-flex justify-content-center">
                    <div class="col-12 section-cta-content text-center">
                        <h3><?php echo get_sub_field("section_title"); ?></h3>
                        <?php echo get_sub_field("section_hero_content"); ?>
                        <div class="section-cta-btn justify-content-center text-center">
                          <a class="button cta-btn" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                        </div>
                    </div>
                  </div>
                </section>
            <?php }
            elseif( get_row_layout() == "list_section" ) // layout: List Section
            {
              $hasBorder = (get_sub_field('bottom_border')) ? 'border-bottom' : '';
              $hasBgColor = get_sub_field('background_color');
              $link = get_sub_field('section_button');
              if( $link ):
                $link_url = $link['url'];
                $link_title = $link['title'];
                $link_target = $link['target'] ? $link['target'] : '_self';
                $btn_alignment = (get_sub_field('section_button_alignment') == 'right') ? 'col-12 col-lg-6' : 'col-12';
              endif
              ?>
                <section class="section-list <?php echo get_sub_field('custom_class_names'); ?> <?php echo $hasBorder; ?> <?php echo $$hasBgColor; ?> container">
                  <div class="row section-list-header">
                    <<?php echo get_sub_field("header_htag"); ?> class="<?php echo $btn_alignment; ?> align-items-center d-flex">
                      <?php echo get_sub_field("section_headerline"); ?>
                    </<?php echo get_sub_field("header_htag"); ?>>
                    <div class="<?php echo $btn_alignment; ?> section-header-btn align-items-center d-flex justify-content-center ">
                      <a class="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                    </div>
                  </div>
                  <div class="section-list-container">
                    <div class="row">
                      <?php
                        while(has_sub_field('content_list')):
                        $image = get_sub_field('icon_svg');
                      ?>
                        <div class="col-12 col-md-6">
                          <div class="row">
                            <div class="col-2"><?php echo output_inline_svg_file($image); ?></div>
                            <div class="col-8 pl-0 pl-md-4">
                              <h4><?php echo get_sub_field('list_headerline'); ?></h4>
                              <?php echo get_sub_field('list_content'); ?>
                            </div>
                          </div>
                        </div>
                      <?php endwhile; ?>
                    </div>
                  </div>
                </section>
            <?php }
            elseif( get_row_layout() == "section_image_with_text" ) // layout: Section image with text
            {
                $imageAlignment = get_sub_field("image_alignment");
                $textAlignement = ($imageAlignment == 'Left') ? "Right" : "Left";
                $image = get_sub_field('section_image');
                $video = get_sub_field('video_id');
                $popup_video = get_sub_field('popup_video');
                $cssImageClass = get_sub_field("section_class");
                if(preg_match('/\bhasWaveBg\b/',$cssImageClass)) {
                  load_Img(".hasWaveBg", "section_background_image");
                }
            ?>
            <section class="<?php if(!preg_match('/\bhasWaveBg\b/',$cssImageClass)) { echo 'container'; } ?> section-image-with-text <?php echo $cssImageClass; ?>">
                <div class="inner-container <?php if(preg_match('/\bhasVideo\b/',$cssImageClass)) { echo 'hasVideoClip'; } ?> <?php if(preg_match('/\bhasWaveBg\b/',$cssImageClass)) { echo 'container'; } ?>">
                    <div class="content f<?php echo $textAlignement; ?>">
                        <?php echo get_sub_field("section_content"); ?>
                    </div>
                    <?php if($image) { ?>
                    <?php if(($popup_video && $video)) { ?><a href="https://www.youtube.com/embed/<?php echo $video; ?>?autohide=1&loop=1&autoplay=1&controls=0&showinfo=0&rel=0&mute=1" class="fancybox"><?php } ?><img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive f<?php echo $imageAlignment; ?>" /><?php if(($popup_video && $video)) { ?></a><?php } ?>
                    <?php } ?>
                    <?php if($video && !$popup_video) { ?>
                    <div class="f<?php echo $imageAlignment; ?>">
                        <div class="videoWrapper">
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $video; ?>?autohide=1&loop=1&autoplay=1&controls=0&showinfo=0&rel=0&mute=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </section>
            <?php }
            elseif( get_row_layout() == "section_multi_images_with_content" ) // layout: Section Multi Images with content
            {
                $imageAlignment = get_sub_field("section_alignment");
                $textAlignement = ($imageAlignment == 'Left') ? "Right" : "Left";
                //$image = get_sub_field('section_image');
            ?>
                <section class="container section-multi-image-with-content">
                    <div class="container">
                        <div class="content f<?php echo $textAlignement; ?>">
                            <?php echo get_sub_field("section_multi_images_with_content_title"); ?>
                            <?php echo get_sub_field("section_multi_images_with_content_content"); ?>
                        </div>
                        <div class="multiImages f<?php echo $imageAlignment; ?>">
                          <?php
                            while(has_sub_field('section_multi_images_with_content_images')):
                            $image = get_sub_field('section_multi_images_with_content_image');
                          ?>
                          <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive" />
                        <?php endwhile; ?>
                        </div>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "section_gallery" ) // layout: Section Gallery
            { ?>
                <section class="container section-gallery">
                    <div class="grid">
                        <div class="grid-sizer"></div>
                        <?php $i = 1;
                            while(has_sub_field('section_gallery_images')):
                            $image = get_sub_field('section_gallery_image');
                          ?>
                            <figure>
                                <a href="<?php echo $image['url']; ?>" class="fancyboxTitle" data-fancybox="images"><img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-fluid img-responsive grid-item <?php if($i==2||$i==8) { echo 'grid-item--width2'; } ?>" id="imgg<?php echo $i; ?>" /></a>
                                <figcaption>
                                    <?php echo get_sub_field("section_gallery_desc"); ?>
                                </figcaption>
                            </figure>
                        <?php $i++; endwhile; ?>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "section_banner" ) // layout: Section Banner
            { ?>
                <section class="section-banner">
                    <div class="bxslider">
                      <?php
                        while(has_sub_field('section_banner_slider')):
                          $image = get_sub_field('section_banner_slider_image');
                      ?>
                          <div class="slider-content">
                            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive" size="" />
                            <div class="section-banner-content">
                                <?php echo get_sub_field("section_banner_slider_content"); ?>
                            </div>
                          </div>
                          <?php endwhile; ?>
                    </div>
                    <?php if(get_sub_field("section_banner_search_bar")) { ?>
                    <div class="section-banner-search">
                      <?php echo get_sub_field("section_banner_search_bar"); ?>
                    </div>
                    <?php } ?>
                </section>
            <?php }
            elseif( get_row_layout() == "section_image_slider" ) // layout: Section Image Slider
            { ?>
                <section class="section-image-slider">
                    <?php echo get_sub_field('section_image_slider_info'); ?>
                    <div class="customize-bxslidercontainer ">
                        <?php if(!$detect->isMobile()) { ?>
                        <div class="slider-control container ">
                            <?php $i = 0; while(has_sub_field('section_image_slider_content')): $logo = get_sub_field('slider_icon'); $iconName = get_sub_field('slider_icon_title');  ?>
                            <a data-slide-index="<?php echo $i; ?>" class="section-slider-control">
                            <div class="section-tab-content-icon"><?php echo $logo; ?><?php echo $iconName; ?></div></a>
                            <?php $i++; endwhile; ?>
                        </div>
                        <?php } ?>
                        <div class="img-slider">
                            <?php
                            while(has_sub_field('section_image_slider_content')):
                              $image = get_sub_field('slider_image');
                              $sliderInfo = get_sub_field('slider_content');
                            ?>
                            <div class="section-imag-content" style="background:url(<?php echo $image['url']; ?>); background-repeat: no-repeat;background-size: cover;background-position: center center;">
                              <div class="section-banner-content"><?php echo $sliderInfo; ?></div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "headline_section" ) // layout: Headline Section
            {
                $titleWidth = get_sub_field("headline_width");
                $image = get_sub_field("headline_image");
            ?>
                <section class="section-headline">
                    <div class="container">
                        <div class="subheadline"><?php echo get_sub_field("page_subtitle"); ?></div>
                        <div class="row">
                            <h1 class="col-12 col-md-<?php echo $titleWidth;?>"><?php echo get_sub_field("page_title"); ?></h1>
                            <?php if($titleWidth!==12): ?>
                                <div class="col-12 col-md-<?php echo (11-$titleWidth);?> <?php if($titleWidth==4) echo 'offset-md-1'; ?>"><?php echo get_sub_field("page_hero_content"); ?></div>
                            <?php endif; ?>
                            <?php
                                if($image):
                                    output_acf_img($image,'lazyImg');
                                endif;
                            ?>
                        </div>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "core_value" ) // layout: Core Value
            { ?>
                <section class="section-core-value">
                    <div class="container">
                        <div class="row align-items-center d-flex justify-content-center">
                            <h2 class="col-12 col-md-8 text-center"><?php echo get_sub_field("headline"); ?></h2>
                            <div class="col-12 col-md-6 text-center"><?php echo get_sub_field("sub_headline"); ?></div>
                        </div>
                        <div class="row text-left align-items-start d-flex justify-content-center">
                            <?php
                              while(has_sub_field('words_cols')):
                            ?>
                            <div class="col-2 col-2-adjust">
                                <div class="word-item">
                                    <div class="word"><?php echo get_sub_field("word"); ?></div>
                                </div>
                                <small class="f16">0<?php echo get_row_index(); ?></small>
                                <h4><?php echo get_sub_field("word"); ?></h4>
                                <?php echo get_sub_field("content"); ?>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <div class="fullWidth image-content" style="background-color: #0d4059;">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-md-7 pr-0 pr-lg-5">
                                    <?php $image = get_sub_field("big_image"); output_acf_img($image,'lazyImg'); ?>
                                </div>
                                <div class="col-12 col-md-5 pr-0 pr-md-0 pr-lg-4">
                                    <h2><?php echo get_sub_field("image_headline"); ?></h2>
                                    <?php echo get_sub_field("image_content"); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="white-bg">&nbsp;</div>
                </section>
            <?php }
            elseif( get_row_layout() == "story_gallery_section" ) // layout: Story Gallery Section
            {
                $borderBottom = get_sub_field("bottom_border");
            ?>
                <section class="section-story-gallery">
                    <div class="story-gallery row align-items-end d-flex justify-content-end">
                        <?php
                          while(has_sub_field('images')):
                            $image = get_sub_field('image');
                            if(get_row_index()<=2):
                        ?>
                        <div class="<?php if(get_row_index()==1) echo 'image-1 col-3'; elseif(get_row_index()==2) echo 'image-2 col-6'; ?>">
                            <?php output_acf_img($image,'lazyImg'); ?>
                        </div>
                        <?php endif; endwhile; ?>
                    </div>
                    <div class="story-gallery story-gallery-2 row align-items-start d-flex justify-content-end">
                        <?php
                          while(has_sub_field('images')):
                            $image = get_sub_field('image');
                            if(get_row_index()>=3):
                        ?>
                        <div class="<?php if(get_row_index()==3) echo 'image-3 col-6'; elseif(get_row_index()==4) echo 'image-4 col-6'; ?>">
                            <?php output_acf_img($image,'lazyImg'); ?>
                        </div>
                        <?php endif; endwhile; ?>
                        <div class="story-gallery-content col-12 col-lg-5">
                            <?php echo get_sub_field("section_subheaderline"); ?>
                            <h3><?php echo get_sub_field("section_headerline"); ?></h3>
                            <div class="row">
                                <?php
                                  while(has_sub_field('story_gallery_list')):
                                    $image = get_sub_field('icon_svg');
                                ?>
                                <div class="col-12 col-lg-6">
                                    <div class="story-icon"><?php echo output_inline_svg_file($image); ?></div>
                                    <?php echo get_sub_field("list_content"); ?>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                    <?php if($borderBottom): ?>
                        <hr class="container" />
                    <?php endif; ?>
                </section>
            <?php }
            elseif( get_row_layout() == "select_custom_post_type" ) // layout: Select Custom Post Type
            {
                $normal_layout= get_sub_field("normal_layout");
                $post_type = get_sub_field("select_posts");
                wp_reset_query();
                $args = array(
                    'post_type'=>$post_type,
                    'post_status' => 'publish',
                    'posts_per_page'=>-1,
                    'orderby'=>'date',
                );
                $the_query = new WP_Query( $args );
            ?>
                <section class="section-custom-post-type">
                    <div class="container">
                        <?php if($post_type=="case_studies") { ?>
                            <div class="grid row align-items-center d-flex justify-content-center">
                                <?php $i = 0;
                                    while ( $the_query->have_posts() ): $the_query->the_post();
                                        $bgimage = get_field("image_for_case_study");
                                  ?>
                                    <div class="col-10 col-md-6 item <?php if($i%2==0) echo 'ontop pr-0 pr-lg-3'; elseif($i%2 === 1) echo 'oncenter pl-0 pl-lg-3'; ?>">
                                            <a class="item-container mr-2" href="">
                                                <?php echo output_acf_img($bgimage,'lazyImg'); ?>
                                                <div class="case-content">
                                                    <h5><?php echo get_field("headline"); ?></h5>
                                                    <?php echo get_field("brief"); ?>
                                                </div>
                                            </a>
                                        </div>
                                <?php $i++; endwhile; wp_reset_postdata(); ?>
                            </div>
                        <?php } else { ?>
                            <?php if($normal_layout) { ?>
                                <div class="row align-items-center d-flex justify-content-center">
                                    <h2 class="col-12 col-md-8 text-center"><?php echo get_sub_field("section_headline"); ?></h2>
                                    <div class="col-12 col-md-6 text-center"><?php echo get_sub_field("section_content"); ?></div>
                                </div>
                                <?php
                                  if( $the_query->have_posts() ) {
                                ?>
                                <div class="grid row">
                                  <?php $i = 0;
                                    while ( $the_query->have_posts() ): $the_query->the_post();
                                        $bgimage = get_field("team_pic");
                                        $name = get_field("name");
                                        $title = get_field("title");
                                        $bio = get_field("bio");
                                        $link = get_field("link");
                                        if( $link ):
                                            $link_url = $link['url'];
                                            $link_title = $link['title'];
                                            $link_target = $link['target'] ? $link['target'] : '_self';
                                        endif
                                  ?>
                                    <div class="col-10 col-md-6 col-lg-4 item normal-item">
                                        <?php echo output_acf_img($bgimage, "lazyImg"); ?>
                                        <h5><?php echo $name; ?></h5>
                                        <small><?php echo $title; ?></small>
                                        <?php echo $bio; ?>
                                        <a class="button pl-0" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                                    </div>
                                  <?php $i++; endwhile; wp_reset_postdata(); }  ?>
                                </div>
                              </div>
                            <?php } else { ?>
                                <div class="items <?php if(!$normal_layout) echo 'wave-items'; ?>">
                                    <?php
                                      if( $the_query->have_posts() ) {
                                    ?>
                                    <div class="grid row">
                                      <?php $i = 0;
                                        while ( $the_query->have_posts() ): $the_query->the_post();
                                            $image = get_field("icon_svg");
                                            $bgimage = get_field("image");
                                      ?>
                                        <div class="col-10 col-md-6 col-lg-4 item <?php if($i%3==0) echo 'ontop'; elseif($i%3 === 1) echo 'oncenter'; elseif($i%3 === 2) echo 'onbottom'; ?>">
                                            <a class="item-container mr-2 <?php if($bgimage) echo 'item-bg-overlay'; ?>" href="" <?php if($bgimage) echo "style='background-image:url(".$bgimage.");filter: grayscale(100%);'" ?>>
                                                <div class="item-icon"><?php echo output_inline_svg_file($image); ?></div>
                                                <h5><?php echo get_field("headline"); ?></h5>
                                                <?php echo get_field("brief"); ?>
                                            </a>
                                        </div>
                                      <?php $i++; endwhile; wp_reset_postdata(); }  ?>
                                    </div>
                                  </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "cols_section" ) // layout: Cols Section
            {
                $className = get_sub_field("custom_class_names");
                $bgColor = get_sub_field("section_background_color");
                $colLayout = get_sub_field("col_layout");
                $sectionWidth = get_sub_field("section_width");
                $bgImage = get_sub_field("image_for_col_background");
                $numberofCols = get_sub_field("number_of_cols_per_row");
                $borderBottom = get_sub_field("bottom_border");
                ?>
                <section class="section-cols <?php echo $className; ?>" style="<?php if($bgColor) echo 'background-color:' . $bgColor .' '; ?>">
                    <?php if($sectionWidth==='withincontainer'): ?><div class="container"><?php endif; ?>
                      <div class="text-center">
                          <h2 class="text-center"><?php echo get_sub_field("section_headerline"); ?></h2>
                          <div class="row justify-content-center d-flex">
                              <div class="col-subline col-6"><?php echo get_sub_field("section_subheaderline"); ?></div>
                              <div class="col-content col-8">
                                  <?php echo get_sub_field("section_content"); ?>
                              </div>
                          </div>
                          <div class="row cols" <?php if($bgImage) echo 'style="background-image:url(' . $bgImage . '); background-position: center center;background-repeat: no-repeat;background-size: cover;"' ?>>
                                <?php
                                  while(has_sub_field('cols')):
                                    $image = get_sub_field('col_image');
                                    $icon = get_sub_field('col_icon_svg');
                                    $colContent = get_sub_field('col_content');
                                ?>
                                <div class="col-<?php echo 12/$numberofCols;?> item-<?php echo get_row_index(); ?>">
                                    <?php if($icon) { ?>
                                        <div class="row">
                                            <div class="col-12 col-lg-1 col-svg">
                                                <?php output_inline_svg_file($icon); ?>
                                            </div>
                                            <div class="text-left col-12 col-lg-11 pl-0 pl-lg-4"><?php echo $colContent; ?></div>
                                        </div>
                                    <?php } else { ?>
                                        <?php if($image) { ?><?php output_acf_img($image,'lazyImg'); ?><?php } ?>
                                        <div class="text-left"><?php echo $colContent; ?></div>
                                    <?php } ?>
                                </div>
                                <?php endwhile; ?>
                          </div>
                      </div>
                      <?php if($borderBottom): ?>
                        <hr class="container" />
                      <?php endif; ?>
                    <?php if($sectionWidth==='withincontainer'): ?></div><?php endif; ?>
                </section>
            <?php }
            elseif( get_row_layout() == "testimonial_section" ) // layout: Testimonial Section
            {
                $bgColor = get_sub_field("testimonial_background_color");
                $image = get_sub_field('testimonial_image');
            ?>
                <section class="section-testimonials" style="<?php if($bgColor) echo 'background-color:' . $bgColor .' '; ?>">
                    <div class="container">
                        <div class="row">
                            <h2 class="col-12 col-md-8 col-lg-7"><?php echo get_sub_field("testimonial_headline"); ?></h2>
                        </div>
                        <div class="row">
                            <div class="col-10 col-lg-5 green-rectangle">
                                <?php output_acf_img($image,'lazyImg touchLeft'); ?>
                            </div>
                            <div class="col-10 col-lg-7 white-bg">
                                <?php
                                  $post_objects = get_sub_field('testimonials');
                                  if( $post_objects ):
                                    foreach( $post_objects as $post):
                                        $headline = get_field("testimonial_headline", $post);
                                        $content = get_field("testimonial_content", $post);
                                        $name = get_field("name", $post);
                                        $title = get_field("title", $post);
                                        $image = wp_get_attachment_image_src(get_field('personal_pic',$post), "medium");
                                ?>
                                    <div class="f32"><?php echo $headline; ?></div>
                                    <div class="testi-content"><?php echo $content; ?></div>
                                    <div class="testi-pic"><img src="<?php echo $image[0]; ?>" alt="person pic" width="50" height="50" class="img-responsive person-pic" /></div>
                                    <div class="testi-person"><?php echo $name; ?></div>
                                    <div class="testi-title"><?php echo $title; ?></div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="section-content bxslider">
                        <?php
                          while(has_sub_field('testimonials')):
                            $image = get_sub_field('testimonial_image');
                            $link = get_sub_field('testimonial_company_link');
                        ?>
                        <div class="testimonial">
                            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive testimonial-image" />
                            <div class="item-content hasBg">
                                <p class="oib-member <?php if(!get_sub_field("oib_member_info")) { echo 'tran'; } ?>"><?php if(get_sub_field("oib_member_info")) { ?><?php echo get_sub_field("oib_member_info"); ?><?php } else { echo '&nbsp;'; } ?></p>
                                <div class="hasBg-content hasBg-content<?php echo $i; ?>">
                                    <div class="hasBg-content-padding">
                                        <p class="testimonial-content"><?php echo get_sub_field("testimonial"); ?></p>
                                        <p class="testimonial-author"><?php echo get_sub_field("testimonial_author_info"); ?></p>
                                        <p class="testimonial-author">
                                            <?php
                                                echo get_sub_field("testimonial_company");
                                                if($link) {
                                                    echo "<span> | </span><a href='http://" . get_sub_field("testimonial_company_link") . "' target='_blank'>" . get_sub_field("testimonial_company_link") . "</a>";
                                                }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "section_select_members" ) // layout: Section Select Members
            {
                $enableLightbox = get_sub_field("enable_lightbox");
                ?>
                <section class="container section-select-members <?php if(get_sub_field("has_topborder")) { echo 'hasTopbar'; } ?>">
                    <h2><?php echo get_sub_field("section_select_members_title"); ?></h2>
                    <div class="members">
                    <?php
                          $post_objects = get_sub_field('members');
                          if( $post_objects ): $i = 0;
                            foreach( $post_objects as $post):
                                $image = wp_get_attachment_image_src(get_field('member_pic',$post->ID), "member-thumbnail");
                                $imageFull = wp_get_attachment_image_src(get_field('member_lightbox_pic',$post->ID), "full");
                                $memberGovernmentMember = get_field("member_governance_title",$post->ID);
                        ?>
                        <div class="member <?php if($enableLightbox) { echo "member-fancybox"; } ?>" <?php if($enableLightbox) { echo "data-lightbox='.member-profile-" . $i . "'"; } ?>>
                            <img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title($post->ID); ?>" width="390" height="390" class="img-responsive team-member" />
                            <div class="item-content">
                                <h4><?php echo get_the_title($post->ID); ?></h4>
                                <?php if($memberGovernmentMember&&is_page("governance")) { ?>
                                <p class="person-title"><?php echo $memberGovernmentMember; ?></p>
                                <?php } else { ?>
                                <p class="person-title"><?php echo get_field("member_job",$post->ID); ?></p>
                                <?php } ?>
                            </div>
                            <div class="hidden">
                                <div class="member-profile member-profile-<?php echo $i; ?>">
                                    <h5><?php echo get_the_title($post->ID); ?></h5>
                                    <?php if($memberGovernmentMember&&is_page("governance")) { ?>
                                    <p class="person-title"><?php echo $memberGovernmentMember; ?></p>
                                    <?php } else { ?>
                                    <p class="person-title"><?php echo get_field("member_job",$post->ID); ?></p>
                                    <?php } ?>
                                    <img src="<?php echo $imageFull[0]; ?>" alt="<?php echo get_the_title($post->ID); ?>" width="<?php echo $imageFull[1]; ?>" height="<?php echo $imageFull[2]; ?>" class="img-responsive team-member" />
                                    <p class="pic-credit"><?php echo get_field("pic_credit", $post->ID); ?></p>
                                    <p class="profile"><?php echo get_field("member_profile",$post->ID); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php $i++; endforeach; wp_reset_postdata(); endif; ?>
                    </div>
                </section>
            <?php }
        }
    }
}

// GET SECTION BUILDER
function build_post_sections()
{
    $question_count = 1;
    $detect = new Mobile_Detect;
    if( get_field('section_builder') )
    {
        while( has_sub_field("section_builder") )
        {
            if( get_row_layout() == "html_section" ) // layout: HTML Section
            {
              $cssClass = get_sub_field("custom_class_names");
            ?>
                <section class="container section-html <?php echo $cssClass; ?>">
                    <?php echo get_sub_field("html_field"); ?>
                </section>
            <?php }
            elseif( get_row_layout() == "post_header_section") // layout: Post Header Section
            {
                $image = get_sub_field("background_image");
                if( $image ) {
                    load_Img(".post-header", "background_image");
                }
                $bgColor = get_sub_field("background_color");
                $hero_image = get_sub_field("hero_image");
                $link = get_sub_field('section_button');
                $post_type = get_post_type( get_the_ID() );
                if( $link ):
                    $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                endif
            ?>
                <section class="section-header post-header" style="background-color: <?php echo $bgColor; ?>;">
                    <div class="container">
                      <div class="row align-items-center d-flex justify-content-left">
                        <div class="col-12 header-content">
                            <a href="<?php echo get_post_type_archive_link( $post_type ); ?>" class="d-block back-casestudy"><?php echo $post_type; ?></a>
                            <h1><?php echo get_sub_field("page_title"); ?></h1>
                            <?php echo get_sub_field("page_headerline"); ?>
                        </div>
                      </div>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "case_study_info" ) // layout: Case Study Info
            {
            ?>
                <section class="container section-case-study-info">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-1">
                            <h4>Year</h4>
                            <?php echo get_sub_field('case_year'); ?>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <h4>Location</h4>
                            <?php echo get_sub_field('case_location'); ?>
                        </div>
                        <div class="col-12 col-md-12 col-lg-6 offset-lg-1">
                            <h4>The Challenge</h4>
                            <?php echo get_sub_field('case_challenge'); ?>
                        </div>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "icon_section" ) // layout: Icon Section
            {
                $icon = get_sub_field("icon");
                $image = get_sub_field("post_image");
            ?>
                <section class="container section-icon">
                    <div class="row">
                        <div class="col-12 col-md-2 col-lg-1 icon-line">
                            <div class="round-svg align-items-center d-flex justify-content-center"><?php output_inline_svg_file($icon); ?></div>
                        </div>
                        <div class="col-12 col-md-8 col-lg-9 offset-md-2 offset-lg-2">
                            <?php output_acf_img($image,'lazyImg'); ?>
                        </div>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "case_study_section" ) // layout: Case Study Section
            {
            ?>
                <section class="container section-case-study">
                    <div class="row align-items-center d-flex">
                        <?php
                            $post_objects = get_sub_field("pick_a_case_study");
                            if( $post_objects ):
                                $headline = get_field('headline',$post_objects);
                                $brief = get_field('brief',$post_objects);
                                $image = get_field('image',$post_objects);
                        ?>
                            <div class="col-12 col-md-5">
                                <?php output_acf_img($image,'lazyImg img-outof-border'); ?>
                                <div class="green-rectangle">&nbsp;</div>
                            </div>
                            <div class="col-12 col-md-5 offset-md-2">
                                <div class="case-study-type">CASE STUDY</div>
                                <h3><?php echo $headline; ?></h3>
                                <?php echo $brief; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "detail_section" ) // layout: Detail Section
            {
            ?>
                <section class="container section-details">
                    <div class="row">
                        <?php if ( is_singular( 'case_studies' ) ) { ?>
                        <div class="col-12 justify-content-left casestudy-result">THE RESULT</div>
                        <?php } ?>
                        <?php
                          $count = count(get_sub_field('details'));
                          if($count>1) :
                        ?>
                            <nav class="col-12">
                              <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                              <?php
                              $i = 0;
                              while(has_sub_field('details')):
                                    $tab = strtolower(get_sub_field("detail_type"));
                                    $tab = preg_replace('/\s+/', '_', $tab);
                              ?>
                              <a class="nav-item nav-link <?php if($i==0) echo 'active'; ?> nav-item-<?php echo $i; ?>" id="nav-<?php echo $tab; ?>-tab" data-toggle="tab" href="#nav-<?php echo $tab; ?>" role="tab" aria-controls="nav-<?php echo $tab; ?>" aria-selected="<?php if($i==0) echo 'true'; else echo 'false'; ?>"><?php echo get_sub_field("detail_type"); ?></a>
                              <?php $i++; endwhile; ?>
                              <hr>
                              </div>
                            </nav>
                        <?php endif; ?>
                        <div class="tab-content col-12 container" id="nav-tabContent">
                            <?php $i = 0;
                              while(has_sub_field('details')):
                                $tab = preg_replace('/\s+/', '_', $tab);
                            ?>
                            <div class="tab-pane fade <?php if($i==0) echo 'show active'; ?> row" id="nav-<?php echo $tab; ?>" role="tabpanel" aria-labelledby="nav-<?php echo $tab; ?>-tab">
                                <div class="col-12 col-lg-4">
                                    <h3><?php echo get_sub_field('detail_headline'); ?></h3>
                                </div>
                                <div class="col-12 col-lg-7 offset-lg-1">
                                    <?php echo get_sub_field('detail'); ?>
                                </div>
                            </div>
                            <?php $i++; endwhile; ?>
                        </div>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "chat_section") // layout: Chat Section
            {
                $image = get_sub_field("section_hero_image");
                $link = get_sub_field('section_button');
                if( $link ):
                    $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                endif
            ?>
                <section class="section-chat container" style="background-color: <?php echo get_sub_field('section_background_color'); ?>">
                  <div class="row align-items-center d-flex justify-content-center">
                    <div class="col-12 col-md-7 col-lg-6 section-chat-content">
                        <h3><?php echo get_sub_field("section_headerline"); ?></h3>
                        <?php echo get_sub_field("section_content"); ?>
                        <div class="section-chat-btn align-items-center d-flex">
                          <a class="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                        </div>
                    </div>
                    <div class="col-4 col-md-5 col-lg-6 ml-auto section-chat-image">
                        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-fluid img-responsive img-outof-container"  />
                    </div>
                  </div>
                </section>
            <?php }
            elseif( get_row_layout() == "cta_section") // layout: CTA Section
            {
                $image = get_sub_field("cta_image");
                $link = get_sub_field('section_button');
                if( $link ):
                    $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                endif
            ?>
                <section class="section-cta container" style="background-color: <?php echo get_sub_field('background_color'); ?>">
                  <div class="row align-items-center d-flex justify-content-center">
                    <div class="col-12 col-md-7 col-lg-6 section-cta-content">
                        <h3><?php echo get_sub_field("section_title"); ?></h3>
                        <?php echo get_sub_field("section_hero_content"); ?>
                        <div class="section-cta-btn align-items-center d-flex">
                          <a class="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                        </div>
                    </div>
                    <div class="col-4 col-md-5 col-lg-6 ml-auto section-cta-image">
                        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-fluid img-responsive img-outof-container"  />
                    </div>
                  </div>
                </section>
            <?php }
            elseif( get_row_layout() == "list_section" ) // layout: List Section
            {
              $hasBorder = (get_sub_field('bottom_border')) ? 'border-bottom' : '';
              $hasBgColor = get_sub_field('background_color');
              $link = get_sub_field('section_button');
              if( $link ):
                $link_url = $link['url'];
                $link_title = $link['title'];
                $link_target = $link['target'] ? $link['target'] : '_self';
                $btn_alignment = (get_sub_field('section_button_alignment') == 'right') ? 'col-12 col-lg-6' : 'col-12';
              endif
              ?>
                <section class="section-list <?php echo get_sub_field('custom_class_names'); ?> <?php echo $hasBorder; ?> <?php echo $$hasBgColor; ?> container">
                  <div class="row section-list-header">
                    <<?php echo get_sub_field("header_htag"); ?> class="<?php echo $btn_alignment; ?> align-items-center d-flex">
                      <?php echo get_sub_field("section_headerline"); ?>
                    </<?php echo get_sub_field("header_htag"); ?>>
                    <div class="<?php echo $btn_alignment; ?> section-header-btn align-items-center d-flex justify-content-center ">
                      <a class="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                    </div>
                  </div>
                  <div class="section-list-container">
                    <div class="row">
                      <?php
                        while(has_sub_field('content_list')):
                        $image = get_sub_field('icon_svg');
                      ?>
                        <div class="col-12 col-md-6">
                          <div class="row">
                            <div class="col-2"><?php echo output_inline_svg_file($image); ?></div>
                            <div class="col-8 pl-0 pl-md-4">
                              <h4><?php echo get_sub_field('list_headerline'); ?></h4>
                              <?php echo get_sub_field('list_content'); ?>
                            </div>
                          </div>
                        </div>
                      <?php endwhile; ?>
                    </div>
                  </div>
                </section>
            <?php }
            elseif( get_row_layout() == "section_image_with_text" ) // layout: Section image with text
            {
                $imageAlignment = get_sub_field("image_alignment");
                $textAlignement = ($imageAlignment == 'Left') ? "Right" : "Left";
                $image = get_sub_field('section_image');
                $video = get_sub_field('video_id');
                $popup_video = get_sub_field('popup_video');
                $cssImageClass = get_sub_field("section_class");
                if(preg_match('/\bhasWaveBg\b/',$cssImageClass)) {
                  load_Img(".hasWaveBg", "section_background_image");
                }
            ?>
            <section class="<?php if(!preg_match('/\bhasWaveBg\b/',$cssImageClass)) { echo 'container'; } ?> section-image-with-text <?php echo $cssImageClass; ?>">
                <div class="inner-container <?php if(preg_match('/\bhasVideo\b/',$cssImageClass)) { echo 'hasVideoClip'; } ?> <?php if(preg_match('/\bhasWaveBg\b/',$cssImageClass)) { echo 'container'; } ?>">
                    <div class="content f<?php echo $textAlignement; ?>">
                        <?php echo get_sub_field("section_content"); ?>
                    </div>
                    <?php if($image) { ?>
                    <?php if(($popup_video && $video)) { ?><a href="https://www.youtube.com/embed/<?php echo $video; ?>?autohide=1&loop=1&autoplay=1&controls=0&showinfo=0&rel=0&mute=1" class="fancybox"><?php } ?><img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive f<?php echo $imageAlignment; ?>" /><?php if(($popup_video && $video)) { ?></a><?php } ?>
                    <?php } ?>
                    <?php if($video && !$popup_video) { ?>
                    <div class="f<?php echo $imageAlignment; ?>">
                        <div class="videoWrapper">
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $video; ?>?autohide=1&loop=1&autoplay=1&controls=0&showinfo=0&rel=0&mute=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </section>
            <?php }
            elseif( get_row_layout() == "section_multi_images_with_content" ) // layout: Section Multi Images with content
            {
                $imageAlignment = get_sub_field("section_alignment");
                $textAlignement = ($imageAlignment == 'Left') ? "Right" : "Left";
                //$image = get_sub_field('section_image');
            ?>
                <section class="container section-multi-image-with-content">
                    <div class="container">
                        <div class="content f<?php echo $textAlignement; ?>">
                            <?php echo get_sub_field("section_multi_images_with_content_title"); ?>
                            <?php echo get_sub_field("section_multi_images_with_content_content"); ?>
                        </div>
                        <div class="multiImages f<?php echo $imageAlignment; ?>">
                          <?php
                            while(has_sub_field('section_multi_images_with_content_images')):
                            $image = get_sub_field('section_multi_images_with_content_image');
                          ?>
                          <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive" />
                        <?php endwhile; ?>
                        </div>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "section_gallery" ) // layout: Section Gallery
            { ?>
                <section class="container section-gallery">
                    <div class="grid">
                        <div class="grid-sizer"></div>
                        <?php $i = 1;
                            while(has_sub_field('section_gallery_images')):
                            $image = get_sub_field('section_gallery_image');
                          ?>
                            <figure>
                                <a href="<?php echo $image['url']; ?>" class="fancyboxTitle" data-fancybox="images"><img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-fluid img-responsive grid-item <?php if($i==2||$i==8) { echo 'grid-item--width2'; } ?>" id="imgg<?php echo $i; ?>" /></a>
                                <figcaption>
                                    <?php echo get_sub_field("section_gallery_desc"); ?>
                                </figcaption>
                            </figure>
                        <?php $i++; endwhile; ?>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "section_banner" ) // layout: Section Banner
            { ?>
                <section class="section-banner">
                    <div class="bxslider">
                      <?php
                        while(has_sub_field('section_banner_slider')):
                          $image = get_sub_field('section_banner_slider_image');
                      ?>
                          <div class="slider-content">
                            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive" size="" />
                            <div class="section-banner-content">
                                <?php echo get_sub_field("section_banner_slider_content"); ?>
                            </div>
                          </div>
                          <?php endwhile; ?>
                    </div>
                    <?php if(get_sub_field("section_banner_search_bar")) { ?>
                    <div class="section-banner-search">
                      <?php echo get_sub_field("section_banner_search_bar"); ?>
                    </div>
                    <?php } ?>
                </section>
            <?php }
            elseif( get_row_layout() == "section_image_slider" ) // layout: Section Image Slider
            { ?>
                <section class="section-image-slider">
                    <?php echo get_sub_field('section_image_slider_info'); ?>
                    <div class="customize-bxslidercontainer ">
                        <?php if(!$detect->isMobile()) { ?>
                        <div class="slider-control container ">
                            <?php $i = 0; while(has_sub_field('section_image_slider_content')): $logo = get_sub_field('slider_icon'); $iconName = get_sub_field('slider_icon_title');  ?>
                            <a data-slide-index="<?php echo $i; ?>" class="section-slider-control">
                            <div class="section-tab-content-icon"><?php echo $logo; ?><?php echo $iconName; ?></div></a>
                            <?php $i++; endwhile; ?>
                        </div>
                        <?php } ?>
                        <div class="img-slider">
                            <?php
                            while(has_sub_field('section_image_slider_content')):
                              $image = get_sub_field('slider_image');
                              $sliderInfo = get_sub_field('slider_content');
                            ?>
                            <div class="section-imag-content" style="background:url(<?php echo $image['url']; ?>); background-repeat: no-repeat;background-size: cover;background-position: center center;">
                              <div class="section-banner-content"><?php echo $sliderInfo; ?></div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "section_image_tabs" ) // layout: Section Image Tabs
            { ?>
                <section class="section-image-tabs container">
                    <?php echo get_sub_field('section_image_slider_info'); ?>
                    <?php if(!$detect->isMobile()) { ?>
                    <div class="section-tabs ">
                        <?php
                        while(has_sub_field('tabs')):
                          $icon = get_sub_field('tab_icon');
                          $tab = get_sub_field('tab');
                          $tabLink = get_sub_field('tab_link');
                          $currentTdb = get_sub_field('current_page');
                        ?>
                            <div class="section-tab <?php if($currentTdb) { echo 'section-tab-active'; } ?>" data-link="<?php echo $tabLink; ?>">
                              <div class="section-tab-content-icon"><?php echo $icon; ?></div>
                              <div class="section-tab-content"><?php echo $tab; ?></div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <?php } else { ?>
                    <select class="section-tab section-tab-select">
                        <?php
                        $i = 0;
                        while(has_sub_field('tabs')):
                          $tab = get_sub_field('tab');
                          $tabLink = get_sub_field('tab_link');
                        ?>
                        <option data-slide-index="<?php echo $i; ?>" class="section-tab-control" data-slide-link="<?php echo $tabLink; ?>" value="<?php echo $i; ?>"><?php echo $tab; ?></a>
                                                <?php $i++; endwhile; ?>"
                    </select>
                    <?php } ?>
                </section>
            <?php }
            elseif( get_row_layout() == "section_tab_system" ) // layout: Section Tabs
            {
                $tabAlignment = get_sub_field("tab_position");
                $hasDouble = get_sub_field("enable_double_filters");
                if($detect->isMobile()&&!$detect->isTablet()) { ?>
                    <?php if(!$hasDouble) { ?>
                        <?php if(!$tabAlignment=="vertical") { ?>
                            <section class="container section-tabs-system mobile-container">
                                <div class="fliter-btns-group">
                                    <?php
                                        $i = 0;
                                        while(has_sub_field('section_tabs')):
                                            $tab = strtolower(get_sub_field("tab"));
                                            $tab = preg_replace('/\s+/', '_', $tab);
                                    ?>
                                        <div class="inline tab mobile-tab"><?php echo get_sub_field("tab");?></div>
                                        <div class="grid section-content mobile-content">
                                            <div class="inner-container"><?php echo get_sub_field("tab_section"); ?></div>
                                            <?php if(get_sub_field("has_slider")) { ?>
                                                <div class="testimonials bxslider">
                                                    <?php
                                                      while(has_sub_field('tab_testimonial_system')):
                                                        $image = get_sub_field('tab_testimonial_image');
                                                        $link = get_sub_field('tab_testimonial_company_link');
                                                    ?>
                                                    <div class="testimonial">
                                                        <div class="testimonial-bg-image" style="background-image: url('<?php echo $image['url']; ?>');">
                                                            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive testimonial-image" />
                                                        </div>
                                                        <div class="item-content hasBg">
                                                            <div class="hasBg-content">
                                                                <div class="hasBg-content-padding">
                                                                    <p class="testimonial-content"><?php echo get_sub_field("tab_testimonial"); ?></p>
                                                                    <p class="testimonial-author"><?php echo get_sub_field("tab_testimonial_author_info"); ?></p>
                                                                    <p class="testimonial-author">
                                                                        <?php
                                                                            echo get_sub_field("tab_testimonial_company");
                                                                            if($link) {
                                                                                echo "<span> | </span><a href='http://" . $link . "' target='_blank'>" . $link . "</a>";
                                                                            }
                                                                        ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endwhile; ?>
                                                 </div>
                                            <?php } ?>
                                        </div>
                                    <?php $i++; endwhile; ?>
                                </div>
                            </section>
                        <?php } else { ?>
                            <section class="container section-tabs-system mobile-container hasTopbar">
                                <div class="inner-container">
                                    <h3><?php echo get_sub_field("tab_vertical_headline"); ?></h3>
                                    <select class='filter-div-select'>
                                        <?php
                                            while(has_sub_field('section_tabs')):
                                                $tab = strtolower(get_sub_field("tab"));
                                                $tab = preg_replace('/\s+/', '_', $tab);
                                        ?>
                                        <option class='filter-list filter-list-item' value='#<?php echo $tab; ?>'><?php echo get_sub_field("tab"); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="grid section-content grid-alignV mobileV-container">
                                    <?php
                                      while(has_sub_field('section_tabs')):
                                        $tab = strtolower(get_sub_field("tab"));
                                        $tab = preg_replace('/\s+/', '_', $tab);
                                    ?>
                                    <div class="<?php echo $tab; ?> element-item" id="<?php echo $tab; ?>">
                                        <div class="inner-container"><?php echo get_sub_field("tab_section"); ?></div>
                                        <?php if(get_sub_field("has_slider")) { ?>
                                        <div class="testimonials bxslider">
                                            <?php
                                              while(has_sub_field('tab_testimonial_system')):
                                                $image = get_sub_field('tab_testimonial_image');
                                                $link = get_sub_field('tab_testimonial_company_link');
                                            ?>
                                            <div class="testimonial">
                                                <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive testimonial-image" />
                                                <div class="item-content hasBg">
                                                    <div class="hasBg-content">
                                                        <div class="hasBg-content-padding">
                                                            <p class="testimonial-content"><?php echo get_sub_field("tab_testimonial"); ?></p>
                                                            <p class="testimonial-author"><?php echo get_sub_field("tab_testimonial_author_info"); ?></p>
                                                            <p class="testimonial-author">
                                                                <?php
                                                                    echo get_sub_field("tab_testimonial_company");
                                                                    if($link) {
                                                                        echo "<span> | </span><a href='http://" . $link . "' target='_blank'>" . $link . "</a>";
                                                                    }
                                                                ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                <?php endwhile; ?>
                                         </div>
                                        <?php } ?>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                            </section>
                        <?php } ?>
                    <?php } else { ?>
                        <section class="container section-tabs-system mobile-container double-filters-mobile-layout">
                            <div class="fliter-btns-group">
                                <?php $i = 0;
                                while(has_sub_field('double_filters_layout')):
                                    $tab = strtolower(get_sub_field("horizontal_tab"));
                                    $tab = preg_replace('/\s+/', '_', $tab);
                                ?>
                                <div class="inline tab mobile-tab"><?php echo get_sub_field("horizontal_tab"); ?></div>
                                <div class="grid section-content mobile-content">
                                    <div class="<?php echo $tab; ?> inner-tabs element-item">
                                        <div class="inner-container">
                                            <h3><?php echo get_sub_field("vertical_tab_headline"); ?></h3>
                                            <select class='filter-div-select'>
                                                <?php
                                                    $j = 0;
                                                    while(has_sub_field('horizontal_tab_content')):
                                                        $tabV = strtolower(get_sub_field("vertical_tab"));
                                                        $tabV = preg_replace('/\s+/', '_', $tabV);
                                                ?>
                                                <option class='filter-list filter-list-item' value='#<?php echo preg_replace("/[^A-Za-z0-9 ]/", '', $tabV); ?>'><?php echo get_sub_field("vertical_tab"); ?></option>
                                                <?php $j++; endwhile; ?>
                                            </select>
                                            <div class="grid-inner section-content grid-alignV mobileV-container">
                                                <?php
                                                  while(has_sub_field('horizontal_tab_content')):
                                                    $tabV = strtolower(get_sub_field("vertical_tab"));
                                                    $tabV = preg_replace('/\s+/', '_', $tabV);
                                                ?>
                                                <div class="<?php echo $tab; ?> <?php echo preg_replace("/[^A-Za-z0-9 ]/", '', $tabV); ?> element-item-inner" id="<?php echo $tabV; ?>">
                                                    <div class="inner-container"><?php echo get_sub_field("vertical_tab_content"); ?></div>
                                                </div>
                                                <?php endwhile; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              <?php $i++; endwhile; ?>
                        </section>
                    <?php } ?>
                <?php } else {
                    if(!$hasDouble) {
                ?>
                    <section class="container section-tabs-system <?php if($tabAlignment=="vertical") echo "hasBorder"; ?>">
                        <?php if($tabAlignment=="vertical") {?><div class="inner-container"><?php } ?>
                            <div class="fliter-btns-group <?php if($tabAlignment=="vertical") echo "fliter-btns-group-alignV fLeft"; ?>">
                                <?php if($tabAlignment=="vertical") {?>
                                <h3><?php echo get_sub_field("tab_vertical_headline"); ?></h3>
                              <?php } $i = 0;
                                while(has_sub_field('section_tabs')):
                                    $tab = strtolower(get_sub_field("tab"));
                                    $tab = preg_replace('/\s+/', '_', $tab);
                              ?>
                              <div class="inline tab <?php if($i==0) { echo "tab-active"; } ?> desktop-tab" data-filter=".<?php echo preg_replace("/[^A-Za-z0-9 ]/", '', $tab); ?>"><?php echo get_sub_field("tab"); ?></div>
                              <?php $i++; endwhile; ?>
                            </div>
                            <div class="grid section-content <?php if($tabAlignment=="vertical") echo "grid-alignV fRight"; ?>">
                                <?php
                                  while(has_sub_field('section_tabs')):
                                    $tab = strtolower(get_sub_field("tab"));
                                    $tab = preg_replace('/\s+/', '_', $tab);
                                ?>
                                <div class="<?php echo preg_replace("/[^A-Za-z0-9 ]/", '', $tab); ?> element-item">
                                    <div class="inner-container"><?php echo get_sub_field("tab_section"); ?></div>
                                    <?php if(get_sub_field("has_slider")) { ?>
                                    <div class="testimonials bxslider">
                                        <?php
                                          while(has_sub_field('tab_testimonial_system')):
                                            $image = get_sub_field('tab_testimonial_image');
                                            $link = get_sub_field('tab_testimonial_company_link');
                                        ?>
                                        <div class="testimonial">
                                            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive testimonial-image" />
                                            <div class="item-content hasBg">
                                                <div class="hasBg-content">
                                                    <div class="hasBg-content-padding">
                                                        <p class="testimonial-content"><?php echo get_sub_field("tab_testimonial"); ?></p>
                                                        <p class="testimonial-author"><?php echo get_sub_field("tab_testimonial_author_info"); ?></p>
                                                        <p class="testimonial-author">
                                                            <?php
                                                                echo get_sub_field("tab_testimonial_company");
                                                                if($link) {
                                                                    echo "<span> | </span><a href='http://" . $link . "' target='_blank'>" . $link . "</a>";
                                                                }
                                                            ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                            <?php endwhile; ?>
                                     </div>
                                    <?php } ?>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        <?php if($tabAlignment=="vertical") {?></div><?php } ?>
                    </section>
                    <?php } else { ?>
                    <section class="container section-tabs-system">
                            <div class="fliter-btns-group">
                              <?php $i = 0;
                                while(has_sub_field('double_filters_layout')):
                                    $tab = strtolower(get_sub_field("horizontal_tab"));
                                    $tab = preg_replace('/\s+/', '_', $tab);
                              ?>
                              <div class="inline tab <?php if($i==0) { echo "tab-active"; } ?>" data-filter=".<?php echo $tab; ?>"><?php echo get_sub_field("horizontal_tab"); ?></div>
                              <?php $i++; endwhile; ?>
                            </div>
                            <div class="grid section-content inner-container">
                                <?php while(has_sub_field('double_filters_layout')):
                                    $tab = strtolower(get_sub_field("horizontal_tab"));
                                    $tab = preg_replace('/\s+/', '_', $tab);
                                ?>
                                <div class="<?php echo $tab; ?> inner-tabs element-item">
                                    <div class="fliter-btns-group-inner fliter-btns-group-alignV fLeft">
                                        <h3><?php echo get_sub_field("vertical_tab_headline"); ?></h3>
                                        <?php $j = 0;
                                          while(has_sub_field('horizontal_tab_content')):
                                            $tabV = strtolower(get_sub_field("vertical_tab"));
                                            $tabV = preg_replace('/\s+/', '_', $tabV);
                                        ?>
                                        <div class="inline inner-tab <?php if($j==0) { echo "tab-active"; } ?>" data-filter=".<?php echo preg_replace("/[^A-Za-z0-9 ]/", '', $tabV); ?>"><?php echo get_sub_field("vertical_tab"); ?></div>
                                        <?php $j++; endwhile; ?>
                                    </div>
                                    <div class="grid-inner section-content grid-alignV fRight">
                                        <?php
                                          while(has_sub_field('horizontal_tab_content')):
                                            $tabV = strtolower(get_sub_field("vertical_tab"));
                                            $tabV = preg_replace('/\s+/', '_', $tabV);
                                        ?>
                                        <div class="<?php echo $tab; ?> <?php echo preg_replace("/[^A-Za-z0-9 ]/", '', $tabV); ?> element-item-inner">
                                            <div class="inner-container"><?php echo get_sub_field("vertical_tab_content"); ?></div>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                    </section>
                <?php } } ?>
            <?php }
            elseif( get_row_layout() == "section_intro" ) // layout: Section Intro
            { ?>
                <section class="section-intro">
                    <div class="hasCrossLine">
                        <div class="container">
                            <div class="inner-container">
                                <div class="hasCrossLine-topic"><?php echo get_sub_field("section_intro_topic"); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="inner-container">
                            <div class="fLeft intro-topic">
                                <h2><?php echo get_sub_field("section_intro_headline"); ?></h2>
                                <?php if(get_sub_field("section_intro_headline_additional_content")) { ?><h3><?php echo get_sub_field("section_intro_headline_additional_content"); ?></h3><?php } ?>
                            </div>
                            <div class="fRight intro-content">
                                <div><?php echo get_sub_field("section_intro_content"); ?></div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "section_instagram" ) // layout: Section Instagram
            {
                ?>
                <section class="section-cols section-cols3 floatUp textleft>">
                    <div class="cols container">
                        <?php
                          while(has_sub_field('instagram_accounts')):
                        ?>
                        <div class="colItem inline">
                            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive col--<?php echo $colNo; ?>" />
                            <?php echo $colContent; ?>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "section_testimonials" ) // layout: Section Testimonials
            { ?>
                <section class="container section-testimonials">
                    <div class="inner-container"><?php echo get_sub_field("section_testimonials_headline"); ?></div>
                    <div class="section-content bxslider">
                        <?php
                          while(has_sub_field('testimonials')):
                            $image = get_sub_field('testimonial_image');
                            $link = get_sub_field('testimonial_company_link');
                        ?>
                        <div class="testimonial">
                            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive testimonial-image" />
                            <div class="item-content hasBg">
                                <p class="oib-member <?php if(!get_sub_field("oib_member_info")) { echo 'tran'; } ?>"><?php if(get_sub_field("oib_member_info")) { ?><?php echo get_sub_field("oib_member_info"); ?><?php } else { echo '&nbsp;'; } ?></p>
                                <div class="hasBg-content hasBg-content<?php echo $i; ?>">
                                    <div class="hasBg-content-padding">
                                        <p class="testimonial-content"><?php echo get_sub_field("testimonial"); ?></p>
                                        <p class="testimonial-author"><?php echo get_sub_field("testimonial_author_info"); ?></p>
                                        <p class="testimonial-author">
                                            <?php
                                                echo get_sub_field("testimonial_company");
                                                if($link) {
                                                    echo "<span> | </span><a href='http://" . get_sub_field("testimonial_company_link") . "' target='_blank'>" . get_sub_field("testimonial_company_link") . "</a>";
                                                }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "section_select_members" ) // layout: Section Select Members
            {
                $enableLightbox = get_sub_field("enable_lightbox");
                ?>
                <section class="container section-select-members <?php if(get_sub_field("has_topborder")) { echo 'hasTopbar'; } ?>">
                    <h2><?php echo get_sub_field("section_select_members_title"); ?></h2>
                    <div class="members">
                    <?php
                          $post_objects = get_sub_field('members');
                          if( $post_objects ): $i = 0;
                            foreach( $post_objects as $post):
                                $image = wp_get_attachment_image_src(get_field('member_pic',$post->ID), "member-thumbnail");
                                $imageFull = wp_get_attachment_image_src(get_field('member_lightbox_pic',$post->ID), "full");
                                $memberGovernmentMember = get_field("member_governance_title",$post->ID);
                        ?>
                        <div class="member <?php if($enableLightbox) { echo "member-fancybox"; } ?>" <?php if($enableLightbox) { echo "data-lightbox='.member-profile-" . $i . "'"; } ?>>
                            <img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title($post->ID); ?>" width="390" height="390" class="img-responsive team-member" />
                            <div class="item-content">
                                <h4><?php echo get_the_title($post->ID); ?></h4>
                                <?php if($memberGovernmentMember&&is_page("governance")) { ?>
                                <p class="person-title"><?php echo $memberGovernmentMember; ?></p>
                                <?php } else { ?>
                                <p class="person-title"><?php echo get_field("member_job",$post->ID); ?></p>
                                <?php } ?>
                            </div>
                            <div class="hidden">
                                <div class="member-profile member-profile-<?php echo $i; ?>">
                                    <h5><?php echo get_the_title($post->ID); ?></h5>
                                    <?php if($memberGovernmentMember&&is_page("governance")) { ?>
                                    <p class="person-title"><?php echo $memberGovernmentMember; ?></p>
                                    <?php } else { ?>
                                    <p class="person-title"><?php echo get_field("member_job",$post->ID); ?></p>
                                    <?php } ?>
                                    <img src="<?php echo $imageFull[0]; ?>" alt="<?php echo get_the_title($post->ID); ?>" width="<?php echo $imageFull[1]; ?>" height="<?php echo $imageFull[2]; ?>" class="img-responsive team-member" />
                                    <p class="pic-credit"><?php echo get_field("pic_credit", $post->ID); ?></p>
                                    <p class="profile"><?php echo get_field("member_profile",$post->ID); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php $i++; endforeach; wp_reset_postdata(); endif; ?>
                    </div>
                </section>
            <?php }
        }
    }
}
