<?php


class wprtc_shortcode {
	
	function __construct() {
		add_shortcode( 'wpRTC', array( $this, 'webRTCsc' ) );
	}

	function webRTCscripts() {
		// Simple WebRTC Core
		wp_enqueue_script('icecomm-core', plugin_dir_url( __FILE__ ).'assets/js/min/icecomm.min.js', array('jquery'), null, false);
		wp_enqueue_script('wpRTC', plugin_dir_url( __FILE__ ).'assets/js/min/wpRTC.min.js', array('icecomm-core'), null, false);
		//wp_enqueue_script('wpRTC', plugin_dir_url( __FILE__ ).'assets/js/wpRTC.js', array('icecomm-core'), null, false);
		
		wp_localize_script( 'wpRTC', 
			'wprtc_info',
			$wprtc = array(
				'wprtc_icecomm' => get_option( 'wprtc_icecomm', '' )
			)
		);
		
		// FONT AWESOME
		wp_enqueue_style('fontAwesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css', null, false);
		
	}
	
	function webRTCsc( $atts ){
		
		$wprtc_video = array();
		// SHORTCODE ATTS
		$wprtc_video['shortcode_options'] = shortcode_atts( array(
			'room_name'    		 => '',
			'privacy'      		 => 'off',
			'default_room' 		 => '',
			'default_room_enter' => '',
			'max_capacity' 		 => 0
		), $atts );
	
		// ROOM NAME
		$wprtc_video['room_name'] = $this->room_name( $wprtc_video['shortcode_options'] );
		
		// CREATE SELECT FOR CHANGING ROOMS
		$room_name_default = $wprtc_video['shortcode_options']['room_name'];
		if( strpos( $room_name_default, ',' ) !== false ) {
			$rooms = explode(',', $room_name_default );
			$wprtc_video['room_select'] = '<form id="roomChange" method="post"><select name="roomName"><option value="-1" selected="selected">' . __('Change Rooms', 'webrtc') . '</option>';
				foreach($rooms as $room){ $wprtc_video['room_select'] .= '<option value="'.$room.'">'.$room.'</option>'; }
			$wprtc_video['room_select'] .= '</select></form>';
		}
	
		
		// PLUGIN DEFAULTS
		$rtcOptions = array(
			'rtcBG'       => '#000',
			'rtcBC'       => '#000',
			'rtcBW'       => '2px',
			'rtcW'        => '100%',
			'rtcH'        => '500px',
			'rtcRH'       => '200px',
			'rtcRvW'      => '100px',
			'private_msg' => __( 'You must be logged in to view this video stream', 'webrtc' ),
		);
		
		if(get_option('rtcBG')) { $rtcOptions['rtcBG'] = get_option('rtcBG'); }
		if(get_option('rtcBC')) { $rtcOptions['rtcBC'] = get_option('rtcBC'); }
		if(get_option('rtcBW')) { $rtcOptions['rtcBW'] = get_option('rtcBW'); }
		if(get_option('rtcW')) { $rtcOptions['rtcW'] = get_option('rtcW'); }
		if(get_option('rtcH')) { $rtcOptions['rtcH'] = get_option('rtcH'); }
	
		if(get_option('rtcRH')) { $rtcOptions['rtcRH'] = get_option('rtcRH'); }
		if(get_option('rtcRvW')) { $rtcOptions['rtcRvW'] = get_option('rtcRvW'); }
				
		if(get_option('rtc_main_private_msg')) { $rtcOptions['private_msg'] = get_option('rtc_main_private_msg'); }
		
		// WRAPPER CLASS
		$wprtc_video['wrapper_class'] = '';
		if(get_option('rtcClass')) { $wprtc_video['wrapper_class'] = get_option('rtcClass'); }
		
		if( $this->__privacy_check( $wprtc_video['shortcode_options']['privacy']) ) { 
			ob_start();
			echo '<p>'.$rtcOptions['private_msg'].'</p>';
			return ob_get_clean();
		} else {
			$this->webRTCscripts();
		}
		
		// MAX CAPACITY
		$wprtc_video['max_capacity'] = '';
		if( intval( $wprtc_video['shortcode_options']['max_capacity'] ) > 0 ) { 
			$wprtc_video['max_capacity'] = 'data-capacity="'.$wprtc_video['shortcode_options']['max_capacity'].'"';
		}
		
		// STYLING
		$inlineStyle = '<style>';
			$inlineStyle .= '.rtcVideoContainer { position: relative; height: auto; width: '.$rtcOptions['rtcW'].'; }';
			$inlineStyle .= 'video.rtcVideoPlayer{ background: '.$rtcOptions['rtcBG'].'; border: '.$rtcOptions['rtcBW'].' solid '.$rtcOptions['rtcBC'].'; height: '.$rtcOptions['rtcH'].'; width: '.$rtcOptions['rtcW'].';}';
			$inlineStyle .= '.largeVideo{ height: '.$rtcOptions['rtcH'].'; width: '.$rtcOptions['rtcW'].';}';
			$inlineStyle .= '#remoteVideos{ height: '.$rtcOptions['rtcRH'].'; width:'.$rtcOptions['rtcW'].'}';
			$inlineStyle .= '#remoteVideos video { float: left; height:100px; width:'.$rtcOptions['rtcRvW'].'}';
		$inlineStyle .= '</style>';
		
		// ECHO
		ob_start();
		
		// STYLE OVERRIDES
		echo $inlineStyle;
		
		// TEMPLATE
		
		include( $this->get_template() );
				
		return ob_get_clean();
	}
	
	function __privacy_check( $privacy ) {
		if( is_user_logged_in() ) { return false; }
		
		$private = false;
		if( $privacy == 'on'|| $privacy == 'On' || $privacy == 'ON'  ) { $private = true; }
		if( get_option('rtc_main_private') === '1' && !is_user_logged_in() ) { $private = true; }
		
		return $private;
	}
	
	public function get_template() {
		
		if( file_exists( get_stylesheet_directory() .  '/video-template.php' ) ) {
			return get_stylesheet_directory() .  '/video-template.php';
		} else {
			return WPWebRTCPath . 'video-template.php';
		}
		
	}
	
	public function room_name( $a ) {
		
		// SET ROOM BY REQUEST
		if(isset($_REQUEST['roomName'])){ 
			return $_REQUEST['roomName'];			
		}
		
		if( strpos( $a['room_name'], ',' ) !== false && isset( $a['default_room'] ) ) {
			return $a['default_room'];
		}
		
		
		if( strpos( $a['room_name'], ',' ) !== false && !isset($_REQUEST['roomName']) ){
			$rooms = explode(',', $a['room_name']);
			return $rooms[0]; 	
		} elseif($a['room_name'] !== '' && !isset($_REQUEST['roomName'])) {
			return $a['room_name']; 
		}
		else {
			return 'default_room';
		}
		
	}

}

?>