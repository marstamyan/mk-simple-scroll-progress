<?php
/**
 * Integration checks against an isolated, installed WordPress site.
 * Usage: php tools/wp-integration.php /absolute/path/to/wordpress
 * Never point this at a real site: this creates posts and changes test options.
 */

$wordpress_path = isset( $argv[1] ) ? realpath( $argv[1] ) : false;
if ( ! $wordpress_path || ! is_file( $wordpress_path . '/.mk-scroll-test-site' ) ) {
	fwrite( STDERR, "Expected an isolated WordPress directory marked .mk-scroll-test-site.\n" );
	exit( 1 );
}

$_SERVER['HTTP_HOST']       = 'localhost:8765';
$_SERVER['SERVER_NAME']     = 'localhost';
$_SERVER['SERVER_PORT']     = '8765';
$_SERVER['REQUEST_METHOD']  = 'GET';
$_SERVER['REQUEST_URI']     = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

require $wordpress_path . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/template.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
require_once ABSPATH . 'wp-admin/includes/screen.php';

$checks = 0;
$plugin_warnings = array();
set_error_handler(
	function ( $severity, $message, $file, $line ) use ( &$plugin_warnings ) {
		if ( false !== strpos( str_replace( '\\', '/', $file ), '/wp-content/plugins/mk-simple-scroll-progress/' ) ) {
			$plugin_warnings[] = "$message ($file:$line)";
		}
		return false;
	}
);

function mk_integration_check( $condition, $message ) {
	global $checks;
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
	++$checks;
	echo "PASS: $message\n";
}

$plugin = 'mk-simple-scroll-progress/mk-simple-scroll-progress.php';
if ( is_plugin_active( $plugin ) ) {
	deactivate_plugins( $plugin );
}
$activation = activate_plugin( $plugin );
if ( is_wp_error( $activation ) ) {
	fwrite( STDERR, $activation->get_error_code() . ': ' . $activation->get_error_message() . "\n" );
}
mk_integration_check( ! is_wp_error( $activation ), 'Plugin activates through WordPress.' );
mk_integration_check( is_plugin_active( $plugin ), 'WordPress records the active plugin.' );

$user = get_user_by( 'login', 'mk_test_admin' );
wp_set_current_user( $user->ID );
set_current_screen( 'toplevel_page_mk-simple-scroll-progress-settings' );
$GLOBALS['title'] = 'MK Simple Scroll Progress Settings';
$GLOBALS['pagenow'] = 'admin.php';
$GLOBALS['plugin_page'] = 'mk-simple-scroll-progress-settings';
remove_action( 'admin_init', '_maybe_update_core' );
remove_action( 'admin_init', '_maybe_update_plugins' );
remove_action( 'admin_init', '_maybe_update_themes' );
do_action( 'admin_init' );
$admin = new MK_Simple_Scroll_Progress_Admin();
delete_option( 'mk_simple_scroll_progress_settings' );
ob_start();
$admin->display_plugin_admin_page();
$settings_html = ob_get_clean();
mk_integration_check( false !== strpos( $settings_html, 'mk_simple_scroll_progress_settings[excluded_pages]' ), 'Settings page renders on a clean installation.' );
mk_integration_check( false !== strpos( $settings_html, '_wpnonce' ), 'Settings form contains the WordPress nonce.' );

update_option(
	'mk_simple_scroll_progress_settings',
	array(
		'position'           => 'invalid',
		'background_color'   => array( 'bad input' ),
		'background_opacity' => 5,
		'height'             => -8,
		'border_radius'      => 300,
		'excluded_pages'     => '9,9,-1,abc,0010',
	)
);
$saved = get_option( 'mk_simple_scroll_progress_settings' );
mk_integration_check( 'top' === $saved['position'] && '#0170B9' === $saved['background_color'], 'WordPress option saving validates enum and malformed color.' );
mk_integration_check( 1 === (int) $saved['background_opacity'] && 1 === $saved['height'] && 100 === $saved['border_radius'], 'WordPress option saving bounds numeric settings.' );
mk_integration_check( '9,10' === $saved['excluded_pages'], 'WordPress option saving normalizes excluded IDs.' );

$front_page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Scroll integration front page' ) );
$posts_page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Scroll integration posts page' ) );
$excluded_page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Scroll integration excluded page' ) );
$post_id = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'Scroll integration post' ) );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $front_page );
update_option( 'page_for_posts', $posts_page );
update_option( 'mk_simple_scroll_progress_settings', array( 'excluded_pages' => "$excluded_page,$posts_page,$post_id" ) );

function mk_integration_render( $query ) {
	$GLOBALS['wp_query'] = new WP_Query( $query );
	$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
	$GLOBALS['post'] = get_post( get_option( 'page_for_posts' ) );
	wp_dequeue_script( 'mk-simple-scroll-progress' );
	wp_dequeue_style( 'mk-simple-scroll-progress' );
	$public = new MK_Simple_Scroll_Progress_Public();
	$public->enqueue_scripts();
	ob_start();
	$public->add_scroll_progress_indicator();
	return ob_get_clean();
}

// Front-end conditional tags should report a front-end request.
set_current_screen( 'front' );
$html = mk_integration_render( array( 'page_id' => $front_page ) );
mk_integration_check( false !== strpos( $html, 'id="mk-scroll-progress-indicator"' ), 'Public page renders the indicator despite an excluded secondary Loop post.' );
mk_integration_check( false !== strpos( $html, 'aria-hidden="true"' ), 'Decorative indicator is hidden from assistive technology.' );
mk_integration_check( wp_script_is( 'mk-simple-scroll-progress', 'enqueued' ) && wp_style_is( 'mk-simple-scroll-progress', 'enqueued' ), 'Public page enqueues both assets.' );
$scripts = wp_scripts();
$script = $scripts->registered['mk-simple-scroll-progress'];
mk_integration_check( MK_SIMPLE_SCROLL_PROGRESS_VERSION === $script->ver && empty( $script->deps ), 'Frontend asset uses the plugin version and has no library dependency.' );

$html = mk_integration_render( array( 'page_id' => $excluded_page ) );
mk_integration_check( '' === $html && ! wp_script_is( 'mk-simple-scroll-progress', 'enqueued' ) && ! wp_style_is( 'mk-simple-scroll-progress', 'enqueued' ), 'Excluded page emits neither markup nor assets.' );
$html = mk_integration_render( array( 'p' => $post_id ) );
mk_integration_check( '' === $html, 'Excluded post emits no markup.' );
$html = mk_integration_render( array( 'page_id' => $posts_page ) );
mk_integration_check( is_home() && '' === $html, 'Assigned posts page respects its excluded ID.' );
$html = mk_integration_render( array( 'year' => (int) gmdate( 'Y' ) ) );
mk_integration_check( is_archive() && false !== strpos( $html, 'mk-scroll-progress-indicator' ), 'An archive remains visible even when its Loop contains excluded posts.' );
$html = mk_integration_render( array( 'feed' => 'rss2' ) );
mk_integration_check( is_feed() && '' === $html, 'Feed responses do not receive the indicator.' );
$html = mk_integration_render( array( 'p' => $front_page, 'embed' => true ) );
mk_integration_check( is_embed() && '' === $html, 'Embedded responses do not receive the indicator.' );

set_current_screen( 'dashboard' );
$html = mk_integration_render( array( 'page_id' => $front_page ) );
mk_integration_check( '' === $html, 'Admin requests do not receive the indicator.' );
mk_integration_check( empty( $plugin_warnings ), 'Plugin emits no PHP warnings or deprecations: ' . implode( '; ', $plugin_warnings ) );
restore_error_handler();
echo "Completed $checks integration checks on WordPress $wp_version / PHP " . PHP_VERSION . ".\n";
