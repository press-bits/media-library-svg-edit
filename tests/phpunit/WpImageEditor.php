<?php

namespace PressBits\UnitTest;

class WpImageEditor {

	public static function alias() {
		if ( ! class_exists( 'WP_Image_Editor' ) ) {
			class_alias( __CLASS__, 'WP_Image_Editor' );
		}
	}

	protected function update_size( $width = false, $height = false ) {}
}