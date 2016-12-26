<?php
/**
 * Scalable Vector Graphics Editing.
 *
 * @package MediaLibrarySvgEditing
 */

namespace PressBits\MediaLibrary;

/**
 * Scalable Vector Graphics Editing.
 *
 * @since 0.1.0
 */
class ScalableVectorGraphicsEditing {

	/**
	 * Whether SVG editing has been enabled.
	 *
	 * @since 0.1.0
	 * @var bool
	 */
	protected static $enabled = false;

	/**
	 * Enable by hooking into the WordPress lifecycle.
	 *
	 * @since 0.1.0
	 */
	public static function enable() {
		if ( static::$enabled ) {
			return;
		}
		add_filter( 'wp_image_editors', [ __CLASS__, 'add_editor' ] );
		add_filter( 'file_is_displayable_image', [ __CLASS__, 'file_is_displayable_image' ], 10, 2 );
		static::$enabled = true;
	}

	/**
	 * Add the SVG editor to available image editors.
	 *
	 * @since 0.1.0
	 * @param array $editors The available editors.
	 * @return array
	 */
	public static function add_editor( $editors ) {
		return $editors;
	}

	/**
	 * Make WordPress treat SVGs as displayable images.
	 *
	 * @since 0.1.0
	 * @param bool   $result Whether the image is displayable.
	 * @param string $path The image path.
	 * @return bool
	 */
	public static function file_is_displayable_image( $result, $path ) {
		$check = wp_check_filetype( $path, [ 'svg' => 'image/svg+xml' ] );
		if ( $check['ext'] ) {
			return true;
		}
		return $result;
	}
}
