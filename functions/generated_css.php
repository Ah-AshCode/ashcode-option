<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 *
 * Get icons from admin ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */

 //
// set up cache folder
// -------------------
$upload_dir = wp_upload_dir();
$cache_name = 'ash-css';
$cache_dir  = trailingslashit( $upload_dir['basedir'] ) . $cache_name;
$cache_url  = trailingslashit( $upload_dir['baseurl'] ) . $cache_name;

defined( 'ASH_CACHE_DIR' ) or define( 'ASH_CACHE_DIR', $cache_dir );
defined( 'ASH_CACHE_URL' ) or define( 'ASH_CACHE_URL', $cache_url );

//
// Enqueue custom styles
// ---------------------
function ash_wp_enqueue_styles() {

    // Check and create cachedir
    if( ! is_dir( ASH_CACHE_DIR ) ) {

        if( ! function_exists( 'WP_Filesystem' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

      WP_Filesystem();

      global $wp_filesystem;

        $wp_filesystem->mkdir( ASH_CACHE_DIR );

    }

    // 1. checking is cache folder writable
    // 2. if user in customizer passed custom.css for avoid conflicts
  //   if( is_writable( ash_CACHE_DIR ) && ! isset( $_POST['wp_customize'] ) ) {

  //   $has_cached = get_option( 'prefix-has-cached' );

  //   if( ! $has_cached ) {
  //     ash_cache_css_file();
  //   }

  //   // check for multisite
  //   global $blog_id;
  //   $is_multisite = ( is_multisite() ) ? '-'. $blog_id : '';

  //   wp_enqueue_style( 'prefix-cs-custom', ash_CACHE_URL .'/custom-style'. $is_multisite .'.css', array(), null );

  // } else {

    // echo generated css directly if cache folder is not writable and in customizer
    $css = ash_generate_css();
    $css_inline = compress($css);


    add_action( 'wp_head', function(){  echo '<!-- ash generated css --><style type="text/css">'. ash_generate_css() .'</style><!-- /ash generated css -->'; }, 99 );

  // }

}
add_action( 'wp_enqueue_scripts', 'ash_wp_enqueue_styles' );

//
// Generate cache css file
// -----------------------
function ash_cache_css_file() {

  if( is_multisite() ) {
    global $blog_id;
    $css_file = ASH_CACHE_DIR . '/custom-style-'. $blog_id .'.css';
  } else {
    $css_file = ASH_CACHE_DIR . '/custom-style.css';
  }

  $css  = "/**\n";
  $css .= " * Do not touch this file! This file created by PHP\n";
  $css .= " * Last modifiyed time: ". date( 'M d Y, h:s:i' ) ."\n";
  $css .= " */\n\n\n";
  $css .= ash_generate_css();

    if( ! function_exists( 'WP_Filesystem' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

  WP_Filesystem();

  global $wp_filesystem;

  if ( ! $wp_filesystem->put_contents( $css_file, $css, FS_CHMOD_FILE ) ) {
    update_option( 'prefix-has-cached', false );
  } else {
    update_option( 'prefix-has-cached', true );
  }

}

/*-----------------------------------------------------------------------------------*/
# Typography Elements Array
/*-----------------------------------------------------------------------------------*/
$ash_typography = array(
    "body"                                              =>      "unique_typography_7",
    "h1.entry-title, h2.entry-title, h3.entry-title, h4.entry-title, h5.entry-title, h6.entry-title"                            =>      "unique_typography_1",
    ".logo span"                                        =>      "typography_tagline",
    ".top-nav, .top-nav ul li a "                       =>      "typography_top_menu",
    "#main-nav, #main-nav ul li a"                      =>      "typography_main_nav",
    ".breaking-news span.breaking-news-title"           =>      "typography_breaking_news",
    ".page-title"                                       =>      "typography_page_title",
    ".post-title"                                       =>      "typography_post_title",
    "h2.post-box-title, h2.post-box-title a"            =>      "typography_post_title_boxes",
    "h3.post-box-title, h3.post-box-title a"            =>      "typography_post_title2_boxes",
    "p.post-meta, p.post-meta a"                        =>      "typography_post_meta",
    "body.single .entry, body.page .entry"              =>      "typography_post_entry",
    "blockquote p"                                      =>      "typography_blockquotes",
    ".widget-top h4, .widget-top h4 a"                  =>      "typography_widgets_title",
    ".footer-widget-top h4, .footer-widget-top h4 a"    =>      "typography_footer_widgets_title",
    "#featured-posts .featured-title h2 a"              =>      "typography_grid_slider_title",
    ".ei-title h2, .slider-caption h2 a, .content .slider-caption h2 a, .slider-caption h2, .content .slider-caption h2, .content .ei-title h2"             =>      "typography_slider_title",
    ".cat-box-title h2, .cat-box-title h2 a, .block-head h3, #respond h3, #comments-title, h2.review-box-header, .woocommerce-tabs .entry-content h2, .woocommerce .related.products h2, .entry .woocommerce h2, .woocommerce-billing-fields h3, .woocommerce-shipping-fields h3, #order_review_heading, #bbpress-forums fieldset.bbp-form legend, #buddypress .item-body h4, #buddypress #item-body h4"            =>      "typography_boxes_title"
);


//
// Generate cache css
// -----------------------
function ash_generate_css() {

  

  // You can include a php file here. for eg:
  // ob_start();
  // include_once 'large-styles.php';
  // $css .= ob_get_clean();

  // now i am writing a simple thing
  // $option = ash_get_option( 'unique_typography_7' );
  // $css .= 'body{';
  // $css .= 'font-family:'. $option['family'] .';';
  // $css .= 'font-weight:'. $option['weight'] .';';
  // $css .= '}';

 $css  = '';

global $ash_typography;

foreach( $ash_typography as $selector => $value){

  $option = ash_get_option( $value );

  if( !empty( $option['family'] ) || !empty( $option['color'] ) || !empty( $option['size'] ) || !empty( $option['weight'] ) || !empty ( $option['transform'] )|| !empty ( $option['lineHeight'] )|| !empty( $option['style'] ) ) {

$css .= $selector."{\n"; 

if( !empty( $option['family'] ) )
    $css .="  font-family: ". ash_get_font( $option['family']  ).";\n";

 if( !empty( $option['color'] ) )
    $css .= "  color :". $option['color'].";\n";
 if( !empty( $option['size'] ) )
    $css .= "  font-size : ".$option['size'].";\n";
 if( !empty( $option['weight'] ) )
    $css .= "  font-weight: ".$option['weight'].";\n";
 if( !empty( $option['style'] ) )
    $css .= "  font-style: ". $option['style'].";\n";
 if( !empty( $option['lineHeight'] ) )
    $css .= "  line-height: ". $option['lineHeight'].";\n";
 if( !empty( $option['transform'] ) )
    $css .= "  text-transform: ". $option['transform'].";\n";
    $css .= "}\n"; 

       }
}
  return compress( $css );

}

//
// Reseting cache for regenerate
// -----------------------
function ash_reset_cache() {
    update_option( 'prefix-has-cached', false );
}
// update your classes/framework.class.php because this filter i added now.
add_action( 'ash_validate_save_after', 'ash_reset_cache' );
// add_action( 'customize_save_after', 'ash_reset_cache' ); // for customizer
// add_action( 'save_post', 'ash_reset_cache' ); // for metabox pages/posts

/*-----------------------------------------------------------------------------------*/
# Enqueue Fonts From Google
/*-----------------------------------------------------------------------------------*/
function ash_enqueue_font ( $got_font) {
    if ($got_font) {

        $font_pieces = explode(":", $got_font);

        $font_name = $font_pieces[0];
        

            $font_name = str_replace (" ","+", $font_pieces[0] );
            
            $protocol = is_ssl() ? 'https' : 'http';
            wp_enqueue_style( $font_name , $protocol.'://fonts.googleapis.com/css?family='.$font_name . '' );
        
    }
}

/*-----------------------------------------------------------------------------------*/
# Get Custom Typography
/*-----------------------------------------------------------------------------------*/
add_action('wp_enqueue_scripts', 'ash_typography');
function ash_typography(){
    global $ash_typography;

    foreach( $ash_typography as $selector => $value){
        $option = ash_get_option( $value );
        if( !empty($option['font']))
            ash_enqueue_font( $option['family'] );
    }

    ash_enqueue_font( 'Droid Sans:regular|700' );

}

/*-----------------------------------------------------------------------------------*/
# Get Font Name
/*-----------------------------------------------------------------------------------*/
function ash_get_font ( $got_font ) {
    if ($got_font) {
        $font_pieces = explode(":", $got_font);
        $font_name = $font_pieces[0];
        $font_name = str_replace('&quot;' , '"' , $font_pieces[0] );
        if (strpos($font_name, ',') !== false)
            return $font_name;
        else
            return "'".$font_name."'";
    }
}


    function compress( $code )
    {
    $code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $code);
    $code = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $code);
    $code = str_replace('{ ', '{', $code);
    $code = str_replace(' }', '}', $code);
    $code = str_replace('; ', ';', $code);

    return $code;
    }