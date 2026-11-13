<?php
/**
 * Shared defaults and validation for saved and submitted settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MK_Simple_Scroll_Progress_Settings {

	public static function get_defaults() {
		return array(
			'excluded_pages'     => '',
			'position'           => 'top',
			'background_color'   => '#0170B9',
			'background_opacity' => 1,
			'height'             => 4,
			'border_radius'      => 0,
		);
	}

	public static function get() {
		return self::sanitize( get_option( 'mk_simple_scroll_progress_settings', array() ) );
	}

	public static function sanitize( $input ) {
		$output = self::get_defaults();
		if ( ! is_array( $input ) ) {
			return $output;
		}

		if ( isset( $input['position'] ) && in_array( $input['position'], array( 'top', 'bottom' ), true ) ) {
			$output['position'] = $input['position'];
		}

		if ( isset( $input['background_color'] ) && is_string( $input['background_color'] ) ) {
			$color = sanitize_hex_color( $input['background_color'] );
			if ( $color ) {
				$output['background_color'] = $color;
			}
		}

		foreach ( array( 'background_opacity' => array( 0, 1 ), 'height' => array( 1, 100 ), 'border_radius' => array( 0, 100 ) ) as $key => $range ) {
			if ( isset( $input[ $key ] ) && is_numeric( $input[ $key ] ) && is_finite( (float) $input[ $key ] ) ) {
				$value = max( $range[0], min( $range[1], (float) $input[ $key ] ) );
				$output[ $key ] = 'background_opacity' === $key ? $value : (int) $value;
			}
		}

		if ( isset( $input['excluded_pages'] ) && is_string( $input['excluded_pages'] ) ) {
			$ids = array();
			foreach ( preg_split( '/[\s,]+/', $input['excluded_pages'], -1, PREG_SPLIT_NO_EMPTY ) as $id ) {
				$id = ltrim( $id, '0' );
				if ( preg_match( '/^[1-9][0-9]*$/D', $id ) && false !== filter_var( $id, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) ) ) {
					$ids[] = (int) $id;
				}
			}
			$output['excluded_pages'] = implode( ',', array_unique( $ids ) );
		}

		return $output;
	}
}
