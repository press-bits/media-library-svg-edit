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
	 * The path of the SVG file.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $file;


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
	 * Instantiate an SVG image editor;
	 *
	 * @since 0.1.0
	 * @param string $file The path of the SVG file to edit.
	 */
	public function __construct( $file ) {
		$this->file = $file;
	}

	/**
	 * Load an SVG image.
	 *
	 * @since 0.1.0
	 * @return bool|WP_Error
	 */
	public function load() {
		if ( $this->svg_image ) {
			return true;
		}

		try {
			$this->svg_image = SVGImage::fromFile( $this->file );
		} catch ( Exception $e ) {
			return new WP_Error( 'svg_editor_load_error', 'Failed to load SVG file', compact( 'path', 'e' ) );
		}

		return $this->svg_image instanceof SVGImage;
	}

}
