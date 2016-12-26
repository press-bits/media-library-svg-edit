<?php
/**
 * Scalable Vector Graphics Editor.
 *
 * @package MediaLibrarySvgEditing
 */

namespace PressBits\MediaLibrary;

use WP_Image_Editor;

/**
 * Scalable Vector Graphics Editor class.
 *
 * @since 0.1.0
 */
class ScalableVectorGraphicsEditor extends WP_Image_Editor {

	/**
	 * Whether the editor can be loaded.
	 *
	 * @since 0.1.0
	 * @return bool
	 */
	public static function test() {
		return true;
	}

	/**
	 * Checks to see if editor supports the mime-type specified.
	 *
	 * @since 0.1.0
	 * @param string $mime_type The mime type to check.
	 * @return bool
	 */
	public static function supports_mime_type( $mime_type ) {
		return 'image/svg+xml' === $mime_type;
	}
}
