<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Field: Typography
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
class ASHFramework_Option_typography extends ASHFramework_Options {

  public function __construct( $field, $value = '', $unique = '' ) {
    parent::__construct( $field, $value, $unique );

  }

  public function output() {



    $defaults_value = array(
      'family'  => 'Arial',
      'font'    => 'websafe',
      'size'    => '',
      'lineHeight'    => '',
      'style'    => 'normal',
      'transform'    => 'none',
      'weight'      => ''
    );

    // line height array from 5px to 90px

    $defaults_line_height[0] = array('text' => 'Default', 'val' =>'');

    for ($i = 5; $i <= 90; $i++) {
        $defaults_line_height[$i] = array('text' => $i . 'px', 'val' => $i . 'px');
     }

    $default_line_heights = apply_filters( 'ash_websafe_fonts_line_height', array($defaults_line_height
    ));

     // line height array from 5px to 90px

    $defaults_size[0] = array('text' => 'Default', 'val' =>'');

    for ($i = 5; $i <= 90; $i++) {
        $defaults_size[$i] = array('text' => $i . 'px', 'val' => $i . 'px');
     }

    $default_defaults_sizes = apply_filters( 'ash_websafe_fonts_size', array($defaults_size
    ));

    $default_style = apply_filters( 'ash_websafe_fonts_style', array(
      'italic',
      'oblique',
      'normal'
    ));

    $default_transform = apply_filters( 'ash_websafe_fonts_transform', array(
      'uppercase',
      'capitalize',
      'lowercase',
      'none'
    ));

    $websafe_fonts = apply_filters( 'ash_websafe_fonts', array(
      'Arial',
      'Arial Black',
      'Comic Sans MS',
      'Impact',
      'Lucida Sans Unicode',
      'Tahoma',
      'Trebuchet MS',
      'Verdana',
      'Courier New',
      'Lucida Console',
      'Georgia, serif',
      'Palatino Linotype',
      'Times New Roman'
    ));

    // get the font $ash_fonts_Weight
    $ash_fonts_weight[] = array('text' => 'Default font weight', 'val' => '');
    $ash_fonts_weight[] = array('text' => '100 - Thin (Hairline)', 'val' => '100');
    $ash_fonts_weight[] = array('text' => '200 - Extra light (Ultra light)', 'val' => '200');
    $ash_fonts_weight[] = array('text' => '300 - Light', 'val' => '300');
    $ash_fonts_weight[] = array('text' => '400 - Normal', 'val' => 'normal');
    $ash_fonts_weight[] = array('text' => '500 - Medium', 'val' => '500');
    $ash_fonts_weight[] = array('text' => '600 - Semi Bold (Demi bold)', 'val' => '600');
    $ash_fonts_weight[] = array('text' => '700 - Bold', 'val' => 'bold');
    $ash_fonts_weight[] = array('text' => '800 - Extra Bold (Ultra bold)', 'val' => '800');
    $ash_fonts_weight[] = array('text' => '900 - Black (Heavy)', 'val' => '900');
    $weight_fonts       = apply_filters( 'ash_websafe_fonts_weight', array($ash_fonts_weight));

    //  get font transform
    $ash_text_transform[] = array('text' => 'Default text transform', 'val' => '');
    $ash_text_transform[] = array('text' => 'Uppercase', 'val' => 'uppercase');
    $ash_text_transform[] = array('text' => 'Capitalize', 'val' => 'capitalize');
    $ash_text_transform[] = array('text' => 'Lowercase', 'val' => 'lowercase');
    $ash_text_transform[] = array('text' => 'None (normal text)', 'val' => 'none');
    $transform_fonts      = apply_filters( 'ash_websafe_fonts_transform', array($ash_text_transform));

    $value             = wp_parse_args( $this->element_value(), $defaults_value );
    $family_value      = $value['family'];
    $line_height_value = $value['lineHeight'];
    $size_value        = $value['size'];
    $font_style        = $value['style'];
    $font_weight       = $value['weight'];
    $text_transform    = $value['transform'];

    $is_size   = ( isset( $this->field['size'] ) && $this->field['size'] === false ) ? false : true;
    $is_line_height    = ( isset( $this->field['lineHeight'] ) && $this->field['lineHeight'] === false ) ? false : true;
    $is_style   = ( isset( $this->field['style'] ) && $this->field['style'] === false ) ? false : true;
    $is_weight   = ( isset( $this->field['weight'] ) && $this->field['weight'] === false ) ? false : true;
    $is_transform   = ( isset( $this->field['transform'] ) && $this->field['transform'] === false ) ? false : true;
    $is_chosen     = ( isset( $this->field['chosen'] ) && $this->field['chosen'] === false ) ? '' : 'chosen ';
    $google_json   = ash_get_google_fonts();
    $chosen_rtl    = ( is_rtl() && ! empty( $is_chosen ) ) ? 'chosen-rtl ' : '';

    if( is_object( $google_json ) ) {

      $googlefonts = array();

      foreach ( $google_json->items as $key => $font ) {
        $googlefonts[$font->family] = $font->variants;
      }

      $is_google = ( array_key_exists( $family_value, $googlefonts ) ) ? true : false;

      echo '<label class="ash-typography-family"><span>Font Family</span>';
      echo '<select name="'. $this->element_name( '[family]' ) .'" class="'. $is_chosen . $chosen_rtl .'ash-typo-family" data-atts="family"'. $this->element_attributes() .'>';

      do_action( 'ash_typography_family', $family_value, $this );

      echo '<optgroup label="'. esc_html__( 'Web Safe Fonts', 'ash-framework' ) .'">';
      foreach ( $websafe_fonts as $websafe_value ) {
        echo '<option value="'. $websafe_value .'" data-variants="'. implode( '|', $default_variants ) .'" data-type="websafe"'. selected( $websafe_value, $family_value, true ) .'>'. $websafe_value .'</option>';
      }
      echo '</optgroup>';

      echo '<optgroup label="'. esc_html__( 'Google Fonts', 'ash-framework' ) .'">';
      foreach ( $googlefonts as $google_key => $google_value ) {
        echo '<option value="'. $google_key .'" data-variants="'. implode( '|', $google_value ) .'" data-type="google"'. selected( $google_key, $family_value, true ) .'>'. $google_key .'</option>';
      }
      echo '</optgroup>';

      echo '</select>';
      echo '</label>';


    if( ! empty( $is_line_height ) ) {

    $defaults_line_height[0] = array('text' => 'Default', 'val' =>'');

    for ($i = 5; $i <= 90; $i++) {
        $defaults_line_height[$i] = array('text' => $i . 'px', 'val' => $i . 'px');
     }

        echo '<label class="ash-typography-lineHeight"><span>Line Height</span>';
        echo '<select name="'. $this->element_name( '[lineHeight]' ) .'" class="'. $is_chosen . $chosen_rtl .'ash-typo-lineHeight" data-atts="lineHeight">';
        foreach ( $defaults_line_height as $lineHeight  ) {
          echo '<option value="'. $lineHeight['text'] .'"'. $this->checked( $line_height_value, $lineHeight['val'], 'selected' ) .'>'. $lineHeight['text'].'</option>';
        }
        echo '</select>';
        echo '</label>';

      }

    if( ! empty( $is_line_height ) ) {

    $defaults_size[0] = array('text' => 'Default', 'val' =>'');

    for ($i = 5; $i <= 90; $i++) {
        $defaults_size[$i] = array('text' => $i . 'px', 'val' => $i . 'px');
     }

        echo '<label class="ash-typography-size"><span>Size</span>';
        echo '<select name="'. $this->element_name( '[size]' ) .'" class="'. $is_chosen . $chosen_rtl .'ash-typo-size" data-atts="size">';
        foreach ( $defaults_size as $size  ) {
          
          echo '<option value="'. $size['text'] .'"'. $this->checked( $size_value, $size['val'], 'selected' ) .'>'. $size['text'].'</option>';
        }
        echo '</select>';
        echo '</label>';

      }

      if( ! empty( $is_style ) ) {

        echo '<label class="ash-typography-style"><span>Style</span>';
        echo '<select name="'. $this->element_name( '[style]' ) .'" class="'. $is_chosen . $chosen_rtl .'ash-typo-style" data-atts="style">';
        foreach ( $default_style as $style  ) {
          
          echo '<option value="'. $style .'"'. $this->checked( $font_style, $style, 'selected' ) .'>'. $style.'</option>';
        }
        echo '</select>';
        echo '</label>';

      }
      if( ! empty( $is_weight ) ) {

        echo '<label class="ash-typography-weight"><span>Weight</span>';
        echo '<select name="'. $this->element_name( '[weight]' ) .'" class="'. $is_chosen . $chosen_rtl .'ash-typo-weight" data-atts="weight">';
        foreach ( $ash_fonts_weight as $weight_val ) {
          
          echo '<option value="'. $weight_val['val'] .'"'. $this->checked( $font_weight, $weight_val['val'], 'selected' ) .'>'. $weight_val['text'].'</option>';
        }
        echo '</select>';
        echo '</label>';

      }
      if( ! empty( $is_transform ) ) {

        echo '<label class="ash-typography-transform"><span>Transform</span>';
        echo '<select name="'. $this->element_name( '[transform]' ) .'" class="'. $is_chosen . $chosen_rtl .'ash-typo-transform" data-atts="transform">';
        foreach ( $ash_text_transform as $transform_val ) {
          
          echo '<option value="'. $transform_val['val'] .'"'. $this->checked( $text_transform, $transform_val['val'], 'selected' ) .'>'. $transform_val['text'].'</option>';
        }
        echo '</select>';
        echo '</label>';

      }
      echo '<input type="text" name="'. $this->element_name( '[font]' ) .'" class="ash-typo-font hidden" data-atts="font" value="'. $value['font'] .'" />';

    } else {

      echo esc_html__( 'Error! Can not load json file.', 'ash-framework' );

    }

    echo $this->element_after();

  }



}
