<?php

class MK_Simple_Scroll_Progress_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function add_plugin_admin_menu() {
		add_menu_page( 'MK Simple Scroll Progress Settings', 'Scroll Progress', 'manage_options', 'mk-simple-scroll-progress-settings', array(
			$this,
			'display_plugin_admin_page'
		), 'dashicons-admin-generic', 80 );
	}

	public function display_plugin_admin_page() {
		?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form method="post" action="options.php">
				<?php
				settings_fields( 'mk_simple_scroll_progress_options' );
				do_settings_sections( 'mk_simple_scroll_progress_options' );
				submit_button( 'Save Settings' );
				?>
            </form>
        </div>
		<?php
	}

	public function register_settings() {
		register_setting( 'mk_simple_scroll_progress_options', 'mk_simple_scroll_progress_settings', array(
			$this,
			'sanitize_settings'
		) );

		add_settings_section( 'mk_simple_scroll_progress_section', 'Scroll Progress Settings', array(
			$this,
			'settings_section_callback'
		), 'mk_simple_scroll_progress_options' );

		add_settings_field( 'excluded_pages', 'Exclude Pages (IDs)', array(
			$this,
			'excluded_pages_callback'
		), 'mk_simple_scroll_progress_options', 'mk_simple_scroll_progress_section' );
		add_settings_field( 'position', 'Progress Bar Position', array(
			$this,
			'position_callback'
		), 'mk_simple_scroll_progress_options', 'mk_simple_scroll_progress_section' );
		add_settings_field( 'background_color', 'Background Color', array(
			$this,
			'background_color_callback'
		), 'mk_simple_scroll_progress_options', 'mk_simple_scroll_progress_section' );
		add_settings_field( 'background_opacity', 'Background Opacity', array(
			$this,
			'background_opacity_callback'
		), 'mk_simple_scroll_progress_options', 'mk_simple_scroll_progress_section' );
		add_settings_field( 'height', 'Progress Bar Height', array(
			$this,
			'height_callback'
		), 'mk_simple_scroll_progress_options', 'mk_simple_scroll_progress_section' );
		add_settings_field( 'border_radius', 'Border Radius', array(
			$this,
			'border_radius_callback'
		), 'mk_simple_scroll_progress_options', 'mk_simple_scroll_progress_section' );
	}

	public function sanitize_settings( $input ) {
		$output = array();

		if ( isset( $input['position'] ) ) {
			$output['position'] = sanitize_text_field( $input['position'] );
		}

		if ( isset( $input['background_color'] ) ) {
			$output['background_color'] = $this->sanitize_color( $input['background_color'] );
		}

		if ( isset( $input['background_opacity'] ) ) {
			$output['background_opacity'] = floatval( $input['background_opacity'] );
		}

		if ( isset( $input['height'] ) ) {
			$output['height'] = intval( $input['height'] );
		}

		if ( isset( $input['border_radius'] ) ) {
			$output['border_radius'] = intval( $input['border_radius'] );
		}

		if ( isset( $input['excluded_pages'] ) ) {
			$output['excluded_pages'] = sanitize_text_field( $input['excluded_pages'] );
		}

		return $output;
	}

	private function sanitize_color( $color ) {
		if ( $this->is_short_hex( $color ) || preg_match( '/^#[a-f0-9]{6}$/i', $color ) ) {
			return $color;
		}

		return '';
	}

	private function is_short_hex( $color ) {
		return preg_match( '/^#[a-f0-9]{3}$/i', $color );
	}

	public function settings_section_callback() {
		echo '<p>Configure where and how the scroll progress indicator should appear.</p>';
	}

	public function excluded_pages_callback() {
		$options = get_option( 'mk_simple_scroll_progress_settings' );
		?>
        <input type="text" name="mk_simple_scroll_progress_settings[excluded_pages]"
               value="<?php echo esc_attr( $options['excluded_pages'] ); ?>"/>
        <p class="description">Enter the IDs of pages/posts to exclude, separated by commas.</p>
		<?php
	}

	public function position_callback() {
		$options = get_option( 'mk_simple_scroll_progress_settings' );
		$default = 'top';
		$value = isset( $options['position'] ) ? $options['position'] : $default;
		?>
        <select name="mk_simple_scroll_progress_settings[position]">
            <option value="top" <?php selected( $value, 'top' ); ?>>Top</option>
            <option value="bottom" <?php selected( $value, 'bottom' ); ?>>Bottom</option>
        </select>
		<?php
	}

	public function background_color_callback() {
		$options = get_option( 'mk_simple_scroll_progress_settings' );
		$default = '#0170B9';
		$value = isset( $options['background_color'] ) ? $options['background_color'] : $default;
		?>
        <input type="text" name="mk_simple_scroll_progress_settings[background_color]"
               value="<?php echo esc_attr( $value ); ?>" class="wp-color-picker-field"
               data-default-color="<?php echo esc_attr( $default ); ?>"/>
		<?php
	}

	public function background_opacity_callback() {
		$options = get_option( 'mk_simple_scroll_progress_settings' );
		$default = '1';
		$value = isset( $options['background_opacity'] ) ? $options['background_opacity'] : $default;
		?>
        <input type="number" name="mk_simple_scroll_progress_settings[background_opacity]"
               value="<?php echo esc_attr( $value ); ?>" min="0" max="1" step="0.1"/>
		<?php
	}

	public function height_callback() {
		$options = get_option( 'mk_simple_scroll_progress_settings' );
		$default = '4';
		$value = isset( $options['height'] ) ? $options['height'] : $default;
		?>
        <input type="number" name="mk_simple_scroll_progress_settings[height]" value="<?php echo esc_attr( $value ); ?>"
               min="1"/>
		<?php
	}

	public function border_radius_callback() {
		$options = get_option( 'mk_simple_scroll_progress_settings' );
		$default = '0';
		$value = isset( $options['border_radius'] ) ? $options['border_radius'] : $default;
		?>
        <input type="number" name="mk_simple_scroll_progress_settings[border_radius]"
               value="<?php echo esc_attr( $value ); ?>" min="0"/>
		<?php
	}
}
