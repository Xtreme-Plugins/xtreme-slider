<?php
// Only run when WP triggers uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Drop custom tables.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Uninstall cleanup.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}xtrsl_slides" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Uninstall cleanup.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}xtrsl_sliders" );

// Remove options.
delete_option( 'xtrsl_settings' );
delete_option( 'xtrsl_db_version' );
