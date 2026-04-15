<?php
defined( 'ABSPATH' ) || exit;

class Xtrsl_Plugin {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	private function init() {
		$this->load_helpers();

		if ( is_admin() ) {
			$this->load_admin();
		}

		$this->load_frontend();
	}

	private function load_helpers() {
		require_once XTRSL_PLUGIN_PATH . 'inc/functions.php';
	}

	private function load_admin() {
		require_once XTRSL_PLUGIN_PATH . 'inc/admin/class-admin.php';
		require_once XTRSL_PLUGIN_PATH . 'inc/admin/class-admin-sliders.php';
		require_once XTRSL_PLUGIN_PATH . 'inc/admin/class-admin-edit.php';
		require_once XTRSL_PLUGIN_PATH . 'inc/admin/class-admin-settings.php';
		new Xtrsl_Admin();
	}

	private function load_frontend() {
		require_once XTRSL_PLUGIN_PATH . 'inc/front/class-shortcode.php';
		require_once XTRSL_PLUGIN_PATH . 'inc/front/class-renderer.php';
		new Xtrsl_Shortcode();
	}
}
