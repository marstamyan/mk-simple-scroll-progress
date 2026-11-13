<?php
/**
 * Standalone PHP regression tests. Run: php tests/php-tests.php
 *
 * These small WordPress API stubs exercise plugin behavior without a database.
 * They do not replace integration testing on an actual WordPress installation.
 */

error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	if ( ! ( error_reporting() & $severity ) ) {
		return false;
	}
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'WPINC', 'wp-includes' );

$GLOBALS['mk_test_hooks']    = array();
$GLOBALS['mk_test_options']  = array();
$GLOBALS['mk_test_settings'] = array();
$GLOBALS['mk_test_fields']   = array();
$GLOBALS['mk_test_query']    = array();
$GLOBALS['mk_test_assets']   = array( 'scripts' => array(), 'styles' => array(), 'inline' => array() );

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	$GLOBALS['mk_test_hooks'][] = array( $hook, $callback, $priority, $accepted_args );
	return true;
}
function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	return add_action( $hook, $callback, $priority, $accepted_args );
}
function plugin_dir_path( $file ) {
	return str_replace( '\\', '/', dirname( $file ) ) . '/';
}
function plugin_dir_url( $file ) {
	$root   = str_replace( '\\', '/', dirname( __DIR__ ) );
	$folder = str_replace( '\\', '/', dirname( $file ) );
	$suffix = trim( substr( $folder, strlen( $root ) ), '/' );
	return 'https://example.test/wp-content/plugins/mk-simple-scroll-progress/' . ( $suffix ? $suffix . '/' : '' );
}
function plugin_basename( $file ) {
	return 'mk-simple-scroll-progress/' . basename( $file );
}
function __( $text, $domain = 'default' ) {
	return $text;
}
function esc_attr( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}
function esc_html( $text ) {
	return esc_attr( $text );
}
function esc_url( $text ) {
	return esc_attr( $text );
}
function esc_html__( $text, $domain = 'default' ) {
	return esc_html( __( $text, $domain ) );
}
function esc_attr__( $text, $domain = 'default' ) {
	return esc_attr( __( $text, $domain ) );
}
function esc_html_e( $text, $domain = 'default' ) {
	echo esc_html__( $text, $domain );
}
function esc_attr_e( $text, $domain = 'default' ) {
	echo esc_attr__( $text, $domain );
}
function admin_url( $path = '' ) {
	return 'https://example.test/wp-admin/' . $path;
}
function get_option( $name, $default = false ) {
	return array_key_exists( $name, $GLOBALS['mk_test_options'] ) ? $GLOBALS['mk_test_options'][ $name ] : $default;
}
function sanitize_hex_color( $color ) {
	if ( '' === $color ) {
		return '';
	}
	return preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ? $color : null;
}
function sanitize_text_field( $text ) {
	return is_array( $text ) || is_object( $text ) ? '' : trim( strip_tags( (string) $text ) );
}
function absint( $value ) {
	return abs( (int) $value );
}
function wp_parse_args( $args, $defaults = array() ) {
	return array_merge( $defaults, is_array( $args ) ? $args : array() );
}
function wp_json_encode( $value, $flags = 0, $depth = 512 ) {
	return json_encode( $value, $flags, $depth );
}
function mk_test_query_flag( $flag ) {
	return ! empty( $GLOBALS['mk_test_query'][ $flag ] );
}
function is_admin() { return mk_test_query_flag( 'admin' ); }
function is_feed() { return mk_test_query_flag( 'feed' ); }
function is_embed() { return mk_test_query_flag( 'embed' ); }
function is_singular( $post_types = '' ) { return mk_test_query_flag( 'singular' ); }
function is_home() { return mk_test_query_flag( 'home' ); }
function is_archive() { return mk_test_query_flag( 'archive' ); }
function is_admin_bar_showing() { return mk_test_query_flag( 'admin_bar' ); }
function get_queried_object_id() {
	return isset( $GLOBALS['mk_test_query']['queried_id'] ) ? $GLOBALS['mk_test_query']['queried_id'] : 0;
}
function get_the_ID() {
	return isset( $GLOBALS['mk_test_query']['loop_id'] ) ? $GLOBALS['mk_test_query']['loop_id'] : false;
}
function wp_enqueue_script( $handle, $src = '', $deps = array(), $version = false, $args = false ) {
	$GLOBALS['mk_test_assets']['scripts'][ $handle ] = array( 'src' => $src, 'deps' => $deps, 'version' => $version, 'args' => $args );
}
function wp_enqueue_style( $handle, $src = '', $deps = array(), $version = false, $media = 'all' ) {
	$GLOBALS['mk_test_assets']['styles'][ $handle ] = array( 'src' => $src, 'deps' => $deps, 'version' => $version, 'media' => $media );
}
function wp_add_inline_style( $handle, $css ) {
	$GLOBALS['mk_test_assets']['inline'][ $handle ][] = $css;
	return true;
}
function wp_add_inline_script( $handle, $script, $position = 'after' ) {
	return true;
}
function current_user_can( $capability ) {
	return ! isset( $GLOBALS['mk_test_query']['can_manage'] ) || $GLOBALS['mk_test_query']['can_manage'];
}
function add_menu_page( $title, $menu, $capability, $slug, $callback, $icon = '', $position = null ) {
	return 'toplevel_page_' . $slug;
}
function add_options_page( $title, $menu, $capability, $slug, $callback ) {
	return 'settings_page_' . $slug;
}
function register_setting( $group, $name, $args = array() ) {
	$GLOBALS['mk_test_settings'][ $name ] = array( 'group' => $group, 'args' => $args );
}
function add_settings_section( $id, $title, $callback, $page, $args = array() ) {}
function add_settings_field( $id, $title, $callback, $page, $section = 'default', $args = array() ) {
	$GLOBALS['mk_test_fields'][ $id ] = array( 'callback' => $callback, 'args' => $args );
}
function get_admin_page_title() { return 'MK Simple Scroll Progress Settings'; }
function settings_fields( $group ) { echo '<input type="hidden" name="option_page" value="' . esc_attr( $group ) . '">'; }
function do_settings_sections( $page ) {
	foreach ( $GLOBALS['mk_test_fields'] as $field ) {
		call_user_func( $field['callback'], $field['args'] );
	}
}
function submit_button( $text = 'Save Changes' ) { echo '<button>' . esc_html( $text ) . '</button>'; }
function settings_errors( $setting = '', $sanitize = false, $hide = false ) {}
function selected( $selected, $current = true, $display = true ) {
	$result = (string) $selected === (string) $current ? ' selected="selected"' : '';
	if ( $display ) { echo $result; }
	return $result;
}
function load_plugin_textdomain( $domain, $deprecated = false, $path = false ) { return true; }
function wp_die( $message = '' ) { throw new RuntimeException( $message ); }

$mk_tests_passed = 0;
$mk_tests_failed = 0;
function mk_expect( $condition, $message ) {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
}
function mk_same( $expected, $actual, $message ) {
	mk_expect( $expected === $actual, $message . '\nExpected: ' . var_export( $expected, true ) . '\nActual: ' . var_export( $actual, true ) );
}
function mk_test( $name, $callback ) {
	global $mk_tests_passed, $mk_tests_failed;
	try {
		$callback();
		++$mk_tests_passed;
		echo "PASS: $name\n";
	} catch ( Throwable $error ) {
		++$mk_tests_failed;
		echo "FAIL: $name\n  " . $error->getMessage() . "\n";
	}
}
function mk_capture( $callback ) {
	ob_start();
	try {
		$callback();
		return ob_get_contents();
	} finally {
		ob_end_clean();
	}
}
function mk_frontend( $query, $settings = array(), $extra_options = array() ) {
	$GLOBALS['mk_test_query']   = $query;
	$GLOBALS['mk_test_options'] = array_merge( $extra_options, array( 'mk_simple_scroll_progress_settings' => $settings ) );
	$GLOBALS['mk_test_assets']  = array( 'scripts' => array(), 'styles' => array(), 'inline' => array() );
	$public = new MK_Simple_Scroll_Progress_Public();
	$public->enqueue_scripts();
	return mk_capture( array( $public, 'add_scroll_progress_indicator' ) );
}
function mk_expect_hidden( $query, $settings = array(), $extra_options = array() ) {
	mk_same( '', trim( mk_frontend( $query, $settings, $extra_options ) ), 'Hidden pages must not receive indicator markup.' );
	mk_same( array(), $GLOBALS['mk_test_assets']['scripts'], 'Hidden pages must not load the progress script.' );
	mk_same( array(), $GLOBALS['mk_test_assets']['styles'], 'Hidden pages must not load the progress stylesheet.' );
}

mk_test( 'Plugin boots without PHP warnings and registers each callback once', function () {
	require dirname( __DIR__ ) . '/mk-simple-scroll-progress.php';
	$seen = array();
	foreach ( $GLOBALS['mk_test_hooks'] as $hook ) {
		$callback = $hook[1];
		$key = $hook[0] . ':' . ( is_array( $callback ) ? get_class( $callback[0] ) . '::' . $callback[1] : $callback );
		mk_expect( ! isset( $seen[ $key ] ), 'Duplicate callback registration: ' . $key );
		$seen[ $key ] = true;
	}
	foreach ( array( 'admin_menu:MK_Simple_Scroll_Progress_Admin::add_plugin_admin_menu', 'admin_init:MK_Simple_Scroll_Progress_Admin::register_settings', 'wp_enqueue_scripts:MK_Simple_Scroll_Progress_Public::enqueue_scripts', 'wp_footer:MK_Simple_Scroll_Progress_Public::add_scroll_progress_indicator' ) as $key ) {
		mk_expect( isset( $seen[ $key ] ), 'Missing plugin hook: ' . $key );
	}
} );

if ( ! class_exists( 'MK_Simple_Scroll_Progress_Settings' ) ) {
	echo "FAIL: Shared settings class was not loaded.\n";
	exit( 1 );
}

mk_test( 'Fresh installations keep the original visible defaults', function () {
	$GLOBALS['mk_test_options'] = array();
	$defaults = MK_Simple_Scroll_Progress_Settings::get_defaults();
	mk_same( 'top', $defaults['position'], 'Default position changed.' );
	mk_same( '#0170B9', $defaults['background_color'], 'Default color changed.' );
	mk_same( 1.0, (float) $defaults['background_opacity'], 'Default opacity changed.' );
	mk_same( 4, $defaults['height'], 'Default height changed.' );
	mk_same( 0, $defaults['border_radius'], 'Default radius changed.' );
	mk_same( '', $defaults['excluded_pages'], 'New installations should not exclude any posts.' );
	mk_same( $defaults, MK_Simple_Scroll_Progress_Settings::get(), 'Missing settings should use defaults.' );
} );

mk_test( 'Valid legacy settings survive normalization and unknown keys are discarded', function () {
	$settings = MK_Simple_Scroll_Progress_Settings::sanitize( array( 'position' => 'bottom', 'background_color' => '#abc', 'background_opacity' => '0.4', 'height' => '12', 'border_radius' => '5', 'excluded_pages' => ' 12,34,12, ,0,garbage ', 'unexpected' => 'discard' ) );
	mk_same( 'bottom', $settings['position'], 'Valid position was lost.' );
	mk_same( '#abc', $settings['background_color'], 'Three-digit colors must remain supported.' );
	mk_same( 0.4, (float) $settings['background_opacity'], 'Valid opacity was lost.' );
	mk_same( 12, $settings['height'], 'Valid height was lost.' );
	mk_same( 5, $settings['border_radius'], 'Valid radius was lost.' );
	mk_same( '12,34', $settings['excluded_pages'], 'Excluded IDs must be a unique, positive integer CSV.' );
	mk_expect( ! array_key_exists( 'unexpected', $settings ), 'Unknown settings must not be stored.' );
} );

mk_test( 'Numeric settings enforce their lower and upper limits on the server', function () {
	$low  = MK_Simple_Scroll_Progress_Settings::sanitize( array( 'background_opacity' => -2, 'height' => 0, 'border_radius' => -1 ) );
	$high = MK_Simple_Scroll_Progress_Settings::sanitize( array( 'background_opacity' => 2, 'height' => 101, 'border_radius' => 101 ) );
	mk_same( 0.0, (float) $low['background_opacity'], 'Negative opacity was not clamped.' );
	mk_same( 1, $low['height'], 'Height below one pixel was accepted.' );
	mk_same( 0, $low['border_radius'], 'Negative radius was accepted.' );
	mk_same( 1.0, (float) $high['background_opacity'], 'Opacity above one was accepted.' );
	mk_same( 100, $high['height'], 'Oversized height was accepted.' );
	mk_same( 100, $high['border_radius'], 'Oversized radius was accepted.' );
} );

mk_test( 'Malformed settings and nested values cannot trigger PHP warnings or CSS injection', function () {
	$defaults = MK_Simple_Scroll_Progress_Settings::get_defaults();
	foreach ( array( null, false, true, 42, 'broken', new stdClass() ) as $input ) {
		mk_same( $defaults, MK_Simple_Scroll_Progress_Settings::sanitize( $input ), 'A malformed settings container did not fall back to defaults.' );
	}
	foreach ( array( array(), new stdClass() ) as $value ) {
		$input = array_fill_keys( array_keys( $defaults ), $value );
		mk_same( $defaults, MK_Simple_Scroll_Progress_Settings::sanitize( $input ), 'Nested values should fall back to defaults.' );
	}
	$settings = MK_Simple_Scroll_Progress_Settings::sanitize( array( 'position' => 'top;display:none', 'background_color' => '#fff;background:url(javascript:alert(1))', 'background_opacity' => NAN, 'height' => INF, 'border_radius' => 'invalid' ) );
	mk_same( $defaults, $settings, 'Invalid scalar values should fall back to defaults.' );
} );

mk_test( 'Corrupted stored settings are normalized before use', function () {
	$defaults = MK_Simple_Scroll_Progress_Settings::get_defaults();
	foreach ( array( false, 'broken', new stdClass(), array( 'background_color' => array(), 'height' => -10 ) ) as $stored ) {
		$GLOBALS['mk_test_options'] = array( 'mk_simple_scroll_progress_settings' => $stored );
		$result = MK_Simple_Scroll_Progress_Settings::get();
		mk_same( '#0170B9', $result['background_color'], 'Corrupt stored color was not normalized.' );
		mk_expect( $result['height'] >= 1 && $result['height'] <= 100, 'Corrupt stored height reached output.' );
	}
	$GLOBALS['mk_test_options'] = array( 'mk_simple_scroll_progress_settings' => array( 'position' => 'bottom' ) );
	$expected = $defaults;
	$expected['position'] = 'bottom';
	mk_same( $expected, MK_Simple_Scroll_Progress_Settings::get(), 'Partial legacy settings lost defaults.' );
} );

mk_test( 'Admin settings use the shared sanitizer and render on a fresh installation', function () {
	$GLOBALS['mk_test_options'] = array();
	$GLOBALS['mk_test_query']   = array();
	$hook_count = count( $GLOBALS['mk_test_hooks'] );
	$admin = new MK_Simple_Scroll_Progress_Admin();
	mk_same( $hook_count, count( $GLOBALS['mk_test_hooks'] ), 'Admin constructor must leave hook registration to the plugin loader.' );
	$input = array( 'height' => -20, 'position' => 'invalid', 'background_color' => array() );
	mk_same( MK_Simple_Scroll_Progress_Settings::sanitize( $input ), $admin->sanitize_settings( $input ), 'Admin sanitizer differs from frontend normalization.' );
	$admin->register_settings();
	mk_expect( isset( $GLOBALS['mk_test_settings']['mk_simple_scroll_progress_settings'] ), 'Plugin option was not registered.' );
	$registration = $GLOBALS['mk_test_settings']['mk_simple_scroll_progress_settings'];
	mk_same( 'mk_simple_scroll_progress_options', $registration['group'], 'Settings form and registration groups must match.' );
	$args = $registration['args'];
	$callback = isset( $args['sanitize_callback'] ) ? $args['sanitize_callback'] : $args;
	mk_expect( is_callable( $callback ), 'Registered settings lack a sanitizer.' );
	mk_same( $admin->sanitize_settings( $input ), call_user_func( $callback, $input ), 'WordPress would call a different sanitizer.' );
	$output = mk_capture( array( $admin, 'display_plugin_admin_page' ) );
	mk_expect( strpos( $output, 'mk_simple_scroll_progress_settings[excluded_pages]' ) !== false, 'Fresh admin form did not render its settings fields.' );
} );

mk_test( 'Visible pages receive versioned assets and a decorative progress indicator', function () {
	$output = mk_frontend( array( 'singular' => true, 'queried_id' => 7, 'loop_id' => 7 ) );
	mk_expect( strpos( $output, 'id="mk-scroll-progress-indicator"' ) !== false, 'Indicator is missing.' );
	mk_expect( preg_match( '/aria-hidden=["\']true["\']/', $output ) === 1, 'Decorative indicator should be hidden from assistive technology.' );
	$assets = $GLOBALS['mk_test_assets'];
	mk_expect( isset( $assets['scripts']['mk-simple-scroll-progress'], $assets['styles']['mk-simple-scroll-progress'] ), 'Progress assets were not enqueued together.' );
	$script = $assets['scripts']['mk-simple-scroll-progress'];
	mk_same( MK_SIMPLE_SCROLL_PROGRESS_URL . 'public/js/mk-simple-scroll-progress-public.js', $script['src'], 'Script URL should use the plugin URL.' );
	mk_same( MK_SIMPLE_SCROLL_PROGRESS_VERSION, $script['version'], 'Script cache version differs from plugin version.' );
	mk_same( MK_SIMPLE_SCROLL_PROGRESS_VERSION, $assets['styles']['mk-simple-scroll-progress']['version'], 'Stylesheet cache version differs from plugin version.' );
	$css = $output;
	foreach ( glob( dirname( __DIR__ ) . '/public/css/*.css' ) as $file ) { $css .= file_get_contents( $file ); }
	foreach ( $assets['inline'] as $blocks ) { $css .= implode( '\n', $blocks ); }
	mk_expect( preg_match( '/pointer-events\s*:\s*none/', $css ) === 1, 'Indicator must not intercept page clicks.' );
} );

mk_test( 'Exclusions follow the singular queried post instead of a secondary Loop', function () {
	mk_expect_hidden( array( 'singular' => true, 'queried_id' => 42, 'loop_id' => 99 ), array( 'excluded_pages' => '42' ) );
	$output = mk_frontend( array( 'singular' => true, 'queried_id' => 7, 'loop_id' => 42 ), array( 'excluded_pages' => '42' ) );
	mk_expect( strpos( $output, 'mk-scroll-progress-indicator' ) !== false, 'An unrelated excluded Loop post hid the current page.' );
} );

mk_test( 'A designated posts page can be excluded without hiding archives', function () {
	mk_expect_hidden( array( 'home' => true, 'queried_id' => 42, 'loop_id' => 99 ), array( 'excluded_pages' => '42' ), array( 'page_for_posts' => 42 ) );
	$output = mk_frontend( array( 'archive' => true, 'queried_id' => 42, 'loop_id' => 42 ), array( 'excluded_pages' => '42' ) );
	mk_expect( strpos( $output, 'mk-scroll-progress-indicator' ) !== false, 'Post exclusions must not also exclude matching taxonomy IDs or archive Loop posts.' );
} );

mk_test( 'Admin, feed and embed responses receive no progress assets or markup', function () {
	foreach ( array( 'admin', 'feed', 'embed' ) as $flag ) {
		mk_expect_hidden( array( $flag => true, 'singular' => true, 'queried_id' => 7 ) );
	}
} );

mk_test( 'Settings link points to the registered admin page', function () {
	$links = mk_simple_scroll_progress_settings_link( array( '<a href="plugins.php">Plugins</a>' ) );
	mk_expect( strpos( $links[0], 'wp-admin/admin.php?page=mk-simple-scroll-progress-settings' ) !== false, 'Settings link does not match the top-level menu page.' );
	mk_same( 2, count( $links ), 'Existing plugin action links must remain available.' );
} );

mk_test( 'Admin page checks permissions and expands short colors for the native selector', function () {
	$admin = new MK_Simple_Scroll_Progress_Admin();
	$GLOBALS['mk_test_query'] = array( 'can_manage' => false );
	mk_same( '', mk_capture( array( $admin, 'display_plugin_admin_page' ) ), 'A user without manage_options must not see the settings form.' );
	$GLOBALS['mk_test_query'] = array();
	$GLOBALS['mk_test_options'] = array( 'mk_simple_scroll_progress_settings' => array( 'background_color' => '#aB3' ) );
	$output = mk_capture( array( $admin, 'background_color_callback' ) );
	mk_expect( strpos( $output, 'type="color"' ) !== false && strpos( $output, 'value="#aaBB33"' ) !== false, 'The native selector requires a six-digit color.' );
} );

echo "\n$mk_tests_passed passed; $mk_tests_failed failed.\n";
exit( $mk_tests_failed ? 1 : 0 );
