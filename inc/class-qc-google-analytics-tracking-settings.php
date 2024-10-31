<?php
 
if ( ! defined( 'ABSPATH' ) ) exit;

class Qc_Google_Analytics_Settings_Page {

	/**
	 * The single instance of Qc_Google_Analytics_Settings_Page.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'qcanlytics_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item () {
		$page = add_options_page( __( 'QC Google Analytics', 'qc-google-analytics' ) , __( 'QC Google Analytics', 'qc-google-analytics' ) , 'manage_options' , $this->parent->_token . '_settings' ,  array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' );

    	// We're including the WP media scripts here because they're needed for the image upload field
    	// If you're not including an image upload then you can leave this function call out
    	wp_enqueue_media();

    	wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
    	wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'qc-google-analytics' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {

		$settings['standard'] = array(
			'title'					=> __( '', 'qc-google-analytics' ),
			'description'			=> __( '', 'qc-google-analytics' ),
			'fields'				=> array(
				array(
					'id' 			=> 'google_analytics_id',
					'label'			=> __( 'Google Analytics ID' , 'qc-google-analytics' ),
					'description'	=> __( 'Enter your google analytics tracking id here.', 'qc-google-analytics' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'UA-########-#', 'qc-google-analytics' )
				),
				
				array(
					'id' 			=> 'disable_tracking',
					'label'			=> __( 'Disable Tracking', 'qc-google-analytics' ),
					'description'	=> __( 'Check this if you want to disable tracking', 'qc-google-analytics' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				
				array(
					'id' 			=> 'exclude_users',
					'label'			=> __( 'Exclude Users From Tracking by role', 'qc-google-analytics' ),
					'description'	=> __( 'Check if you want to disable tracking of some users by role', 'qc-google-analytics' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array( 'administrator' => 'Administrator', 'author' => 'Author', 'contributor' => 'Contributor', 'editor' => 'Editor','subscriber'=>'Subscriber' ),
					'default'		=> array( 'administrator' )
				),

				array(
					'id' 			=> 'page_speed',
					'label'			=> __( 'Increase Page Speed', 'qc-google-analytics' ),
					'description'	=> __( 'Increase page speed by hiding the tracking script from google Page Insight.', 'qc-google-analytics' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
			)
		);

		

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
				}

				if ( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {

		//Check for available adds or promo, kadir - 09-19-16
		$iframeCode = "";
		
		//changed by Raju, Qc . we will not use the promo image anymore. 
		//$size = getimagesize('https://www.quantumcloud.com/wp/link-existency-checker.png');
		//if( isset( $size[0] ) && $size[0] == 200 ){
		//	$iframeCode = '<iframe style="min-height: 400px;" src="https://www.quantumcloud.com/wp/plugins/sidebar-rt/index.php" frameborder="0"></iframe>';
		//}
		
		
		$iframeCode = '<div class="qc-promo-plugins" style="text-align: center;">';
		$iframeCode .= '<img src="'.QCCLR_ASSETS_URL.'/img/qc-logo-full.png" alt="QuantumCloud Logo">';
		$iframeCode .= "<br><br><hr><br>";
		$iframeCode .= '<a href="http://www.quantumcloud.com" target="_blank">QuantumCloud</a>';
		$iframeCode .= '</div>';
		
		
		
		// Build page HTML
		$html = '
		
		<style>
#post-body-content h2 { padding-left: 0; }
.wrap { background: #fff; box-shadow: 0px 0px 25px -5px rgba(0,0,0,0.45); padding: 20px; border-radius: 10px; }
.clear { clear: both; }
.form-table { border: 1px solid #F0F0F0; }
.form-table th { width: 350px; padding: 20px 10px 20px 16px }
.form-table tr:nth-child(odd) { background: #F0F0F0; }
</style>
		
		
		<div class="wrap" id="' . $this->parent->_token . '_settings">
		<div id="poststuff">			
				<div id="post-body" class="metabox-holder columns-2">				
					<div id="post-body-content" style="position: relative;">' . "\n";
			$html .= '<h2>' . __( 'Google Analytics Settings' , 'qc-google-analytics' ) . '</h2>' . "\n";

			$tab = '';
			if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab .= $_GET['tab'];
			}

			// Show page tabs
			if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;
				foreach ( $this->settings as $section => $data ) {

					// Set tab class
					$class = 'nav-tab';
					if ( ! isset( $_GET['tab'] ) ) {
						if ( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if ( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab
					$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

					++$c;
				}

				$html .= '</h2>' . "\n";
			}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'qc-google-analytics' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>';
		$html .= '<div id="postbox-container-1" id="postbox-container">
						<!-- Plugin Logo -->
						<div style="border: 1px solid #ccc; padding: 10px 0; text-align: center;">
							QC Google Analytics
						</div>
						
						<!-- Promo Block 1 -->
						<div style="margin-top: 20px;">
							'.$iframeCode.'
						</div>
						
					  </div>';
		$html .= '</div><div class="clear"></div></div><div class="clear"></div></div>' . "\n";

		echo $html;
	}

	/**
	 * Main Qc_Google_Analytics_Settings_Page Instance
	 *
	 * Ensures only one instance of Qc_Google_Analytics_Settings_Page is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Qc_Google_Analytics()
	 * @return Main Qc_Google_Analytics_Settings_Page instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}