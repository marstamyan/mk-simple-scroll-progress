<?php
/**
 * Plugin Name: MK Simple Scroll Progress
 * Plugin URI: https://github.com/marstamyan/mk-simple-scroll-progress
 * Description: A simple scroll progress indicator plugin.
 * Version: 1.0.1
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Mamikon
 * Author URI: https://linkedin.com/in/mamikon-arustamyan-3969301ab?/
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mk-simple-scroll-progress
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'MK_SIMPLE_SCROLL_PROGRESS_VERSION', '1.0.1' );
define( 'MK_SIMPLE_SCROLL_PROGRESS_PATH', plugin_dir_path( __FILE__ ) );
define( 'MK_SIMPLE_SCROLL_PROGRESS_URL', plugin_dir_url( __FILE__ ) );

require_once MK_SIMPLE_SCROLL_PROGRESS_PATH . 'includes/class-mk-simple-scroll-progress.php';

function run_mk_simple_scroll_progress() {
	$plugin = new MK_Simple_Scroll_Progress();
	$plugin->run();
}

run_mk_simple_scroll_progress();

function mk_simple_scroll_progress_settings_link( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=mk-simple-scroll-progress-settings' ) ) . '">' . esc_html__( 'Settings', 'mk-simple-scroll-progress' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mk_simple_scroll_progress_settings_link' );
