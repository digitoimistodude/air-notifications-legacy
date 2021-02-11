<?php
/**
 * Plugin Name: Air notifications
 * Plugin URI: https://github.com/digitoimistodude/air-notifications
 * Description: Add simple notifications to your desired location
 * Version: 1.0.0
 * Author: Digitoimisto Dude Oy, Niku Hietanen
 * Author URI: https://www.dude.fi
 * Requires at least: 5.0
 * Tested up to: 5.6
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Text Domain: air-notifications
 * Domain Path: /languages
 *
 * @package air-notifications
 */

namespace Air_Notifications;

require __DIR__ . '/inc/notifications.php';
require __DIR__ . '/inc/scripts.php';
require __DIR__ . '/inc/post-type.php';
require __DIR__ . '/inc/custom-meta-box.php';

// Register scripts
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\register_scripts' );

// Register the notification action
add_action( 'air_notifications_do_notifications', __NAMESPACE__ . '\do_notifications', 10, 1 );

// Register post type and meta boxes
add_action( 'init', __NAMESPACE__ . '\register_notification_post_type' );
add_action( 'cmb2_admin_init', __NAMESPACE__ . '\register_meta_boxes' );

// Update the notification title on post save
add_action( 'save_post_air-notification', __NAMESPACE__ . '\save_notification_title', 10, 1 );

add_action( 'init', function() {
  load_plugin_textdomain( 'air-notifications', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );
