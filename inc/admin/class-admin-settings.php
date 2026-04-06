<?php
defined( 'ABSPATH' ) || exit;

class XS_Admin_Settings {

	public static function render() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display flag, no data processing.
		$saved = isset( $_GET['saved'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['saved'] ) );

		$s = get_option( 'xs_settings', array() );
		$d = $s['defaults'] ?? array();

		?>
		<div class="wrap xs-admin-wrap">
			<div class="xs-admin-header">
				<div class="xs-admin-logo">
					<a href="https://xtremeplugins.com/plugins/xtreme-slider" target="_blank" rel="noopener noreferrer">
						<img src="<?php echo esc_url( XS_PLUGIN_URL . 'assets/img/xtreme-slider.svg' ); ?>" alt="Xtreme Slider">
					</a>
				</div>
			</div>

			<?php if ( $saved ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'xtreme-slider' ); ?></p></div>
			<?php endif; ?>

			<form method="post">
				<?php wp_nonce_field( 'xs_save_settings', 'xs_settings_nonce' ); ?>

				<div class="xs-settings-layout">

					<div class="xs-form-section">
						<h2><?php esc_html_e( 'Default Slider Settings', 'xtreme-slider' ); ?></h2>
						<p class="xs-form-desc"><?php esc_html_e( 'These defaults are applied when creating a new slider. Can be overridden per slider.', 'xtreme-slider' ); ?></p>
						<div class="xs-form-grid">
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Default Layout', 'xtreme-slider' ); ?></label>
								<select name="settings[defaults][layout]">
									<option value="default" <?php selected( $d['layout'] ?? 'default', 'default' ); ?>><?php esc_html_e( 'Default', 'xtreme-slider' ); ?></option>
									<option value="cool" <?php selected( $d['layout'] ?? 'default', 'cool' ); ?>><?php esc_html_e( 'Cool', 'xtreme-slider' ); ?></option>
									<option value="3d" <?php selected( $d['layout'] ?? 'default', '3d' ); ?>><?php esc_html_e( '3D', 'xtreme-slider' ); ?></option>
								</select>
							</div>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Default Visible Slides', 'xtreme-slider' ); ?></label>
								<input type="number" name="settings[defaults][visible_count]" value="<?php echo esc_attr( $d['visible_count'] ?? 3 ); ?>" min="1" max="6">
							</div>
							<div class="xs-form-row">
								<label class="xs-toggle-label">
									<input type="checkbox" name="settings[defaults][autoplay]" value="1" <?php checked( $d['autoplay'] ?? 0 ); ?>>
									<?php esc_html_e( 'Enable autoplay by default', 'xtreme-slider' ); ?>
								</label>
							</div>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Default Autoplay Speed (ms)', 'xtreme-slider' ); ?></label>
								<input type="number" name="settings[defaults][autoplay_speed]" value="<?php echo esc_attr( $d['autoplay_speed'] ?? 4000 ); ?>" min="2000" max="10000" step="500">
							</div>
							<div class="xs-form-row">
								<label class="xs-toggle-label">
									<input type="checkbox" name="settings[defaults][fullscreen]" value="1" <?php checked( $d['fullscreen'] ?? 0 ); ?>>
									<?php esc_html_e( 'Fullscreen by default', 'xtreme-slider' ); ?>
								</label>
							</div>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Default Image Ratio', 'xtreme-slider' ); ?></label>
								<select name="settings[defaults][image_ratio]">
									<option value="16:10" <?php selected( $d['image_ratio'] ?? '16:10', '16:10' ); ?>><?php esc_html_e( '16:10 (Landscape)', 'xtreme-slider' ); ?></option>
									<option value="1:1" <?php selected( $d['image_ratio'] ?? '16:10', '1:1' ); ?>><?php esc_html_e( '1:1 (Square)', 'xtreme-slider' ); ?></option>
								</select>
							</div>
						</div>
					</div>

					<div class="xs-form-section">
						<h2><?php esc_html_e( 'Default Gradient (Cool Layout)', 'xtreme-slider' ); ?></h2>
						<div class="xs-form-grid">
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Start Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="settings[defaults][gradient_start]" value="<?php echo esc_attr( $d['gradient_start'] ?? '#ec38bc' ); ?>" class="xs-color-picker">
							</div>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'End Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="settings[defaults][gradient_end]" value="<?php echo esc_attr( $d['gradient_end'] ?? '#7303c0' ); ?>" class="xs-color-picker">
							</div>
						</div>
					</div>

					<div class="xs-form-actions">
						<button type="submit" class="xs-btn xs-btn-primary xs-btn-lg" id="xs-save-btn"><span class="xs-spinner"></span> <?php esc_html_e( 'Save Settings', 'xtreme-slider' ); ?></button>
					</div>

				</div>
			</form>
		</div>
		<?php
	}

	public static function process_save() {
		if ( ! isset( $_POST['xs_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xs_settings_nonce'] ) ), 'xs_save_settings' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'xtreme-slider' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized.', 'xtreme-slider' ) );
		}

		$raw = isset( $_POST['settings'] ) ? map_deep( wp_unslash( $_POST['settings'] ), 'sanitize_text_field' ) : array();

		$settings = array(
			'defaults' => array(
				'layout'         => in_array( $raw['defaults']['layout'] ?? '', array( 'default', 'cool', '3d' ), true ) ? sanitize_text_field( $raw['defaults']['layout'] ) : 'default',
				'visible_count'  => max( 1, min( 6, absint( $raw['defaults']['visible_count'] ?? 3 ) ) ),
				'autoplay'       => isset( $raw['defaults']['autoplay'] ) ? 1 : 0,
				'autoplay_speed' => max( 2000, min( 10000, absint( $raw['defaults']['autoplay_speed'] ?? 4000 ) ) ),
				'fullscreen'     => isset( $raw['defaults']['fullscreen'] ) ? 1 : 0,
				'image_ratio'    => in_array( $raw['defaults']['image_ratio'] ?? '', array( '16:10', '1:1' ), true ) ? sanitize_text_field( $raw['defaults']['image_ratio'] ) : '16:10',
				'gradient_start' => xs_sanitize_hex_color( sanitize_text_field( $raw['defaults']['gradient_start'] ?? '#ec38bc' ) ),
				'gradient_end'   => xs_sanitize_hex_color( sanitize_text_field( $raw['defaults']['gradient_end'] ?? '#7303c0' ) ),
			),
		);

		update_option( 'xs_settings', $settings );

		wp_safe_redirect( admin_url( 'admin.php?page=xs-settings&saved=1' ) );
		exit;
	}
}
