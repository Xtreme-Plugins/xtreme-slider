<?php
defined( 'ABSPATH' ) || exit;

class Xtrsl_Shortcode {

	public function __construct() {
		add_shortcode( 'xtreme_slider', array( $this, 'render' ) );
	}

	public function render( $atts ) {
		$atts = shortcode_atts( array(
			'id'         => 0,
			'layout'     => '',
			'visible'    => '',
			'autoplay'   => '',
			'fullscreen' => '',
		), $atts, 'xtreme_slider' );

		$slider_id = absint( $atts['id'] );
		if ( ! $slider_id ) {
			return '<!-- XtremeSlider: ' . esc_html__( 'No slider ID specified', 'xtreme-slider' ) . ' -->';
		}

		$slider = xtrsl_get_slider( $slider_id );
		if ( ! $slider || 'active' !== $slider->status ) {
			return '<!-- XtremeSlider: ' . esc_html__( 'Slider not found or inactive', 'xtreme-slider' ) . ' -->';
		}

		$slides = xtrsl_get_slides( $slider_id );
		if ( empty( $slides ) ) {
			return '<!-- XtremeSlider: ' . esc_html__( 'No slides found', 'xtreme-slider' ) . ' -->';
		}

		// Allow shortcode attribute overrides.
		$layout     = $atts['layout'] && in_array( $atts['layout'], array( 'default', 'cool', '3d' ), true ) ? $atts['layout'] : $slider->layout;
		$visible    = $atts['visible'] ? max( 1, min( 6, absint( $atts['visible'] ) ) ) : intval( $slider->visible_count );
		$autoplay   = '' !== $atts['autoplay'] ? filter_var( $atts['autoplay'], FILTER_VALIDATE_BOOLEAN ) : (bool) $slider->autoplay;
		$fullscreen = '' !== $atts['fullscreen'] ? filter_var( $atts['fullscreen'], FILTER_VALIDATE_BOOLEAN ) : (bool) $slider->fullscreen;

		// Enqueue frontend assets.
		wp_enqueue_style( 'xtrsl-slider', XTRSL_PLUGIN_URL . 'assets/css/slider.css', array(), XTRSL_VERSION );
		wp_enqueue_script( 'xtrsl-slider', XTRSL_PLUGIN_URL . 'assets/js/slider.js', array(), XTRSL_VERSION, true );

		$renderer = new Xtrsl_Renderer();
		return $renderer->render( $slider, $slides, array(
			'layout'     => $layout,
			'visible'    => $visible,
			'autoplay'   => $autoplay,
			'speed'      => intval( $slider->autoplay_speed ),
			'fullscreen' => $fullscreen,
			'ratio'      => $slider->image_ratio ?? '16:10',
		) );
	}
}
