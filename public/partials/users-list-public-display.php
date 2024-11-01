<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://walledmahmoud.github.io/WalledMahmoud/
 * @since      1.0.0
 *
 * @package    Users_List
 * @subpackage Users_List/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap users-list">

    <div class="tablenav top">
        <div class="users-roles-div">
            <ul class="users-roles-nav">
                <?php echo $this->users_role_filter(); ?>
            </ul>
        </div>
        <div class="users-list-pagination">
            <?php echo $this->users_lists_pagination(); ?>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="users-list-table">
            <thead>
                <tr>
                    <th scope="col" id="avatar" class="column-avatar"><?php esc_html_e('Avatar', 'users-list'); ?></th>
                    <th scope="col" id="username" class="column-username column-primary <?php echo $this->prepare_sortable_classes('user_name'); ?>">
                        <a href="<?php echo esc_url($sort_link_username); ?>" data-sort-order="<?php echo $this->prepare_new_orderdir('user_name'); ?>" data-sort-orderby="user_name">
                            <span><?php esc_html_e('Username', 'users-list'); ?> <i class="fa fa-sort"></i>
                        </a>
                    </th>
                    <th scope="col" id="name" class="column-name <?php echo $this->prepare_sortable_classes('display_name'); ?>">
                        <a href="<?php echo esc_url($sort_link_displayname); ?>" data-sort-order="<?php echo $this->prepare_new_orderdir('display_name'); ?>" data-sort-orderby="display_name">
                            <span><?php esc_html_e('Name', 'users-list'); ?></span> <i class="fa fa-sort"></i>
                    </th>
                    <th scope="col" id="email" class="column-email"><?php esc_html_e('Email', 'users-list'); ?></th>
                    <th scope="col" id="role" class="column-role"><?php esc_html_e('Role', 'users-list'); ?></th>
                </tr>
            </thead>

            <template id="user_table_row" style="display: none;">
                <tr>
                    <td id="user_avatar"></td>
                    <td><strong><a id="user_name_link"></a></strong></td>
                    <td id="display_name"></td>
                    <td id="user_email"></td>
                    <td id="user_roles"></td>
                </tr>
            </template>

            <template id="user_table_noresults" style="display: none;">
                <tr class="no-items">
                    <td class="colspanchange" colspan="4"><?php esc_html_e('No Users found', 'users-list'); ?></td>
                </tr>
            </template>

            <tbody id="list_table_body">

                <?php
                if ($this->user_elements) {
                    foreach ($this->user_elements as $user) { ?>
                <tr>
                    <td><?php echo get_avatar($user->ID, 64); ?></td>
                    <td><strong><a href="<?php echo get_edit_user_link($user->ID); ?>"><?php echo $user->user_login; ?></a></strong></td>
                    <td><?php echo $user->display_name; ?></td>
                    <td><?php echo $user->user_email; ?></td>
                    <td><?php echo $this->format_roles($user->roles); ?></td>
                </tr>
                <?php
                }
            } else { ?>
                <tr class="no-items">
                    <td class="colspanchange" colspan="4"><?php esc_html_e('No Users found', 'users-list'); ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>            
</div>