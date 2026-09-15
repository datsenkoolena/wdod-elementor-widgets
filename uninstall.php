<?php
/**
 * Uninstall routine.
 *
 * Removes every trace the plugin left in the database: the stored version
 * option, the per-user "notice dismissed" flags and the cached team post
 * list. Templates, widgets and shortcodes leave no other data behind.
 *
 * @package WDOD\ElementorWidgets
 */

defined( 'ABSPATH' ) || exit;
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'wdod_ew_version' );
delete_transient( 'wdod_ew_team_posts' );

// Remove the dismissed-notice flag from every user.
delete_metadata( 'user', 0, 'wdod_ew_dismissed_notice', '', true );
