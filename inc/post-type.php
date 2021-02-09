<?php
/**
 * Register post types
 *
 * @Author: Niku Hietanen
 * @Date: 2020-06-26 13:10:21
 * @Last Modified by:   Timi Wahalahti
 * @Last Modified time: 2020-10-06 13:53:58
 * @package air-notifications
 */

namespace Air_Notifications;

use WP_Error;

/**
 * Register notification post type
 */
// Register Custom Post Type
function register_notification_post_type() {

	$labels = [
		'name'                  => _x( 'Notifications', 'Post Type General Name', 'air-notifications' ),
		'singular_name'         => _x( 'Notification', 'Post Type Singular Name', 'air-notifications' ),
		'menu_name'             => __( 'Notifications', 'air-notifications' ),
		'name_admin_bar'        => __( 'Notification', 'air-notifications' ),
		'all_items'             => __( 'All Notifications', 'air-notifications' ),
		'add_new_item'          => __( 'Add New Notification', 'air-notifications' ),
		'add_new'               => __( 'Add New', 'air-notifications' ),
		'new_item'              => __( 'New Notification', 'air-notifications' ),
		'edit_item'             => __( 'Edit Notification', 'air-notifications' ),
		'update_item'           => __( 'Update Notification', 'air-notifications' ),
		'view_item'             => __( 'View Notification', 'air-notifications' ),
		'view_items'            => __( 'View Notifications', 'air-notifications' ),
		'search_items'          => __( 'Search Notifications', 'air-notifications' ),
		'not_found'             => __( 'Not found', 'air-notifications' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'air-notifications' ),
		'items_list'            => __( 'Notification list', 'air-notifications' ),
		'items_list_navigation' => __( 'Notification list navigation', 'air-notifications' ),
		'filter_items_list'     => __( 'Filter notification list', 'air-notifications' ),
  ];
	$args = [
		'label'                 => __( 'Notification', 'air-notifications' ),
		'labels'                => $labels,
		'supports'              => [ 'custom-fields' ],
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 20,
		'menu_icon'             => 'dashicons-warning',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'rewrite'               => false,
		'capability_type'       => 'page',
		'show_in_rest'          => false,
  ];
	register_post_type( 'air-notification', $args );
}

/**
 * Take notification title from text content
 *
 * @param String $post_id Post ID
 */
function save_notification_title( $post_id ) {
  if ( wp_is_post_revision( $post_id ) ) {
    return;
  }

  $notification_content = get_post_meta( $post_id, 'air_notification_content' , true );

  if ( ! $notification_content ) {
    return;
  }

  // Unhook to prevent forever looping this hook
  remove_action( 'save_post_air-notification',  __NAMESPACE__ . '\save_notification_title' );

  wp_update_post(
    [
      'ID' => $post_id,
      'post_title' => strip_tags( $notification_content ),
    ]
  );
}
