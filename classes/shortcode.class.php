<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Shortcodes Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
class ASHFramework_Shortcode_Manager extends ASHFramework_Abstract{

  /**
   *
   * shortcode options
   * @access public
   * @var array
   *
   */
  public $options = array();

  /**
   *
   * shortcodes options
   * @access public
   * @var array
   *
   */
  public $shortcodes = array();

  /**
   *
   * exclude_post_types
   * @access public
   * @var array
   *
   */
  public $exclude_post_types = array();

  /**
   *
   * instance
   * @access private
   * @var class
   *
   */
  private static $instance = null;

  // run shortcode construct
  public function __construct( $options ) {

    $this->options = apply_filters( 'ash_shortcode_options', $options );
    $this->exclude_post_types = apply_filters( 'ash_shortcode_exclude', $this->exclude_post_types );

    if( ! empty( $this->options ) ) {

      $this->shortcodes = $this->get_shortcodes();
      $this->addAction( 'media_buttons', 'media_shortcode_button', 99 );
      $this->addAction( 'admin_footer', 'shortcode_dialog', 99 );
      $this->addAction( 'customize_controls_print_footer_scripts', 'shortcode_dialog', 99 );
      $this->addAction( 'wp_ajax_ash-get-shortcode', 'shortcode_generator', 99 );
  

    }

  }

  // instance
  public static function instance( $options = array() ){
    if ( is_null( self::$instance ) && ASH_ACTIVE_SHORTCODE ) {
      self::$instance = new self( $options );
    }
    return self::$instance;
  }

  // add shortcode button
  public function media_shortcode_button( $editor_id ) {

    global $post;

    $post_type = ( isset( $post->post_type ) ) ? $post->post_type : '';

    if( ! in_array( $post_type, $this->exclude_post_types ) ) {
      echo '<a href="#" class="button button-primary ash-shortcode" data-editor-id="'. $editor_id .'">'. esc_html__( 'Add Shortcode', 'ash-framework' ) .'</a>';
    }

  }

  // shortcode dialog
  public function shortcode_dialog() {
  ?>
    <div id="ash-shortcode-dialog" class="ash-dialog" title="<?php esc_html_e( 'Add Shortcode', 'ash-framework' ); ?>">
      <div class="ash-dialog-header">
        <select class="<?php echo ( is_rtl() ) ? 'chosen-rtl ' : ''; ?>ash-dialog-select" data-placeholder="<?php esc_html_e( 'Select a shortcode', 'ash-framework' ); ?>">
          <option value=""></option>
          <?php
            foreach ( $this->options as $group ) {
              echo '<optgroup label="'. $group['title'] .'">';
              foreach ( $group['shortcodes'] as $shortcode ) {
                $view = ( isset( $shortcode['view'] ) ) ? $shortcode['view'] : 'normal';
                echo '<option value="'. $shortcode['name'] .'" data-view="'. $view .'">'. $shortcode['title'] .'</option>';
              }
              echo '</optgroup>';
            }
          ?>
        </select>
      </div>
      <div class="ash-dialog-load"></div>
      <div class="ash-insert-button hidden">
        <a href="#" class="button button-primary ash-dialog-insert"><?php esc_html_e( 'Insert Shortcode', 'ash-framework' ); ?></a>
      </div>
    </div>
  <?php
  }

  // shortcode generator function for dialog
  public function shortcode_generator() {

    $request = ash_get_var( 'shortcode' );

    if( empty( $request ) ) { die(); }

    $shortcode = $this->shortcodes[$request];

    if( isset( $shortcode['fields'] ) ) {

      foreach ( $shortcode['fields'] as $key => $field ) {

        if( isset( $field['id'] ) ) {
          $field['attributes'] = ( isset( $field['attributes'] ) ) ? wp_parse_args( array( 'data-atts' => $field['id'] ), $field['attributes'] ) : array( 'data-atts' => $field['id'] );
        }

        $field_default = ( isset( $field['default'] ) ) ? $field['default'] : '';

        if( in_array( $field['type'], array('image_select', 'checkbox') ) && isset( $field['options'] ) ) {
          $field['attributes']['data-check'] = true;
        }

        echo ash_add_element( $field, $field_default, 'shortcode' );

      }

    }

    if( isset( $shortcode['clone_fields'] ) ) {

      $clone_id = isset( $shortcode['clone_id'] ) ? $shortcode['clone_id'] : $shortcode['name'];

      echo '<div class="ash-shortcode-clone" data-clone-id="'. $clone_id .'">';
      echo '<a href="#" class="ash-remove-clone"><i class="fa fa-trash"></i></a>';

      foreach ( $shortcode['clone_fields'] as $key => $field ) {

        $field['sub']        = true;
        $field['attributes'] = ( isset( $field['attributes'] ) ) ? wp_parse_args( array( 'data-clone-atts' => $field['id'] ), $field['attributes'] ) : array( 'data-clone-atts' => $field['id'] );
        $field_default       = ( isset( $field['default'] ) ) ? $field['default'] : '';

        if( in_array( $field['type'], array('image_select', 'checkbox') ) && isset( $field['options'] ) ) {
          $field['attributes']['data-check'] = true;
        }

        echo ash_add_element( $field, $field_default, 'shortcode' );

      }

      echo '</div>';

      echo '<div class="ash-clone-button"><a id="shortcode-clone-button" class="button" href="#"><i class="fa fa-plus-circle"></i> '. $shortcode['clone_title'] .'</a></div>';

    }

    die();
  }

  // getting shortcodes from config array
  public function get_shortcodes() {

    $shortcodes = array();

    foreach ( $this->options as $group_value ) {
      foreach ( $group_value['shortcodes'] as $shortcode ) {
        $shortcodes[$shortcode['name']] = $shortcode;
      }
    }

    return $shortcodes;
  }

  // add shortcode

}

function ash_button( $params, $content=null ) {
    extract(shortcode_atts(array(
        'size' => 'default',
        'type' => 'default',
        'value' => 'button',
        'href' => "#"
    ), $params ) );

    $content = preg_replace( '/<br class="nc".\/>/', '', $content );
    $result = '<a class="btn btn-' . $size . ' btn-outline-' . $type . '" href="' . $href . '">' . $value . '</a>';
    return force_balance_tags( $result );
}
add_shortcode( 'ash_button', 'ash_button' );