<?php
/**
 *
 * @link       https://walledmahmoud.github.io/WalledMahmoud/
 * @since      1.0.0
 *
 * @package    Users_List
 * @subpackage Users_List/includes
 */

/**
 * This class handles the users list table shortcode registration.
 *
 * @since      1.0.0
 * @package    Users_List
 * @subpackage Users_List/includes
 * @author     Walled Mahmoud Soliman <walledm128@gmail.com>
 */
class Users_List_Table_Shortcode {

    public $shortcode = 'users_list_table';

    public function register_shortcode_init() {
        add_shortcode($this->shortcode, array($this, 'users_list_shortcode'));
    }

  /**
  * @return string The shortcode output
  */
    public function users_list_shortcode() {
        $table = new Users_list_fetch();
        return $table->users_list_page_content();
    }

} 
