<?php

namespace PressBits\UnitTest;

class WpImageEditor {
	protected $size;

	public static function alias() {
		if ( ! class_exists( 'WP_Image_Editor' ) ) {
			class_alias( __CLASS__, 'WP_Image_Editor' );
		}
	}

	protected function update_size( $width = false, $height = false ) {
		$this->size = [ 'width' => (int) $width, 'height' => (int) $height ];
		return true;
	}

	protected function generate_filename( $suffix = null, $dest_path = null, $extension = null ) {
		return implode( 'x', $this->size ) . '.svg';
	}
}
