<?php
/**
 * Scalable Vector Graphics Editor.
 *
 * @package MediaLibrarySvgEditing
 */

namespace PressBits\MediaLibrary;

use JangoBrick\SVG\SVGImage;
use JangoBrick\SVG\Reading\SVGReader;

use WP_Image_Editor;
use WP_Error;

/**
 * Scalable Vector Graphics Editor class.
 *
 * @since 0.1.0
 */
class ScalableVectorGraphicsEditor extends WP_Image_Editor {

	/**
	 * The loaded SVG image.
	 *
	 * @since 0.1.0
	 * @var SVGImage
	 */
	protected $svg_image;

	/**
	 * Whether the editor can be loaded.
	 *
	 * @since 0.1.0
	 * @param array $args Argument array is ignored.
	 * @return bool
	 */
	public static function test( $args = array() ) {
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

	/**
	 * Load an SVG image.
	 *
	 * @since 0.1.0
	 * @param string $path The path to the SVG file to load.
	 * @return bool|WP_Error
	 */
	public function load( $path ) {
		try {
			$this->svg_image = SVGImage::fromFile( $path );
		} catch ( Exception $e ) {
			return new WP_Error( 'svg_editor_load_error', 'Failed to load SVG file', compact( 'path', 'e' ) );
		}
		return $this->svg_image instanceof SVGImage;
	}

}
