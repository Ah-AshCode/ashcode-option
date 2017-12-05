<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Email validate
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! function_exists( 'ash_validate_email' ) ) {
  function ash_validate_email( $value, $field ) {

    if ( ! sanitize_email( $value ) ) {
      return esc_html__( 'Please write a valid email address!', 'cs-framework' );
    }

  }
  add_filter( 'ash_validate_email', 'ash_validate_email', 10, 2 );
}

/**
 *
 * Numeric validate
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! function_exists( 'ash_validate_numeric' ) ) {
  function ash_validate_numeric( $value, $field ) {

    if ( ! is_numeric( $value ) ) {
      return esc_html__( 'Please write a numeric data!', 'cs-framework' );
    }

  }
  add_filter( 'ash_validate_numeric', 'ash_validate_numeric', 10, 2 );
}

/**
 *
 * Required validate
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! function_exists( 'ash_validate_required' ) ) {
  function ash_validate_required( $value ) {
    if ( empty( $value ) ) {
      return esc_html__( 'Fatal Error! This field is required!', 'cs-framework' );
    }
  }
  add_filter( 'ash_validate_required', 'ash_validate_required' );
}
