<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Field: Color gardient
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
class ASHFramework_Option_gardient extends ASHFramework_Options {

  public function __construct( $field, $value = '', $unique = '' ) {
    parent::__construct( $field, $value, $unique );
  }

  public function output() {

    echo $this->element_before();

    $defaults_value = array(
      'color_1'  => '#000000',
      'color_2'  => '#000000',
    );

    $this->value  = wp_parse_args( $this->element_value(), $defaults_value );


    // background attributes
    echo '<fieldset>';
    echo ash_add_element( array(
        'pseudo'          => true,
        'id'              => $this->field['id'].'_color_1',
        'type'            => 'color_picker',
        'name'            => $this->element_name('[color_1]'),
        'attributes'      => array(
          'data-atts'     => 'bgcolor',
        ),
        'value'           => $this->value['color_1'],
        'default'         => ( isset( $this->field['default']['color_1'] ) ) ? $this->field['default']['color_1'] : '',
        'rgba'            => ( isset( $this->field['rgba'] ) && $this->field['rgba'] === false ) ? false : '',
    ) );
        echo ash_add_element( array(
        'pseudo'          => true,
        'id'              => $this->field['id'].'_color_2',
        'type'            => 'color_picker',
        'name'            => $this->element_name('[color_2]'),
        'attributes'      => array(
          'data-atts'     => 'bgcolor',
        ),
        'value'           => $this->value['color_2'],
        'default'         => ( isset( $this->field['default']['color_2'] ) ) ? $this->field['default']['color_1'] : '',
        'rgba'            => ( isset( $this->field['rgba'] ) && $this->field['rgba'] === false ) ? false : '',
    ) );
    echo '</fieldset>';
    echo $this->element_after();

  }



}
