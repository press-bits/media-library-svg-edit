<?php
/**
 * Scalable Vector Graphics Editor.
 *
 * @package MediaLibrarySvgEditing
 */

namespace PressBits\MediaLibrary;

use JangoBrick\SVG\SVGImage;

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

		$this->update_size();

		return $this->svg_image instanceof SVGImage;
	}

	/**
	 * Resize the SVG image.
	 *
	 * @since 0.1.0
	 * @param int|null $max_w New width in pixels.
	 * @param int|null $max_h New height in pixels.
	 * @param bool     $crop Whether to crop.
	 * @return bool
	 */
	public function resize( $max_w, $max_h, $crop = false ) {
		$dimensions = image_resize_dimensions( $this->size['width'], $this->size['height'], $max_w, $max_h, $crop );
		if ( ! $dimensions ) {
			// No change.
			return true;
		}

		list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dimensions;

		if ( $crop ) {
			$this->crop( $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h, $src_abs = false );
		} else {
			$this->svg_image->getDocument()->setWidth( $dst_w );
			$this->svg_image->getDocument()->setHeight( $dst_h );
		}

		$this->update_size( $dst_w, $dst_h );

		return true;
	}

	/**
	 * Set current image size.
	 *
	 * @since 0.1.0
	 * @param bool|int $width Optional width in pixels.
	 * @param bool|int $height Optional height in pixels.
	 * @return bool
	 */
	protected function update_size( $width = false, $height = false ) {

		if ( ! $width ) {
			$width = $this->svg_image->getDocument()->getWidth();
		}

		if ( ! $height ) {
			$height = $this->svg_image->getDocument()->getHeight();
		}

		return parent::update_size( $width, $height );
	}
}
