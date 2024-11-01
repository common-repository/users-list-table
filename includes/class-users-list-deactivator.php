<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://walledmahmoud.github.io/WalledMahmoud/
 * @since      1.0.0
 *
 * @package    Users_List
 * @subpackage Users_List/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Users_List
 * @subpackage Users_List/includes
 * @author     Walled Mahmoud Soliman <walledm128@gmail.com>
 */
class Users_List_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Remove Users List Page On Deactivation
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		
		$page_slug = 'users-list';
		$page = get_page_by_path($page_slug);
		if ( $page ) {
			wp_delete_post($page->ID, true);
		}
	}

}
