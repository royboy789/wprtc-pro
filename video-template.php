<?php
	/**
	* 
	* $wprtc_video is an array of options to use
	* $wprtc_video['shortcode_options'] - default options from shortcode
	* $wprtc_video['room_name'] - current room name
	* $wprtc_video['wrapper_class'] - wrapper class
	* $wprtc_video['max_capacity'] - max capacity of room
	* $wprtc_video['room_select'] - select box of rooms
	*
	*/
	
	// CURRENT ROOM NAME
	echo '<h2 class="videoTitle">'.$wprtc_video['room_name'].'</h2>';
	
	// VIDEO CONTAINER
	echo '<div class="rtcVideoContainer '.$wprtc_video['wrapper_class'].'">';
	
		// MUTE CONTROLES
		echo '<div class="mute_controls">';
			echo '<a href="#" class="mute audio" title="mute audio"><span class="fa fa-microphone"></span></a>';
			echo '<a href="#" class="mute video" title="mute video"><span class="fa fa-power-off on"></span></a>';
		echo '</div>';
		
		// LOCAL VIDEO
		echo '<div class="largeVideo">';
			echo '<video autoplay data-room=" '.$wprtc_video['room_name'].'" oncontextmenu="return false;" class="rtcVideoPlayer localVideo" '.$wprtc_video['max_capacity'].'>';
			echo '</video>';
		echo '</div>';
		
		// CHANGE ROOM SELECT
		if(isset( $wprtc_video['room_select'] )) { 
			echo $wprtc_video['room_select']; 
		}
		
		// REMOTE VIDEO COLLECTION
		echo '<div id="remoteVideos"></div>';
		
	echo '</div>';
	
?>