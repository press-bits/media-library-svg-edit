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

	/**
	 * Load an SVG image.
	 *
	 * @since 0.1.0
	 * @param string $path The path to the SVG file to load.
	 * @return bool|WP_Error
	 */
	public function load( $path ) {
		if ( ! file_exists( $path ) ) {
			return new WP_Error( 'svg_editor_file_not_found', 'Could not load a non-existant file.', $path );
		}
		$this->svg_image = SVGImage::fromFile( $path );
		return $this->svg_image instanceof SVGImage;
	}

}
