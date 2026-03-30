<?php
defined( 'ABSPATH' ) || exit;

class XS_Admin_Edit {

	public static function render() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display parameter, no data processing.
		$slider_id = isset( $_GET['slider_id'] ) ? absint( wp_unslash( $_GET['slider_id'] ) ) : 0;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display flag, no data processing.
		$saved     = isset( $_GET['saved'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['saved'] ) );

		$slider = $slider_id ? xs_get_slider( $slider_id ) : null;
		$slides = $slider_id ? xs_get_slides( $slider_id ) : array();
		$is_new = ! $slider;

		// Defaults for new slider.
		if ( ! $slider ) {
			$slider = (object) array(
				'id'             => 0,
				'title'          => '',
				'layout'         => xs_setting( 'defaults', 'layout', 'default' ),
				'visible_count'  => xs_setting( 'defaults', 'visible_count', 3 ),
				'autoplay'       => xs_setting( 'defaults', 'autoplay', 0 ),
				'autoplay_speed' => xs_setting( 'defaults', 'autoplay_speed', 4000 ),
				'fullscreen'     => xs_setting( 'defaults', 'fullscreen', 0 ),
				'image_ratio'    => xs_setting( 'defaults', 'image_ratio', '16:10' ),
				'link_hover_color' => '#ee212b',
				'gradient_start' => xs_setting( 'defaults', 'gradient_start', '#ec38bc' ),
				'gradient_end'   => xs_setting( 'defaults', 'gradient_end', '#7303c0' ),
				'status'         => 'active',
			);
		}

		$speed_seconds = intval( $slider->autoplay_speed ) / 1000;
		?>
		<div class="wrap xs-admin-wrap">
			<div class="xs-admin-header">
				<div class="xs-admin-logo">
					<a href="https://xtremeplugins.com/plugins/xtreme-slider" target="_blank" rel="noopener noreferrer">
						<img src="<?php echo esc_url( XS_PLUGIN_URL . 'assets/img/xtreme-slider.webp' ); ?>" alt="Xtreme Slider">
					</a>
				</div>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=xtreme-slider' ) ); ?>" class="xs-btn xs-btn-secondary"><?php echo '&larr; ' . esc_html__( 'All Sliders', 'xtreme-slider' ); ?></a>
			</div>

			<form method="post" id="xs-edit-form">
				<?php wp_nonce_field( 'xs_save_slider', 'xs_edit_nonce' ); ?>
				<input type="hidden" name="slider_id" value="<?php echo esc_attr( $slider->id ); ?>">

				<div class="xs-edit-layout">

					<!-- Main Column -->
					<div class="xs-edit-main">

						<!-- Title + Status -->
						<div class="xs-form-section">
							<div class="xs-title-row">
								<div class="xs-title-field">
									<input type="text" name="title" class="xs-input-full" value="<?php echo esc_attr( $slider->title ); ?>" placeholder="<?php esc_attr_e( 'Enter slider name...', 'xtreme-slider' ); ?>">
								</div>
								<div class="xs-title-status">
									<label class="xs-status-toggle">
										<input type="hidden" name="status" value="draft">
										<input type="checkbox" name="status" value="active" <?php checked( $slider->status, 'active' ); ?>>
										<span class="xs-toggle-track"></span>
										<span class="xs-toggle-text"></span>
									</label>
								</div>
							</div>
						</div>

						<!-- Slides -->
						<div class="xs-form-section">
							<h2><?php esc_html_e( 'Slides', 'xtreme-slider' ); ?> <span class="xs-slide-counter">(<span id="xs-slide-count"><?php echo count( $slides ); ?></span> / 10)</span></h2>
							<p class="xs-form-desc"><?php esc_html_e( 'Add up to 10 images. Drag to reorder. The number of visible slides per view is configured in the sidebar.', 'xtreme-slider' ); ?></p>

							<div id="xs-slides-grid" class="xs-slides-grid">
								<?php foreach ( $slides as $slide ) : ?>
									<div class="xs-slide-card" data-image-id="<?php echo esc_attr( $slide->image_id ); ?>">
										<div class="xs-slide-img">
											<img src="<?php echo esc_url( $slide->image_url ); ?>" alt="">
										</div>
										<div class="xs-slide-fields">
											<input type="hidden" name="slides[image_id][]" value="<?php echo esc_attr( $slide->image_id ); ?>">
											<input type="hidden" name="slides[image_url][]" value="<?php echo esc_url( $slide->image_url ); ?>">
											<input type="text" name="slides[title][]" value="<?php echo esc_attr( $slide->title ); ?>" placeholder="<?php esc_attr_e( 'Title (on image)', 'xtreme-slider' ); ?>">
											<input type="text" name="slides[caption][]" value="<?php echo esc_attr( $slide->caption ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Caption (below image)', 'xtreme-slider' ); ?>">
											<input type="text" name="slides[description][]" value="<?php echo esc_attr( $slide->description ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Description (optional)', 'xtreme-slider' ); ?>">
											<input type="text" name="slides[link_url][]" value="<?php echo esc_attr( $slide->link_url ); ?>" placeholder="<?php esc_attr_e( 'Link URL (optional)', 'xtreme-slider' ); ?>">
										</div>
										<button type="button" class="xs-slide-remove" title="<?php esc_attr_e( 'Remove slide', 'xtreme-slider' ); ?>">&times;</button>
									</div>
								<?php endforeach; ?>
							</div>

							<button type="button" id="xs-add-slides" class="xs-btn xs-btn-secondary xs-btn-lg" <?php echo count( $slides ) >= 10 ? 'disabled' : ''; ?>>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
								<?php esc_html_e( 'Add Images', 'xtreme-slider' ); ?>
							</button>
						</div>

						<!-- Shortcode (only shown for saved sliders) -->
						<?php if ( ! $is_new ) : ?>
						<div class="xs-form-section">
							<h2><?php esc_html_e( 'Shortcode', 'xtreme-slider' ); ?></h2>
							<p class="xs-form-desc"><?php esc_html_e( 'Copy this shortcode and paste it into any page, post, or Elementor Shortcode widget.', 'xtreme-slider' ); ?></p>
							<div class="xs-shortcode-box">
								<code id="xs-shortcode-value">[xtreme_slider id="<?php echo esc_attr( $slider->id ); ?>"]</code>
								<button type="button" class="xs-copy-btn xs-copy-btn-lg" data-copy='[xtreme_slider id="<?php echo esc_attr( $slider->id ); ?>"]'><?php esc_html_e( 'Copy', 'xtreme-slider' ); ?></button>
							</div>
						</div>
						<?php endif; ?>

					</div>

					<!-- Sidebar -->
					<div class="xs-edit-sidebar">

						<div class="xs-form-section">
							<h2><?php esc_html_e( 'Layout', 'xtreme-slider' ); ?></h2>
							<div class="xs-radio-cards xs-radio-cards-3">
								<label class="xs-radio-card <?php echo 'default' === $slider->layout ? 'active' : ''; ?>">
									<input type="radio" name="layout" value="default" <?php checked( $slider->layout, 'default' ); ?>>
									<span class="xs-radio-card-icon">
										<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16"/><line x1="9" y1="4" x2="9" y2="20"/><line x1="15" y1="4" x2="15" y2="20"/></svg>
									</span>
									<span class="xs-radio-card-label"><?php esc_html_e( 'Default', 'xtreme-slider' ); ?></span>
								</label>
								<label class="xs-radio-card <?php echo 'cool' === $slider->layout ? 'active' : ''; ?>">
									<input type="radio" name="layout" value="cool" <?php checked( $slider->layout, 'cool' ); ?>>
									<span class="xs-radio-card-icon">
										<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="8" y1="6" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="18"/></svg>
									</span>
									<span class="xs-radio-card-label"><?php esc_html_e( 'Cool', 'xtreme-slider' ); ?></span>
								</label>
								<label class="xs-radio-card <?php echo '3d' === $slider->layout ? 'active' : ''; ?>">
									<input type="radio" name="layout" value="3d" <?php checked( $slider->layout, '3d' ); ?>>
									<span class="xs-radio-card-icon">
										<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
									</span>
									<span class="xs-radio-card-label"><?php esc_html_e( '3D', 'xtreme-slider' ); ?></span>
								</label>
							</div>
						</div>

						<div class="xs-form-section">
							<h2><?php esc_html_e( 'Display Options', 'xtreme-slider' ); ?></h2>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Visible Slides (1–6)', 'xtreme-slider' ); ?></label>
								<input type="number" name="visible_count" value="<?php echo esc_attr( $slider->visible_count ); ?>" min="1" max="6" class="xs-input-sm">
							</div>
							<div class="xs-form-row">
								<label class="xs-toggle-label">
									<input type="checkbox" name="fullscreen" value="1" <?php checked( $slider->fullscreen, 1 ); ?>>
									<?php esc_html_e( 'Fullscreen (edge-to-edge)', 'xtreme-slider' ); ?>
								</label>
							</div>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Image Ratio', 'xtreme-slider' ); ?></label>
								<select name="image_ratio">
									<option value="16:10" <?php selected( $slider->image_ratio ?? '16:10', '16:10' ); ?>><?php esc_html_e( '16:10 (Landscape)', 'xtreme-slider' ); ?></option>
									<option value="1:1" <?php selected( $slider->image_ratio ?? '16:10', '1:1' ); ?>><?php esc_html_e( '1:1 (Square)', 'xtreme-slider' ); ?></option>
								</select>
							</div>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Link Hover Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="link_hover_color" value="<?php echo esc_attr( $slider->link_hover_color ?? '#ee212b' ); ?>" class="xs-color-picker">
							</div>
						</div>

						<div class="xs-form-section">
							<h2><?php esc_html_e( 'Autoplay', 'xtreme-slider' ); ?></h2>
							<div class="xs-form-row">
								<label class="xs-toggle-label">
									<input type="checkbox" name="autoplay" value="1" id="xs-autoplay-toggle" <?php checked( $slider->autoplay, 1 ); ?>>
									<?php esc_html_e( 'Enable auto-scrolling', 'xtreme-slider' ); ?>
								</label>
							</div>
							<div class="xs-form-row xs-autoplay-speed" style="<?php echo $slider->autoplay ? '' : 'display:none;'; ?>">
								<label><?php
									/* translators: %s: speed in seconds */
									printf( esc_html__( 'Speed: %ss', 'xtreme-slider' ), '<span id="xs-speed-label">' . esc_html( $speed_seconds ) . '</span>' );
								?></label>
								<input type="range" name="autoplay_speed" min="2000" max="10000" step="500" value="<?php echo esc_attr( $slider->autoplay_speed ); ?>" id="xs-speed-range">
							</div>
						</div>

						<div class="xs-form-section xs-gradient-section" id="xs-gradient-section" style="<?php echo 'cool' !== $slider->layout ? 'display:none;' : ''; ?>">
							<h2><?php esc_html_e( 'Background Gradient', 'xtreme-slider' ); ?></h2>
							<p class="xs-form-desc"><?php esc_html_e( 'Visible behind the slider in Cool layout.', 'xtreme-slider' ); ?></p>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Start Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="gradient_start" value="<?php echo esc_attr( $slider->gradient_start ); ?>" class="xs-color-picker">
							</div>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'End Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="gradient_end" value="<?php echo esc_attr( $slider->gradient_end ); ?>" class="xs-color-picker">
							</div>
							<div class="xs-gradient-preview" id="xs-gradient-preview" style="background: linear-gradient(135deg, <?php echo esc_attr( $slider->gradient_start ); ?>, <?php echo esc_attr( $slider->gradient_end ); ?>);"></div>
						</div>

						<div class="xs-form-actions">
							<button type="submit" class="xs-btn xs-btn-primary xs-btn-lg xs-btn-full" id="xs-save-btn"><span class="xs-spinner"></span> <?php echo $is_new ? esc_html__( 'Create Slider', 'xtreme-slider' ) : esc_html__( 'Save Changes', 'xtreme-slider' ); ?></button>
						</div>
					</div>

				</div>
			</form>
		</div>
		<?php
	}

	public static function process_save() {
		if ( ! isset( $_POST['xs_edit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xs_edit_nonce'] ) ), 'xs_save_slider' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'xtreme-slider' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized.', 'xtreme-slider' ) );
		}

		global $wpdb;

		$slider_id      = absint( wp_unslash( $_POST['slider_id'] ?? 0 ) );
		$title          = sanitize_text_field( wp_unslash( $_POST['title'] ?? '' ) );
		$layout         = sanitize_text_field( wp_unslash( $_POST['layout'] ?? 'default' ) );
		$layout         = in_array( $layout, array( 'default', 'cool', '3d' ), true ) ? $layout : 'default';
		$visible_count  = max( 1, min( 6, absint( wp_unslash( $_POST['visible_count'] ?? 3 ) ) ) );
		$autoplay       = isset( $_POST['autoplay'] ) ? 1 : 0;
		$autoplay_speed = max( 2000, min( 10000, absint( wp_unslash( $_POST['autoplay_speed'] ?? 4000 ) ) ) );
		$fullscreen     = isset( $_POST['fullscreen'] ) ? 1 : 0;
		$image_ratio    = sanitize_text_field( wp_unslash( $_POST['image_ratio'] ?? '16:10' ) );
		$image_ratio    = in_array( $image_ratio, array( '16:10', '1:1' ), true ) ? $image_ratio : '16:10';
		$link_hover_color = xs_sanitize_hex_color( sanitize_text_field( wp_unslash( $_POST['link_hover_color'] ?? '#ee212b' ) ) );
		$gradient_start   = xs_sanitize_hex_color( sanitize_text_field( wp_unslash( $_POST['gradient_start'] ?? '#ec38bc' ) ) );
		$gradient_end     = xs_sanitize_hex_color( sanitize_text_field( wp_unslash( $_POST['gradient_end'] ?? '#7303c0' ) ) );
		$status         = sanitize_text_field( wp_unslash( $_POST['status'] ?? 'active' ) );
		$status         = in_array( $status, array( 'active', 'draft' ), true ) ? $status : 'active';
		$now            = current_time( 'mysql' );

		$data = array(
			'title'          => $title,
			'layout'         => $layout,
			'visible_count'  => $visible_count,
			'autoplay'       => $autoplay,
			'autoplay_speed' => $autoplay_speed,
			'fullscreen'     => $fullscreen,
			'image_ratio'      => $image_ratio,
			'link_hover_color' => $link_hover_color,
			'gradient_start'   => $gradient_start,
			'gradient_end'   => $gradient_end,
			'status'         => $status,
			'updated_at'     => $now,
		);

		$format = array( '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s' );

		if ( $slider_id ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, update operation.
			$wpdb->update( $wpdb->prefix . 'xs_sliders', $data, array( 'id' => $slider_id ), $format, array( '%d' ) );
		} else {
			$data['created_at'] = $now;
			$format[]           = '%s';
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table, insert operation.
			$wpdb->insert( $wpdb->prefix . 'xs_sliders', $data, $format );
			$slider_id = $wpdb->insert_id;
		}

		// Save slides.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, delete before re-insert.
		$wpdb->delete( $wpdb->prefix . 'xs_slides', array( 'slider_id' => $slider_id ), array( '%d' ) );

		if ( ! empty( $_POST['slides']['image_id'] ) && is_array( $_POST['slides']['image_id'] ) ) {
			$image_ids    = array_map( 'absint', wp_unslash( $_POST['slides']['image_id'] ) );
			$image_urls   = array_map( 'esc_url_raw', wp_unslash( $_POST['slides']['image_url'] ?? array() ) );
			$titles       = array_map( 'sanitize_text_field', wp_unslash( $_POST['slides']['title'] ?? array() ) );
			$captions     = array_map( 'sanitize_text_field', wp_unslash( $_POST['slides']['caption'] ?? array() ) );
			$descriptions = array_map( 'sanitize_text_field', wp_unslash( $_POST['slides']['description'] ?? array() ) );
			$link_urls    = array_map( 'sanitize_text_field', wp_unslash( $_POST['slides']['link_url'] ?? array() ) );

			$max = min( count( $image_ids ), 10 );
			for ( $i = 0; $i < $max; $i++ ) {
				if ( ! $image_ids[ $i ] ) {
					continue;
				}
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table, insert operation.
				$wpdb->insert(
					$wpdb->prefix . 'xs_slides',
					array(
						'slider_id'   => $slider_id,
						'image_id'    => $image_ids[ $i ],
						'image_url'   => $image_urls[ $i ] ?? '',
						'title'       => $titles[ $i ] ?? '',
						'caption'     => $captions[ $i ] ?? '',
						'description' => $descriptions[ $i ] ?? '',
						'link_url'    => $link_urls[ $i ] ?? '',
						'sort_order'  => $i,
						'created_at'  => $now,
					),
					array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s' )
				);
			}
		}

		// Redirect to edit page with success message.
		wp_safe_redirect( admin_url( 'admin.php?page=xs-edit&slider_id=' . $slider_id . '&saved=1' ) );
		exit;
	}
}
