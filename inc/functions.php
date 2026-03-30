<?php
defined( 'ABSPATH' ) || exit;

/**
 * Get a plugin setting value.
 *
 * @param string $group   Setting group key.
 * @param string $key     Setting key within the group.
 * @param mixed  $default Fallback if not set.
 * @return mixed
 */
function xs_setting( $group, $key, $default = null ) {
	$settings = get_option( 'xs_settings', array() );
	return isset( $settings[ $group ][ $key ] ) ? $settings[ $group ][ $key ] : $default;
}

/**
 * Get a slider by ID.
 *
 * @param int $slider_id
 * @return object|null
 */
function xs_get_slider( $slider_id ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
	return $wpdb->get_row( $wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}xs_sliders WHERE id = %d LIMIT 1",
		$slider_id
	) );
}

/**
 * Get all slides for a slider, ordered by sort_order.
 *
 * @param int $slider_id
 * @return array
 */
function xs_get_slides( $slider_id ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
	return $wpdb->get_results( $wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}xs_slides WHERE slider_id = %d ORDER BY sort_order ASC",
		$slider_id
	) );
}

/**
 * Get all sliders.
 *
 * @param string $status Optional status filter.
 * @return array
 */
function xs_get_all_sliders( $status = '' ) {
	global $wpdb;
	if ( $status ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}xs_sliders WHERE status = %s ORDER BY created_at DESC",
			$status
		) );
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
	return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}xs_sliders ORDER BY created_at DESC" );
}

/**
 * Count slides for a slider.
 *
 * @param int $slider_id
 * @return int
 */
function xs_count_slides( $slider_id ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
	return (int) $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM {$wpdb->prefix}xs_slides WHERE slider_id = %d",
		$slider_id
	) );
}

/**
 * Sanitize a hex color value.
 *
 * @param string $color
 * @return string
 */
function xs_sanitize_hex_color( $color ) {
	if ( preg_match( '/^#[a-fA-F0-9]{6}$/', $color ) ) {
		return $color;
	}
	return '#ec38bc';
}
