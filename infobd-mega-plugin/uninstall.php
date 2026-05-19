<?php
/**
 * Uninstaller — runs only when user deletes the plugin.
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

delete_option( 'infobd_mega_settings' );

// Optionally remove all post meta (keep posts intact)
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_infobd_%'" );
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_game_%'" );
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_product_%'" );
