<?php
defined( 'ABSPATH' ) || exit;

class Xtrsl_Admin {

	public function __construct() {
		add_action( 'admin_menu',            array( $this, 'register_menus' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'admin_body_class',      array( $this, 'add_body_class' ) );
		add_action( 'admin_init',            array( $this, 'handle_form_submissions' ) );
	}

	/**
	 * Process form submissions before headers are sent.
	 * wp_safe_redirect() must run before WordPress outputs the page.
	 */
	public function handle_form_submissions() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only routing; nonce verified inside each handler.
		$page   = isset( $_GET['page'] )   ? sanitize_text_field( wp_unslash( $_GET['page'] ) )   : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only routing; nonce verified inside each handler.
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

		// Delete action (GET-based) — must run before the POST check.
		if (
			'xtreme-slider' === $page
			&& 'delete' === $action
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified inside process_delete().
			&& isset( $_GET['slider_id'], $_GET['_wpnonce'] )
		) {
			Xtrsl_Admin_Sliders::process_delete();
		}

		// POST form submissions.
		if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'POST' !== strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) ) {
			return;
		}

		// Edit page save.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified inside process_save().
		if ( 'xtrsl-edit' === $page && isset( $_POST['xtrsl_edit_nonce'] ) ) {
			Xtrsl_Admin_Edit::process_save();
		}

		// Settings page save.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified inside process_save().
		if ( 'xtrsl-settings' === $page && isset( $_POST['xtrsl_settings_nonce'] ) ) {
			Xtrsl_Admin_Settings::process_save();
		}
	}

	public function register_menus() {
		add_menu_page(
			'XtremeSlider',
			'Xtreme Slider',
			'manage_options',
			'xtreme-slider',
			array( 'Xtrsl_Admin_Sliders', 'render' ),
			'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#00d4ff"><path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14zM5 15l3.5-4.5 2.5 3.01L14.5 9l4.5 6H5z"/></svg>' ),
			56
		);

		add_submenu_page( 'xtreme-slider', __( 'All Sliders', 'xtreme-slider' ), __( 'All Sliders', 'xtreme-slider' ), 'manage_options', 'xtreme-slider',    array( 'Xtrsl_Admin_Sliders',  'render' ) );
		add_submenu_page( 'xtreme-slider', __( 'Add New', 'xtreme-slider' ),     __( 'Add New', 'xtreme-slider' ),     'manage_options', 'xtrsl-edit',       array( 'Xtrsl_Admin_Edit',     'render' ) );
		add_submenu_page( 'xtreme-slider', __( 'Settings', 'xtreme-slider' ),    __( 'Settings', 'xtreme-slider' ),    'manage_options', 'xtrsl-settings',   array( 'Xtrsl_Admin_Settings', 'render' ) );
	}

	public function add_body_class( $classes ) {
		$screen = get_current_screen();
		if ( $screen && $this->is_xtrsl_page( $screen->id ) ) {
			$classes .= ' xtrsl-admin-page';
		}
		return $classes;
	}

	public function enqueue_assets( $hook ) {
		if ( ! $this->is_xtrsl_page( $hook ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'xtrsl-admin', XTRSL_PLUGIN_URL . 'assets/css/admin.css', array(), XTRSL_VERSION );
		wp_enqueue_script( 'xtrsl-admin', XTRSL_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker' ), XTRSL_VERSION, true );
		wp_localize_script( 'xtrsl-admin', 'xtrslAdmin', array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'xtrsl_admin_nonce' ),
			'maxSlides'  => 10,
			'pluginUrl'  => XTRSL_PLUGIN_URL,
		) );
	}

	private function is_xtrsl_page( $hook ) {
		$xtrsl_hooks = array(
			'toplevel_page_xtreme-slider',
			'xtreme-slider_page_xtrsl-edit',
			'xtreme-slider_page_xtrsl-settings',
		);
		return in_array( $hook, $xtrsl_hooks, true );
	}
}
