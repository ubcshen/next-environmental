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

if( function_exists('acf_add_options_page') ) {
  // add parent
  $parent = acf_add_options_page(array(
    'page_title'  => 'Next Environmental Inc Settings',
    'menu_title'  => 'Site Settings',
    'redirect'    => false
  ));

  acf_add_options_sub_page(array(
    'page_title'  => 'Footer Setting',
    'menu_title'  => 'Footer Setting',
    'menu_slug'   => 'footer-setting',
    'parent_slug'   => $parent['menu_slug'],
    'capability'  => 'activate_plugins',
    'redirect'    => false
  ));

  acf_add_options_sub_page(array(
    'page_title'  => 'Header Setting',
    'menu_title'  => 'Header Setting',
    'menu_slug'   => 'header-setting',
    'parent_slug'   => $parent['menu_slug'],
    'capability'  => 'activate_plugins',
    'redirect'    => false
  ));

  acf_add_options_sub_page(array(
    'page_title'  => '404 Setting',
    'menu_title'  => '404 Setting',
    'menu_slug'   => '404-setting',
    'parent_slug'   => $parent['menu_slug'],
    'capability'  => 'activate_plugins',
    'redirect'    => false
  ));

  acf_add_options_sub_page(array(
    'page_title'  => 'Site Setting',
    'menu_title'  => 'Site Setting',
    'menu_slug'   => 'site-setting',
    'parent_slug'   => $parent['menu_slug'],
    'capability'  => 'activate_plugins',
    'redirect'    => false
  ));
}

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
    $enableLazy = get_field('use_lazyload', 'option');
    if($enableLazy) {
      return  'src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="'.$src.'"';
    } else {
      return  'src="'.$src.'" data-src="'.$src.'"';
    }
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
                <section class="section-chat">
                  <div class="svg-arrow-chat">
                    <svg xmlns="http://www.w3.org/2000/svg" width="261" height="497" viewBox="0 0 261 497">
                      <path id="Header_arrow" d="M73.991,476.919c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S104.282,497,93.5,497,73.991,488.01,73.991,476.919ZM0,476.919c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S30.291,497,19.514,497,0,488.01,0,476.919Zm111.392-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S111.392,431.114,111.392,420.024Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081S67.693,440.1,56.916,440.1,37.4,431.114,37.4,420.024Zm110.579-56.9c0-11.09,8.737-20.08,19.514-20.08s19.514,8.991,19.514,20.08-8.737,20.081-19.514,20.081S147.981,374.218,147.981,363.128Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.991,19.514,20.08-8.737,20.081-19.514,20.081S73.991,374.218,73.991,363.128Zm111.392-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S185.383,317.322,185.383,306.233Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S111.392,317.322,111.392,306.233ZM221.972,248.5c0-11.091,8.737-20.081,19.514-20.081S261,237.409,261,248.5s-8.737,20.081-19.514,20.081S221.972,259.591,221.972,248.5Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.081-19.514,20.081S147.981,259.591,147.981,248.5Zm37.4-57.733c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S185.383,201.858,185.383,190.767Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S111.392,201.858,111.392,190.767Zm36.589-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S147.981,144.962,147.981,133.872Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S73.991,144.962,73.991,133.872Zm37.4-56.9c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S111.392,88.067,111.392,76.976Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S67.693,97.057,56.916,97.057,37.4,88.067,37.4,76.976Zm36.589-56.9C73.991,8.99,82.727,0,93.5,0s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S73.991,31.171,73.991,20.081ZM0,20.081C0,8.99,8.737,0,19.514,0S39.028,8.99,39.028,20.081s-8.737,20.08-19.514,20.08S0,31.171,0,20.081Z" opacity="0.1"/>
                    </svg>
                  </div>
                  <div class="container" style="background-color: <?php echo get_sub_field('section_background_color'); ?>">
                      <div class="row align-items-center d-flex justify-content-center">
                        <div class="col-12 col-md-7 col-lg-5 section-chat-content">
                            <h3><?php echo get_sub_field("section_headerline"); ?></h3>
                            <?php echo get_sub_field("section_content"); ?>
                            <div class="section-chat-btn align-items-center d-flex justify-content-left">
                              <a class="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                            </div>
                        </div>
                        <div class="col-12 col-md-5 col-lg-7 ml-auto section-chat-image">
                            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-fluid img-responsive img-outof-container"  />
                        </div>
                      </div>
                  </div>
                </section>
            <?php }
            elseif( get_row_layout() == "cta_section") // layout: CTA Section
            {
                $className = get_sub_field("custom_class_names");
                $link = get_sub_field('section_button');
                if( $link ):
                    $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                endif
            ?>
                <section class="section-cta container <?php echo $className; ?>">
                  <div class="row align-items-center d-flex justify-content-center">
                    <div class="col-12 section-cta-content text-center">
                        <h3><?php echo get_sub_field("section_title"); ?></h3>
                        <?php echo get_sub_field("section_hero_content"); ?>
                        <div class="section-cta-btn justify-content-center text-center">
                          <a class="button d-inline-block cta-btn" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                        </div>
                    </div>
                  </div>
                </section>
            <?php }
            elseif( get_row_layout() == "contact_section") // layout: Contact Section
            { ?>
                <section class="section-contact container">
                  <div class="row">
                    <div class="col-12 col-md-8 pr-md-5 contact-form">
                        <?php echo do_shortcode(get_sub_field("contact_html")); ?>
                        <hr class="mobile-show">
                    </div>
                    <div class="col-12 col-md-4 contact-section pl-md-5">
                        <div class="section-contact-info justify-content-start text-left">
                          <div class="row">
                            <div class="col-12 sub-title">
                              <h4>OFFICE & CONTACT INFO</h4>
                            </div>
                          </div>
                          <div class="row email info">
                              <div class="col-2">
                                <?php $image = get_sub_field('email_icon'); echo output_inline_svg_file($image); ?>
                              </div>
                              <div class="col-8">
                                <h4 class="d-block">Email</h4>
                                <a href="mailto:<?php echo get_sub_field('email'); ?>"><?php echo get_sub_field('email'); ?></a>
                              </div>
                          </div>
                          <div class="row phone info">
                              <div class="col-2">
                                <?php $imagePhone = get_sub_field('phone_icon'); echo output_inline_svg_file($imagePhone); ?>
                              </div>
                              <div class="col-8">
                                <h4 class="d-block">Phone</h4>
                                <a href="tel:<?php echo get_sub_field('phone'); ?>"><?php echo get_sub_field('phone'); ?></a>
                              </div>
                          </div>
                          <div class="row fax info">
                              <div class="col-2">
                                <?php $imageFax = get_sub_field('fax_icon'); echo output_inline_svg_file($imageFax); ?>
                              </div>
                              <div class="col-8">
                                <h4 class="d-block">Fax</h4>
                                <a href="fax:<?php echo get_sub_field('fax'); ?>"><?php echo get_sub_field('fax'); ?></a>
                              </div>
                          </div>
                          <div class="row social-section">
                            <div class="col-12 sub-title">
                              <h4>FOLLOW OUR SOCIAL</h4>
                            </div>
                          </div>
                          <div class="row">
                                <div class="col-12 container-fluid">
                                    <div class="row">
                                      <?php
                                        while(has_sub_field('social_media')):
                                          $image = get_sub_field('social_icon');
                                          $link = get_sub_field('link');
                                      ?>
                                      <div class="col social-icon">
                                          <a class="sm-icon" href="<?php echo $link; ?>" target="_blank"><?php echo output_inline_svg_file($image); ?></a>
                                      </div>
                                      <?php endwhile; ?>
                                    </div>
                                </div>
                          </div>
                        </div>
                    </div>
                  </div>
                  <div class="svg-chat-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="126" height="240" viewBox="0 0 126 240"><path d="M35.72,230.3A9.424,9.424,0,1,1,45.14,240,9.562,9.562,0,0,1,35.72,230.3ZM0,230.3a9.562,9.562,0,0,1,9.421-9.7,9.7,9.7,0,0,1,0,19.394A9.562,9.562,0,0,1,0,230.3Zm53.776-27.475a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,53.776,202.828Zm-35.72,0a9.562,9.562,0,0,1,9.421-9.7,9.7,9.7,0,0,1,0,19.394A9.562,9.562,0,0,1,18.056,202.828Zm53.383-27.475a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,71.439,175.353Zm-35.72,0a9.424,9.424,0,1,1,9.421,9.7A9.562,9.562,0,0,1,35.72,175.353ZM89.5,147.879a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,89.5,147.879Zm-35.72,0a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,53.776,147.879ZM107.159,120a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,107.159,120Zm-35.72,0a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,71.439,120ZM89.5,92.121a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,89.5,92.121Zm-35.72,0a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,53.776,92.121ZM71.439,64.647a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,71.439,64.647Zm-35.72,0a9.424,9.424,0,1,1,9.421,9.7A9.562,9.562,0,0,1,35.72,64.647ZM53.776,37.172a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,53.776,37.172Zm-35.72,0a9.562,9.562,0,0,1,9.421-9.7,9.7,9.7,0,0,1,0,19.394A9.562,9.562,0,0,1,18.056,37.172ZM35.72,9.7a9.424,9.424,0,1,1,18.841,0,9.424,9.424,0,1,1-18.841,0ZM0,9.7A9.562,9.562,0,0,1,9.421,0a9.562,9.562,0,0,1,9.42,9.7,9.562,9.562,0,0,1-9.42,9.7A9.562,9.562,0,0,1,0,9.7Z" fill="#b9b9b9" opacity="0.2"/></svg>
                  </div>
                  <hr class="mobile-show">
                </section>
            <?php }
            elseif( get_row_layout() == "content_section") // layout: Content Section
            {
                $titleAlignemnt = get_sub_field("title_alignment");
                $title_col_width = get_sub_field('title_col_width');
                $image = get_sub_field('content_image');
            ?>
                <section class="section-content container">
                    <div class="row align-items-center d-flex justify-content-center">
                        <?php
                            if($image):
                                output_acf_img($image,'lazyImg');
                            endif;
                        ?>
                    </div>
                    <?php if($titleAlignemnt == 'center') { ?>
                        <div class="row content-container">
                            <div class="col-12 section-cta-content text-center">
                                <h3><?php echo get_sub_field("content_title"); ?></h3>
                                <?php echo get_sub_field("content"); ?>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div class="row content-container">
                            <h3 class="col-12 col-md-<?php echo $title_col_width; ?>"><?php echo get_sub_field("content_title"); ?></h3>
                            <div class="col-12 col-md-<?php echo (11-$title_col_width); ?> <?php if($title_col_width == 4 || $title_col_width == 5) echo 'offset-md-1'; ?>"><?php echo get_sub_field("content"); ?></div>
                        </div>
                        <?php } ?>
                </section>
            <?php }
            elseif( get_row_layout() == "risks_tabs") // layout: Risks Tabs
            { ?>
                <section class="section-risks">
                    <div class="row">
                        <div class="col-12 col-md-5 risk-container">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1061" height="1060" viewBox="0 0 1061 1060" class="circle">
                              <g id="Group_195" data-name="Group 195" transform="translate(436 -2228)">
                                <g id="Group_191" data-name="Group 191">
                                  <ellipse id="Ellipse_230" data-name="Ellipse 230" cx="530.5" cy="530" rx="530.5" ry="530" transform="translate(-436 2228)" fill="#47c16d"/>
                                  <circle id="Ellipse_344" data-name="Ellipse 344" cx="452" cy="452" r="452" transform="translate(-357 2307)" fill="#231f20" opacity="0.1"/>
                                  <circle id="Ellipse_345" data-name="Ellipse 345" cx="357" cy="357" r="357" transform="translate(-262 2402)" fill="#231f20" opacity="0.1"/>
                                  <circle id="Ellipse_342" data-name="Ellipse 342" cx="258" cy="258" r="258" transform="translate(-163 2501)" fill="#231f20" opacity="0.1"/>
                                  <circle id="Ellipse_346" data-name="Ellipse 346" cx="165" cy="165" r="165" transform="translate(-70 2594)" fill="#231f20"/>
                                </g>
                                <g id="Group_192" data-name="Group 192">
                                  <ellipse id="Ellipse_232" data-name="Ellipse 232" cx="82.5" cy="84" rx="82.5" ry="84" transform="translate(12 2674)" fill="#fff"/>
                                  <ellipse id="Ellipse_343" data-name="Ellipse 343" cx="36.5" cy="38" rx="36.5" ry="38" transform="translate(58 2720)" fill="#231f20"/>
                                </g>
                              </g>
                            </svg>
                            <div class="risk-pointer" id="risk5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="472.124" height="390.264" viewBox="0 0 472.124 390.264"><circle cx="10" cy="10" r="10" transform="translate(440.405 370.264)" /><path d="M2975.041,10426.2l553.932,40.389-.563,5.316-533.189,12.151Z" transform="translate(4110.266 -10047.576) rotate(38)" /></svg>
                            </div>
                            <div class="risk-tab-container">
                                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                  <?php
                                  $i = 0;
                                  while(has_sub_field('risks')):
                                        $tab = strtolower(get_sub_field("risk_level"));
                                        $tab = preg_replace('/\s+/', '_', $tab);
                                  ?>
                                  <a class="nav-item nav-link <?php if($i==4) echo 'active'; ?> nav-item-<?php echo $i; ?>" data-pointer="risk<?php echo ($i+1); ?>" id="nav-<?php echo $tab; ?>-tab" data-toggle="tab" href="#nav-<?php echo $tab; ?>" role="tab" aria-controls="nav-<?php echo $tab; ?>" aria-selected="<?php if($i==0) echo 'true'; else echo 'false'; ?>"><?php echo get_sub_field("risk_level"); ?></a>
                                  <?php $i++; endwhile; ?>
                              </div>
                            </div>
                        </div>
                        <div class="mobile-show-640 col-12 mobile--price-dropdown">
                            <select class="mobile-dropdown-risk" id="mobile-tab" >
                              <?php
                              $i = 0;
                              while(has_sub_field('risks')):
                                $tab = strtolower(get_sub_field("risk_level"));
                                $tab = preg_replace('/\s+/', '_', $tab);
                              ?>
                              <option class="nav-item nav-link nav-item-<?php echo $i; ?>" data-pointer="risk<?php echo ($i+1); ?>" id="nav-<?php echo $tab; ?>-tab" data-title="#title-<?php echo $tab; ?>" data-toggle="tab" ole="tab" aria-controls="nav-<?php echo $tab; ?>" aria-selected="<?php if($i==0) echo 'true'; else echo 'false'; ?>" value="risk<?php echo ($i+1); ?>" <?php if($i==4) echo 'selected="selected"'; ?>><?php echo get_sub_field("risk_level"); ?></option>
                              <?php $i++; endwhile; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 offset-md-1 risk-content-container tab-content d-flex align-items-center justify-content-center">
                            <div class="dvd">&nbsp;</div>
                            <?php $i = 0;
                              while(has_sub_field('risks')):
                                $tab = strtolower(get_sub_field("risk_level"));
                                $tab = preg_replace('/\s+/', '_', $tab);
                            ?>
                            <div class="tab-pane fade <?php if($i==4) echo 'show active'; ?> row" id="nav-<?php echo $tab; ?>" role="tabpanel" aria-labelledby="nav-<?php echo $tab; ?>-tab">
                                <div class="col-12 col-md-9 pl-md-5 tab-content-col">
                                    <h2><hr align="left" /><?php echo get_sub_field('risk_level'); ?></h2>
                                    <?php echo get_sub_field('risk_content'); ?>
                                </div>
                            </div>
                            <?php $i++; endwhile; ?>
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
                $btn_alignment = (get_sub_field('section_button_alignment') == 'right') ? 'col-12 col-md-6' : 'col-12';
              endif
              ?>
                <section class="section-list <?php echo get_sub_field('custom_class_names'); ?> <?php echo $hasBorder; ?> <?php echo $$hasBgColor; ?> container">
                  <div class="row section-list-header">
                    <<?php echo get_sub_field("header_htag"); ?> class="<?php echo $btn_alignment; ?> align-items-center d-flex">
                      <?php echo get_sub_field("section_headerline"); ?>
                    </<?php echo get_sub_field("header_htag"); ?>>
                    <div class="desktop-show <?php echo $btn_alignment; ?> section-header-btn align-items-center d-flex justify-content-center ">
                      <a class="button cta-btn" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                    </div>
                  </div>
                  <div class="section-list-container">
                    <div class="row">
                      <?php
                        while(has_sub_field('content_list')):
                        $image = get_sub_field('icon_svg');
                      ?>
                        <div class="col-12 col-md-6 list-item">
                          <div class="row">
                            <div class="col-2"><?php echo output_inline_svg_file($image); ?></div>
                            <div class="col-10 col-md-8 pl-2 pl-md-4">
                              <h4><?php echo get_sub_field('list_headerline'); ?></h4>
                              <?php echo get_sub_field('list_content'); ?>
                            </div>
                          </div>
                        </div>
                      <?php endwhile; ?>
                    </div>
                  </div>
                  <div class="mobile-list-btn section-header-btn mobile-show">
                      <a class="button cta-btn" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
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
            elseif( get_row_layout() == "headline_section" ) // layout: Headline Section
            {
                $titleWidth = get_sub_field("headline_width");
                $image = get_sub_field("headline_image");
            ?>
                <section class="section-headline">
                    <div class="svg-arrow-headline d-none d-md-block">
                        <svg xmlns="http://www.w3.org/2000/svg" width="261" height="497" viewBox="0 0 261 497">
                          <path id="Header_arrow" d="M73.991,476.919c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S104.282,497,93.5,497,73.991,488.01,73.991,476.919ZM0,476.919c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S30.291,497,19.514,497,0,488.01,0,476.919Zm111.392-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S111.392,431.114,111.392,420.024Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081S67.693,440.1,56.916,440.1,37.4,431.114,37.4,420.024Zm110.579-56.9c0-11.09,8.737-20.08,19.514-20.08s19.514,8.991,19.514,20.08-8.737,20.081-19.514,20.081S147.981,374.218,147.981,363.128Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.991,19.514,20.08-8.737,20.081-19.514,20.081S73.991,374.218,73.991,363.128Zm111.392-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S185.383,317.322,185.383,306.233Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S111.392,317.322,111.392,306.233ZM221.972,248.5c0-11.091,8.737-20.081,19.514-20.081S261,237.409,261,248.5s-8.737,20.081-19.514,20.081S221.972,259.591,221.972,248.5Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.081-19.514,20.081S147.981,259.591,147.981,248.5Zm37.4-57.733c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S185.383,201.858,185.383,190.767Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S111.392,201.858,111.392,190.767Zm36.589-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S147.981,144.962,147.981,133.872Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S73.991,144.962,73.991,133.872Zm37.4-56.9c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S111.392,88.067,111.392,76.976Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S67.693,97.057,56.916,97.057,37.4,88.067,37.4,76.976Zm36.589-56.9C73.991,8.99,82.727,0,93.5,0s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S73.991,31.171,73.991,20.081ZM0,20.081C0,8.99,8.737,0,19.514,0S39.028,8.99,39.028,20.081s-8.737,20.08-19.514,20.08S0,31.171,0,20.081Z" opacity="0.1"/>
                        </svg>
                    </div>
                    <div class="container">
                        <div class="subheadline d-inline-block"><?php echo get_sub_field("page_subtitle"); ?></div>
                        <div class="row">
                            <h1 class="col-12 col-md-<?php echo $titleWidth;?>"><?php echo get_sub_field("page_title"); ?></h1>
                            <?php if($titleWidth!==12): ?>
                                <div class="col-12 col-md-<?php echo (11-$titleWidth);?> <?php if($titleWidth==4|| $titleWidth==5) echo 'offset-md-1'; ?>"><?php echo get_sub_field("page_hero_content"); ?></div>
                            <?php endif; ?>
                            <?php
                                if($image):
                                    $animation = get_sub_field('image_animation');
                                    if($animation)
                                        output_acf_img($image,'fit-container lazyImg pt-3 pt-md-0 animate__animated animate__fadeInUp animate__slower');
                                    else
                                        output_acf_img($image,'lazyImg pt-3 pt-md-0');
                                endif;
                            ?>
                        </div>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "home_page_banner" ) // layout: Home Page Banner
            {
                $link = get_sub_field("button");
                if( $link ):
                    $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                endif
            ?>
                <section class="section-home-header">
                    <div class="svg-arrow-head-home d-none d-md-block">
                        <svg xmlns="http://www.w3.org/2000/svg" width="261" height="497" viewBox="0 0 261 497">
                          <path id="Header_arrow" d="M73.991,476.919c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S104.282,497,93.5,497,73.991,488.01,73.991,476.919ZM0,476.919c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S30.291,497,19.514,497,0,488.01,0,476.919Zm111.392-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S111.392,431.114,111.392,420.024Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081S67.693,440.1,56.916,440.1,37.4,431.114,37.4,420.024Zm110.579-56.9c0-11.09,8.737-20.08,19.514-20.08s19.514,8.991,19.514,20.08-8.737,20.081-19.514,20.081S147.981,374.218,147.981,363.128Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.991,19.514,20.08-8.737,20.081-19.514,20.081S73.991,374.218,73.991,363.128Zm111.392-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S185.383,317.322,185.383,306.233Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S111.392,317.322,111.392,306.233ZM221.972,248.5c0-11.091,8.737-20.081,19.514-20.081S261,237.409,261,248.5s-8.737,20.081-19.514,20.081S221.972,259.591,221.972,248.5Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.081-19.514,20.081S147.981,259.591,147.981,248.5Zm37.4-57.733c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S185.383,201.858,185.383,190.767Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S111.392,201.858,111.392,190.767Zm36.589-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S147.981,144.962,147.981,133.872Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S73.991,144.962,73.991,133.872Zm37.4-56.9c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S111.392,88.067,111.392,76.976Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S67.693,97.057,56.916,97.057,37.4,88.067,37.4,76.976Zm36.589-56.9C73.991,8.99,82.727,0,93.5,0s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S73.991,31.171,73.991,20.081ZM0,20.081C0,8.99,8.737,0,19.514,0S39.028,8.99,39.028,20.081s-8.737,20.08-19.514,20.08S0,31.171,0,20.081Z" opacity="0.1"/>
                        </svg>
                    </div>
                    <div id="carouselHomeIndicators" class="carousel slide h-100" data-ride="carousel">
                        <ol class="carousel-indicators">
                        <?php
                            while(has_sub_field('images')):
                        ?>
                            <li data-target="#carouselHomeIndicators" data-slide-to="<?php echo get_row_index()-1; ?>"></li>
                        <?php endwhile; ?>
                        </ol>
                        <div class="carousel-inner">
                            <?php
                                while(has_sub_field('images')):
                                    $image = get_sub_field('image');
                            ?>
                                <div class="carousel-item <?php if(get_row_index()===1) echo 'active'; ?>">
                                    <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="full-width"  />
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <div class="container w-100">
                            <div class="carousel-content">
                                <div class="row">
                                    <div class="col-12 col-md-8 white-font my-auto">
                                        <h1><?php echo get_sub_field("headline"); ?></h1>
                                        <h5><?php echo get_sub_field("headline"); ?>content</h5>
                                        <a class="button d-inline-block" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="carousel-control-prev" href="#carouselHomeIndicators" role="button" data-slide="prev">
                            <span class="prev-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="54.179" height="30.12" viewBox="0 0 54.179 30.12"><path d="M21.157,24.968A1.8,1.8,0,0,1,22.284,28.1l-11.005,10.1H57.121c.063,0,.125,0,.188,0a1.805,1.805,0,0,1-.188,3.606H11.279L22.284,51.9a1.8,1.8,0,1,1-2.441,2.648L5.419,41.325a1.8,1.8,0,0,1,0-2.648L19.842,25.456a1.8,1.8,0,0,1,1.315-.488Z" transform="translate(-4.84 -24.966)" /></svg></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselHomeIndicators" role="button" data-slide="next">
                            <span class="next-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="54.18" height="30.12" viewBox="0 0 54.18 30.12"><g transform="translate(-4.84 -977.328)"><path d="M42.7,24.968A1.8,1.8,0,0,0,41.576,28.1l11.005,10.1H6.739c-.063,0-.125,0-.188,0a1.805,1.805,0,1,0,.188,3.606H52.581L41.576,51.9a1.8,1.8,0,1,0,2.441,2.648L58.441,41.325a1.8,1.8,0,0,0,0-2.648L44.018,25.456a1.8,1.8,0,0,0-1.315-.488Z" transform="translate(0 952.362)" /></g></svg></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "financial" ) // layout: Financial
            {
                $image = get_sub_field("section_pic");
                $borderBottom = get_sub_field("bottom_border");
            ?>
                <section class="section-financial" id="section-financial">
                    <div class="container">
                        <div class="row financial-container">
                            <div class="col-10 col-md-4 blue-rectangle">
                                <?php output_acf_img($image,'lazyImg touchLeftsm'); ?>
                            </div>
                            <div class="col-10 col-md-4 cover">
                                <?php output_acf_img($image,'lazyImg touchLeftsm'); ?>
                            </div>
                            <div class="col-12 col-md-7 col-md-6 offset-md-1 d-flex align-items-end section-financial-content">
                                <div class="financial-content">
                                    <div class="subline"><?php echo get_sub_field("subheadline"); ?></div>
                                    <h3><?php echo get_sub_field("headline"); ?></h3>
                                    <?php echo get_sub_field("content"); ?>
                                    <hr class="number-hr" />
                                    <div class="row number-section">
                                        <?php
                                            while(has_sub_field('financial_numbers')):
                                        ?>
                                          <div class="col-6 col-md-4">
                                              <div class="number-info">
                                                <span class="f32 counter"><?php echo get_sub_field("number"); ?></span>
                                                <?php if(get_sub_field("number_additional_info")) { ?><span class="f32"><?php echo get_sub_field("number_additional_info"); ?></span><?php } ?>
                                              </div>
                                              <?php echo get_sub_field("number_content"); ?>
                                          </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if($borderBottom): ?>
                        <hr class="container" />
                    <?php endif; ?>
                </section>
            <?php }
            elseif( get_row_layout() == "casestudies_gallery" ) // layout: CaseStudies Gallery
            {
                $link = get_sub_field("section_button");
                if( $link ):
                    $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                endif
            ?>
                <section class="section-casestudies-gallery">
                    <div class="container">
                        <div class="row">
                            <h2 class="col-12 col-md-6 col-lg-5"><?php echo get_sub_field("section_headerline"); ?></h2>
                            <div class="col-12 col-md-6 col-lg-5 offset-lg-2 desktop-show-640">
                                <a class="button cta-btn d-inline-block" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                            </div>
                        </div>
                        <div class="row gallery">
                            <div class="gallery-item-col col-12 col-md-7">
                                <?php
                                  while(has_sub_field('gallery')):
                                    $image = get_sub_field("case_study_pic");
                                    $location = get_sub_field("case_study_location");
                                ?>
                                <?php if(get_row_index()===1) { ?>
                                    <div class="gallery-item h-100">
                                        <?php output_acf_img($image,'lazyImg'); ?>
                                        <div class="gallery-content">
                                            <h5><?php echo get_sub_field("case_study_title"); ?></h5>
                                            <?php if($location): ?><?php echo $location; ?><?php endif; ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php endwhile; ?>
                            </div>
                            <div class="gallery-item-col col-12 col-md-4">
                                <?php
                                  while(has_sub_field('gallery')):
                                    $image = get_sub_field("case_study_pic");
                                    $location = get_sub_field("case_study_location");
                                ?>
                                <?php if(get_row_index() > 1) { ?>
                                    <div class="gallery-item gallery-item-<?php echo get_row_index(); ?> h-50 <?php if(get_row_index() === 2) { echo 'pb-3'; } elseif(get_row_index() === 3) { echo 'pt-3'; } ?>">
                                        <?php output_acf_img($image,'lazyImg'); ?>
                                        <div class="gallery-content">
                                            <h5><?php echo get_sub_field("case_study_title"); ?></h5>
                                            <?php if($location): ?><?php echo $location; ?><?php endif; ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <div class="mobile-show-640 gallery-button-bottom">
                            <a class="button cta-btn d-inline-block" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
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
                            <div class="col-12 col-md-6 text-center head-content"><?php echo get_sub_field("sub_headline"); ?></div>
                        </div>
                        <div class="row mobile-blue">
                            <div class="mobile-up container">
                                <div class="row text-left align-items-start d-flex justify-content-center">
                                    <?php
                                      while(has_sub_field('words_cols')):
                                    ?>
                                    <div class="col-12 col-2-adjust">
                                        <div class="word-item">
                                            <div class="word"><?php echo get_sub_field("word"); ?></div>
                                        </div>
                                        <small class="f16 d-inline-block">0<?php echo get_row_index(); ?></small>
                                        <h4><?php echo get_sub_field("word"); ?></h4>
                                        <?php echo get_sub_field("content"); ?>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fullWidth image-content" style="background-color: #0d4059;">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <?php $image = get_sub_field("big_image"); output_acf_img($image,'lazyImg'); ?>
                                </div>
                                <div class="col-12 col-md-6 pr-0 pl-0 pl-md-4">
                                    <h2><hr class="d-block ml-0"><?php echo get_sub_field("image_headline"); ?></h2>
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
                $animation = get_sub_field('image_animation');
            ?>
                <section class="section-story-gallery">
                    <div class="story-gallery row align-items-end d-flex justify-content-end">
                        <?php
                          while(has_sub_field('images')):
                            $image = get_sub_field('image');
                            if(get_row_index()<=2):
                        ?>
                        <div class="<?php if(get_row_index()==1) echo 'image-1 col-4 col-md-3'; elseif(get_row_index()==2) echo 'image-2 col-5 col-md-6'; ?>">
                            <?php
                            if($animation) {
                                output_acf_img($image,'lazyImg pt-3 pt-md-0 animate__animated animate__fadeInUp animate__slower');
                            }
                            else {
                                output_acf_img($image,'lazyImg');
                            } ?>
                        </div>
                        <?php endif; endwhile; ?>
                    </div>
                    <div class="story-gallery story-gallery-2 row align-items-start d-flex justify-content-end">
                        <?php
                          while(has_sub_field('images')):
                            $image = get_sub_field('image');
                            if(get_row_index()>=3):
                        ?>
                        <div class="<?php if(get_row_index()==3) echo 'image-3 col-md-6 col-7'; elseif(get_row_index()==4) echo 'image-4 col-5 col-md-6'; ?>">
                            <?php
                            if($animation) {
                                output_acf_img($image,'lazyImg pt-3 pt-md-0 animate__animated animate__fadeInUp animate__slower');
                            }
                            else {
                                output_acf_img($image,'lazyImg');
                            } ?>
                        </div>
                        <?php endif; endwhile; ?>
                        <div class="story-gallery-content col-12 col-md-5">
                            <?php echo get_sub_field("section_subheaderline"); ?>
                            <h3><?php echo get_sub_field("section_headerline"); ?></h3>
                            <div class="row">
                                <?php
                                  while(has_sub_field('story_gallery_list')):
                                    $image = get_sub_field('icon_svg');
                                ?>
                                <div class="col-12 col-md-6 gallery-point">
                                    <div class="row">
                                        <div class="story-icon col-2 col-md-12"><?php echo output_inline_svg_file($image); ?></div>
                                        <div class="story-content col-10 col-md-12"><?php echo get_sub_field("list_content"); ?></div>
                                    </div>
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
                $className = get_sub_field("custom_class_names");
                wp_reset_query();
                $args = array(
                    'post_type'=>$post_type,
                    'post_status' => 'publish',
                    'posts_per_page'=>-1,
                    'orderby'=>'date',
                );
                $the_query = new WP_Query( $args );
            ?>
                <section class="section-custom-post-type <?php echo $className; ?>">
                    <div class="container">
                        <?php if($post_type=="case_studies") { ?>
                            <div class="grid row align-items-center d-flex justify-content-center cs-container">
                                <?php $i = 0;
                                    while ( $the_query->have_posts() ): $the_query->the_post();
                                        $bgimage = get_field("image_for_case_study");
                                  ?>
                                    <div class="col-12 col-md-6 item <?php if($i%2==0) echo 'ontop pl-0 pr-0 pr-md-3'; elseif($i%2 === 1) echo 'oncenter pr-0 pl-0 pl-md-3'; ?>">
                                            <?php echo output_acf_img($bgimage,'lazyImg'); ?>
                                            <div class="case-content">
                                                <a class="item-container mr-2" href="<?php echo get_post_permalink(); ?>"><h5><?php echo get_field("headline"); ?></h5></a>
                                                <?php echo get_field("brief"); ?>
                                            </div>
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
                                        $image = get_field("team_pic_in_modal");
                                        $name = get_field("name");
                                        $level = get_field("management_level");
                                        $title = get_field("title");
                                        $bio = get_field("bio");
                                        $link = get_field("link");
                                        $fullStory = get_field("full_story");
                                        if( $link ):
                                            $link_url = $link['url'];
                                            $link_title = $link['title'];
                                            $link_target = $link['target'] ? $link['target'] : '_self';
                                        endif
                                  ?>
                                    <div class="col-12 col-md-6 col-lg-4 item normal-item">
                                        <a class="button pl-0 link no-arrow" data-toggle="modal" data-target="#<?php echo 'team'.get_row_index().'Modal'; ?>"><?php if($bgimage) { ?><?php echo output_acf_img($bgimage, "lazyImg"); ?><?php } ?></a>
                                        <a class="button pl-0 link no-arrow" data-toggle="modal" data-target="#<?php echo 'team'.get_row_index().'Modal'; ?>"><h5><?php echo $name; ?></h5></a>
                                        <small class="d-block"><?php echo $title; ?></small>
                                        <?php echo $bio; ?>
                                        <?php if($bgimage) { ?>
                                            <a class="button pl-0 link" data-toggle="modal" data-target="#<?php echo 'team'.get_row_index().'Modal'; ?>">READ MORE</a>
                                            <div class="modal fade" id="<?php echo 'team'.get_row_index().'Modal'; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo 'team'.get_row_index().'Modal'; ?>Label" aria-hidden="true">
                                              <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                  <div class="modal-body">
                                                    <div class="fullWidth image-content">
                                                        <div class="container">
                                                            <div class="row">
                                                                <div class="col-12 col-md-6 green-rectangle pl-0">
                                                                    <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-fluid img-responsive img-outof-right-container"  />
                                                                </div>
                                                                <div class="col-12 col-md-6 pl-0 pl-md-5 modal-copy-container">
                                                                    <div class="row justify-content-end pr-3 pt-1 modal-copy">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                    </div>
                                                                    <h3><hr class="d-block ml-0"><?php echo $level; ?></h3>
                                                                    <h4><?php echo $name; ?></h4>
                                                                    <small class="d-block"><?php echo $title; ?></small>
                                                                    <?php echo $fullStory; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                        <?php } else { ?>
                                            <a class="button pl-0 link" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                                        <?php } ?>
                                    </div>
                                  <?php $i++; endwhile; wp_reset_postdata(); }  ?>
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
                                        <div class="col-12 col-md-4 item <?php if($i%3==0) echo 'ontop'; elseif($i%3 === 1) echo 'oncenter'; elseif($i%3 === 2) echo 'onbottom'; ?>">
                                            <a class="item-container mr-2 <?php if($bgimage) echo 'item-bg-overlay'; ?>" href="<?php echo get_permalink(); ?>" <?php if($bgimage) echo "style='background-image:url(".$bgimage[url].");filter: grayscale(100%);'" ?>>
                                                <div class="item-icon"><?php echo output_inline_svg_file($image); ?></div>
                                                <div class="sit-bottom">
                                                    <h5><?php echo get_field("headline"); ?></h5>
                                                    <?php echo get_field("brief"); ?>
                                                </div>
                                            </a>
                                        </div>
                                      <?php $i++; endwhile; wp_reset_postdata(); }  ?>
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
                $bgcPosition = get_sub_field("section_background_color_position");
                $colClassNames = get_sub_field("cols_section_custom_class_names");
                $link = get_sub_field("button_after_col_section");
                $isInside = get_sub_field("col_info_inside");
                $showNumber = get_sub_field("show_number");
                $animation = get_sub_field('image_animation');
                if( $link ):
                    $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                endif
                ?>
                <section class="section-cols <?php echo $className; ?>" style="<?php if($bgColor && $bgcPosition === 'Full') echo 'background-color:' . $bgColor .' '; ?>">
                    <?php if($sectionWidth==='withincontainer'): ?><div class="container col-head-section"><?php endif; ?>
                      <div class="text-center <?php if(!get_sub_field('section_content')) echo 'pb-100'; ?>">
                          <h2 class="text-center"><?php echo get_sub_field("section_headerline"); ?></h2>
                          <div class="row justify-content-center d-flex m-bottom">
                              <div class="col-subline col-10 <?php if(is_page('careers')) echo 'col-md-8'; else echo 'col-md-6'; ?>"><?php echo get_sub_field("section_subheaderline"); ?></div>
                              <?php if(get_sub_field("section_content")) { ?>
                                  <div class="col-content col-12 col-md-8">
                                      <?php echo get_sub_field("section_content"); ?>
                                  </div>
                              <?php } ?>
                          </div>
                      </div>
                    <?php if($sectionWidth==='withincontainer'): ?></div><?php endif; ?>
                    <?php if($sectionWidth==='withincontainer'): ?><div class="cols-section" style="<?php if($bgColor && $bgcPosition === 'Middle') echo 'background-color:' . $bgColor .' '; ?>"><div class="container"><?php endif; ?>
                          <div class="row cols pb-0 pb-md-5 <?php echo $colClassNames; ?>" <?php if($bgImage) echo 'style="background-image:url(' . $bgImage . '); background-position: center center;background-repeat: no-repeat;background-size: cover;"' ?>>
                                <?php
                                  while(has_sub_field('cols')):
                                    $image = get_sub_field('col_image');
                                    $icon = get_sub_field('col_icon_svg');
                                    $colContent = get_sub_field('col_content');
                                ?>
                                <?php if(!$isInside) { ?>
                                    <?php if(!is_page("contact")) { ?>
                                        <div class="item-col <?php if(is_page('story')) echo 'col-6'; else echo 'col-12'; ?> col-md-<?php echo 12/$numberofCols;?> item-<?php echo get_row_index(); ?> <?php if($numberofCols==2) { if(get_row_index()%2==0) echo 'pl-md-3 pl-md-5'; else echo ' pr-md-3 pr-md-5'; } ?>">
                                            <?php if($icon) { ?>
                                                <div class="row">
                                                    <div class="col-2 col-svg">
                                                        <?php output_inline_svg_file($icon); ?>
                                                    </div>
                                                    <div class="text-left col-10 pl-4">
                                                        <?php if(get_sub_field("col_title")) { ?><h4><?php echo get_sub_field("col_title"); ?></h4><?php } ?>
                                                        <?php echo $colContent; ?>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <?php if($image) {
                                                    if($animation) {
                                                        output_acf_img($image,'lazyImg pt-3 pt-md-0 animate__animated animate__fadeInUp animate__slower');
                                                    }
                                                    else {
                                                        output_acf_img($image,'lazyImg');
                                                    }
                                                } ?>
                                                <?php if($showNumber) { ?><small class="f16 d-block">0<?php echo get_row_index(); ?></small><?php } ?>
                                                <?php if(get_sub_field("col_title")) { ?><h4><?php echo get_sub_field("col_title"); ?></h4><?php } ?>
                                                <div class="text-left"><?php echo $colContent; ?></div>
                                            <?php } ?>
                                        </div>
                                    <?php } elseif(is_page("contact")&&$numberofCols==4) { ?>
                                        <div class="col-12 col-md-3 item-<?php echo get_row_index(); ?> location-office col4 <?php if((get_row_index()-1)%2==0) echo 'ontop'; elseif((get_row_index()-1)%2 === 1) echo 'oncenter'; ?>">
                                            <div class="col4-container"><?php if($image) { ?><?php output_acf_img($image,'lazyImg'); ?><?php } ?>
                                                <div class="office-location">
                                                    <h2><?php echo get_sub_field("col_title"); ?></h2>
                                                </div>
                                            </div>
                                            <div class="address"><?php echo $colContent; ?></div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="col-12 col-md-<?php echo 12/$numberofCols;?> item-<?php echo get_row_index(); ?> location-office">
                                        <?php if($image) {
                                            output_acf_img($image,'lazyImg');
                                        } ?>
                                        <div class="office-location">
                                            <h2><?php echo get_sub_field("col_title"); ?></h2>
                                            <div class="address"><?php echo $colContent; ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php endwhile; ?>
                                <?php if($link) { ?>
                                    <div class="row ml-auto mr-auto mt-5 col-btn-bottom">
                                      <a class="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                                    </div>
                                <?php } ?>
                          </div>
                      </div>
                      <?php if($borderBottom): ?>
                        <div class="container"><hr class="mt-3 mt-md-5" /></div>
                      <?php endif; ?>
                    <?php if($sectionWidth==='withincontainer'): ?></div><?php endif; ?>
                </section>
            <?php }
            elseif( get_row_layout() == "testimonial_section" ) // layout: Testimonial Section
            {
                $bgColor = get_sub_field("testimonial_background_color");
                $image = get_sub_field('testimonial_image');
                $className = get_sub_field("custom_class_names");
            ?>
                <section class="section-testimonials <?php echo $className; ?>" style="<?php if($bgColor) echo 'background-color:' . $bgColor .' '; ?>">
                    <div class="svg-arrow-testimonial d-none d-md-block">
                        <svg xmlns="http://www.w3.org/2000/svg" width="241" height="458" viewBox="0 0 241 458">
                          <path id="Testimonial_arrow" d="M68.321,439.495A18.025,18.025,0,1,1,86.34,458,18.267,18.267,0,0,1,68.321,439.495ZM0,439.495a18.267,18.267,0,0,1,18.019-18.5,18.512,18.512,0,0,1,0,37.01A18.267,18.267,0,0,1,0,439.495Zm102.857-52.431a18.025,18.025,0,1,1,18.019,18.5A18.267,18.267,0,0,1,102.857,387.064Zm-68.321,0a18.025,18.025,0,1,1,18.019,18.5A18.267,18.267,0,0,1,34.536,387.064Zm102.106-52.431a18.025,18.025,0,1,1,18.019,18.505A18.267,18.267,0,0,1,136.642,334.633Zm-68.321,0A18.025,18.025,0,1,1,86.34,353.138,18.267,18.267,0,0,1,68.321,334.633ZM171.177,282.2a18.025,18.025,0,1,1,18.019,18.5A18.267,18.267,0,0,1,171.177,282.2Zm-68.321,0a18.025,18.025,0,1,1,18.019,18.5A18.267,18.267,0,0,1,102.857,282.2ZM204.962,229a18.025,18.025,0,1,1,18.019,18.505A18.267,18.267,0,0,1,204.962,229Zm-68.321,0a18.025,18.025,0,1,1,18.019,18.505A18.267,18.267,0,0,1,136.642,229Zm34.536-53.2A18.025,18.025,0,1,1,189.2,194.3,18.267,18.267,0,0,1,171.177,175.8Zm-68.321,0A18.025,18.025,0,1,1,120.876,194.3,18.267,18.267,0,0,1,102.857,175.8Zm33.785-52.431a18.025,18.025,0,1,1,18.019,18.5A18.267,18.267,0,0,1,136.642,123.367Zm-68.321,0a18.025,18.025,0,1,1,18.019,18.5A18.267,18.267,0,0,1,68.321,123.367Zm34.536-52.431a18.025,18.025,0,1,1,18.019,18.505A18.267,18.267,0,0,1,102.857,70.936Zm-68.321,0A18.025,18.025,0,1,1,52.555,89.441,18.267,18.267,0,0,1,34.536,70.936ZM68.321,18.505a18.025,18.025,0,1,1,36.037,0,18.025,18.025,0,1,1-36.037,0ZM0,18.505A18.267,18.267,0,0,1,18.019,0,18.267,18.267,0,0,1,36.037,18.505a18.266,18.266,0,0,1-18.018,18.5A18.267,18.267,0,0,1,0,18.505Z" fill="#0093b2" opacity="0.2"/>
                        </svg>
                    </div>
                    <div class="container">
                        <div class="row">
                            <h2 class="col-12 col-md-8 col-md-7 pl-0"><?php echo get_sub_field("testimonial_headline"); ?></h2>
                        </div>
                        <div class="row testimonials-container">
                            <div class="col-12 col-md-5 green-rectangle">
                                <?php output_acf_img($image,'lazyImg touchLeft'); ?>
                            </div>
                            <div class="col-12 col-md-5 cover">
                                <?php output_acf_img($image,'lazyImg touchLeft'); ?>
                            </div>
                            <div class="white-bg">
                                <div id="testimonialsControls" class="carousel slide" data-ride="carousel">
                                      <div class="carousel-inner">
                                <?php
                                  $post_objects = get_sub_field('testimonials');
                                  if( $post_objects ): $i = 0;
                                    foreach( $post_objects as $post):
                                        $headline = get_field("testimonial_headline", $post);
                                        $content = get_field("testimonial_content", $post);
                                        $name = get_field("name", $post);
                                        $title = get_field("title", $post);
                                        $image = wp_get_attachment_image_src(get_field('personal_pic',$post), "medium");
                                ?>
                                        <div class="carousel-item <?php if($i==0) echo 'active'; ?> pl-0 pl-md-3">
                                            <div class="f32"><?php echo $headline; ?></div>
                                            <div class="testi-content"><?php echo $content; ?></div>
                                            <div class="testi-pic"><img src="<?php echo $image[0]; ?>" alt="person pic" width="50" height="50" class="img-responsive person-pic" /></div>
                                            <div class="testi-person"><?php echo $name; ?></div>
                                            <div class="testi-title"><?php echo $title; ?></div>
                                        </div>
                                      <?php $i++; endforeach; endif; ?>
                                      </div>
                                </div>
                                <a class="carousel-control-prev" href="#testimonialsControls" role="button" data-slide="prev">
                                    <span class="prev-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="54.179" height="30.12" viewBox="0 0 54.179 30.12"><path d="M21.157,24.968A1.8,1.8,0,0,1,22.284,28.1l-11.005,10.1H57.121c.063,0,.125,0,.188,0a1.805,1.805,0,0,1-.188,3.606H11.279L22.284,51.9a1.8,1.8,0,1,1-2.441,2.648L5.419,41.325a1.8,1.8,0,0,1,0-2.648L19.842,25.456a1.8,1.8,0,0,1,1.315-.488Z" transform="translate(-4.84 -24.966)" fill="#00629b"/></svg></span>
                                    <span class="sr-only"></span>
                                </a>
                                <a class="carousel-control-next" href="#testimonialsControls" role="button" data-slide="next">
                                    <span class="next-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="54.18" height="30.12" viewBox="0 0 54.18 30.12"><g transform="translate(-4.84 -977.328)"><path d="M42.7,24.968A1.8,1.8,0,0,0,41.576,28.1l11.005,10.1H6.739c-.063,0-.125,0-.188,0a1.805,1.805,0,1,0,.188,3.606H52.581L41.576,51.9a1.8,1.8,0,1,0,2.441,2.648L58.441,41.325a1.8,1.8,0,0,0,0-2.648L44.018,25.456a1.8,1.8,0,0,0-1.315-.488Z" transform="translate(0 952.362)" fill="#00629b"/></g></svg></span>
                                    <span class="sr-only"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="white-bg-full">&nbsp;</div>
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
                    <div class="svg-arrow-headline d-none d-md-block">
                        <svg xmlns="http://www.w3.org/2000/svg" width="261" height="497" viewBox="0 0 261 497">
                          <path id="Header_arrow" d="M73.991,476.919c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S104.282,497,93.5,497,73.991,488.01,73.991,476.919ZM0,476.919c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S30.291,497,19.514,497,0,488.01,0,476.919Zm111.392-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S111.392,431.114,111.392,420.024Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081S67.693,440.1,56.916,440.1,37.4,431.114,37.4,420.024Zm110.579-56.9c0-11.09,8.737-20.08,19.514-20.08s19.514,8.991,19.514,20.08-8.737,20.081-19.514,20.081S147.981,374.218,147.981,363.128Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.991,19.514,20.08-8.737,20.081-19.514,20.081S73.991,374.218,73.991,363.128Zm111.392-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S185.383,317.322,185.383,306.233Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S111.392,317.322,111.392,306.233ZM221.972,248.5c0-11.091,8.737-20.081,19.514-20.081S261,237.409,261,248.5s-8.737,20.081-19.514,20.081S221.972,259.591,221.972,248.5Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.081-19.514,20.081S147.981,259.591,147.981,248.5Zm37.4-57.733c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S185.383,201.858,185.383,190.767Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S111.392,201.858,111.392,190.767Zm36.589-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S147.981,144.962,147.981,133.872Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S73.991,144.962,73.991,133.872Zm37.4-56.9c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S111.392,88.067,111.392,76.976Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S67.693,97.057,56.916,97.057,37.4,88.067,37.4,76.976Zm36.589-56.9C73.991,8.99,82.727,0,93.5,0s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S73.991,31.171,73.991,20.081ZM0,20.081C0,8.99,8.737,0,19.514,0S39.028,8.99,39.028,20.081s-8.737,20.08-19.514,20.08S0,31.171,0,20.081Z" opacity="0.1"/>
                        </svg>
                    </div>
                    <div class="container">
                      <div class="row align-items-center d-flex justify-content-left">
                        <div class="col-12 header-content">
                            <?php
                                $link = '';
                                if($post_type=='sectors') $link = '/sectors';
                                elseif($post_type=='services') $link = '/services';
                                elseif($post_type=='case_studies') $link = '/case-studies';
                            ?>
                            <a href="<?php echo $link; ?>" class="d-inline-block back-casestudy">
                                <?php echo str_replace("_", " ", $post_type); ?>
                            </a>
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
                        <div class="col-12 col-md-6 col-lg-1 col-info">
                            <h4>Year</h4>
                            <?php echo get_sub_field('case_year'); ?>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 col-info">
                            <h4>Location</h4>
                            <?php echo get_sub_field('case_location'); ?>
                        </div>
                        <div class="col-12 col-md-12 col-lg-6 offset-lg-1 col-info">
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
                    <div class="row desktop-show">
                        <div class="col-3 col-md-2 col-lg-1 icon-line">
                            <div class="round-svg align-items-center d-flex justify-content-center"><?php output_inline_svg_file($icon); ?></div>
                            <svg xmlns="http://www.w3.org/2000/svg" id="mySVG" viewBox="0 0 60 55" preserveAspectRatio="xMidYMin slice" style="width: 30px; height: 10px; overflow: visible">
                              <circle id="circle" cx="10" cy="10" r="10"/>
                              <path id="myline" class="st0" stroke-dasharray="0" d="M165 0 v400 20" />
                            </svg>
                        </div>
                        <div class="col-12 col-md-8 col-lg-9 offset-md-2 offset-lg-2 icon-image">
                            <?php output_acf_img($image,'lazyImg'); ?>
                        </div>
                    </div>
                    <div class="row mobile-show">
                        <div class="col-12 col-md-8 col-lg-9 offset-md-2 offset-lg-2 icon-image">
                            <?php output_acf_img($image,'lazyImg'); ?>
                        </div>
                    </div>
                    <?php
                        $count = count(get_sub_field('mobile_details_dropdown'));
                        if($count>1) :
                    ?>
                        <div class="row mobile-show pb-4">
                            <nav class="col-12">
                              <select class="mobile-dropdown" id="mobile-tab" >
                              <?php
                              while(has_sub_field('mobile_details_dropdown')):
                                    $tab = strtolower(get_sub_field("dropdown"));
                                    $tab = preg_replace('/\s+/', '_', $tab);
                              ?>
                              <option class="nav-item nav-link nav-item-<?php echo $i; ?>  mobile-tab-item" id="nav-<?php echo $tab; ?>-tab" data-title="#title-<?php echo $tab; ?>" data-toggle="tab" ole="tab" aria-controls="nav-<?php echo $tab; ?>" aria-selected="<?php if($i==0) echo 'true'; else echo 'false'; ?>" value="#nav-<?php echo $tab; ?>"><?php echo get_sub_field("dropdown"); ?></option>
                              <?php endwhile; ?>
                              </select>
                            </nav>
                        </div>
                    <?php endif; ?>
                    <div class="row mobile-show">
                        <div class="col-3 icon-line icon-line-mobile">
                            <div class="round-svg align-items-center d-flex justify-content-center"><?php output_inline_svg_file($icon); ?></div>
                        </div>
                        <div class="tab-content col-9 pl-0 mobile-tab-title align-items-center d-flex justify-content-center">
                            <?php
                              $i = 0;
                              while(has_sub_field('mobile_details_dropdown')):
                                    $tab = strtolower(get_sub_field("dropdown"));
                                    $tab = preg_replace('/\s+/', '_', $tab);
                              ?>
                              <h3 class="mb-0 tab-pane title-detail fade <?php if($i==0) echo 'show active'; ?>" id="title-nav-<?php echo $tab; ?>">
                                  <?php echo get_sub_field("content_title"); ?>
                              </h3>
                              <?php $i++; endwhile; ?>
                        </div>
                    </div>
                </section>
            <?php }
            elseif( get_row_layout() == "case_study_section" ) // layout: Case Study Section
            {
            ?>
                <section class="container section-case-study">
                    <div class="svg-arrow-case-study">
                        <svg xmlns="http://www.w3.org/2000/svg" width="126" height="240" viewBox="0 0 126 240"><path d="M35.72,230.3A9.424,9.424,0,1,1,45.14,240,9.562,9.562,0,0,1,35.72,230.3ZM0,230.3a9.562,9.562,0,0,1,9.421-9.7,9.7,9.7,0,0,1,0,19.394A9.562,9.562,0,0,1,0,230.3Zm53.776-27.475a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,53.776,202.828Zm-35.72,0a9.562,9.562,0,0,1,9.421-9.7,9.7,9.7,0,0,1,0,19.394A9.562,9.562,0,0,1,18.056,202.828Zm53.383-27.475a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,71.439,175.353Zm-35.72,0a9.424,9.424,0,1,1,9.421,9.7A9.562,9.562,0,0,1,35.72,175.353ZM89.5,147.879a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,89.5,147.879Zm-35.72,0a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,53.776,147.879ZM107.159,120a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,107.159,120Zm-35.72,0a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,71.439,120ZM89.5,92.121a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,89.5,92.121Zm-35.72,0a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,53.776,92.121ZM71.439,64.647a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,71.439,64.647Zm-35.72,0a9.424,9.424,0,1,1,9.421,9.7A9.562,9.562,0,0,1,35.72,64.647ZM53.776,37.172a9.425,9.425,0,1,1,9.421,9.7A9.562,9.562,0,0,1,53.776,37.172Zm-35.72,0a9.562,9.562,0,0,1,9.421-9.7,9.7,9.7,0,0,1,0,19.394A9.562,9.562,0,0,1,18.056,37.172ZM35.72,9.7a9.424,9.424,0,1,1,18.841,0,9.424,9.424,0,1,1-18.841,0ZM0,9.7A9.562,9.562,0,0,1,9.421,0a9.562,9.562,0,0,1,9.42,9.7,9.562,9.562,0,0,1-9.42,9.7A9.562,9.562,0,0,1,0,9.7Z" fill="#a2acab" opacity="0.2"/></svg>
                    </div>
                    <div class="row align-items-center d-flex">
                        <?php
                            $post_objects = get_sub_field("pick_a_case_study");
                            if( $post_objects ):
                                $headline = get_field('headline',$post_objects);
                                $brief = get_field('brief',$post_objects);
                                $image = get_field('image',$post_objects);
                        ?>
                            <div class="col-7 col-md-5 case-study-img">
                                <?php output_acf_img($image,'lazyImg img-outof-border'); ?>
                                <div class="green-rectangle">&nbsp;</div>
                            </div>
                            <div class="col-12 col-md-5 offset-md-2 case-study-content">
                                <div class="case-study-type">CASE STUDY</div>
                                <h3><?php echo $headline; ?></h3>
                                <?php echo $brief; ?>
                                <a class="button d-inline-block mg-t-60" href="<?php echo get_permalink($post_objects); ?>" target="_self">READ CASE STUDY</a>
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
                            <nav class="col-12 desktop-show">
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
                                $tab = strtolower(get_sub_field("detail_type"));
                                $tab = preg_replace('/\s+/', '_', $tab);
                            ?>
                            <div class="tab-pane fade <?php if($i==0) echo 'show active'; ?> row" id="nav-<?php echo $tab; ?>" role="tabpanel" aria-labelledby="nav-<?php echo $tab; ?>-tab">
                                <div class="col-12 col-md-6 pl-0 <?php if($count>1) echo 'desktop-show-640'; ?>">
                                    <h3><?php echo get_sub_field('detail_headline'); ?></h3>
                                </div>
                                <div class="col-12 col-md-6 pl-0 pl-md-4 detail-content-copy">
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
                <section class="section-chat">
                  <div class="container" style="background-color: <?php echo get_sub_field('section_background_color'); ?>">
                      <div class="svg-arrow-chat">
                        <svg xmlns="http://www.w3.org/2000/svg" width="261" height="497" viewBox="0 0 261 497">
                          <path id="Header_arrow" d="M73.991,476.919c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S104.282,497,93.5,497,73.991,488.01,73.991,476.919ZM0,476.919c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S30.291,497,19.514,497,0,488.01,0,476.919Zm111.392-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S111.392,431.114,111.392,420.024Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081S67.693,440.1,56.916,440.1,37.4,431.114,37.4,420.024Zm110.579-56.9c0-11.09,8.737-20.08,19.514-20.08s19.514,8.991,19.514,20.08-8.737,20.081-19.514,20.081S147.981,374.218,147.981,363.128Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.991,19.514,20.08-8.737,20.081-19.514,20.081S73.991,374.218,73.991,363.128Zm111.392-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S185.383,317.322,185.383,306.233Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S111.392,317.322,111.392,306.233ZM221.972,248.5c0-11.091,8.737-20.081,19.514-20.081S261,237.409,261,248.5s-8.737,20.081-19.514,20.081S221.972,259.591,221.972,248.5Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.081-19.514,20.081S147.981,259.591,147.981,248.5Zm37.4-57.733c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S185.383,201.858,185.383,190.767Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S111.392,201.858,111.392,190.767Zm36.589-56.9c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S147.981,144.962,147.981,133.872Zm-73.991,0c0-11.091,8.737-20.081,19.514-20.081s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S73.991,144.962,73.991,133.872Zm37.4-56.9c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08-8.737,20.081-19.514,20.081S111.392,88.067,111.392,76.976Zm-73.991,0c0-11.09,8.737-20.08,19.514-20.08s19.514,8.99,19.514,20.08S67.693,97.057,56.916,97.057,37.4,88.067,37.4,76.976Zm36.589-56.9C73.991,8.99,82.727,0,93.5,0s19.514,8.99,19.514,20.081-8.737,20.08-19.514,20.08S73.991,31.171,73.991,20.081ZM0,20.081C0,8.99,8.737,0,19.514,0S39.028,8.99,39.028,20.081s-8.737,20.08-19.514,20.08S0,31.171,0,20.081Z" opacity="0.1"/>
                        </svg>
                      </div>
                      <div class="row align-items-center d-flex justify-content-center">
                        <div class="col-12 col-md-7 col-lg-5 section-chat-content">
                            <h3><?php echo get_sub_field("section_headerline"); ?></h3>
                            <?php echo get_sub_field("section_content"); ?>
                            <div class="section-chat-btn align-items-center d-flex">
                              <a class="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                            </div>
                        </div>
                        <div class="col-12 col-md-5 col-lg-7 ml-auto section-chat-image">
                            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-fluid img-responsive img-outof-container"  />
                        </div>
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
                          <a class="button d-inline-block cta-btn" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
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
                $btn_alignment = (get_sub_field('section_button_alignment') == 'right') ? 'col-12 col-md-6' : 'col-12';
              endif
              ?>
                <section class="section-list <?php echo get_sub_field('custom_class_names'); ?> <?php echo $hasBorder; ?> <?php echo $$hasBgColor; ?> container">
                  <div class="row section-list-header">
                    <<?php echo get_sub_field("header_htag"); ?> class="<?php echo $btn_alignment; ?> align-items-center d-flex">
                      <?php echo get_sub_field("section_headerline"); ?>
                    </<?php echo get_sub_field("header_htag"); ?>>
                    <div class="<?php echo $btn_alignment; ?> section-header-btn align-items-center d-flex justify-content-center ">
                      <a class="button cta-btn" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
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
        }
    }
}
