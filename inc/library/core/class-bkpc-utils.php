<?php
/**
 * Provides utility functions for sanitization, time calculations,
 * and directory size operations.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      0.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BKPC_Utils' ) ) {
	class BKPC_Utils {
		public function __construct() {}

		/**
		 * Recursively sanitize all values in an array using sanitize_text_field.
		 */
		public function sanitize_text_field_array( $request_data ) {
			if ( ! is_array( $request_data ) ) {
				return array();
			}

			$sanitized_data = array();

			foreach ( $request_data as $key => $value ) {
				$sanitized_key = sanitize_text_field( $key );
				if ( is_array( $value ) ) {
					$sanitized_data[ $sanitized_key ] = $this->sanitize_text_field_array( $value );
				} else {
					$sanitized_data[ $sanitized_key ] = sanitize_text_field( $value );
				}
			}

			return $sanitized_data;
		}

		/**
		 * Convert a timestamp to human-readable time elapsed string.
		 */
		public function get_time_elapsed( $timestamp ) {
			$time_difference = time() - $timestamp;

			if ( $time_difference < 1 ) {
				return 'less than 1 second ago';
			}

			$condition = array(
				12 * 30 * 24 * 60 * 60 => 'year',
				30 * 24 * 60 * 60      => 'month',
				24 * 60 * 60           => 'day',
				60 * 60                => 'hour',
				60                     => 'minute',
				1                      => 'second',
			);

			foreach ( $condition as $seconds => $string ) {
				$diff = $time_difference / $seconds;
				if ( $diff >= 1 ) {
					$time = round( $diff );
					return $time . ' ' . $string . ( $time > 1 ? 's' : '' ) . ' ago';
				}
			}
		}

		/**
		 * Get formatted directory size string.
		 */
		public function show_dir_size( $path ) {
			return $this->get_dir_size( $path );
		}

		/**
		 * Calculate directory size with optional formatting.
		 */
		public function get_dir_size( $path, $in_bytes = false ) {
			$size = $this->calculate_dir_size( $path );

			if ( true === $in_bytes ) {
				return $size;
			}

			if ( $size < 1024 ) {
				$size = $size . ' Bytes';
			} elseif ( ( $size < 1048576 ) && ( $size > 1023 ) ) {
				$size = round( $size / 1024, 1 ) . ' KB';
			} elseif ( ( $size < 1073741824 ) && ( $size > 1048575 ) ) {
				$size = round( $size / 1048576, 1 ) . ' MB';
			} else {
				$size = round( $size / 1073741824, 1 ) . ' GB';
			}

			return $size;
		}

		/**
		 * Recursively calculate the total size of a directory in bytes.
		 */
		private function calculate_dir_size( $path ) {
			$files = scandir( $path );
			$size  = 0;

			foreach ( $files as $file ) {
				if ( '.' !== $file && '..' !== $file ) {
					$absolute_path = trailingslashit( $path ) . $file;
					if ( is_dir( $absolute_path ) ) {
						$size += $this->calculate_dir_size( $absolute_path );
					} else {
						$size += filesize( $absolute_path );
					}
				}
			}

			return $size;
		}
	}
}
