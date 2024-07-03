<?php

class MK_Simple_Scroll_Progress_Public {

	public function __construct() {
		$this->plugin_name = 'mk-simple-scroll-progress';
		$this->version = '1.0.0';
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../public/js/mk-simple-scroll-progress-public.js', array(), $this->version, true );
	}

	public function add_scroll_progress_indicator() {
		$options = get_option( 'mk_simple_scroll_progress_settings' );
		$excluded_pages = isset( $options['excluded_pages'] ) ? explode( ',', $options['excluded_pages'] ) : array();

		if ( in_array( get_the_ID(), $excluded_pages ) ) {
			return;
		}

		$position = isset( $options['position'] ) ? $options['position'] : 'top';
		$background_color = isset( $options['background_color'] ) ? $options['background_color'] : '#0170B9';
		$background_opacity = isset( $options['background_opacity'] ) ? $options['background_opacity'] : '1';
		$height = isset( $options['height'] ) ? $options['height'] : '4';
		$border_radius = isset( $options['border_radius'] ) ? $options['border_radius'] : '0';

		echo '<div id="mk-scroll-progress-indicator" style="position:fixed; left:0; width:0; height:' . esc_attr( $height ) . 'px; background-color:' . esc_attr( $background_color ) . '; opacity:' . esc_attr( $background_opacity ) . '; border-radius:' . esc_attr( $border_radius ) . 'px; z-index:99999; ' . ( $position == 'top' ? 'top:0;' : 'bottom:0;' ) . '"></div>';
	}
}
