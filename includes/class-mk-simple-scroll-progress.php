<?php

class MK_Simple_Scroll_Progress {

	protected $loader;

	public function __construct() {
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies() {
		require_once MK_SIMPLE_SCROLL_PROGRESS_PATH . 'includes/class-mk-simple-scroll-progress-admin.php';
		require_once MK_SIMPLE_SCROLL_PROGRESS_PATH . 'includes/class-mk-simple-scroll-progress-public.php';
	}

	private function define_admin_hooks() {
		$plugin_admin = new MK_Simple_Scroll_Progress_Admin();

		add_action( 'admin_menu', array( $plugin_admin, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $plugin_admin, 'register_settings' ) );
	}

	private function define_public_hooks() {
		$plugin_public = new MK_Simple_Scroll_Progress_Public();

		add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $plugin_public, 'add_scroll_progress_indicator' ) );
	}

	public function run() {
		// Placeholder for future code
	}
}
