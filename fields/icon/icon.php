<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Field: Icon
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
class ASHFramework_Option_icon extends ASHFramework_Options {

  public function __construct( $field, $value = '', $unique = '' ) {
    parent::__construct( $field, $value, $unique );
  }

  public function output() {

    echo $this->element_before();

    $value  = $this->element_value();
    $hidden = ( empty( $value ) ) ? ' hidden' : '';

    echo '<div class="ash-icon-select">';
    echo '<span class="ash-icon-preview'. $hidden .'"><i class="'. $value .'"></i></span>';
    echo '<a href="#" class="button button-primary ash-icon-add">'. esc_html__( 'Add Icon', 'ash-framework' ) .'</a>';
    echo '<a href="#" class="button ash-warning-primary ash-icon-remove'. $hidden .'">'. esc_html__( 'Remove Icon', 'ash-framework' ) .'</a>';
    echo '<input type="text" name="'. $this->element_name() .'" value="'. $value .'"'. $this->element_class( 'ash-icon-value' ) . $this->element_attributes() .' />';
    echo '</div>';

    echo $this->element_after();

  }

}
