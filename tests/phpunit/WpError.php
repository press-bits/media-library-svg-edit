<?php

namespace PressBits\UnitTest\ScalableVectorGraphics;

class WpError {

	public static function alias() {
		if ( ! class_exists( 'WP_Error' ) ) {
			class_alias( __CLASS__, 'WP_Error' );
		}
	}

	public function __construct( $code, $message, $data ) {
	}
}
