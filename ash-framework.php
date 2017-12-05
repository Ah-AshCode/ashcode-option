<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 *
 * ------------------------------------------------------------------------------------------------
 *
 * ASHCODE Framework
 * A Lightweight and easy-to-use WordPress Options Framework
 *
 * Plugin Name: ASHCODE Framework
 * Plugin URI: http://ASHCODEFramework.com/
 * Author: ASHCODE
 * Author URI: http://ASHCODEFramework.com/
 * Version: 1.0.0
 * Description: A Lightweight and easy-to-use WordPress Options Framework
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: cs-framework
 *
 * ------------------------------------------------------------------------------------------------
 *
 * Copyright 2015 Codestar <info@codestarlive.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * ------------------------------------------------------------------------------------------------
 *
 */

defined( 'ASH_OPTION' )     or  define( 'ASH_OPTION',     '_ash_opt' );
// ----------------------------------------------------------------------
require_once plugin_dir_path( __FILE__ ) .'/ash-framework-path.php';
require_once plugin_dir_path( __FILE__ ) .'/functions/generated_css.php';
require_once plugin_dir_path( __FILE__ ) .'/functions/ash_core.php';
new ASHCODE_CORE();
// ----------------------------------------------------------------------

if( ! function_exists( 'ash_framework_init' ) && ! class_exists( 'CSFramework' ) ) {
  function ash_framework_init() {

    // active modules
    defined( 'ASH_ACTIVE_FRAMEWORK' )   or  define( 'ASH_ACTIVE_FRAMEWORK',   true  );
    defined( 'ASH_ACTIVE_METABOX'   )   or  define( 'ASH_ACTIVE_METABOX',     true  );
    defined( 'ASH_ACTIVE_TAXONOMY'   )  or  define( 'ASH_ACTIVE_TAXONOMY',    true  );
    defined( 'ASH_ACTIVE_SHORTCODE' )   or  define( 'ASH_ACTIVE_SHORTCODE',   true  );
    defined( 'ASH_ACTIVE_CUSTOMIZE' )   or  define( 'ASH_ACTIVE_CUSTOMIZE',   true  );
    defined( 'ASH_ACTIVE_LIGHT_THEME' ) or  define( 'ASH_ACTIVE_LIGHT_THEME', false );

    // helpers
    // ash_locate_template( 'functions/deprecated.php'     );
    // ash_locate_template( 'functions/fallback.php'       );
    ash_locate_template( 'functions/helpers.php'        );
    ash_locate_template( 'functions/actions.php'        );
    ash_locate_template( 'functions/enqueue.php'        );
    ash_locate_template( 'functions/sanitize.php'       );
    // ash_locate_template( 'functions/validate.php'       );

    // classes
    ash_locate_template( 'classes/abstract.class.php'   );
    ash_locate_template( 'classes/options.class.php'    );
    ash_locate_template( 'classes/framework.class.php'  );
    ash_locate_template( 'classes/metabox.class.php'    );
    ash_locate_template( 'classes/taxonomy.class.php'   );
    ash_locate_template( 'classes/shortcode.class.php'  );
    // ash_locate_template( 'classes/customize.class.php'  );

    // configs
    ash_locate_template( 'config/framework.config.php'  );
    ash_locate_template( 'config/metabox.config.php'    );
    ash_locate_template( 'config/taxonomy.config.php'   );
    ash_locate_template( 'config/shortcode.config.php'  );
    ash_locate_template( 'config/demo.config.php'  );

  }
  add_action( 'init', 'ash_framework_init', 10 );
}