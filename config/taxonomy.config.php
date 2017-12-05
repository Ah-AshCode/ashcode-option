<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// TAXONOMY OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options     = array();

// -----------------------------------------
// Taxonomy Options                        -
// -----------------------------------------
$options[]   = array(
  'id'       => '_custom_taxonomy_options',
  'taxonomy' => 'category', // category, post_tag or your custom taxonomy name
  'icon'        => 'fa fa-star',
  'title'       => 'Overview',
  'fields'   => array(

    array(
      'id'    => 'section_1_text',
      'type'  => 'gardient',
      'title' => 'Text Field',
            'default'   => array(
          'color_1'  => '#000000',
          'color_2'  => '#000000',
          ),
    ),

    array(
      'id'    => 'section_1_textarea',
      'type'  => 'textarea',
      'title' => 'Textarea Field',
    ),

  ),
);

$options[]   = array(
  'id'       => '_custom_taxonomy_options',
  'taxonomy' => 'cpt-tag', // category, post_tag or your custom taxonomy name
  'fields'   => array(

    array(
      'id'    => 'section_1_text',
      'type'  => 'text',
      'title' => 'Text Field',
    ),

  ),
);

ASHFramework_Taxonomy::instance( $options );
