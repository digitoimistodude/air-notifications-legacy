<?php
/**
 * @Author: Niku Hietanen
 * @Date: 2020-06-29 15:18:55
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2020-10-07 13:58:28
 * @package air-notifications
 */

namespace Air_Notifications;

/**
 * Register scripts
 */
function register_scripts() {
  wp_register_script( 'air-notifications', false, [], false, true ); // phpcs:ignore

  $script = '(function() {
    // Find all notifications
    var notifications = document.querySelectorAll(".air-notification");

    for (var index = 0; index < notifications.length; index++) {
      var notification = notifications[index];
      var saveCookie = notification.dataset.saveCookie === "on";

      if (saveCookie && window.localStorage.getItem(notification.id)) {
        // Cookies are in use and this has been dismissed before, so continue
        continue;
      }

      // Show the notice if user has not dismissed it or cookies are not enabled
      notification.classList.add("show");
      notification.setAttribute("aria-hidden", "false");

      // Find close button
      var closeButton = notification.querySelector(".air-notification__close");

      if ("undefined" !== typeof(closeButton) && closeButton) {

        closeButton.addEventListener("click", function(event) {
          var currentNotification = this.parentNode;
          var saveCookie = currentNotification.dataset.saveCookie === "on";


          // Dismiss the notification, animate first
          currentNotification.classList.add("closing");

          window.setTimeout(function() {
            currentNotification.classList.add("dismissed");
          }, 400);

          // Save cookie
          if (saveCookie) {
            window.localStorage.setItem(currentNotification.id, true);
          }
        });
      }
    }
  })()';

  wp_add_inline_script( 'air-notifications', $script );
}

/**
 * Register base styles
 */
function register_styles() {
  wp_register_style( 'air-notifications', plugin_dir_url( __DIR__ ) . 'styles/default.css', array(), PLUGIN_VERSION );
}
