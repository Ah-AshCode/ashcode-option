<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Framework admin enqueue style and scripts
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! function_exists( 'ash_admin_enqueue_style' ) ) {
  function ash_admin_enqueue_style() {

    // admin utilities
    wp_enqueue_media();

    // wp core styles
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style( 'wp-jquery-ui-dialog' );

    // framework core styles
    wp_enqueue_style( 'ash-framework', ASH_URI .'/assets/css/ash-framework.css', array(), '1.0.0', 'all' );
    wp_enqueue_style( 'font-awesome', ASH_URI .'/assets/css/font-awesome.css', array(), '4.7.0', 'all' );

    if ( ASH_ACTIVE_LIGHT_THEME ) {
      wp_enqueue_style( 'ash-framework-theme', ASH_URI .'/assets/css/ash-framework-light.css', array(), "1.0.0", 'all' );
    }

    if ( is_rtl() ) {
      wp_enqueue_style( 'ash-framework-rtl', ASH_URI .'/assets/css/ash-framework-rtl.css', array(), '1.0.0', 'all' );
    }


  }
  add_action( 'admin_enqueue_scripts', 'ash_admin_enqueue_style' );
}


if( ! function_exists( 'ash_scripts' ) ) {
  function ash_scripts() {

    // wp core scripts
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script( 'jquery-ui-dialog' );
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'jquery-ui-accordion' );

    // framework core scripts
    wp_enqueue_script( 'ash-plugins',    ASH_URI .'/assets/js/ash-plugins.js',    array(), false, '1.0.0' );
    wp_enqueue_script( 'ash-framework',  ASH_URI .'/assets/js/ash-framework.js',  array( 'ash-plugins' ), false, '1.0.0' );

  }
  add_action( 'admin_enqueue_scripts', 'ash_scripts' );
}

     add_action( 'wp_head', 'theme_admin_options_generate_css' );
     add_action('wp_footer', 'theme_admin_options_auto_enqueue_gfonts');
   function theme_admin_options_generate_css()
  {
    # code...
  }

     function theme_admin_options_auto_enqueue_gfonts()
  {

          ?>
      <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.5.18/webfont.js"></script>
      <script>
      </script>

      <?php
  }