<?php
/**
 * Setup menus in WP admin.
 *
 * @author 		ProSports
 * @category 	Admin
 * @package 	ProSports/Admin
 * @version     1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SP_Admin_Menus' ) ) :

/**
 * SP_Admin_Menus Class
 */
class SP_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_filter( 'admin_menu', array( $this, 'menu_clean' ), 5 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 6 );
		add_action( 'admin_menu', array( $this, 'config_menu' ), 7 );
		add_action( 'admin_menu', array( $this, 'overview_menu' ), 8 );
		add_action( 'admin_menu', array( $this, 'leagues_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'seasons_menu' ), 10 );

		add_action( 'admin_head', array( $this, 'menu_highlight' ) );
		add_action( 'admin_head', array( $this, 'menu_rename' ) );
		add_action( 'parent_file', array( $this, 'parent_file' ) );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );
		add_filter( 'prosports_sitemap_taxonomy_post_types', array( $this, 'sitemap_taxonomy_post_types' ), 10, 2 );
	}

	/**
	 * Add menu item
	 */
	public function admin_menu() {
		global $menu;

	    if ( current_user_can( 'manage_prosports' ) )
	    	$menu[] = array( '', 'read', 'separator-prosports', '', 'wp-menu-separator prosports' );

		$main_page = add_menu_page( __( 'ProSports', 'ProSports' ), __( 'ProSports', 'prosports' ), 'manage_prosports', 'prosports', array( $this, 'settings_page' ), apply_filters( 'dashicons-admin-generic', null ), '4' );
	}

	/**
	 * Add menu item
	 */
	public function overview_menu() {
		//add_submenu_page( 'prosports', __( 'Overview', 'prosports' ), __( 'Overview', 'prosports' ), 'manage_prosports', 'prosports-overview', array( $this, 'overview_page' ) );
	}

	/**
	 * Add menu item
	 */
	public function config_menu() {
		add_submenu_page( 'prosports', __( 'Configure', 'sportmanager' ), __( 'Configure', 'prosports' ), 'manage_prosports', 'prosports-config', array( $this, 'config_page' ) );
	}

	/**
	 * Add menu item
	 */
	public function leagues_menu() {
		//add_submenu_page( 'prosports', __( 'Competitions', 'prosports' ), __( 'Competitions', 'prosports' ), 'manage_prosports', 'edit-tags.php?taxonomy=sp_league');
	}

	/**
	 * Add menu item
	 */
	public function seasons_menu() {
		//add_submenu_page( 'prosports', __( 'Seasons', 'prosports' ), __( 'Seasons', 'prosports' ), 'manage_prosports', 'edit-tags.php?taxonomy=sp_season');
	}


	/**
	 * Highlights the correct top level admin menu item for post type add screens.
	 *
	 * @access public
	 * @return void
	 */
	public function menu_highlight() {
		global $typenow;
		$screen = get_current_screen();
		if ( $screen->id == 'edit-sp_role' )
			$this->highlight_admin_menu( 'edit.php?post_type=sp_player', 'edit-tags.php?taxonomy=sp_role&post_type=sp_player' );			
		elseif ( is_sp_config_type( $typenow ) )
			$this->highlight_admin_menu( 'prosports', 'prosports-config' );
		elseif ( $typenow == 'sp_calendar' )
			$this->highlight_admin_menu( 'edit.php?post_type=sp_event', 'edit.php?post_type=sp_calendar' );
		elseif ( $typenow == 'sp_table' )
			$this->highlight_admin_menu( 'edit.php?post_type=sp_team', 'edit.php?post_type=sp_table' );
		elseif ( $typenow == 'sp_list' )
			$this->highlight_admin_menu( 'edit.php?post_type=sp_player', 'edit.php?post_type=sp_list' );
	}

	/**
	 * Renames admin menu items.
	 *
	 * @access public
	 * @return void
	 */
	public function menu_rename() {
		global $menu, $submenu;

		if ( isset( $submenu['prosports'] ) && isset( $submenu['prosports'][0] ) && isset( $submenu['prosports'][0][0] ) )
			$submenu['prosports'][0][0] = __( 'Settings', 'prosports' );
	}

	public function parent_file( $parent_file ) {
		global $current_screen;
		$taxonomy = $current_screen->taxonomy;
		if ( in_array( $taxonomy, array( 'sp_league', 'sm_seasons' ) ) )
			$parent_file = 'prosports';
		return $parent_file;
	}

	/**
	 * Reorder the SP menu items in admin.
	 *
	 * @param mixed $menu_order
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$prosports_menu_order = array();

		// Get the index of our custom separator
		$prosports_separator = array_search( 'separator-prosports', $menu_order );

		// Get index of menu items
		$prosports_event = array_search( 'edit.php?post_type=sp_event', $menu_order );
		$prosports_team = array_search( 'edit.php?post_type=sp_team', $menu_order );
		$prosports_player = array_search( 'edit.php?post_type=sp_player', $menu_order );
		$prosports_staff = array_search( 'edit.php?post_type=sp_staff', $menu_order );

		// Loop through menu order and do some rearranging
		foreach ( $menu_order as $index => $item ):

			if ( ( ( 'prosports' ) == $item ) ):
				$prosports_menu_order[] = 'separator-prosports';
				$prosports_menu_order[] = $item;
				$prosports_menu_order[] = 'edit.php?post_type=sp_event';
				$prosports_menu_order[] = 'edit.php?post_type=sp_team';
				$prosports_menu_order[] = 'edit.php?post_type=sp_player';
				$prosports_menu_order[] = 'edit.php?post_type=sp_staff';
				unset( $menu_order[ $prosports_separator ] );
				unset( $menu_order[ $prosports_event ] );
				unset( $menu_order[ $prosports_team ] );
				unset( $menu_order[ $prosports_player ] );
				unset( $menu_order[ $prosports_staff ] );

				// Apply to added menu items
				$menu_items = apply_filters( 'prosports_menu_items', array() );
				foreach ( $menu_items as $menu_item ):
					$prosports_menu_order[] = $menu_item;
					$index = array_search( $menu_item, $menu_order );
					unset( $menu_order[ $index ] );
				endforeach;

			elseif ( !in_array( $item, array( 'separator-prosports' ) ) ) :
				$prosports_menu_order[] = $item;
			endif;

		endforeach;

		// Return order
		return $prosports_menu_order;
	}

	/**
	 * custom_menu_order
	 * @return bool
	 */
	public function custom_menu_order() {
		if ( ! current_user_can( 'manage_prosports' ) )
			return false;
		return true;
	}

	/**
	 * Clean the SP menu items in admin.
	 */
	public function menu_clean() {
		global $menu, $submenu, $current_user;

		// Find where our separator is in the menu
		foreach( $menu as $key => $data ):
			if ( is_array( $data ) && array_key_exists( 2, $data ) && $data[2] == 'edit.php?post_type=sp_separator' )
				$separator_position = $key;
		endforeach;

		// Swap our separator post type with a menu separator
		if ( isset( $separator_position ) ):
			$menu[ $separator_position ] = array( '', 'read', 'separator-prosports', '', 'wp-menu-separator prosports' );
		endif;

	    // Remove "Competitions" and "Seasons" links from Events submenu
		if ( isset( $submenu['edit.php?post_type=sp_event'] ) ):
			$submenu['edit.php?post_type=sp_event'] = array_filter( $submenu['edit.php?post_type=sp_event'], array( $this, 'remove_leagues' ) );
			$submenu['edit.php?post_type=sp_event'] = array_filter( $submenu['edit.php?post_type=sp_event'], array( $this, 'remove_seasons' ) );
		endif;

	    // Remove "Venues", "Competitions" and "Seasons" links from Teams submenu
		if ( isset( $submenu['edit.php?post_type=sp_team'] ) ):
			$submenu['edit.php?post_type=sp_team'] = array_filter( $submenu['edit.php?post_type=sp_team'], array( $this, 'remove_venues' ) );
			$submenu['edit.php?post_type=sp_team'] = array_filter( $submenu['edit.php?post_type=sp_team'], array( $this, 'remove_leagues' ) );
			$submenu['edit.php?post_type=sp_team'] = array_filter( $submenu['edit.php?post_type=sp_team'], array( $this, 'remove_seasons' ) );
		endif;

	    // Remove "Competitions" and "Seasons" links from Players submenu
		if ( isset( $submenu['edit.php?post_type=sp_player'] ) ):
			$submenu['edit.php?post_type=sp_player'] = array_filter( $submenu['edit.php?post_type=sp_player'], array( $this, 'remove_leagues' ) );
			$submenu['edit.php?post_type=sp_player'] = array_filter( $submenu['edit.php?post_type=sp_player'], array( $this, 'remove_seasons' ) );
		endif;

	    // Remove "Competitions" and "Seasons" links from Staff submenu
		if ( isset( $submenu['edit.php?post_type=sp_staff'] ) ):
			$submenu['edit.php?post_type=sp_staff'] = array_filter( $submenu['edit.php?post_type=sp_staff'], array( $this, 'remove_leagues' ) );
			$submenu['edit.php?post_type=sp_staff'] = array_filter( $submenu['edit.php?post_type=sp_staff'], array( $this, 'remove_seasons' ) );
		endif;

		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);

		if ( in_array( $user_role, array( 'sp_player', 'sp_staff', 'sp_event_manager', 'sp_team_manager' ) ) ):
			remove_menu_page( 'upload.php' );
			remove_menu_page( 'edit-comments.php' );
			remove_menu_page( 'tools.php' );
		endif;
	}

	/**
	 * Init the overview page
	 */
	public function overview_page() {
		include( 'views/html-admin-overview.php' );
	}

	/**
	 * Init the config page
	 */
	public function config_page() {
		include( 'views/html-admin-config.php' );
	}

	/**
	 * Init the settings page
	 */
	public function settings_page() {
		include_once( 'class-sp-admin-settings.php' );
		SP_Admin_Settings::output();
	}

	public function remove_add_new( $arr = array() ) {
		return $arr[0] != __( 'Add New', 'prosports' );
	}

	public function remove_leagues( $arr = array() ) {
		return $arr[0] != __( 'Competitions', 'prosports' );
	}

	public function remove_positions( $arr = array() ) {
		return $arr[0] != __( 'Positions', 'prosports' );
	}

	public function remove_seasons( $arr = array() ) {
		return $arr[0] != __( 'Seasons', 'prosports' );
	}

	public function remove_venues( $arr = array() ) {
		return $arr[0] != __( 'Venues', 'prosports' );
	}

	public static function highlight_admin_menu( $p = 'prosports', $s = 'prosports' ) {
		global $parent_file, $submenu_file;
		$parent_file = $p;
		$submenu_file = $s;
	}

	public static function sitemap_taxonomy_post_types( $post_types = array(), $taxonomy ) {
		$post_types = array_intersect( $post_types, sp_primary_post_types() );
		// Remove teams from venues taxonomy post type array
		if ( $taxonomy === 'sp_venue' && ( $key = array_search( 'sp_team', $post_types ) ) !== false ):
			unset( $post_types[ $key ] );
		endif;

		return $post_types;
	}
}

endif;

return new SP_Admin_Menus();