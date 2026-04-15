<?php
defined( 'ABSPATH' ) || exit;

class Xtrsl_Activator {

	public static function activate() {
		self::create_tables();
		self::set_defaults();
		flush_rewrite_rules();
		update_option( 'xtrsl_db_version', XTRSL_DB_VERSION );
	}

	private static function create_tables() {
		global $wpdb;
		$c = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( "CREATE TABLE {$wpdb->prefix}xtrsl_sliders (
			id               BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title            VARCHAR(255) NOT NULL DEFAULT '',
			layout           ENUM('default','cool','3d') NOT NULL DEFAULT 'default',
			visible_count    TINYINT(3) UNSIGNED NOT NULL DEFAULT 3,
			autoplay         TINYINT(1) NOT NULL DEFAULT 0,
			autoplay_speed   INT UNSIGNED NOT NULL DEFAULT 4000,
			fullscreen       TINYINT(1) NOT NULL DEFAULT 0,
			image_ratio      VARCHAR(10) NOT NULL DEFAULT '16:10',
			link_hover_color VARCHAR(7) NOT NULL DEFAULT '#ee212b',
			gradient_start   VARCHAR(7) NOT NULL DEFAULT '#ec38bc',
			gradient_end     VARCHAR(7) NOT NULL DEFAULT '#7303c0',
			status           ENUM('active','draft') NOT NULL DEFAULT 'active',
			created_at       DATETIME NOT NULL,
			updated_at       DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY status (status)
		) $c;" );

		dbDelta( "CREATE TABLE {$wpdb->prefix}xtrsl_slides (
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
		if ( ! get_option( 'xtrsl_settings' ) ) {
			update_option( 'xtrsl_settings', array(
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
}
