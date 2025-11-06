<?php

namespace BKPC\Backup_Copilot;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Utils' ) ) {

	class BKPC_Utils extends Backup_Copilot {
		public function __construct() {
			parent::__construct();

			// Core (only these 2 classes can loaded here)
			$this->fs = new BKPC_FS;
			$this->db = new BKPC_DB;
		}

		public function sanitize_text_field_array( $request_data ) {
			if ( ! is_array( $request_data ) ) {
				return false;
			}

			foreach ( $request_data as $key => $value ) {
				if ( is_array( $value ) ) {
					$value = $this->sanitize_text_field_array( $value );
				} else {
					$value = sanitize_text_field( $value );
				}
			}

			return $request_data;
		}

		public function get_time_elapsed( $timestamp ) {
			$time_difference = time() - $timestamp;

			if ( $time_difference < 1 ) { 
				return 'less than 1 second ago'; 
			}

			$condition = array( 
				12 * 30 * 24 * 60 * 60 =>  'year',
				30 * 24 * 60 * 60      =>  'month',
				24 * 60 * 60           =>  'day',
				60 * 60                =>  'hour',
				60                     =>  'minute',
				1                      =>  'second',
			);

			foreach ( $condition as $seconds => $string ) {
				$diff = $time_difference / $seconds;
				if ( $diff >= 1 ) {
					$time = round( $diff );
					return $time  . ' ' . $string . ( $time > 1 ? 's' : '' ) . ' ago';
				}
			}
		}

		public function show_dir_size( $path ) {
			return $this->get_dir_size( $path );
		}

		public function get_dir_size( $path, $in_bytes = false ) {
			$size = $this->calculate_dir_size($path);

			if ( $in_bytes === true ) {
				return $size;
			}

			if ( $size < 1024 ) {
				$size = $size . " Bytes";
			} elseif ( ( $size < 1048576 ) && ( $size > 1023 ) ) {
				$size = round( $size / 1024, 1 ) . " KB";
			} elseif ( ( $size < 1073741824 ) && ( $size > 1048575 ) ) {
				$size = round( $size / 1048576, 1 ) . " MB";
			} else {
				$size = round( $size / 1073741824, 1 ) . " GB";
			}

			return $size;
		}

		private function calculate_dir_size( $path ) {
			$files = scandir( $path );
			$size = 0;

			foreach ( $files as $file ) {
				if ( $file !== '.' && $file !== '..' ) {
					$absolute_path = $path . DIRECTORY_SEPARATOR . $file;
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
