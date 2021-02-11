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
    var body = document.getElementsByTagName("body")[0];

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

      body.classList.add("has-notifications");

      // Find close button
      var closeButton = notification.querySelector(".air-notification__close");

      if ("undefined" !== typeof(closeButton) && closeButton) {

        closeButton.addEventListener("click", function(event) {
          // Dismiss the notification
          event.target.parentNode.parentNode.parentNode.classList.add("dismissed");

          debugger;

          // Remove body class
          body.classList.remove("has-notifications");

          // Save cookie
          if (saveCookie) {
            window.localStorage.setItem(this.parentNode.id, true);
          }
        });
      }
    }
  })()';

  wp_add_inline_script( 'air-notifications', $script );
}
