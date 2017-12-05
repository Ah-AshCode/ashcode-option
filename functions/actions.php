<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Get icons from admin ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! function_exists( 'ash_get_icons' ) ) {
  function ash_get_icons() {

    do_action( 'ash_add_icons_before' );
    echo 'hello';

    $jsons = apply_filters( 'ash_add_icons_json', glob( ASH_DIR . '/fields/icon/*.json' ) );

    if( ! empty( $jsons ) ) {

      foreach ( $jsons as $path ) {

        $object = ash_get_icon_fonts( 'fields/icon/'. basename( $path ) );

        if( is_object( $object ) ) {

          echo ( count( $jsons ) >= 2 ) ? '<h4 class="ash-icon-title">'. $object->name .'</h4>' : '';

          foreach ( $object->icons as $icon ) {
            echo '<a class="ash-icon-tooltip" data-ash-icon="'. $icon .'" data-title="'. $icon .'"><span class="ash-icon ash-selector"><i class="'. $icon .'"></i></span></a>';
          }

        } else {
          echo '<h4 class="ash-icon-title">'. esc_html__( 'Error! Can not load json file.', 'ash-framework' ) .'</h4>';
        }

      }

    }

    do_action( 'ash_add_icons' );
    do_action( 'ash_add_icons_after' );

    die();
  }
  add_action( 'wp_ajax_ash-get-icons', 'ash_get_icons' );
}

/**
 *
 * Export options
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! function_exists( 'ash_export_options' ) ) {
  function ash_export_options() {

    header('Content-Type: plain/text');
    header('Content-disposition: attachment; filename=backup-options-'. gmdate( 'd-m-Y' ) .'.txt');
    header('Content-Transfer-Encoding: binary');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo ash_encode_string( get_option( ash_OPTION ) );

    die();
  }
  add_action( 'wp_ajax_ash-export-options', 'ash_export_options' );
}

/**
 *
 * Set icons for wp dialog
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! function_exists( 'ash_set_icons' ) ) {
  function ash_set_icons() {

    echo '<div id="ash-icon-dialog" class="ash-dialog" title="'. esc_html__( 'Add Icon', 'ash-framework' ) .'">';
    echo '<div class="ash-dialog-header ash-text-center"><input type="text" placeholder="'. esc_html__( 'Search a Icon...', 'ash-framework' ) .'" class="ash-icon-search" /></div>';
    echo '<div class="ash-dialog-load"><div class="ash-icon-loading">'. esc_html__( 'Loading...', 'ash-framework' ) .'</div></div>';
    echo '</div>';

  }
  add_action( 'admin_footer', 'ash_set_icons' );
  add_action( 'customize_controls_print_footer_scripts', 'ash_set_icons' );
}
