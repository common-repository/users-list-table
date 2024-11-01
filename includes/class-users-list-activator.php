<?php

/**
 * Fired during plugin activation
 *
 * @link       https://walledmahmoud.github.io/WalledMahmoud/
 * @since      1.0.0
 *
 * @package    Users_List
 * @subpackage Users_List/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Users_List
 * @subpackage Users_List/includes
 * @author     Walled Mahmoud Soliman <walledm128@gmail.com>
 */
class Users_List_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		/**
		* Create Page Users List On Activation
		 */
		if ( ! current_user_can( 'activate_plugins' ) ) return;

		set_transient( '_users_list_screen_activation_redirect', true, 30 );  
		
		global $wpdb;
		
		if ( null === $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'users-list'", 'ARRAY_A' ) ) {
		   
		  $current_user = wp_get_current_user();
		  
		  $content_shortcode = new Users_List_Table_Shortcode();

		  $page = array(
			'post_title'  => __( 'Users List' ),
			'post_status' => 'publish',
			'post_content'  => '['. $content_shortcode->shortcode .']',
			'post_author' => $current_user->ID,
			'post_type'   => 'page',
		  );
		  
		  // insert the post into the database
		  wp_insert_post( $page );
		}
	}

	public function users_list_screen_activation_redirect() {

		// Bail if no activation redirect
		if ( ! get_transient( '_users_list_screen_activation_redirect' ) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_users_list_screen_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;
		}
	
		// Redirect to users list page
		wp_safe_redirect( menu_page_url("users-list") );
		exit;
	}
}
