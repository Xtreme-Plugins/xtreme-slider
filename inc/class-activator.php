<?php
defined( 'ABSPATH' ) || exit;

class XS_Activator {

	public static function activate() {
		self::create_tables();
		self::set_defaults();
		flush_rewrite_rules();
		update_option( 'xs_db_version', XS_DB_VERSION );
	}

	private static function create_tables() {
		global $wpdb;
		$c = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( "CREATE TABLE {$wpdb->prefix}xs_sliders (
			id               BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title            VARCHAR(255) NOT NULL DEFAULT '',
			layout           ENUM('default','cool','3d') NOT NULL DEFAULT 'default',
			visible_count    TINYINT(3) UNSIGNED NOT NULL DEFAULT 3,
			autoplay         TINYINT(1) NOT NULL DEFAULT 0,
			autoplay_speed   INT UNSIGNED NOT NULL DEFAULT 4000,
			fullscreen       TINYINT(1) NOT NULL DEFAULT 0,
			image_ratio      VARCHAR(10) NOT NULL DEFAULT '16:10',
			fixed_height     SMALLINT UNSIGNED NOT NULL DEFAULT 0,
			link_hover_color VARCHAR(7) NOT NULL DEFAULT '#ee212b',
			gradient_start   VARCHAR(7) NOT NULL DEFAULT '#ec38bc',
			gradient_end     VARCHAR(7) NOT NULL DEFAULT '#7303c0',
			status           ENUM('active','draft') NOT NULL DEFAULT 'active',
			created_at       DATETIME NOT NULL,
			updated_at       DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY status (status)
		) $c;" );

		dbDelta( "CREATE TABLE {$wpdb->prefix}xs_slides (
			id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			slider_id   BIGINT(20) UNSIGNED NOT NULL,
			image_id    BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
			image_url   TEXT DEFAULT NULL,
			title       VARCHAR(255) NOT NULL DEFAULT '',
			caption     VARCHAR(255) NOT NULL DEFAULT '',
			description TEXT DEFAULT NULL,
			link_url    TEXT DEFAULT NULL,
			sort_order  INT NOT NULL DEFAULT 0,
			created_at  DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY slider_id (slider_id),
			KEY sort_order (sort_order)
		) $c;" );
	}

	private static function set_defaults() {
		if ( ! get_option( 'xs_settings' ) ) {
			update_option( 'xs_settings', array(
				'defaults' => array(
					'layout'         => 'default',
					'visible_count'  => 3,
					'autoplay'       => 0,
					'autoplay_speed' => 4000,
					'fullscreen'     => 0,
					'image_ratio'    => '16:10',
					'gradient_start' => '#ec38bc',
					'gradient_end'   => '#7303c0',
				),
			) );
		}
	}

	public static function maybe_upgrade() {
		if ( get_option( 'xs_db_version' ) === XS_DB_VERSION ) {
			return;
		}
		self::create_tables();
		// Ensure fixed_height column exists on live table for in-place upgrades.
		global $wpdb;
		$table = $wpdb->prefix . 'xs_sliders';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Schema check for upgrade.
		$col = $wpdb->get_results( "SHOW COLUMNS FROM `{$table}` LIKE 'fixed_height'" );
		if ( empty( $col ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Adding column during plugin upgrade.
			$wpdb->query( "ALTER TABLE `{$table}` ADD COLUMN `fixed_height` SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER `image_ratio`" );
		}
		update_option( 'xs_db_version', XS_DB_VERSION );
	}
}
