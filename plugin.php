<?php
/**
 * Plugin Name: wpRTC Pro - WebRTC for WordPress
 * Description: This plugin will allow you to easily create peer-to-peer video using shortcode, as users enter the page they will enter the video chat
 * Version: 1.1.1
 * Author: Roy Sivan
 * Author URI: http://www.roysivan.com
 * License: GPL2
 */

require_once('includes/settings.php');
require_once('includes/licensing.php');
require_once('includes/shortcode.php');


define('WordPressWebRTC', '2.0'); 

class WordPressWebRTC {
	function WordPressWebRTC(){
		global $wpdb;
	
		$this->__init();
	}
	
	function __init() {
		new pluginLicense();
		new wprtc_shortcode();
		new wprtc_setting();
		
		add_action( 'admin_enqueue_scripts', array( $this, 'adminSettings' ) );
		add_action('wp_enqueue_scripts' , array( $this, 'wprtcCSS' ) );
	}

	function adminSettings( $hook_suffix ) {
	    wp_enqueue_style( 'wp-color-picker' );
	    wp_enqueue_script('jquery-ui-tabs');
	    wp_enqueue_script( 'my-script-handle', plugin_dir_url( __FILE__ ).'includes/js/wpRTCadmin.js', array( 'wp-color-picker' ), false, true );

	    wp_enqueue_style('tab-ui', plugin_dir_url( __FILE__ ).'includes/css/jquery-ui-1.10.4.custom.min.css', null, false);
	    wp_enqueue_style('wpRTCcss', plugin_dir_url( __FILE__ ).'includes/css/wprtc.css', null, false);
	}
	
	function wprtcCSS(){
		wp_enqueue_style('wpRTCcss', plugin_dir_url( __FILE__ ).'includes/css/wprtc.css', null, false);
	}
}

new WordPressWebRTC();
?>