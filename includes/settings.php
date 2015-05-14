<?php

class wprtc_setting {
	
	function init() {
		add_action( 'admin_menu', array( $this, 'wprtc_menu' ) );
		add_action( 'admin_init', array( $this, 'wprtc_posthandler' ) );
	}
	
	function wprtc_posthandler() {
		
		if( isset( $_POST['_wprt_icecomm_save'] ) ) {
			update_option( 'wprtc_icecomm', $_POST['_wprtc_icecomm'] );
		}
		
	}
	
	function wprtc_menu() {
	    add_menu_page( 'wpRTC', 'wpRTC', 'manage_options', 'wp-rtc', array( $this, 'wprtc_main_options' ), 'dashicons-admin-generic', 81 );
	    add_submenu_page( 'wp-rtc', __('wpRTC Settings', 'wprtc'), __('wpRTC Settings', 'wprtc'), 'manage_options', 'wp-rtc-settings', array( $this, 'wprtc_settings_options' ) );
	    add_submenu_page( 'wp-rtc', __('wpRTC Styling', 'wprtc'), __('wpRTC Styling', 'wprtc'), 'manage_options', 'wp-rtc-css', array( $this, 'wprtc_css_options' ) );
	}
	
	
	
	function wprtc_main_options() {
	    if ( !current_user_can( 'manage_options' ) )  {
	        wp_die( __( 'You do not have sufficient permissions to access this page.', 'wprtc' ) );
	    }
	    
	    $wprtc_icecomm = get_option( 'wprtc_icecomm', '' );
	    
	    echo '<style> .feature-filter { padding: 20px; } strong { font-weight: bold; }</style>';
	    echo '<div class="wrap feature-filter">';
	        echo '<h1>' . __('wpRTC - Real Time Video Sharing for WordPress', 'wprtc') . '</h1>';
	        echo '<table width="100%" cellpadding="5" cellspacing="5" border="0">';
	            echo '<tr>';
					echo '<td width="50%" valign="top">';
	                	echo '<h2>' . __('ICECOMM API', 'wprct') . '</h2>';
	                	echo '<p> wpRTC is now powered by <a target="_blank" href="http://icecomm.io">Icecomm.io</a> a cross browser WebRTC library.';
	                	echo '<br />You will need to setup a FREE account on <a target="_blank" href="http://icecomm.io">Icecomm.io</a> to get an API key to use this plugin</p>';
	                	
	                	echo '<form method="post">';
	                	
	                		echo '<table cellpadding="5" cellspacing="0" border="0">';
	                			echo '<tr>';
	                				echo '<th valign="top"><label for="_wprtc_icecomm">Icecomm.io API Key</label></th>';
	                				echo '<td><input id="_wprtc_icecomm" name="_wprtc_icecomm" value="' . $wprtc_icecomm . '" style="display:block;width:100%" />';
	                				echo '<p class="description">You can find your API key on your account page once you have registered</p>';
	                				echo '</td>';
	                			echo '</tr>';
	                		echo '</table>';
							echo '<input class="button-primary" type="submit" value="Save API Key" name="_wprt_icecomm_save" />';
	                	echo '</form>';
	                    echo '<h3>' . __('Shortcode', 'wprtc') . '</h3>';
	                    echo '<p>' . __('The plugin comes with a built in shortcode to help you with setting up your videos easily', 'wprtc') . '</p>';
	                    echo '<pre><code>[wpRTC]</code></pre>';
	                    // ATTS
	                    echo '<h4>' . __('Shortcode Attributes', 'wprtc') . '</h4>';
	                    // room_title
	                    echo '<p><strong>room_title</strong> - ' .__('title over your video.', 'wprtc') . '<br/>'; // i18n: room_title is an attribute. Shouldn't be translated
	                    echo '<code>[wpRTC room_title="..."]</code><br/><em>' .__('leave blank to remove title.', 'wprtc') . '</em></p>';
	                    // room_name
	                    echo '<p><strong>room_name</strong> - ' . __('set up your room name or multiple rooms.', 'wprtc');
						echo '<p><code>[wpRTC room_name="testing"]</code> </p>';
	                    echo '<p><code>[wpRTC room_name="testing, testingAgain, anotherRoom"]</code> <br/><em>' . __('This feature adds in a drop down box under the video so users can change rooms using the same page and video code. wpRTC PRO', 'wprtc') . '</em></p>';
	                    // privacy
	                    echo '<p><strong>privacy</strong> - ' . __('override default settings.', 'wprtc') . '<br/>';
	                    echo '<code>[wpRTC privacy="on"]</code> - <em>' . __('wpRTC PRO', 'wprtc') . '</em></p>';
	                echo '</td>';
	                echo '<td valign="top">';
	                	echo '<h3>' . __('Documentation', 'wprtc' ). '</h3>';
	                	echo '<p>' . __('In depth documentation available online', 'wprtc') . '<br/>';
	                	echo '<a class="button-primary" href="http://www.roysivan.com/wp-webrtc" target="_blank">' . __('View Documentation', 'wprtc') . '</a></p>';
	                    echo '<h3>' . __('wpRTC PRO</h3>', 'wprtc');
	                    echo '<p>' . __('Upgrade to wpRTC PRO to get additional features and future release functionality', 'wprtc') . '</p>';
	                    echo '<h4>A' . __('dditional Features in wpRTC Pro', 'wprtc') . '</h4>';
	                    echo '<ul>';
	                        echo '<li>' . __('<strong>Multiple Rooms</strong> <br/> 1 shortcode, 1 video, multiple rooms', 'wprtc') . '</li>';
	                        echo '<li>' . __('<strong>Privacy Override</strong> <br/> Want all your video streams to be sign-in only, except a few? The override helps you do just that.', 'wprtc') . '</li>';
	                        echo '<li>' . __('<strong>Support</strong> <br/> Preferred support - <a target="_blank" href="http://www.roysivan.com/plugin-support">Plugin Support Page</a>', 'wprtc') . '</li>';
	                    echo '</ul>';
	                    echo '<h3><strong>' . __('Thank You</strong> - for purchasing wpRTC Pro</h3>', 'wprtc');
	                echo '</td>';
	            echo '</tr>';
	        echo '</table>';
	    echo '</div>';
	}
	
	function wprtc_settings_options() {
	
	    // Handle Saving
	    $toSave = array('rtc_main_private', 'rtc_main_private_msg');
	
	    foreach($_POST as $key => $value) {
	        if( in_array( $key, $toSave ) ) {
	            update_option($key, $value);
	        }
	    }
	    $currentVal = array();
	    foreach($toSave as $key) {
	        $currentVal[$key] = get_option($key);
	    }
	    echo '<style> .feature-filter { padding: 20px; } label,strong { font-weight: bold; } select,textarea{ width: 400px; } textarea {height: 200px; }</style>';
	    echo '<div class="wrap feature-filter">';
	        echo '<h2>' . __('wpRTC - Real Time Video Sharing for WordPress', 'wprtc') . '</h2>';
	        echo '<form name="wprtcSettings" method="post" action="">';
	            echo '<p><label>' . __('Private Videos', 'wprtc') . '</label><br/>';
	            echo '<select name="rtc_main_private">';
	                echo '<option value="1"';
	                    if($currentVal['rtc_main_private'] == '1') echo 'selected="selected"';
	                echo '>' . __('On', 'wprtc') . '</option>';
	                echo '<option value="0"';
	                    if($currentVal['rtc_main_private'] == '0') echo 'selected="selected"';
	                echo '>' . __('Off', 'wprtc') . '</option>';
	            echo '</select><br/><em>' . __('Turn this option on if you must be logged in to see video', 'wprtc') . '</em></p>';
	            if($currentVal['rtc_main_private'] == '1'):
	                echo '<p><label>Private Video Message</label><br/>';
	                echo '<textarea name="rtc_main_private_msg" placeholder="' . esc_attr( __('Videos are private', 'wprtc') ) . '">'.$currentVal['rtc_main_private_msg'].'</textarea>';
	                echo '<br/><em>' . __('Message displayed when video privacy is turned on', 'wprtc') . '</em></p>';
	            endif;
	        echo '<br/><input type="submit" value="' . esc_attr( __('Save Settings', 'wprtc') ) . '" class="button-primary" /></form>';
	    echo '</div>';
	
	}

//	function resetOptions() {
//		$rtcOptions = array(
//			'rtcBG'  => '#000',
//			'rtcBC'  => '#000',
//			'rtcBW'  => '2px',
//			'rtcW'   => '100%',
//			'rtcH'   => '500px',
//			'rtcRH'  => '200px',
//			'rtcRvW' => '100px',
//		);
//
//		foreach ( $rtcOptions as $key => $value ) {
//			update_option( $key, $value );
//		}
//	}
	
	function wprtc_css_options() {
	    if ( !current_user_can( 'manage_options' ) )  {
	        wp_die( __( 'You do not have sufficient permissions to access this page.', 'wprtc' ) );
	    }
	
	    // Handle Saving
	    $toSave = array('rtcBG', 'rtcBC', 'rtcBW', 'rtcW', 'rtcH', 'rtcRH', 'rtcRvW', 'rtcClass');
	
	    // Delete All
	    if( isset($_POST['deleteAllwpRTC']) && $_POST['deleteAllwpRTC'] == '1' ) {
	        foreach($toSave as $key) {
	            delete_option($key);
	        }
	    }
	
	    foreach($_POST as $key => $value) {
	        if( in_array( $key, $toSave ) ) {
	            update_option($key, $value);
	        }
	    }
	
	    $currentVal = array();
	    foreach($toSave as $key) {
	        $currentVal[$key] = get_option($key);
	    }
	  
	    echo '<table width="100%" cellpadding="5" cellspacing="0" border="0"><tbody>';
	        echo '<tr><td  valign="top"><form name="wprtcStyles" method="post" action="">';
	            echo '<div class="tabbedContent">';
	                echo '<ul>';
	                    echo '<li><a href="#tabLocal">' . __('Local Video Styling', 'wprtc') . '</a></li>';
	                    echo '<li><a href="#tabRemote">' . __('Remote Video Styling', 'wprtc') . '</a></li>';
	                    echo '<li><a href="#tabAdv">' . __('Advanced Styling', 'wprtc') . '</a></li>';
	                echo '</ul>';
	                echo '<div id="tabLocal">';
	                	echo '<h3>' . __('Background Color', 'wprtc') . '</h3>';
	                    echo '<p><label>' . __('Background Color', 'wprtc') . '</label><br/>';
	                    echo '<input name="rtcBG" placeholder="' . esc_attr( __('Video Background', 'wprtc') ) . '" class="color-picker" value="'.$currentVal['rtcBG'].'" /></p><hr/>';
	                	echo '<h3>' . __('Video Size', 'wprtc') . '</h3>';
	                    echo '<p><label>' . __('Video Width (i.e 500px)', 'wprtc') . '</label><br/>';
	                    echo '<input name="rtcW" placeholder="' . esc_attr( __('Video Width', 'wprtc') ) . '" value="'.$currentVal['rtcW'].'" /></p><hr/>';
	                    echo '<p><label>' . __('Video Height  (i.e 500px)', 'wprtc') . '</label><br/>';
	                    echo '<input name="rtcH" placeholder="' . esc_attr( __('Video Height', 'wprtc') ) . '" value="'.$currentVal['rtcH'].'" /></p><hr/>';
	                	echo '<h3>' . __('Video Border', 'wprtc') . '</h3>';
	                    echo '<p><label>' . __('Video Border Color', 'wprtc') . '</label><br/>';
	                    echo '<input name="rtcBC" placeholder="' . esc_attr( __('Border Color', 'wprtc') ) . '" value="'.$currentVal['rtcBC'].'" class="color-picker" /></p><hr/>';
	                    echo '<p><label>' . __('Video Border Width (i.e 2px)', 'wprtc') . '</label><br/>';
	                    echo '<input name="rtcBW" placeholder="' . esc_attr( __('Border Width', 'wprtc') ). '" value="'.$currentVal['rtcBW'].'" /></p><hr/>';
	                echo '</div>';
	                echo '<div id="tabRemote">';
	                    echo '<h3>' . __('Remove Video Styling', 'wprtc') . '</h3>';
	                    echo '<p><label>' . __('Remote Video Container Height (i.e 150px)', 'wprtc') . '</label><br/>';
	                    echo '<input name="rtcRH" placeholder="' . esc_attr( __('Container Height', 'wprtc') ) . '" value="'.$currentVal['rtcRH'].'" /></p><hr/>';
	                    echo '<p><label>' . __('Remote Video Width (i.e 150px)', 'wprtc') . '</label><br/>';
	                    echo '<input name="rtcRvW" placeholder="' . esc_attr( __('Video Width', 'wprtc') ) . '" value="'.$currentVal['rtcRvW'].'" /></p><hr/>';
	                echo '</div>';
	                echo '<div id="tabAdv">';
	                	echo '<h3>' . __('Advanced Styling', 'wprtc') . '</h3>';
	                	echo '<p><label>' . __('CSS Class for Video Wrapper', 'wprtc') . '</label><br/>';
	                	echo '<input name="rtcClass" placeholder="' . esc_attr( __('CSS class', 'wprtc') ) . '" value="'.$currentVal['rtcClass'].'"/><br/>';
	                	echo '<em>' . __('This will add an additional class to the rtcVideoContainer video wrapper div', 'wprtc') . '</p><hr/>';
	                echo '</div>';
	            echo '</div>';
	        echo '<br/><input type="submit" value="' . esc_attr( __('Save All Styling', 'wprtc') ) . '" class="button-primary" /></form>';
	        //echo '<form method="post"><input type="hidden" value="1" name="deleteAllwpRTC" /><input type="submit" class="button-primary" value="' . esc_attr( __('Reset Defaults', 'wprtc') ) . '" /></form>';
	        echo '</td></tr>';
	    echo '</tbody></table>';
	}

}
?>