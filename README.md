# Air Notifications

The basic idea is to show notifications in one or more locations, the logic being similar to WordPress menu locations. You can register as many notice locations and types you want.

## Requirements

CMB2 plugin installed and activated or CMB2 loaded with composer.

## Register locations

There is one location called `default` registered by default. To add more locations, use `air_notifications_register-locations` -filter.

```
<?php
  add_filter( 'air_notifications_register_locations', 'add_notification_locations', 10, 1 );

  function add_notification_locations( $locations ) {
    $locations[] = [
      'top-bar' => [
        'name' => __( 'Top bar', 'air-notifications' ),
      ]
    ];
    return $locations;
  }
?>
```

## Basic usage in a template

This prints all notifications saved with the location

```
<div class="notification-wrapper">
  <?php do_action( 'air_notifications_do_notifications', [ 'location' => 'top-bar' ] ); ?>
</div>
```

## Set notice types

```
  add_filter( 'air_notifications_register_types', function( $types ) {
    return [
      'notice' => [
        'prepend' => __( 'Notice: ', 'air-notifications'),
        'append' => '',
      ],
      'alert'  => [
        'prepend' => __( 'Alert: ', 'air-notifications'),
        'append'  => '',
      ],
    ];
  }, 10, 1);
```

## Add notices via code

You can also create notices manually in templates, for example in form validation

```
<div class="notification-wrapper">
  <?php do_action(
    'air_notifications_do_notifications',
    [
      'notifications' => [
        [
          'ID'          => 12314214, // This should be unique'ish
          'content'     => 'Notification content',
          'type'        => 'alert',
          'dismissable' => true,
          'cookie'      => false,
        ],
      ],
    ]
  ); ?>
</div>
```
