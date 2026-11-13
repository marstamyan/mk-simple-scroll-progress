<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MK_Simple_Scroll_Progress_Admin {

	public function add_plugin_admin_menu() {
		add_menu_page(
			__( 'MK Simple Scroll Progress Settings', 'mk-simple-scroll-progress' ),
			__( 'Scroll Progress', 'mk-simple-scroll-progress' ),
			'manage_options',
			'mk-simple-scroll-progress-settings',
			array( $this, 'display_plugin_admin_page' ),
			'dashicons-admin-generic',
			80
		);
	}

	public function display_plugin_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'mk_simple_scroll_progress_options' );
				do_settings_sections( 'mk_simple_scroll_progress_options' );
				submit_button( __( 'Save Settings', 'mk-simple-scroll-progress' ) );
				?>
			</form>
		</div>
		<?php
	}

	public function register_settings() {
		register_setting(
			'mk_simple_scroll_progress_options',
			'mk_simple_scroll_progress_settings',
			array( 'type' => 'array', 'sanitize_callback' => array( $this, 'sanitize_settings' ) )
		);
		add_settings_section(
			'mk_simple_scroll_progress_section',
			__( 'Scroll Progress Settings', 'mk-simple-scroll-progress' ),
			array( $this, 'settings_section_callback' ),
			'mk_simple_scroll_progress_options'
		);

		$fields = array(
			'excluded_pages'     => __( 'Exclude Pages (IDs)', 'mk-simple-scroll-progress' ),
			'position'           => __( 'Progress Bar Position', 'mk-simple-scroll-progress' ),
			'background_color'   => __( 'Background Color', 'mk-simple-scroll-progress' ),
			'background_opacity' => __( 'Background Opacity', 'mk-simple-scroll-progress' ),
			'height'             => __( 'Progress Bar Height', 'mk-simple-scroll-progress' ),
			'border_radius'      => __( 'Border Radius', 'mk-simple-scroll-progress' ),
		);
		foreach ( $fields as $key => $label ) {
			add_settings_field(
				$key,
				$label,
				array( $this, $key . '_callback' ),
				'mk_simple_scroll_progress_options',
				'mk_simple_scroll_progress_section',
				array( 'label_for' => 'mk-scroll-progress-' . $key )
			);
		}
	}

	public function sanitize_settings( $input ) {
		return MK_Simple_Scroll_Progress_Settings::sanitize( $input );
	}

	public function settings_section_callback() {
		echo '<p>' . esc_html__( 'Configure where and how the scroll progress indicator should appear.', 'mk-simple-scroll-progress' ) . '</p>';
	}

	public function excluded_pages_callback() {
		$options = MK_Simple_Scroll_Progress_Settings::get();
		?>
		<input type="text" id="mk-scroll-progress-excluded_pages" name="mk_simple_scroll_progress_settings[excluded_pages]" value="<?php echo esc_attr( $options['excluded_pages'] ); ?>" class="regular-text" aria-describedby="mk-scroll-progress-exclusions-help" />
		<p class="description" id="mk-scroll-progress-exclusions-help"><?php esc_html_e( 'Enter page or post IDs separated by commas. You can also exclude the assigned posts page. Invalid IDs are ignored.', 'mk-simple-scroll-progress' ); ?></p>
		<?php
	}

	public function position_callback() {
		$options = MK_Simple_Scroll_Progress_Settings::get();
		?>
		<select id="mk-scroll-progress-position" name="mk_simple_scroll_progress_settings[position]">
			<option value="top" <?php selected( $options['position'], 'top' ); ?>><?php esc_html_e( 'Top', 'mk-simple-scroll-progress' ); ?></option>
			<option value="bottom" <?php selected( $options['position'], 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'mk-simple-scroll-progress' ); ?></option>
		</select>
		<?php
	}

	public function background_color_callback() {
		$options = MK_Simple_Scroll_Progress_Settings::get();
		$color = $options['background_color'];
		// Native color inputs require a six-digit hex value.
		if ( 4 === strlen( $color ) ) {
			$color = '#' . $color[1] . $color[1] . $color[2] . $color[2] . $color[3] . $color[3];
		}
		?>
		<input type="color" id="mk-scroll-progress-background_color" name="mk_simple_scroll_progress_settings[background_color]" value="<?php echo esc_attr( $color ); ?>" />
		<?php
	}

	public function background_opacity_callback() {
		$this->number_field( 'background_opacity', 0, 1, '0.1' );
	}

	public function height_callback() {
		$this->number_field( 'height', 1, 100 );
		echo '<p class="description">' . esc_html__( 'Height in pixels (1–100).', 'mk-simple-scroll-progress' ) . '</p>';
	}

	public function border_radius_callback() {
		$this->number_field( 'border_radius', 0, 100 );
		echo '<p class="description">' . esc_html__( 'Corner radius in pixels (0–100).', 'mk-simple-scroll-progress' ) . '</p>';
	}

	private function number_field( $key, $min, $max, $step = '1' ) {
		$options = MK_Simple_Scroll_Progress_Settings::get();
		printf(
			'<input type="number" id="mk-scroll-progress-%1$s" name="mk_simple_scroll_progress_settings[%1$s]" value="%2$s" min="%3$s" max="%4$s" step="%5$s" />',
			esc_attr( $key ),
			esc_attr( $options[ $key ] ),
			esc_attr( $min ),
			esc_attr( $max ),
			esc_attr( $step )
		);
	}
}
