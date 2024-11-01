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
 * This class is responsible for prepare users list table with users elements
 *
 * @since      1.0.0
 * @package    Users_List
 * @subpackage Users_List/includes
 * @author     Walled Mahmoud Soliman <walledm128@gmail.com>
 */
class Users_list_fetch {

    /**
	 * The users per page option
	 *
	 * @since    1.0.0
	 */
    public $page = 1;
    public $per_page = 10;
    public $total_pages = 0;


     /**
	 * The main query order
	 *
	 * @since    1.0.0
     */
    public $order = 'asc';
    public $orderby = 'user_name';
    public $columns_order = [ 'user_name', 'display_name' ];

    /**
	 * Users elements
	 *
	 * @since    1.0.0
	 */
    public $user_elements;
    public $all_user_elements = 0;

    /**
	 * Users roles
	 *
	 * @since    1.0.0
     */
    public $role = '';

    /**
    * Outputs users list page HTML.
     */
    public function users_list_page_content () {

        if ( ! current_user_can( 'list_users' )) {
            return;
        }

        $this->users_list_query();

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/users-list-public-display.php';
    }

    /**
    * Outputs the users list in a JSON format.
    * Callback for ajax request.
    * Protect your functions using current_user_can(), always assume Nonces can be compromised.
    * https://codex.wordpress.org/Function_Reference/wp_verify_nonce
     */
    public function load_users_list () {

        if ( ! current_user_can( 'list_users' ) ) {
            return;
        }

        $users_data = [];

        $this->users_list_query();

        if ( $this->user_elements ) {
            foreach ($this->user_elements as $user) {
                $users_data[] = [
                    'user_name'    => $user->user_login,
                    'user_link'    => get_edit_user_link( $user->ID ),
                    'display_name' => $user->display_name,
                    'user_email'   => $user->user_email,
                    'user_avatar'  =>  get_avatar( $user->ID, 64 ),
                    'user_roles'   => $this->format_roles( $user->roles )
                ];
            }
        }

        /**
        * Store the user elements.
        * Send JSON.
         */
        $result['user_elements'] = $users_data;
        $result['all_user_elements'] = $this->all_user_elements;
        $result['total_pages'] = $this->total_pages;
        $result['total_pages_formatted'] = number_format_i18n( $this->total_pages );

        wp_send_json( $result );
        die();
    }

    /**
    * Create users query. 
    * Filter option.
    * Sorting option.
     */
    private function users_list_query () {
        
        // The Role Query
        $role = $this->request( 'role' );
        if ( ! $role ) {
            $role__in = array();
        } else {
            $role__in = (array)$role;
        }
        $this->role = $role;

        //The Order Type Query
        $order = $this->request( 'order' );
        if ( ! in_array( $order, [ 'asc', 'desc' ] ) ) {
            $order = $this->order;
        }
        $this->order = $order;

        // The Order Query
        $orderby = $this->request( 'orderby' );
        if ( ! in_array( $orderby, $this->columns_order ) ) {
            $orderby = $this->orderby;
        }
        $this->orderby = $orderby;

        // Retrieves page number query argument
        $current_page = $this->request( 'paged' );
        if ( ! (int)$current_page ) {
            $current_page = 1;
        }
        $this->page = (int)$current_page;

        // Get the page offset .
        $offset = $this->per_page * $current_page - $this->per_page;

        /**
        ** The WP_User_Query
        * https://codex.wordpress.org/Class_Reference/WP_User_Query
         */
        $args = array(
            'count_total' => true,
            'orderby'     => $orderby,
            'order'       => $order,
            'offset'      => $offset,
            'number'      => $this->per_page,
            'role__in'    => $role__in
        );
        $query = new WP_User_Query( $args );

        $this->user_elements = $query->get_results();
        $this->all_user_elements = $query->get_total();
        $this->total_pages = ceil( $this->all_user_elements / $this->per_page );
    }

    /**
    * Prepare the roles with three elements
    * [name] - [title] - [count] 
     */
    public function prepare_users_list_roles () {

        $roles          = [];
        $all_roles      = count_users();
        $wp_roles       = wp_roles();
        $wp_roles_name  = $wp_roles->role_names;
        
        foreach ($wp_roles_name as $role_name => $role_title) {
            if ( array_key_exists( $role_name, $all_roles['avail_roles'] ) && (int)$all_roles['avail_roles'][ $role_name ] ) {

                $roles[ $role_name ] = [
                    'name'  => $role_name,
                    'title' => translate_user_role( $role_title ),
                    'count' => (int)$all_roles['avail_roles'][ $role_name ]
                ];
            }
        }
        return $roles;
    }

    /**
    * Creates sort link with current filter options
    *
    * @param $orderby          Field to order by
    * @param $current_orderby  Current query order by field
    * @param $current_order    Current query order Type
    *
    * @return string       URL
     */
    public function sort_link ( $orderby, $current_orderby, $current_order ) {

        if ( $orderby == $current_orderby ) {
            $order = $current_order == 'asc' ? 'desc' : 'asc';
        } else {
            $order = 'asc';
        }

        // Query with new arguments
        $query_string = add_query_arg( 'orderby', $orderby, $_SERVER['QUERY_STRING'] );
        $query_string = add_query_arg( 'order', $order, $query_string );
        $query_string = remove_query_arg( 'paged', $query_string );

        return rtrim( get_permalink(),'/' ) . $query_string;
    }

    /**
    * Prepares class names for column header depending on current query sorting options.
    *
    * @param $orderby      Order field name.
    *
    * @return string       Class names string.
     */
    public function prepare_sortable_classes ( $orderby ) {

        if ( $orderby == $this->orderby ) {
            $class[] = 'sorted';

            if ( $this->order == 'desc' ) {
                $class[] = 'desc';
            } else {
                $class[] = 'asc';
            }
        } else {
            $class[] = 'sortable desc';
        }

        return join( ' ', $class );
    }

    /**
    * ASC to DESC
    * DESC to ASC
    * @param $orderby
    *
    * @return string       ASC | DESC
    */
    public function prepare_new_orderdir ( $orderby ) {

        if ( $orderby == $this->orderby ) {
            if ( $this->order == 'desc' ) {
                $order = 'asc';
            } else {
                $order = 'desc';
            }
        } else {
            $order = 'asc';
        }

        return $order;
    }

    /**
    * Generates select <option> tags depending on current query total page number.
    *
    * @return null|string      Options tags HTML
    */
    public function prepare_pagination_select_options () {
        
        if ( ! $this->total_pages ) {
            return null;
        }

        $select_tag = '';

        for( $i = 1; $i <= $this->total_pages; $i++ ){

            if( $i === $this->page ){
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            $select_tag .= '<option value="'. $i .'" '. $selected .'>'. $i .'</option>';
        }

        return $select_tag;
    }

    /**
    * Return role link with current sorting options.
    *
    * @param string $role      (Optional) Role ID
    *
    * @return string       URL
    */
    public function role_link ( $role = '' ) {

        $query_string = remove_query_arg( 'paged', $_SERVER['QUERY_STRING'] );

        if ( $role ) {
            $query_string = add_query_arg( 'role', $role, $query_string );
        } else {
            $query_string = remove_query_arg( 'role', $query_string );
        }

        return rtrim( get_permalink(),'/' ) . $query_string;
    }

    /**
    * Generates roles filter navigation.
    *
    * @return null|string      Roles navigation HTML
    */
    public function users_role_filter () {

        $roles = $this->prepare_users_list_roles();
        
        if ( ! $roles ) {
            return null;
        }

        $current_role = $this->request( 'role' );

        $output = '<li><a href="'. esc_url( $this->role_link() ) .'" class="'. ( ! $current_role ? 'current' : '' ) .'" data-filter-role="">'. esc_attr__( 'All', 'users_list' ) .' <span class="count">('. $this->all_user_elements .')</span></a> |</li> ';

        foreach ($roles as $role) {

            if ( $role['name'] == $current_role ) {
                $current_class = 'current';
            } else {
                $current_class = '';
            }
            $output .= '<li><a href="'. esc_url( $this->role_link( $role['name'] ) ) . '" class="' . $current_class . '" data-filter-role="' . $role['name'] . '">' . $role['title'] . ' <span class="count">('. $role['count'] .')</span></a> <span class="separator">|</span></li> ';
        }

        return $output;
    }

    /**
    * Generates pagination content.
    * @return string       Pagination HTML.
    */
    public function users_lists_pagination () {

        if ( ! $this->all_user_elements ) {
            return null;
        }

        $query_string = $_SERVER['QUERY_STRING'];
        $base_url = rtrim( get_permalink(), '/');

        if ( (int)$this->total_pages === 1 ) {
            $paginator_class = 'one-page';
        } else {
            $paginator_class = '';
        }

        $output = '<div class="tablenav-pages '. $paginator_class .'">';

        $output .= '<span class="pagination-links">';

        // Disable & Enable previous page link
        if ( $this->page <= 1 ) {
            $disabled = 'disabled="disabled"';
        } else {
            $disabled = '';
        }

        // Previous Page
        $output .= sprintf( '<a class="prev-page" %s href="%s"><span aria-hidden="true">%s</span></a> ',
            $disabled,
            esc_url( $base_url . add_query_arg( 'paged', $this->page - 1, $query_string ) ),
            '<i class="fa fa-chevron-left"></i>'
        );

        $output .= '<span class="paging-input">';

        // Current page selector
        $output .= '<select name="paged" class="current-page-selector">'. $this->prepare_pagination_select_options() . '</select>';

        // Total pages label
        $html_total_pages = sprintf( '<span class="total-pages">%s</span>', number_format_i18n( $this->total_pages ) );
        $output .= sprintf( _x( '%1$s of %2$s', 'paging', 'users_list' ),
            '<span class="tablenav-paging-text">',
            $html_total_pages ) . '</span></span> ';

        // Disable/enable next page link
        if ( $this->page + 1 > $this->total_pages ) {
            $disabled = 'disabled="disabled"';
        } else {
            $disabled = '';
        }

        // Next...
        $output .= sprintf( '<a class="next-page" %s href="%s"><span aria-hidden="true">%s</span></a> ',
            $disabled,
            esc_url( $base_url . add_query_arg( 'paged', $this->page + 1, $query_string ) ),
            ' <i class="fa fa-chevron-right"></i>'
        );

        $output .= '</div>';

        return $output;
    }

    /**
    * Formats user roles array for displaying on front-end.
    *
    * @param $role_array
    *
    * @return null|string
    */
    public function format_roles ( $role_array ) {

        $roles = $this->prepare_users_list_roles();

        if ( ! $role_array || ! is_array( $role_array ) ) {
            return null;
        }
        $array = [];

        foreach ($role_array as $role_name) {
            $array[] = $roles[ $role_name ]['title'];
        }
        if ( $array ) {
            return implode( ', ', $array );
        } else {
            return "null";
        }
    }

    /**
    * Checks if value exists in POST/GET variable.
    *
    * @param $key      POST or GET argument
    *
    * @return mixed|null
    */
    private function request ( $key ) {
        
        if ( isset( $_POST[ $key ] ) ) {
            return filter_input( INPUT_POST, $key );
        } elseif ( isset( $_GET[ $key ] ) ) {
            return filter_input( INPUT_GET, $key );
        } else {
            return null;
        }
    }

}
