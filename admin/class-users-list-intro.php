<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://walledmahmoud.github.io/WalledMahmoud/
 * @since      1.0.0
 *
 * @package    Users_List
 * @subpackage Users_List/admin
 */

/**
 * This class responsible for create user sub-menu page to introduce the plugin
 *
 * @package    Users_List
 * @subpackage Users_List/admin
 * @author     Walled Mahmoud Soliman <walledm128@gmail.com>
 */
class Users_List_Admin_Intro {

	/**
	* This function to introduce the plugin under users menu
	*/
	public function setup_plugin_intro_menu() {

		//Add the menu to the Users set of menu items
		add_users_page(
			'Users List', 					    // The title to be displayed in the browser window for this page.
			'Users List',					    // The text to be displayed for this menu item
			'manage_options',					// Which type of users can see this menu item
			'users-list',			            // The unique ID - that is, the slug - for this menu item
			array( $this, 'introduce_users_list')// The name of the function to call when rendering this menu's page
		);

	}
	
	public function introduce_users_list( ) {
		require_once plugin_dir_path( __FILE__ ) . 'partials/users-list-admin-display.php';
	}


}
