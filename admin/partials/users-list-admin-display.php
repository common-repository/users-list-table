<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://walledmahmoud.github.io/WalledMahmoud/
 * @since      1.0.0
 *
 * @package    Users_List
 * @subpackage Users_List/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrapper users-list-admin">

    <h1><i class="fa fa-users"><?php esc_html_e(' Users List', 'users-list'); ?></i></h1>
    <div class="home-content">

        <div class='users-list-offers'>
            <h2><?php esc_html_e('what can users list plugin offer ', 'users-list')?><i class="fa fa-question-circle"></i></h2>
            <ul class="users-list perks">
                <li><i class="fa fa-table"><?php esc_html_e(' Creates a custom HTML table to list the users.', 'users-list') ?></i></li>
                <li><i class="fa fa-sort"><?php esc_html_e(' Filter the users by role and order them in alphabetical order by display_name and username.', 'users-list') ?></i></li>
                <li><i class="fa fa-fighter-jet"><?php esc_html_e(' The role filter and ordering work via AJAX.', 'users-list') ?></i></li>
            </ul>
        </div>

        <div class='users-list-way'>
            <h2><?php esc_html_e('How it works ', 'users-list')?><i class="fa fa-exclamation-triangle"></i></h2>
            <ul class="users-list how">
                <li><i class="fa fa-code"><?php esc_html_e(' You can list the users in frontend through [users_list_table] shortcode.', 'users-list') ?></i></li>
                <li><i class="fa fa-heart"><?php esc_html_e(' To make it easier for you to test, we created a page called users-list with the shortcode directly and it will be deleted automatically if you deactivated the plugin, have fun.', 'users-list') ?></i></li>
            </ul>
        </div>

        <div class="show-users">
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'users-list' ) ) ); ?>"><?php esc_html_e( 'Show Users', 'users-list' ); ?></a>
        </div>

    </div>
</div>
