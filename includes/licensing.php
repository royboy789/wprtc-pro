<?php 

class wprtc_license {

    public function init(){
        global $wpdb;
        
        define( 'EDD_SAMPLE_STORE_URL', 'http://roysivan.com' );
        define( 'EDD_SAMPLE_ITEM_NAME', __('wpRTC PRO - webRTC for WordPress', 'wprtc') );

        add_action( 'admin_init', array( $this, 'licenseActivate' ) );
        add_action( 'admin_init', array( $this, 'licenseDeActivate' ) );
        add_action( 'admin_init', array( $this, 'edd_sample_register_option') );
        add_action( 'admin_init', array( $this, 'plugin_updater' ) );
        add_action( 'admin_init', array( $this, 'wprtc_license_check' ) );
        add_action( 'admin_menu', array( $this, 'licensePage' ) );
    }   

    function edd_sample_register_option() {
        // creates our settings in the options table
        register_setting( 'wprtc_license', 'wprtc_license', array( $this, 'sanitize_license' ) );
    }

    function sanitize_license( $new ) {
        $old = get_option( 'wprtc_license' );
        if( $old && $old != $new ) {
            delete_option( 'wprtc_license_status' ); // new license has been entered, so must reactivate
        }
        return $new;
    }

    function licensePage() {
        add_submenu_page( 'wp-rtc', 'wpRTC Licensing', 'wpRTC Pro License', 'manage_options', 'wp-rtc-license', array( $this, 'wprtc_license_page' ) );
    }

    function wprtc_license_page() {
        $license = get_option( 'wprtc_license' );
        $status = get_option( 'wprtc_license_status' );
    ?>
        <div class="wrap">
            <h2><?php _e('Plugin License Options', 'wprtc'); ?></h2>
            <form method="post">
            
                <?php settings_fields('wprtc_license'); ?>
                
                <table class="form-table">
                    <tbody>
                        <tr valign="top">   
                            <th scope="row" valign="top">
                                <?php _e('License Key', 'wprtc'); ?>
                            </th>
                            <td>
                                <input id="wprtc_license_key" name="wprtc_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
                                <label class="description" for="wprtc_license_key"><?php _e('Enter your license key', 'wprtc'); ?></label>
                            </td>
                        </tr>
                        <?php if( false !== $license ) { ?>
                            <tr valign="top">   
                                <th scope="row" valign="top">
                                    <?php _e('Activate License', 'wprtc'); ?>
                                    <br/>
                                    <?php if( $status !== false && $status == 'valid' ) { ?>
                                        <span style="color:green;"><?php _e('Your license is active', 'wprtc'); ?></span>
                                    <?php } else { ?>
                                        <span style="color:red;"><?php _e('Your license is not active, please activate your license', 'wprtc'); ?></span>
                                    <?php } ?>
                                </th>
                                <td>
                                    <?php if( $status !== false && $status == 'valid' ) { ?>
                                        <?php wp_nonce_field( 'edd_sample_nonce', 'edd_sample_nonce' ); ?>
                                        <br/>
                                        <input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php _e('Deactivate License', 'wprtc'); ?>"/>
                                    <?php } else {
                                        wp_nonce_field( 'edd_sample_nonce', 'edd_sample_nonce' ); ?>
                                        <input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License', 'wprtc'); ?>"/>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } else {
                            echo '<tr><td colspan="2">';
                            submit_button();
                            echo '</td></tr>';
                        } ?>
                    </tbody>
                </table>            
            </form>
        </div>
    <?php
    }

    function licenseActivate() {
        // listen for our activate button to be clicked
        if( isset( $_POST['edd_license_activate'] ) ) {

            // run a quick security check 
            if( ! check_admin_referer( 'edd_sample_nonce', 'edd_sample_nonce' ) )   
                return; // get out if we didn't click the Activate button

            // retrieve the license from the database
            $license = trim( $_POST['wprtc_license_key'] );

            // data to send in our API request
            $api_params = array( 
                'edd_action'=> 'activate_license', 
                'license'   => $license, 
                'item_name' => urlencode( EDD_SAMPLE_ITEM_NAME )
            );
            
            // Call the custom API.
            $response = wp_remote_get( add_query_arg( $api_params, EDD_SAMPLE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

            // make sure the response came back okay
            if ( is_wp_error( $response ) )
                return false;

            // decode the license data
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );
            
            // $license_data->license will be either "active" or "inactive"

            update_option( 'wprtc_license_status', $license_data->license );
            update_option( 'wprtc_license', $_POST['wprtc_license_key'] );

        }
    }

    function licenseDeActivate() {
        // listen for our activate button to be clicked
        if( isset( $_POST['edd_license_deactivate'] ) ) {

            // run a quick security check 
            if( ! check_admin_referer( 'edd_sample_nonce', 'edd_sample_nonce' ) )   
                return; // get out if we didn't click the Activate button

            // retrieve the license from the database
            $license = trim( get_option( 'wprtc_license' ) );
                

            // data to send in our API request
            $api_params = array( 
                'edd_action'=> 'deactivate_license', 
                'license'   => $license, 
                'item_name' => urlencode( EDD_SAMPLE_ITEM_NAME ) // the name of our product in EDD
            );
            
            // Call the custom API.
            $response = wp_remote_get( add_query_arg( $api_params, EDD_SAMPLE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

            // make sure the response came back okay
            if ( is_wp_error( $response ) )
                return false;

            // decode the license data
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );
            
            // $license_data->license will be either "deactivated" or "failed"
            if( $license_data->license == 'deactivated' )
                update_option( 'wprtc_license', '' );
                update_option( 'wprtc_license_status', 'invalid' );

        }
    }

    function plugin_updater() {

        // Initial Setup
        $license = get_option('wprtc_license');
        $status = get_option('wprtc_license_status');


        if( !$license ) { update_option('wprtc_license', ''); }
        if( !$status ) { update_option( 'wprtc_license_status', 'invalid' ); }

        if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
            include( dirname( __FILE__ ) . '/pluginUpdater.php' );
        }
        // retrieve our license key from the DB
        $license_key = trim( get_option( 'wprtc_license' ) );
		
        // setup location
		$location = __FILE__;
		$location = str_replace('includes/licensing.php', 'plugin.php', $location);
		
        // setup the updater
        $edd_updater = new EDD_SL_Plugin_Updater( EDD_SAMPLE_STORE_URL, $location, array( 
            'version'   => '2.0.1',
            'license'   => $license_key,
            'item_name' => EDD_SAMPLE_ITEM_NAME,
            'author'    => 'Roy Sivan'
        ));

    }
    
    function wprtc_license_check() {

		global $wp_version;
	
		$license = trim( get_option( 'wprtc_license' ) );
			
		$api_params = array( 
			'edd_action' => 'check_license', 
			'license' => $license, 
			'item_name' => urlencode( EDD_SAMPLE_ITEM_NAME ) 
		);
	
		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, EDD_SAMPLE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
	
	
		if ( is_wp_error( $response ) )
			return false;
	
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		if( $license_data && !$license_data->license == 'valid' ) {
			add_action( 'admin_notices', function() {
//				echo '<div class="error"><p><strong>wpRTC is not licensed.</strong> You can get your license from the order history page. <a href="/wp-admin/admin.php?page=wp-rtc-license">Activate wpRTC</a> to receive future updates.</p></div>';

				echo '<div class="error"><p>';
				printf(__('<strong>wpRTC is not licensed.</strong> You can get your license from the order history page. <a href="%1$s">Activate wpRTC</a> to receive future updates.', 'wprtc'),
					'/wp-admin/admin.php?page=wp-rtc-license'
					);
				echo '</p></div>';
			} );
		}
	}


}

?>