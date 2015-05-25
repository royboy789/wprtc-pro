<?php
/**
 * Plugin Name:		wpRTC - WebRTC for WordPress
 * Plugin URI:		https://wordpress.org/plugins/wprtc-real-time-video-for-wp/
 * Description:		This plugin will allow you to easily create peer-to-peer video chats
 * Version:			2.0.0
 * Author:			Roy Sivan, Michal Bluma, Michael Beil
 * Author URI:		http://roysivan.com
 * Text Domain:		wprtc
 * Domain Path:     /languages
 * License:			GPLv3
 * License URI:		http://www.gnu.org/licenses/gpl.html
 */

require_once('includes/settings.php');
require_once('includes/shortcode.php');


define('WordPressWebRTC', '2.0.0');

class WordPressWebRTC {
	const text_domain = 'wprtc';

	function WordPressWebRTC(){
		global $wpdb;
	}

	function __init() {
		new wprtc_shortcode();
		$wprtc_settings = new wprtc_setting();
		$wprtc_settings->init();

		// Enqueue scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'adminSettings' ) );
		add_action( 'wp_enqueue_scripts' , array( $this, 'wprtcCSS' ) );

		// Load Translations
		add_action( 'plugins_loaded', array( $this, 'load_translation' ) );
	}

	function adminSettings( $hook_suffix ) {
	    wp_enqueue_style( 'wp-color-picker' );
	    wp_enqueue_script('jquery-ui-tabs');
	    wp_enqueue_script( 'my-script-handle', plugin_dir_url( __FILE__ ).'includes/assets/js/min/wpRTCadmin.min.js', array( 'wp-color-picker' ), false, true );

	    wp_enqueue_style('tab-ui', plugin_dir_url( __FILE__ ).'includes/assets/css/jquery-ui-1.10.4.custom.min.css', null, false);
	    wp_enqueue_style('wpRTCcss', plugin_dir_url( __FILE__ ).'includes/assets/css/wprtc.css', null, false);
	}

	function wprtcCSS(){
		wp_enqueue_style('wpRTCcss', plugin_dir_url( __FILE__ ).'includes/assets/css/wprtc.css', null, false);
	}

	public function load_translation() {
		load_plugin_textdomain(self::text_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
	}
}

$wp_rtc = new WordPressWebRTC();
$wp_rtc->__init();

?>
