<?php

namespace PressBits\UnitTest;

class WpErrorMock {

	public static function init() {
		if ( ! class_exists( 'WP_Error' ) ) {
			class_alias( __CLASS__, 'WP_Error' );
		}
	}

	public function __construct( $code, $message, $data ) {
	}
}
