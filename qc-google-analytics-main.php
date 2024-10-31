<?php
/**
 * Plugin Name: QuantumCloud PageSpeed Friendly Analytics Tracking
 * Plugin URI: https://wordpress.org/plugins/pagespeed-friendly-analytics-tracking
 * Description: QuantumCloud’s PageSpeed Friendly Analytics Tracking for Google does the simple job of adding the analytics tracking code to your WordPress website in all pages with an option to increase your pagespeed score in Google pagespeed insight
 * Version: 1.2.0
 * Author: QuantumCloud
 * Author URI: https://www.quantumcloud.com/
 * Requires at least: 3.0
 * Tested up to: 4.6.0
 * License: GPL2
 */

//Include files and scripts
require_once( 'inc/class-qc-google-analytics.php' );
require_once( 'inc/class-qc-google-analytics-tracking-settings.php' );
require_once( 'inc/class-qc-google-analytics-admin-api.php' );

/*
*
* Init the main class and throw an instance 
*
*/
function Qc_Google_Analytics_Admin_Init () {
	$instance = Qc_Google_Analytics_Main::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Qc_Google_Analytics_Settings_Page::instance( $instance );
	}

	return $instance;
}

Qc_Google_Analytics_Admin_Init();

//FrontEnd Action Binding
function qc_google_analytics_inc() 
{ 
	$flag			=	0;
	$roleHideOn 	= 	0;
	$user_ID 		= 	get_current_user_id();		 
	$analytics_id 	=  	get_option( 'qcanlytics_google_analytics_id' );

	$exclude_users 	=	get_option('qcanlytics_exclude_users');
	$disable_tracking 	=	get_option('qcanlytics_disable_tracking');
	$page_speed_inc 	=	get_option('qcanlytics_page_speed');
		
	
	if ( is_user_logged_in() ) 
	{ 
		$user = new WP_User( $user_ID );
		if ( !empty( $user->roles ) && is_array( $user->roles ) ) 
		{
			foreach ( $user->roles as $role )
				 $role;
		}
	}

	if( get_option('qcanlytics_google_analytics_id') == '' )
	{
		$flag = 1; 
	}

	if( $disable_tracking == 'on' )
	{
		$flag = 1; 
	}

	if( $page_speed_inc == 'on' )
	{
		$flag = 1; 
	}

	if(!empty($exclude_users) && is_user_logged_in())
	{

	 if (in_array('administrator',$exclude_users) && $role=='administrator' ) {
		$flag=1; 
		$roleHideOn = 1;
	 }
	 else if (in_array('author',$exclude_users) && $role=='author' ) {
		$flag=1;  
		$roleHideOn = 1;
	 }
	 else if (in_array('contributor',$exclude_users) && $role=='contributor' ) {
		$flag=1; 
		$roleHideOn = 1; 
	 }
	 else if (in_array('editor',$exclude_users) && $role=='editor' ) {
		$flag=1;  
		$roleHideOn = 1;
	 }
	 else if (in_array('subscriber',$exclude_users) && $role=='subscriber' ) {
		$flag=1; 
		$roleHideOn = 1; 
	 }
	}
	  
	 
	if( $flag != '1' )
	{ 
	?>
		<script>
			/*Normal Tracking Mode*/
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			
			ga('create', '<?php echo esc_attr( get_option('qcanlytics_google_analytics_id') ); ?>', 'auto');
			ga('send', 'pageview');
			
		</script>
	<?php 
	} 

	if( $page_speed_inc == 'on' && $roleHideOn == 0 )
	{ 
		if (!isset($_SERVER['HTTP_USER_AGENT']) || stripos($_SERVER['HTTP_USER_AGENT'], 'Speed Insights') === false) { ?>
		<script>
			/*Page Speed Increased Mode*/
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			
			ga('create', '<?php echo esc_attr( get_option('qcanlytics_google_analytics_id') ); ?>', 'auto');
			ga('send', 'pageview');
			
		</script>
	<?php }
	} 

} 

//Action Hook for Head Inclusion
add_action( 'wp_head', 'qc_google_analytics_inc', 10 );