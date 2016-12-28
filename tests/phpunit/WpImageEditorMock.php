<?php

namespace PressBits\UnitTest;

class WpImageEditorMock {

	public static function init() {
		if ( ! class_exists( 'WP_Image_Editor' ) ) {
			class_alias( __CLASS__, 'WP_Image_Editor' );
		}
	}
}
