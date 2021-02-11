<?php
/**
 * General functionality
 *
 * @Author: Niku Hietanen
 * @Date: 2020-06-25 15:19:26
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2020-10-07 11:28:38
 * @package air-notifications
 */

namespace Air_Notifications;

/**
 * Build notifications
 *
 * @param Array $args Arguments
 * @return String Notification markup
 */
function do_notifications( $args ) {
  // Enqueue script only if we're doing notifications
  \wp_enqueue_script( 'air-notifications' );

  $default_args = [
    'location' => 'default',
  ];

  $args = \wp_parse_args( $args, $default_args );

  // Check if these are manually added notifications, otherwise load from CPT
  $notifications = isset( $args['notifications'] ) ? $args['notifications'] : get_notifications( $args['location'] );

  if ( empty( $notifications ) ) {
    return;
  }

  $output = '<div class="notification-wrapper">';

  foreach ( $notifications as $notification ) {
    $output .= single_notification( $notification );
  }

  $output .= '</div>';

  echo $output; // phpcs:ignore

  return $output;
}

/**
 * Get notifications for certain notification location
 *
 * @param String $location Location slug
 * @return Array Notifications
 */
function get_notifications( $location ) {
  $settings = get_notification_settings();

  $args = [
    'post_type'  => 'air-notification',
    'meta_query' => [
      'relation' => 'AND',
      [
        'relation' => 'OR',
        [
          'key'     => 'air_notification_location',
          'value'   => $location,
          'compare' => '=',
        ],
        [
          'key'     => 'air_notification_location',
          'compare' => 'NOT EXISTS',
        ],
      ],
      [
        'relation' => 'OR',
        [
          'key'     => 'air_notification_schedule_end',
          'value'   => '',
          'compare' => '=',
        ],
        [
          'key'     => 'air_notification_schedule_end',
          'value'   => time(),
          'compare' => '>',
        ],
        [
          'key'     => 'air_notification_schedule_end',
          'compare' => 'NOT EXISTS',
        ]
      ]
    ],
  ];

  $notification_posts = get_posts( $args );

  $notifications = array_map( function( $notification ) use ( $settings ) {
    return [
      'ID'          => $notification->ID,
      'content'     => get_post_meta( $notification->ID, 'air_notification_content', true ) ?: '',
      'cookie'      => get_post_meta( $notification->ID, 'air_notification_cookie', true ) ?: $settings['defaults']['cookie'],
      'dismissable' => get_post_meta( $notification->ID, 'air_notification_dismissable', true ) ?: $settings['defaults']['dismissable'],
      'type'        => get_post_meta( $notification->ID, 'air_notification_type', true ) ?: $settings['defaults']['type'],
      'location'    => get_post_meta( $notification->ID, 'air_notification_location', true ) ?: $settings['defaults']['location'],
    ];
  }, $notification_posts );

  return \apply_filters( 'air_notifications_get_notifications', $notifications, $location );
}

/**
 * Build the notification markup
 *
 * @param Object $notification Notification post
 * @return String Notification markup
 */
function single_notification( $notification ) {
  $id = 'air-notification-' . $notification['ID'];

  $type_settings = get_notification_type( $notification['type'] );
  $notification_settings = get_notification_settings();

  $classes = 'air-notification';
  $classes .= ' air-notification--' . $notification['type'];
  $classes .= $notification['dismissable'] ? ' air-notification--dismissable' : '';

  $output = '<div class="' . $classes . '" id="' . $id . '" data-save-cookie="' . $notification['cookie'] . '" aria-hidden="true">';

  if ( $type_settings['prepend'] ) {
    $output .= '<span class="air-notification__prepend">' . $type_settings['prepend'] . '</span>';
  }

  $output .= '<span class="air-notification__content">' . wp_kses_post( $notification['content'] ) . '</span>';


  if ( $type_settings['append'] ) {
    $output .= '<span class="air-notification__append">' . $type_settings['append'] . '</span>';
  }

  if ( $notification['dismissable'] ) {
    $output .= '<button type="button" class="air-notification__close" data-notification-id="' . $id . '"><span aria-hidden="true">' . $notification_settings['dismissable_icon'] . '</span><span class="screen-reader-text">' . __( 'Close notification', 'air-notifications' ) . '</span></button>';
  }

  $output .= '</div>';

  return \apply_filters( 'air_notifications_single_notification', $output, $notification );
}

/**
 * Build the notification type list.
 *
 * @return Array List of notification types
 */
function get_notification_types() {
  // Set defaults
  $types = [
    'notice' => [
      'name'    => esc_html__( 'Notice', 'air-notifications' ),
      'prepend' => '<svg aria-hidden="true" class="exclamation-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.3 22.5" width="22.3" height="22.5"><path fill="none" stroke-width="1" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M21.7 21.1c.2.3.1.6-.1.8-.1.1-.3.1-.4.1h-20c-.3.1-.6-.2-.7-.5 0-.1 0-.3.1-.4L10.7.9c.2-.5.6-.5.9 0l10.1 20.2zM11.2 15.5v-7"/><path fill="none" stroke-width="1" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M11.2 17.5c-.1 0-.2.1-.2.3 0 0 0 0 0 0 0 .1.1.2.3.2h0c.1 0 .2-.1.2-.3 0 0 0 0 0 0-.1-.1-.2-.2-.3-.2h0"/></svg><span class="screen-reader-text">' . esc_html__( 'Notice: ', 'air-notifications' ) . '</span>',
      'append'  => '',
    ],
    'alert'  => [
      'name'    => esc_html__( 'Alert', 'air-notifications' ),
      'prepend' => esc_html__( 'Alert: ', 'air-notifications' ),
      'append'  => '',
    ],
  ];

  return \apply_filters( 'air_notifications_register_types', $types );
}

/**
 * Get the notification type
 *
 * @param String $type Notification type slug
 */
function get_notification_type( $type ) {
  $types = get_notification_types();

  if ( isset( $types[ $type ] ) ) {
    return $types[ $type ];
  }
  return false;
}

/**
 * Get the locations
 */
function get_locations() {
  $locations = [
    'default' => [
      'name' => esc_html__( 'Default location', 'air-notifications' ),
    ],
  ];

  return \apply_filters( 'air_notifications_register_locations', $locations );
}

/**
 * Get default settings
 *
 * @return Array Notification plugin settings
 */
function get_notification_settings() {
  $settings = [
    'dismissable_icon' => '<svg aria-hidden="true" width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M.5.5l23 23M23.5.5l-23 23"/></svg>',
    'user_selectable' => [
      'cookie'       => true,
      'dismissable'  => true,
      'location'     => true,
      'type'         => true,
      'schedule_end' => true,
    ],
    'defaults' => [
      'cookie'      => false,
      'dismissable' => true,
      'location'    => 'default',
      'type'        => 'notice',
    ],
  ];

  return apply_filters( 'air_notifications_settings', $settings );
}
