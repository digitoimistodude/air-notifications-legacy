<?php
/**
 * @Author: Niku Hietanen
 * @Date: 2020-06-26 14:00:00
 * @Last Modified by:   Timi Wahalahti
 * @Last Modified time: 2021-02-11 16:34:09
 * @package air-notifications
 */

namespace Air_Notifications;

function register_meta_boxes() {
  $settings = get_notification_settings();

  $cmb = new_cmb2_box( [
      'id'              => 'air_notification_meta_box',
      'title'           => esc_html__( 'Notification', 'air-notifications' ),
      'object_types'    => [ 'air-notification' ],
      'context'         => 'normal',
      'priority'        => 'high',
      'remove_box_wrap' => true,
      'show_names'      => true,
   ] );

  $cmb->add_field(
    [
    'name'        => esc_html__( 'Content', 'air-notifications' ) . ' <span class="required">*</span>',
    'id'          => 'air_notification_content',
    'type'        => 'wysiwyg',
    'options'     => [
      'editor_class'  => 'cmb2-required',
      'wpautop'       => false,
      'media_buttons' => false,
      'textarea_rows' => 5,
      'teeny'         => true,
      'required'      => true,
      'tinymce'       => [
        'paste_remove_styles' => true,
        'block_formats'       => '',
        'toolbar1'            => 'bold,italic,link,unlink',
        'forced_root_block'   => false,
        ],
      ],
    ]
  );

  if ( $settings['user_selectable']['schedule_end'] ) {
    $cmb->add_field(
      [
        'name'        => esc_html__( 'Enabled until', 'air-notifications' ),
        'description' => esc_html__( 'Hide the notification after scheduled time', 'air-notifications' ),
        'id'          => 'air_notification_schedule_end',
        'type'        => 'text_datetime_timestamp',
        'date_format' => get_option( 'date_format' ),
        'time_format' => get_option( 'time_format' ),
      ]
    );
  }

  if ( $settings['user_selectable']['location'] ) {

    $locations = get_locations();

    $location_names = array_map( function( $location ) {
      return $location['name'];
    }, $locations );

    $cmb->add_field( [
      'name'             => esc_html__( 'Location', 'air-notifications' ),
      'description'      => esc_html__( 'Select the location this notice is shown in', 'air-notifications' ),
      'id'               => 'air_notification_location',
      'type'             => 'select',
      'show_option_none' => false,
      'default'          => $settings['defaults']['location'],
      'required'         => true,
      'options'          => $location_names,
    ] );

  }

  if ( $settings['user_selectable']['type'] ) {
    $types = get_notification_types();

    $types = array_map( function( $type ) {
      return $type['name'];
    }, $types );

    $cmb->add_field(
      [
        'name'             => esc_html__( 'Type', 'air-notifications' ),
        'id'               => 'air_notification_type',
        'type'             => 'select',
        'show_option_none' => false,
        'default'          => $settings['defaults']['type'],
        'required'         => true,
        'options'          => $types,
      ]
    );
  }

  if ( $settings['user_selectable']['dismissable'] ) {

    $cmb->add_field(
      [
        'name'    => esc_html__( 'Dismissable', 'air-notifications' ),
        'desc'    => esc_html__( 'User can dismiss the notification', 'air-notifications' ),
        'id'      => 'air_notification_dismissable',
        'default' => isset( $_GET['post'] ) ? '' : $settings['defaults']['dismissable'], // Set default only on new posts
        'type'    => 'radio_inline',
        'options' => [
          'on'  => esc_html__( 'Yes', 'air-notifications' ),
          'off' => esc_html__( 'No', 'air-notifications' ),
        ],
      ]
    );

  }

  if ( $settings['user_selectable']['cookie'] ) {

    $cmb->add_field(
      [
        'name'    => esc_html__( 'One-time', 'air-notifications' ),
        'desc'    => esc_html__( 'User will not see the notification again after dismissing it', 'air-notifications' ),
        'id'      => 'air_notification_cookie',
        'default' => isset( $_GET['post'] ) ? '' : $settings['defaults']['cookie'], // Set default only on new posts
        'type'    => 'radio_inline',
        'options' => [
          'off'  => esc_html__( 'Yes', 'air-notifications' ),
          'on' => esc_html__( 'No', 'air-notifications' ),
        ],
      ]
    );

  }
}
