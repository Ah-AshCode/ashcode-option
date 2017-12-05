<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 *
 * ASHCODE_CORE Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
class ASHCODE_CORE {

	/**
	 * Holds the current theme version.
	 *
	 * @since 5.0.0
	 *
	 * @access private
	 * @var string
	 */
	private $theme_version = '1.0.0';

	/**
	 * Holds the WP_Theme object of zage.
	 *
	 * @since 5.0.0
	 *
	 * @access private
	 * @var WP_Theme object
	 */
	private $theme_object;


	/**
	 * Holds the URL to the zage live demo site.
	 *
	 * @since 5.0.0
	 *
	 * @access private
	 * @var string
	 */
	private $theme_url = 'https://zage.theme-zage.com/';


	/**
	 * Holds the URL to Themezage company site.
	 *
	 * @since 5.0.0
	 *
	 * @access private
	 * @var string
	 */
	private $theme_zage_url = 'https://theme-zage.com/';

		//@todo refactor all code to use TEMPLATEPATH instead
    static $get_template_directory = '';  // here we store the value from get_template_directory(); - it looks like the wp function does a lot of stuff each time is called

	//@todo refactor all code to use STYLESHEETPATH instead
    static $get_template_directory_uri = ''; // here we store the value from get_template_directory_uri(); - it looks like the wp function does a lot of stuff each time is called

     /**
     * stack_filename => stack_name
     * @var array
     */
    public static $demo_list = array ();

    /**
	 * zage_Product_registration
	 *
	 * @access public
	 * @var object zage_Product_registration.
	 */
	public $registration;


	/**
	 * The Envato token.
	 *
	 * @since 5.0.0
	 *
	 * @static
	 * @access private
	 * @var string
	 */
	private static $token;

	/**
	 * Construct the admin object.
	 *
	 * @since 3.9.0
	 */
	public function __construct() {



		add_action( 'wp_before_admin_bar_render', array( $this, 'add_wp_toolbar_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'edit_admin_menus' ) );
		add_action( 'admin_head', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'after_switch_theme', array( $this, 'activation_redirect' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );


		add_filter( 'tgmpa_notice_action_links', array( $this, 'edit_tgmpa_notice_action_links' ) );
		$prefix = ( defined( 'WP_NETWORK_ADMIN' ) && WP_NETWORK_ADMIN ) ? 'network_admin_' : '';
		add_filter( "tgmpa_{$prefix}plugin_action_links", array( $this, 'edit_tgmpa_action_links' ), 10, 4 );

		//Get demos data on theme activation.
		// add_action( 'after_switch_theme', array( 'zage_Importer_Data', 'get_data' ), 5 );



		// Load jQuery in the demos page.
		if ( isset( $_GET['page'] ) && 'zage-demos' === $_GET['page'] ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'add_jquery' ) );
		}
	}

	/**
	 * Adds jQuery.
	 *
	 * @since 5.0.0
	 * @access public
	 * @return void
	 */
	public function add_jquery() {
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Adds the news dashboard widget.
	 *
	 * @since 3.9.0
	 * @access public
	 * @return void
	 */
	public function add_dashboard_widget() {
		// Create the widget.
		wp_add_dashboard_widget( 'themezage_news', apply_filters( 'zage_dashboard_widget_title', esc_attr__( 'Themezage News', 'zage' ) ), array( $this, 'display_news_dashboard_widget' ) );

		// Make sure our widget is on top off all others.
		global $wp_meta_boxes;

		// Get the regular dashboard widgets array.
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

		// Backup and delete our new dashboard widget from the end of the array.
		$zage_widget_backup = array( 'themezage_news' => $normal_dashboard['themezage_news'] );
		unset( $normal_dashboard['themezage_news'] );

		// Merge the two arrays together so our widget is at the beginning.
		$sorted_dashboard = array_merge( $zage_widget_backup, $normal_dashboard );

		// Save the sorted array back into the original metaboxes.
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}

	/**
	 * Renders the news dashboard widget.
	 *
	 * @since 3.9.0
	 * @access public
	 * @return void
	 */
	public function display_news_dashboard_widget() {

		// Create two feeds, the first being just a leading article with data and summary, the second being a normal news feed.
		$feeds = array(
			'first' => array(
				'link'         => 'http://theme-zage.com/blog/',
				'url'          => 'http://theme-zage.com/feed/',
				'title'        => esc_attr__( 'Themezage News', 'zage' ),
				'items'        => 1,
				'show_summary' => 1,
				'show_author'  => 0,
				'show_date'    => 1,
			),
			'news' => array(
				'link'         => 'http://theme-zage.com/blog/',
				'url'          => 'http://theme-zage.com/feed/',
				'title'        => esc_attr__( 'Themezage News', 'zage' ),
				'items'        => 4,
				'show_summary' => 0,
				'show_author'  => 0,
				'show_date'    => 0,
			),
		);

		wp_dashboard_primary_output( 'themezage_news', $feeds );
	}

	/**
	 * Create the admin toolbar menu items.
	 *
	 * @since 3.8.0
	 * @access public
	 * @return void
	 */
	public function add_wp_toolbar_menu() {

		global $wp_admin_bar;
			$zage_parent_menu_title = '<span class="ab-icon"></span><span class="ab-label">zage</span>';
			$this->add_wp_toolbar_menu_item( $zage_parent_menu_title, false, admin_url( 'admin.php?page=zage' ), array( 'class' => 'zage-menu' ), 'zage' );
			$this->add_wp_toolbar_menu_item( esc_attr__( 'Product Registration', 'zage' ), 'zage', admin_url( 'admin.php?page=zage' ) );
			$this->add_wp_toolbar_menu_item( esc_attr__( 'Support', 'zage' ), 'zage', admin_url( 'admin.php?page=zage-support' ) );
			$this->add_wp_toolbar_menu_item( esc_attr__( 'Install Demos', 'zage' ), 'zage', admin_url( 'admin.php?page=zage-demos' ) );
			$this->add_wp_toolbar_menu_item( esc_attr__( 'Plugins', 'zage' ), 'zage', admin_url( 'admin.php?page=zage-plugins' ) );
			$this->add_wp_toolbar_menu_item( esc_attr__( 'System Status', 'zage' ), 'zage', admin_url( 'admin.php?page=zage-system-status' ) );
			$this->add_wp_toolbar_menu_item( esc_attr__( 'Theme Options', 'zage' ), 'zage', admin_url( 'admin.php?page=ash-framework' ) );

	}

	/**
	 * Add the top-level menu item to the adminbar.
	 *
	 * @since 3.8.0
	 * @access public
	 * @param  string       $title       The title.
	 * @param  string|false $parent      The parent node.
	 * @param  string       $href        Link URL.
	 * @param  array        $custom_meta An array of custom meta to apply.
	 * @param  string       $custom_id   A custom ID.
	 */
	public function add_wp_toolbar_menu_item( $title, $parent = false, $href = '', $custom_meta = array(), $custom_id = '' ) {

		global $wp_admin_bar;


			if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
				return;
			}

			// Set custom ID.
			if ( $custom_id ) {
				$id = $custom_id;
			} else { // Generate ID based on $title.
				$id = strtolower( str_replace( ' ', '-', $title ) );
			}

			// Links from the current host will open in the current window.
			$meta = strpos( $href, site_url() ) !== false ? array() : array( 'target' => '_blank' ); // External links open in new tab/window.
			$meta = array_merge( $meta, $custom_meta );

			$wp_admin_bar->add_node( array(
				'parent' => $parent,
				'id'     => $id,
				'title'  => $title,
				'href'   => $href,
				'meta'   => $meta,
			) );


	}

	/**
	 * Modify the menu.
	 *
	 * @since 3.8.0
	 * @access public
	 * @return void
	 */
	public function edit_admin_menus() {
		global $submenu;


			$submenu['zage'][0][0] = esc_attr__( 'Product Registration', 'zage' ); // Change zage to Product Registration.

	}

	/**
	 * Redirect to admin page on theme activation.
	 *
	 * @since 3.8.0
	 * @access public
	 * @return void
	 */
	public function activation_redirect() {

			header( 'Location:' . admin_url() . 'admin.php?page=zage' );

	}

	/**
	 * Actions to run on initial theme activation.
	 *
	 * @since 3.8.0
	 * @access public
	 * @return void
	 */
	public function admin_init() {

		if ( current_user_can( 'edit_theme_options' ) ) {

			if ( isset( $_GET['zage-deactivate'] ) && 'deactivate-plugin' === $_GET['zage-deactivate'] ) {
				check_admin_referer( 'zage-deactivate', 'zage-deactivate-nonce' );

				$plugins = TGM_Plugin_Activation::$instance->plugins;

				foreach ( $plugins as $plugin ) {
					if ( $plugin['slug'] == $_GET['plugin'] ) {
						deactivate_plugins( $plugin['file_path'] );
					}
				}
			}
			if ( isset( $_GET['zage-activate'] ) && 'activate-plugin' === $_GET['zage-activate'] ) {
				check_admin_referer( 'zage-activate', 'zage-activate-nonce' );

				$plugins = TGM_Plugin_Activation::$instance->plugins;

				foreach ( $plugins as $plugin ) {
					if ( isset( $_GET['plugin'] ) && $plugin['slug'] == $_GET['plugin'] ) {
						activate_plugin( $plugin['file_path'] );

						wp_redirect( admin_url( 'admin.php?page=zage-plugins' ) );
						exit;
					}
				}
			}
		}
	}

	/**
	 * Adds the admin menu.
	 *
	 * @access  public
	 * @return void
	 */
	public function admin_menu() {


			// Work around for theme check.
			$zage_menu_page_creation_method    = 'add_menu_page';
			$zage_submenu_page_creation_method = 'add_submenu_page';

			$welcome_screen = $zage_menu_page_creation_method( 'zage', 'zage', 'administrator', 'zage', array( $this, 'welcome_screen' ), 'dashicons-zagea-logo', '2.111111' );

			$support       = $zage_submenu_page_creation_method( 'zage', esc_attr__( 'zage Support', 'zage' ), esc_attr__( 'Support', 'zage' ), 'administrator', 'zage-support', array( $this, 'support_tab' ) );
			$demos         = $zage_submenu_page_creation_method( 'zage', esc_attr__( 'Install zage Demos', 'zage' ), esc_attr__( 'Install Demos', 'zage' ), 'administrator', 'zage-demos', array( $this, 'demos_tab' ) );
			$plugins       = $zage_submenu_page_creation_method( 'zage', esc_attr__( 'Plugins', 'zage' ), esc_attr__( 'Plugins', 'zage' ), 'administrator', 'zage-plugins', array( $this, 'plugins_tab' ) );
			$status        = $zage_submenu_page_creation_method( 'zage', esc_attr__( 'System Status', 'zage' ), esc_attr__( 'System Status', 'zage' ), 'administrator', 'zage-system-status', array( $this, 'system_status_tab' ) );
			$theme_options = $zage_submenu_page_creation_method( 'zage', esc_attr__( 'Theme Options', 'zage' ), esc_attr__( 'Theme Options', 'zage' ), 'administrator', 'themes.php?page=zage_options' );

			if ( ! class_exists( 'zageReduxFrameworkPlugin' ) ) {
				$theme_options_global = $zage_submenu_page_creation_method( 'admin.php', esc_attr__( 'Theme Options', 'zage' ), esc_attr__( 'Theme Options', 'zage' ), 'administrator', 'admin.php?page=ash-framework' );
			}

			add_action( 'admin_print_scripts-' . $welcome_screen, array( $this, 'welcome_screen_scripts' ) );
			add_action( 'admin_print_scripts-' . $support, array( $this, 'support_screen_scripts' ) );
			add_action( 'admin_print_scripts-' . $demos, array( $this, 'demos_screen_scripts' ) );
			add_action( 'admin_print_scripts-' . $plugins, array( $this, 'plugins_screen_scripts' ) );
			add_action( 'admin_print_scripts-' . $status, array( $this, 'status_screen_scripts' ) );

	}

	/**
	 * Include file.
	 *
	 * @access  public
	 * @return void
	 */
	public function welcome_screen() {
		require_once( 'welcome.php' );
	}

	/**
	 * Include file.
	 *
	 * @access  public
	 * @return void
	 */
	public function support_tab() {
		require_once( 'support.php' );
	}

	/**
	 * Include file.
	 *
	 * @access  public
	 * @return void
	 */
	public function demos_tab() {
		require_once( 'install-demos.php' );
	}

	/**
	 * Include file.
	 *
	 * @access  public
	 * @return void
	 */
	public function plugins_tab() {
		require_once( 'zage-plugins.php' );
	}

	/**
	 * Include file.
	 *
	 * @access  public
	 * @return void
	 */
	public function system_status_tab() {
		require_once( 'system-status.php' );
	}

	/**
	 * Renders the admin screens header with title, logo and tabs.
	 *
	 * @since 5.0.0
	 *
	 * @access  public
	 * @param string $screen The current screen.
	 * @return void
	 */
	public function get_admin_screens_header( $screen = 'welcome' ) {
		?>

		<h2 class="nav-tab-wrapper">
			<a href="<?php echo ( 'welcome' === $screen ) ? '#' : esc_url_raw( admin_url( 'admin.php?page=zage' ) ); ?>" class="<?php echo ( 'welcome' === $screen ) ? 'nav-tab-active' : ''; ?> nav-tab"><?php esc_attr_e( 'Product Registration', 'zage' ); ?></a>
			<a href="<?php echo ( 'support' === $screen ) ? '#' : esc_url_raw( admin_url( 'admin.php?page=zage-support' ) ); ?>" class="<?php echo ( 'support' === $screen ) ? 'nav-tab-active' : ''; ?> nav-tab"><?php esc_attr_e( 'Support', 'zage' ); ?></a>
			<a href="<?php echo ( 'demos' === $screen ) ? '#' : esc_url_raw( admin_url( 'admin.php?page=zage-demos' ) ); ?>" class="<?php echo ( 'demos' === $screen ) ? 'nav-tab-active' : ''; ?> nav-tab"><?php esc_attr_e( 'Install Demos', 'zage' ); ?></a>
			<a href="<?php echo ( 'plugins' === $screen ) ? '#' : esc_url_raw( admin_url( 'admin.php?page=zage-plugins' ) ); ?>" class="<?php echo ( 'plugins' === $screen ) ? 'nav-tab-active' : ''; ?> nav-tab"><?php esc_attr_e( 'Plugins', 'zage' ); ?></a>
			<a href="<?php echo ( 'system-status' === $screen ) ? '#' : esc_url_raw( admin_url( 'admin.php?page=zage-system-status' ) ); ?>" class="<?php echo ( 'system-status' === $screen ) ? 'nav-tab-active' : ''; ?> nav-tab"><?php esc_attr_e( 'System Status', 'zage' ); ?></a>
		</h2>
		<div class="bb_admin_head">
		<?php if ( 'welcome' === $screen ) : ?>
		<h1><?php _e( 'Welcome to zage!', 'zage' ); ?></h1>
	   <?php elseif ('demos' === $screen ) : ?>
	   	<h1><?php _e( 'zage Demos', 'zage' ); ?></h1>
	   	<?php elseif ('plugins' === $screen ) : ?>
	   	<h1><?php _e( 'zage Plugins', 'zage' ); ?></h1>
	   	<?php elseif ('system-status' === $screen ) : ?>
	   	<h1><?php _e( 'zage System Status', 'zage' ); ?></h1>
	   	<?php elseif ('support' === $screen ) : ?>
	   	<h1><?php _e( 'zage Support', 'zage' ); ?></h1>
	   	<?php endif; ?>
         <em><?php _e( 'Version : ', 'zage' ); ?><?php echo $this->theme_version; ?></em>
		<?php if ( 'demos' === $screen ) : ?>
        </div>
			<div class="updated error importer-notice importer-notice-1" style="display: none;margin: 40px 0;">
				<p><strong><?php esc_attr_e( "We're sorry but the demo data could not be imported. It is most likely due to low PHP configurations on your server. There are two possible solutions.", 'zage' ); ?></strong></p>

				<p><strong><?php esc_attr_e( 'Solution 1:', 'zage' ); ?></strong> <?php esc_attr_e( 'Import the demo using an alternate method.', 'zage' ); ?><a href="https://theme-zage.com/zage-doc/demo-content-info/alternate-demo-method/" class="button-primary" target="_blank" style="margin-left: 10px;"><?php esc_attr_e( 'Alternate Method', 'zage' ); ?></a></p>
				<p><strong><?php esc_attr_e( 'Solution 2:', 'zage' ); ?></strong> <?php printf( __( 'Fix the PHP configurations in the System Status that are reported in <strong style="color: red;">RED</strong>, then use the %s, then reimport.', 'zage' ), '<a href="' . admin_url() . 'plugin-install.php?tab=plugin-information&amp;plugin=wordpress-reset&amp;TB_iframe=true&amp;width=830&amp;height=472">' . esc_attr__( 'Reset WordPress Plugin', 'zage' ) . '</a>' ); ?><a href="<?php echo admin_url( 'admin.php?page=zage-system-status' ); ?>" class="button-primary" target="_blank" style="margin-left: 10px;"><?php esc_attr_e( 'System Status', 'zage' ); ?></a></p>
			</div>

			<div class="updated importer-notice importer-notice-2" style="display: none;"><p><?php printf( esc_html__( 'Demo data successfully imported. Now, please install and run %s plugin once.', 'zage' ), '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=regenerate-thumbnails&amp;TB_iframe=true&amp;width=830&amp;height=472' ) . ' class="thickbox" title="' . esc_attr__( 'Regenerate Thumbnails', 'zage' ) . '">' . esc_attr__( 'Regenerate Thumbnails', 'zage' ) . '</a>' ); ?></p></div>

			<div class="updated error importer-notice importer-notice-3" style="display: none;">
				<p><strong><?php esc_attr_e( "We're sorry but the demo data could not be imported. It is most likely due to low PHP configurations on your server. There are two possible solutions.", 'zage' ); ?></strong></p>

				<p><strong><?php esc_attr_e( 'Solution 1:', 'zage' ); ?></strong> <?php esc_attr_e( 'Import the demo using an alternate method.', 'zage' ); ?><a href="https://theme-zage.com/zage-doc/demo-content-info/alternate-demo-method/" class="button-primary" target="_blank" style="margin-left: 10px;"><?php esc_attr_e( 'Alternate Method', 'zage' ); ?></a></p>
				<p><strong><?php esc_attr_e( 'Solution 2:', 'zage' ); ?></strong> <?php printf( __( 'Fix the PHP configurations in the System Status that are reported in <strong style="color: red;">RED</strong>, then use the %s, then reimport.', 'zage' ), '<a href="' . admin_url() . 'plugin-install.php?tab=plugin-information&amp;plugin=wordpress-reset&amp;TB_iframe=true&amp;width=830&amp;height=472">' . esc_attr__( 'Reset WordPress Plugin', 'zage' ) . '</a>' ); ?><a href="<?php echo admin_url( 'admin.php?page=zage-system-status' ); ?>" class="button-primary" target="_blank" style="margin-left: 10px;"><?php esc_attr_e( 'System Status', 'zage' ); ?></a></p>
			</div>
		<?php endif; ?>
		<?php if ( 'welcome' === $screen ) : ?>
		<div class="zage-important-notice">
		<p class="about-description">
		<?php printf( esc_html__( 'zage is now installed and ready to use! Get ready to build something beautiful. Please register your purchase to get automatic theme updates, import zage demos and install premium plugins. Read below for additional information. We hope you enjoy it! %s', 'zage' ), '<a href="//youtube.com/embed/X92mpPz1COM?rel=0?KeepThis=true&TB_iframe=true" class="thickbox" title="' . esc_attr__( 'Guided Tour of zage', 'zage' ) . '">' . esc_attr__( 'Watch Our Quick Guided Tour!', 'zage' ) . '</a>' ); ?>
			</p>
		</div>
		<?php endif; ?>

		<?php
	}

	/**
	 * Add styles to admin.
	 *
	 * @access  public
	 * @return void
	 */
	public function admin_styles() {
		?>

			<style type="text/css">
				@media screen and (max-width: 782px) {
					#wp-toolbar > ul > .zage-menu {
						display: block;
					}

					#wpadminbar .zage-menu > .ab-item .ab-icon {
						padding-top: 6px !important;
						height: 40px !important;
						font-size: 30px !important;
					}
				}
				#wpadminbar .zage-menu > .ab-item .ab-icon:before,
				.dashicons-zagea-logo:before{
					content: "\e900";
					font-family: 'icomoon';
					speak: none;
					font-style: normal;
					font-weight: normal;
					font-variant: normal;
					text-transform: none;
					line-height: 1;
                    color: #8BC34A !important;
					/* Better Font Rendering. */
					-webkit-font-smoothing: antialiased;
					-moz-osx-font-smoothing: grayscale;
				}
				.zage-important-notice {
					    padding: 30px;
					    background: #fff;
					    margin: 0px 0px 30px;
					}
					.wrap.about-wrap.zage-wrap .nav-tab-wrapper {
					    margin-bottom: 30px;
					}
					.wrap.about-wrap.zage-wrap h1 {
					    padding: 15px;
					    /* padding: 0; */
					    color: #32373c;
					    line-height: 1.2em;
					    font-size: 2.8em;
					    font-weight: 400;
					    font-weight: bold;
					    display: inline-block;
					    background: #fff;
					    margin: 0 20px 20px 0 !important;
					}
					.zage-wrap .ab-icon {
					    position: relative;
					    float: left;
					    font: 400 20px/1 dashicons;
					    speak: none;
					    -webkit-font-smoothing: antialiased;
					    -moz-osx-font-smoothing: grayscale;
					    background-image: none!important;
					    margin-right: 6px;
					}
					.zage-wrap .ab-icon:before {
						    content: "\f120";
						    top: 2px;
						}
					.wrap.about-wrap.zage-wrap .importer-notice-1,
					.wrap.about-wrap.zage-wrap .importer-notice-2,
					.wrap.about-wrap.zage-wrap .importer-notice-3{
						margin: 40px 0 !important;
					}
					.wrap.about-wrap.zage-wrap .button, .bb-button {
					    margin-right: 10px;
					    box-shadow: none;
					    border-radius: 2px;
					    border: none;
					    height: 28px;
					    position: relative;
					    box-sizing: border-box;
					    display: inline-block;
					    text-decoration: none;
					    font-size: 13px;
					    line-height: 28px;
					    white-space: nowrap;
					    margin: 0px 5px 0px 0px;
					}
					.button.red, .button.red:active , .button.red:focus{
					    background: #EF6161;
					    color: #fff;
					    border-radius: 1px;
					}
					.button.green, .button.green:active , .button.green:focus{
					    background: #86C724;
					    color: #fff;
					    border-radius: 1px;
					}
					.button.green:hover {
					    background: #74B11A;
					    color: #fff;
					}
					.button.red:hover {
					    background: #DC4D4D;
					    color: #fff;
					}
					.zage-demo-themes .theme.zage-demo-installed .button-install-demo,
					.zage-demo-themes .theme .button-uninstall-demo,
					.zage-demo-themes .theme.zage-demo-installed .preview-demo {
						display: none !important;
					}
					.zage-demo-themes .theme.zage-demo-installed .button-uninstall-demo,
					.zage-demo-themes .theme .button-install-demo {
						display: inline-block !important;
					}
					.bb_admin_head{
						position: relative;
					}
					.bb_admin_head em{
						position: absolute;
					    top: -13px;
					    background-color: #8BC34A;
					    padding: 3px;
					    left: 15px;
					    color: #fff;
					}

				.zage-install-plugins .theme .update-message { display: block !important; cursor: default; }
			</style>

			<?php

	}

	/**
	 * Enqueues scripts.
	 *
	 * @since 5.0.3
	 * @access  public
	 * @return void
	 */
	public function admin_scripts() {

			global $pagenow;

			$version = '1.1';
			wp_enqueue_style( 'zage_css', trailingslashit( get_template_directory_uri()) . 'assets/admin/css/admin.css', array(), $version );

			// Add script to check for zage option slider changes.
			if ( 'post-new.php' == $pagenow || 'edit.php' == $pagenow || 'post.php' == $pagenow ) {
				wp_enqueue_script( 'slider_preview', trailingslashit( get_template_directory_uri()) . 'assets/admin/js/zage-builder-slider-preview.js', array(), $version, true );
			}
			if ( 'themes.php' == $pagenow || 'update-core.php' == $pagenow ) {
				wp_enqueue_script( 'welcome_screen', trailingslashit( get_template_directory_uri()) . 'assets/admin/js/zage-theme-update.js', array(), $version, true );
			}
		}


	/**
	 * Enqueues scripts & styles.
	 *
	 * @access  public
	 * @return void
	 */
	public function welcome_screen_scripts() {
		$ver = '1.1';
		wp_enqueue_style( 'zage_admin_css', trailingslashit( get_template_directory_uri()) . 'assets/admin/css/zage-admin.css', array(), $ver );
		wp_enqueue_style( 'welcome_screen_css', trailingslashit( get_template_directory_uri()) . 'assets/admin/css/zage-welcome-screen.css', array(), $ver );
	}

	/**
	 * Enqueues scripts & styles.
	 *
	 * @access  public
	 * @return void
	 */
	public function support_screen_scripts() {
		$ver = '1.1';
		wp_enqueue_style( 'zage_admin_css', trailingslashit( get_template_directory_uri()) . 'assets/admin/css/zage-admin.css', array(), $ver );
	}

	/**
	 * Enqueues scripts & styles.
	 *
	 * @access  public
	 * @return void
	 */
	public function demos_screen_scripts() {
		$ver = '1.1';
		wp_enqueue_style( 'zage_admin_css', trailingslashit( get_template_directory_uri()) . 'assets/admin/css/zage-admin.css', array(), $ver );
		wp_enqueue_style( 'zage_css', trailingslashit( get_template_directory_uri()) . 'assets/admin/css/admin.css', array(), $ver );
		wp_enqueue_script( 'zage_admin_js', trailingslashit( get_template_directory_uri()) . 'assets/admin/js/zage-admin.js', array(), $ver, true );

		wp_localize_script('zage_admin_js', 'zage_async', array(

         'zage_ajax_url' => admin_url('admin-ajax.php')

    ));
	}

	/**
	 * Enqueues scripts & styles.
	 *
	 * @access  public
	 * @return void
	 */
	public function plugins_screen_scripts() {
		$ver = '1.1';
		wp_enqueue_style( 'zage_admin_css', trailingslashit( get_template_directory_uri()) . 'assets/admin/css/zage-admin.css', array(), $ver );
		wp_enqueue_script( 'zage_admin_js', trailingslashit( get_template_directory_uri()) . 'assets/admin/js/zage-admin.js', array(), $ver, true );
	}

	/**
	 * Enqueues scripts & styles.
	 *
	 * @access  public
	 * @return void
	 */
	public function status_screen_scripts() {
		$ver = '1.1';
		wp_enqueue_style( 'zage_admin_css', trailingslashit( get_template_directory_uri()) . 'assets/admin/css/zage-admin.css', array(), $ver );
		wp_enqueue_script( 'zage_admin_js', trailingslashit( get_template_directory_uri()) . 'assets/admin/js/zage-admin.js', array(), $ver, true );
	}

	/**
	 * Get the plugin link.
	 *
	 * @access  public
	 * @param array $item The plugin in question.
	 * @return  array
	 */
	public function plugin_link( $item ) {
		$installed_plugins = get_plugins();

		$item['sanitized_plugin'] = $item['name'];

		$actions = array();

		// We have a repo plugin.
		if ( ! $item['version'] ) {
			$item['version'] = TGM_Plugin_Activation::$instance->does_plugin_have_update( $item['slug'] );
		}

		$disable_class = '';
		$data_version  = '';


		// We need to display the 'Install' hover link.
		if ( ! isset( $installed_plugins[ $item['file_path'] ] ) ) {
			if ( ! $disable_class ) {
				$url = esc_url( wp_nonce_url(
					add_query_arg(
						array(
							'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
							'plugin'        => urlencode( $item['slug'] ),
							'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
							'tgmpa-install' => 'install-plugin',
							'return_url'    => 'zage_plugins',
						),
						TGM_Plugin_Activation::$instance->get_tgmpa_url()
					),
					'tgmpa-install',
					'tgmpa-nonce'
				) );
			} else {
				$url = '#';
			}
			$actions = array(
				'install' => '<a href="' . $url . '" class="button button-primary' . $disable_class . '"' . $data_version . ' title="' . sprintf( esc_attr__( 'Install %s', 'zage' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Install', 'zage' ) . '</a>',
			);
		} elseif ( is_plugin_inactive( $item['file_path'] ) ) {
			// We need to display the 'Activate' hover link.
			if ( ! $disable_class ) {
				$url = esc_url( add_query_arg(
					array(
						'plugin'               => urlencode( $item['slug'] ),
						'plugin_name'          => urlencode( $item['sanitized_plugin'] ),
						'zage-activate'       => 'activate-plugin',
						'zage-activate-nonce' => wp_create_nonce( 'zage-activate' ),
					),
					admin_url( 'admin.php?page=zage-plugins' )
				) );
			} else {
				$url = '#';
			}

			$actions = array(
				'activate' => '<a href="' . $url . '" class="button button-primary' . $disable_class . '"' . $data_version . ' title="' . sprintf( esc_attr__( 'Activate %s', 'zage' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Activate' , 'zage' ) . '</a>',
			);
		} elseif ( version_compare( $installed_plugins[ $item['file_path'] ]['Version'], $item['version'], '<' ) ) {
			$disable_class = '';
			// We need to display the 'Update' hover link.
			$url = wp_nonce_url(
				add_query_arg(
					array(
						'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
						'plugin'        => urlencode( $item['slug'] ),
						'tgmpa-update'  => 'update-plugin',
						'version'       => urlencode( $item['version'] ),
						'return_url'    => 'zage_plugins',
					),
					TGM_Plugin_Activation::$instance->get_tgmpa_url()
				),
				'tgmpa-update',
				'tgmpa-nonce'
			);
			if ( ( 'LayerSlider' == $item['slug'] || 'revslider' == $item['slug'] ) && ! zage()->registration->is_registered() ) {
				$disable_class = ' disabled zage-no-token';
			}
			$actions = array(
				'update' => '<a href="' . $url . '" class="button button-primary' . $disable_class . '" title="' . sprintf( esc_attr__( 'Update %s', 'zage' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Update', 'zage' ) . '</a>',
			);
		} elseif ( is_plugin_active( $item['file_path'] ) ) {
			$url = esc_url( add_query_arg(
				array(
					'plugin'                 => urlencode( $item['slug'] ),
					'plugin_name'            => urlencode( $item['sanitized_plugin'] ),
					'zage-deactivate'       => 'deactivate-plugin',
					'zage-deactivate-nonce' => wp_create_nonce( 'zage-deactivate' ),
				),
				admin_url( 'admin.php?page=zage-plugins' )
			) );
			$actions = array(
				'deactivate' => '<a href="' . $url . '" class="button button-primary" title="' . sprintf( esc_attr__( 'Deactivate %s', 'zage' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Deactivate', 'zage' ) . '</a>',
			);
		}

		return $actions;
	}

	/**
	 * Removes install link for zage Builder, if zage Core was not updated to 3.0
	 *
	 * @since 5.0.0
	 * @param array  $action_links The action link(s) for a required plugin.
	 * @param string $item_slug The slug of a required plugin.
	 * @param array  $item Data belonging to a required plugin.
	 * @param string $view_context Specifying the kind of action (install, activate, update).
	 * @return array The action link(s) for a required plugin.
	 */
	public function edit_tgmpa_action_links( $action_links, $item_slug, $item, $view_context ) {
		if ( 'zage-builder' === $item_slug && 'install' === $view_context ) {
			$zage_core_version = TGM_Plugin_Activation::$instance->get_installed_version( TGM_Plugin_Activation::$instance->plugins['Zage-core']['slug'] );

			if ( version_compare( $zage_core_version, '3.0', '<' ) ) {
				$action_links['install'] = '<span class="zage-not-installable" style="color:#555555;">' . esc_attr__( 'zage Builder will be installable, once zage Core plugin is updated.', 'zage' ) . '<span class="screen-reader-text">' . esc_attr__( 'zage Builder', 'zage' ) . '</span></span>';
			}
		}

		return $action_links;
	}


	/**
		 * Removes install link for zage Builder, if zage Core was not updated to 3.0
		 *
		 * @since 5.0.0
		 * @param array $action_links The action link(s) for a required plugin.
		 * @return array The action link(s) for a required plugin.
		 */
		public function edit_tgmpa_notice_action_links( $action_links ) {
			$current_screen = get_current_screen();

			if ( 'zage_page_zage-plugins' == $current_screen->id ) {
				$link_template = '<a id="manage-plugins" class="button-primary" style="margin-top:1em;" href="#zage-install-plugins">' . esc_attr__( 'Manage Plugins Below', 'zage' ) . '</a>';
				$action_links  = array( 'install' => $link_template );
			} else {
				$link_template = '<a id="manage-plugins" class="button-primary" style="margin-top:1em;" href="' . esc_url( self_admin_url( 'admin.php?page=zage-plugins' ) ) . '#zage-install-plugins">' . esc_attr__( 'Go Manage Plugins', 'zage' ) . '</a>';
				$action_links  = array( 'install' => $link_template );
			}

			return $action_links;
		}


	/**
	 * Sets the theme version.
	 *
	 * @since 5.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function set_theme_version() {
		$theme_version = '1.1';

		$this->theme_version = $theme_version;
	}

	/**
	 * Sets the WP_Object for the theme.
	 *
	 * @since 5.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function set_theme_object() {
		$theme_object = wp_get_theme();
		if ( $theme_object->parent_theme ) {
			$template_dir = basename( zage::$template_dir_path );
			$theme_object = wp_get_theme( $template_dir );
		}

		$this->theme_object = $theme_object;
	}

	/**
	 * Override some LayerSlider data.
	 *
	 * @since 5.0.5
	 * @access public
	 * @return void
	 */
	public function layerslider_overrides() {

	    // Disable auto-updates.
	    $GLOBALS['lsAutoUpdateBox'] = false;
	}

	public static function normalize_version( $ver ) {
		if ( ! is_string( $ver ) ) {
			return $ver;
		}
		$ver_parts = explode( '.', $ver );
		$count     = count( $ver_parts );
		// Keep only the 1st 3 parts if longer.
		if ( 3 < $count ) {
			return absint( $ver_parts[0] ) . '.' . absint( $ver_parts[1] ) . '.' . absint( $ver_parts[2] );
		}
		// If a single digit, then append '.0.0'.
		if ( 1 === $count ) {
			return absint( $ver_parts[0] ) . '.0.0';
		}
		// If 2 digits, append '.0'.
		if ( 2 === $count ) {
			return absint( $ver_parts[0] ) . '.' . absint( $ver_parts[1] ) . '.0';
		}
		return $ver;
	}


	public static function let_to_num( $size ) {
		$l   = substr( $size, -1 );
		$ret = substr( $size, 0, -1 );
		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
		}
		return $ret;
	}

   /**
	 * Has user associated with current token purchased zage?
	 *
	 * @access public
	 * @since 5.0.0
	 * @return bool
	 */
	public static function is_registered() {
		// If no token is set, the product is not registered.
		if ( empty( self::$token ) ) {
			return false;
		} elseif ( self::$registered ) {
			return true;
		} elseif ( get_site_transient( 'zage_envato_api_down' ) ) {
			return true;
		} else {
			return false;
		}
	}

}

	ASHCODE_CORE::$get_template_directory = get_template_directory();
	ASHCODE_CORE::$get_template_directory_uri = get_template_directory_uri();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
