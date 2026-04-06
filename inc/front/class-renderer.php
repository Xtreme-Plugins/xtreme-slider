<?php
defined( 'ABSPATH' ) || exit;

class XS_Renderer {

	public function render( $slider, $slides, $opts ) {
		$uid        = 'xs-' . $slider->id . '-' . wp_rand( 100, 999 );
		$layout     = $opts['layout'];
		$visible    = $opts['visible'];
		$autoplay   = $opts['autoplay'] ? 'true' : 'false';
		$speed      = $opts['speed'];
		$fullscreen = $opts['fullscreen'];
		$total      = count( $slides );

		$ratio        = $opts['ratio'] ?? '16:10';
		$fixed_height = isset( $opts['fixed_height'] ) ? absint( $opts['fixed_height'] ) : 0;

		$wrap_class = 'xs-slider-wrap';
		$wrap_class .= ' xs-layout-' . esc_attr( $layout );
		if ( 'fixed' === $ratio ) {
			$wrap_class .= ' xs-ratio-fixed';
		} else {
			$wrap_class .= ' xs-ratio-' . esc_attr( str_replace( ':', '-', $ratio ) );
		}
		if ( $fullscreen ) {
			$wrap_class .= ' xs-fullscreen';
		}

		$hover_color = esc_attr( $slider->link_hover_color ?? '#ee212b' );
		$style = "--xs-link-hover: {$hover_color};";
		if ( 'cool' === $layout ) {
			$start = ! empty( $slider->gradient_start ) ? esc_attr( $slider->gradient_start ) : '';
			$end   = ! empty( $slider->gradient_end )   ? esc_attr( $slider->gradient_end )   : '';
			if ( $start || $end ) {
				$from   = $start ?: $end;
				$to     = $end   ?: $start;
				$style .= " background: linear-gradient(135deg, {$from}, {$to});";
			} else {
				$wrap_class .= ' xs-no-gradient';
			}
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $wrap_class ); ?>"
		     id="<?php echo esc_attr( $uid ); ?>"
		     data-visible="<?php echo esc_attr( $visible ); ?>"
		     data-autoplay="<?php echo esc_attr( $autoplay ); ?>"
		     data-speed="<?php echo esc_attr( $speed ); ?>"
		     data-layout="<?php echo esc_attr( $layout ); ?>"
		     data-total="<?php echo esc_attr( $total ); ?>"
		     style="<?php echo esc_attr( $style ); ?>">

			<div class="xs-slider-viewport">
				<div class="xs-slider-track">
					<?php foreach ( $slides as $index => $slide ) :
						$img_url = esc_url( $slide->image_url );
						$title   = esc_attr( $slide->title );
						$link        = $slide->link_url ? esc_url( $slide->link_url ) : '';
						$is_external = $link && preg_match( '#^https?://#', $slide->link_url );
					?>
						<div class="xs-slide" data-index="<?php echo esc_attr( $index ); ?>">
							<?php if ( $link ) : ?>
								<a href="<?php echo esc_url( $link ); ?>" class="xs-slide-link"<?php echo $is_external ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
							<?php endif; ?>

							<div class="xs-slide-inner"<?php echo ( 'fixed' === $ratio && $fixed_height > 0 ) ? ' style="height:' . esc_attr( $fixed_height ) . 'px;"' : ''; ?>>
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="xs-slide-image" loading="lazy">
								<?php if ( '3d' === $layout ) : ?>
									<div class="xs-slide-overlay"></div>
								<?php endif; ?>
								<?php if ( $slide->title ) : ?>
									<?php if ( 'default' === $layout ) : ?>
										<div class="xs-slide-caption xs-caption-vertical"><?php echo esc_html( $slide->title ); ?></div>
									<?php else : ?>
										<div class="xs-slide-caption"><?php echo esc_html( $slide->title ); ?></div>
									<?php endif; ?>
								<?php endif; ?>
							</div>

							<?php if ( 'default' === $layout && ( $slide->caption || $slide->description ) ) : ?>
								<div class="xs-slide-info">
									<?php if ( $slide->caption ) : ?>
										<h3 class="xs-slide-info-caption"><?php echo esc_html( $slide->caption ); ?></h3>
									<?php endif; ?>
									<?php if ( $slide->description ) : ?>
										<p class="xs-slide-info-desc"><?php echo esc_html( $slide->description ); ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ( $link ) : ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Navigation Arrows -->
			<?php if ( 'default' === $layout ) : ?>
				<button class="xs-nav xs-nav-prev" aria-label="<?php esc_attr_e( 'Previous slide', 'xtreme-slider' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square"><polyline points="15 18 9 12 15 6"/></svg>
				</button>
				<button class="xs-nav xs-nav-next" aria-label="<?php esc_attr_e( 'Next slide', 'xtreme-slider' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square"><polyline points="9 18 15 12 9 6"/></svg>
				</button>
			<?php else : ?>
				<button class="xs-nav xs-nav-prev" aria-label="<?php esc_attr_e( 'Previous slide', 'xtreme-slider' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
				</button>
				<button class="xs-nav xs-nav-next" aria-label="<?php esc_attr_e( 'Next slide', 'xtreme-slider' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
				</button>
			<?php endif; ?>

			<!-- Pagination Dots (not shown for default layout) -->
			<?php if ( 'default' !== $layout && $total > $visible ) : ?>
			<div class="xs-dots">
				<?php
				$pages = ceil( $total / $visible );
				if ( '3d' === $layout ) {
					$pages = $total;
				}
				for ( $i = 0; $i < $pages; $i++ ) : ?>
					<?php
					/* translators: %d: slide number */
					$dot_label = sprintf( __( 'Go to slide %d', 'xtreme-slider' ), $i + 1 );
					?>
					<button class="xs-dot <?php echo 0 === $i ? 'active' : ''; ?>" data-page="<?php echo esc_attr( $i ); ?>" aria-label="<?php echo esc_attr( $dot_label ); ?>"></button>
				<?php endfor; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
