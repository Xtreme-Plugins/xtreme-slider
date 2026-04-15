<?php
defined( 'ABSPATH' ) || exit;

class Xtrsl_Admin_Sliders {

	public static function render() {
		$sliders = xtrsl_get_all_sliders();
		?>
		<div class="wrap xs-admin-wrap">
			<div class="xs-admin-header">
				<div class="xs-admin-logo">
					<a href="https://xtremeplugins.com/plugins/xtreme-slider" target="_blank" rel="noopener noreferrer">
						<img src="<?php echo esc_url( XTRSL_PLUGIN_URL . 'assets/img/xtreme-slider.webp' ); ?>" alt="Xtreme Slider">
					</a>
				</div>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=xtrsl-edit' ) ); ?>" class="xs-btn xs-btn-primary"><?php echo '+ ' . esc_html__( 'Add New Slider', 'xtreme-slider' ); ?></a>
			</div>

			<?php if ( empty( $sliders ) ) : ?>
				<div class="xs-empty-state">
					<div class="xs-empty-icon">
						<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M5 15l3.5-4.5 2.5 3 3.5-4.5L19 15"/><circle cx="8.5" cy="8.5" r="1.5"/></svg>
					</div>
					<h2><?php esc_html_e( 'No sliders yet', 'xtreme-slider' ); ?></h2>
					<p><?php esc_html_e( 'Create your first slider to get started.', 'xtreme-slider' ); ?></p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=xtrsl-edit' ) ); ?>" class="xs-btn xs-btn-primary xs-btn-lg"><?php esc_html_e( 'Create Slider', 'xtreme-slider' ); ?></a>
				</div>
			<?php else : ?>
				<table class="xs-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Title', 'xtreme-slider' ); ?></th>
							<th><?php esc_html_e( 'Layout', 'xtreme-slider' ); ?></th>
							<th><?php esc_html_e( 'Slides', 'xtreme-slider' ); ?></th>
							<th><?php esc_html_e( 'Shortcode', 'xtreme-slider' ); ?></th>
							<th><?php esc_html_e( 'Status', 'xtreme-slider' ); ?></th>
							<th><?php esc_html_e( 'Date', 'xtreme-slider' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'xtreme-slider' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $sliders as $slider ) :
							$count    = xtrsl_count_slides( $slider->id );
							$edit_url = admin_url( 'admin.php?page=xtrsl-edit&slider_id=' . $slider->id );
							$del_url  = wp_nonce_url( admin_url( 'admin.php?page=xtreme-slider&action=delete&slider_id=' . $slider->id ), 'xtrsl_delete_' . $slider->id );
						?>
							<tr>
								<td><a href="<?php echo esc_url( $edit_url ); ?>" class="xs-link"><?php echo esc_html( $slider->title ? $slider->title : __( '(Untitled)', 'xtreme-slider' ) ); ?></a></td>
								<td><span class="xs-badge xs-badge-<?php echo esc_attr( $slider->layout ); ?>"><?php echo esc_html( ucfirst( $slider->layout ) ); ?></span></td>
								<td><?php echo esc_html( $count ); ?></td>
								<td>
									<code class="xs-shortcode-display">[xtreme_slider id="<?php echo esc_attr( $slider->id ); ?>"]</code>
									<button type="button" class="xs-copy-btn" data-copy='[xtreme_slider id="<?php echo esc_attr( $slider->id ); ?>"]' title="<?php esc_attr_e( 'Copy shortcode', 'xtreme-slider' ); ?>">
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
									</button>
								</td>
								<td><span class="xs-status xs-status-<?php echo esc_attr( $slider->status ); ?>"><?php echo esc_html( ucfirst( $slider->status ) ); ?></span></td>
								<td><?php echo esc_html( date_i18n( 'M j, Y', strtotime( $slider->created_at ) ) ); ?></td>
								<td>
									<a href="<?php echo esc_url( $edit_url ); ?>" class="xs-action-link"><?php esc_html_e( 'Edit', 'xtreme-slider' ); ?></a>
									<a href="<?php echo esc_url( $del_url ); ?>" class="xs-action-link xs-action-delete" onclick="return confirm('<?php echo esc_js( __( 'Delete this slider and all its slides?', 'xtreme-slider' ) ); ?>');"><?php esc_html_e( 'Delete', 'xtreme-slider' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	public static function process_delete() {
		if ( ! isset( $_GET['slider_id'], $_GET['_wpnonce'] ) ) {
			wp_die( esc_html__( 'Invalid request.', 'xtreme-slider' ) );
		}

		$slider_id = absint( wp_unslash( $_GET['slider_id'] ) );

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'xtrsl_delete_' . $slider_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'xtreme-slider' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized.', 'xtreme-slider' ) );
		}

		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, delete operation.
		$wpdb->delete( $wpdb->prefix . 'xtrsl_slides', array( 'slider_id' => $slider_id ), array( '%d' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, delete operation.
		$wpdb->delete( $wpdb->prefix . 'xtrsl_sliders', array( 'id' => $slider_id ), array( '%d' ) );

		wp_safe_redirect( admin_url( 'admin.php?page=xtreme-slider&deleted=1' ) );
		exit;
	}
}
