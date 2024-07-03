<?php
/**
 * Plugin Name: MK Simple Scroll Progress
 * Plugin URI: https://mamikonars.github.io/personal/
 * Description: A simple scroll progress indicator plugin.
 * Version: 1.0.0
 * Author: Mamikon
 * Author URI: https://linkedin.com/in/mamikon-arustamyan-3969301ab?/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: mk-simple-scroll-progress
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'MK_SIMPLE_SCROLL_PROGRESS_VERSION', '1.0.0' );
define( 'MK_SIMPLE_SCROLL_PROGRESS_PATH', plugin_dir_path( __FILE__ ) );
define( 'MK_SIMPLE_SCROLL_PROGRESS_URL', plugin_dir_url( __FILE__ ) );

require_once MK_SIMPLE_SCROLL_PROGRESS_PATH . 'includes/class-mk-simple-scroll-progress.php';

function run_mk_simple_scroll_progress() {
	$plugin = new MK_Simple_Scroll_Progress();
	$plugin->run();
}

run_mk_simple_scroll_progress();

function mk_simple_scroll_progress_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=mk-simple-scroll-progress-settings">' . __( 'Settings' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mk_simple_scroll_progress_settings_link' );