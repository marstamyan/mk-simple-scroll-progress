<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MK_Simple_Scroll_Progress_Public {

	private function should_display() {
		if ( is_admin() || is_feed() || is_embed() ) {
			return false;
		}

		$options = MK_Simple_Scroll_Progress_Settings::get();
		$excluded_pages = array_map( 'intval', explode( ',', $options['excluded_pages'] ) );
		// The last post in a secondary Loop is not necessarily the requested page.
		$page_id = ( is_singular() || is_home() ) ? (int) get_queried_object_id() : 0;

		return 0 === $page_id || ! in_array( $page_id, $excluded_pages, true );
	}

	public function enqueue_scripts() {
		if ( ! $this->should_display() ) {
			return;
		}

		wp_enqueue_style( 'mk-simple-scroll-progress', MK_SIMPLE_SCROLL_PROGRESS_URL . 'public/css/mk-simple-scroll-progress-public.css', array(), MK_SIMPLE_SCROLL_PROGRESS_VERSION );
		wp_enqueue_script( 'mk-simple-scroll-progress', MK_SIMPLE_SCROLL_PROGRESS_URL . 'public/js/mk-simple-scroll-progress-public.js', array(), MK_SIMPLE_SCROLL_PROGRESS_VERSION, true );
	}

	public function add_scroll_progress_indicator() {
		if ( ! $this->should_display() ) {
			return;
		}

		$options = MK_Simple_Scroll_Progress_Settings::get();
		$style = sprintf(
			'height:%dpx;background-color:%s;opacity:%s;border-radius:%dpx;',
			$options['height'],
			$options['background_color'],
			(string) $options['background_opacity'],
			$options['border_radius']
		);

		printf(
			'<div id="mk-scroll-progress-indicator" class="mk-scroll-progress-%s" style="%s" aria-hidden="true"></div>',
			esc_attr( $options['position'] ),
			esc_attr( $style )
		);
	}
}
