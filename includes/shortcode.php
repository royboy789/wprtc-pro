<?php


class wprtc_shortcode {
	
	function __construct() {
		add_shortcode( array( $this ), 'webRTCsc' );
	}

	function webRTCscripts() {
		// Simple WebRTC Core
		wp_enqueue_script('simple-core', 'http://simplewebrtc.com/latest.js', array('jquery'), null, false);
		wp_enqueue_script('wpRTC', plugin_dir_url( __FILE__ ).'js/wpRTC.js', array('simple-core'), null, false);
		
		// FONT AWEOMSE
		wp_enqueue_style('fontAwesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css', null, false);
		
	}
	
	function webRTCsc( $atts ){
		
		// SHORTCODE ATTS
		$a = shortcode_atts( array(
			'room_name' => '',
			'room_title' => '',
			'privacy' => 'off',
			'max_capacity' => 0
		), $atts );
	
		// ROOM NAME
		if( strpos( $a['room_name'], ',' ) !== false && !isset($_POST['roomName']) ){
			$rooms = explode(',', $a['room_name']);
			$roomName = $rooms[0]; 	
		} elseif($a['room_name'] !== '' && !isset($_POST['roomName'])) { $roomName = $a['room_name']; }
		else {
			$roomName = 'default_room';
		}
	
		if( strpos( $a['room_name'], ',' ) !== false ) {
			$rooms = explode(',', $a['room_name']);
			$select = '<form id="roomChange" method="post"><select name="roomName"><option value="-1" selected="selected">Change Rooms</option>';
				foreach($rooms as $room){ $select .= '<option value="'.$room.'">'.$room.'</option>'; }
			$select .= '</select></form>';
		}
	
		if(isset($_POST['roomName'])){ $roomName = $_POST['roomName']; }
	
		
		// PLUGIN DEFAULTS
		$rtcOptions = array(
			'rtcBG' => '#000',
			'rtcBC' => '#000',
			'rtcBW' => '2px',
			'rtcW' => '100%',
			'rtcH' => '500px',
			'rtcRH' => '200px',
			'rtcRvW' => '100px',
			'private_msg' => 'You must be logged in to view this video stream',
			'rtcClass' => ''
		);
		
		if(get_option('rtcBG')) { $rtcOptions['rtcBG'] = get_option('rtcBG'); }
		if(get_option('rtcBC')) { $rtcOptions['rtcBC'] = get_option('rtcBC'); }
		if(get_option('rtcBW')) { $rtcOptions['rtcBW'] = get_option('rtcBW'); }
		if(get_option('rtcW')) { $rtcOptions['rtcW'] = get_option('rtcW'); }
		if(get_option('rtcH')) { $rtcOptions['rtcH'] = get_option('rtcH'); }
	
		if(get_option('rtcRH')) { $rtcOptions['rtcRH'] = get_option('rtcRH'); }
		if(get_option('rtcRvW')) { $rtcOptions['rtcRvW'] = get_option('rtcRvW'); }
	
		if(get_option('rtcClass')) { $rtcOptions['rtcClass'] = get_option('rtcClass'); }
		
		if(get_option('rtc_main_private_msg')) { $rtcOptions['private_msg'] = get_option('rtc_main_private_msg'); }
		
		if(get_option('rtc_main_private') === '1' && !is_user_logged_in() || $a['privacy'] == 'on' ) { 
			ob_start();
			echo '<p>'.$rtcOptions['private_msg'].'</p>';
			return ob_get_clean();
		} else {
			webRTCscripts();
		}
		$maxCap = '';
		if( intval($a['max_capacity']) > 0 ) { $maxCap = 'data-capacity="'.$a['max_capacity'].'"';}
		
		$inlineStyle = '<style>';
			$inlineStyle .= '.rtcVideoContainer { position: relative; height: auto; width: '.$rtcOptions['rtcW'].'; }';
			$inlineStyle .= 'video.rtcVideoPlayer{ background: '.$rtcOptions['rtcBG'].'; border: '.$rtcOptions['rtcBW'].' solid '.$rtcOptions['rtcBC'].'; height: '.$rtcOptions['rtcH'].'; width: '.$rtcOptions['rtcW'].';}';
			$inlineStyle .= '.largeVideo{ height: '.$rtcOptions['rtcH'].'; width: '.$rtcOptions['rtcW'].';}';
			$inlineStyle .= '#remoteVideos{ height: '.$rtcOptions['rtcRH'].'; width:'.$rtcOptions['rtcW'].'}';
			$inlineStyle .= '#remoteVideos video { float: left; height:100px; width:'.$rtcOptions['rtcRvW'].'}';
		$inlineStyle .= '</style>';
		
		// ECHO
		ob_start();
		echo $inlineStyle;
		if($a['room_title'] !== '') { echo '<h2 class="videoTitle">'.$a['room_title'].'</h2>'; }
		echo '<div class="rtcVideoContainer '.$rtcOptions['rtcClass'].'">';
			echo '<div class="largeVideo"><video data-room="'.$roomName.'" data-maxCap="'.$a['max_capacity'].'" class="rtcVideoPlayer" id="localVideo" oncontextmenu="return false;" '.$maxCap.'></video></div>';
			if(isset($select)) { echo $select; }
			echo '<div id="remoteVideos"></div>';
		echo '</div>';
		return ob_get_clean();
	}

}

?>