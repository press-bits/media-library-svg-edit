<?php
/**
 * Scalable Vector Graphics Editing.
 *
 * @package MediaLibrarySvgEditing
 */

namespace PressBits\MediaLibrary\ScalableVectorGraphics;

use JangoBrick\SVG\SVGImage;

/**
 * Scalable Vector Graphics Editing.
 *
 * @since 0.1.0
 */
class Editing {

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
		add_filter( 'wp_get_attachment_metadata', [ __CLASS__, 'svg_attachment_metadata' ], 10, 2 );
		add_filter( 'admin_body_class', [ __CLASS__, 'admin_body_class' ] );

		add_action( 'admin_print_styles', [ __CLASS__, 'admin_print_styles' ] );

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
		array_unshift( $editors, 'PressBits\\MediaLibrary\\ScalableVectorGraphics\\Editor' );
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
		$check = wp_check_filetype( $path, [ 'svg' => MIMEType::SVG_IMAGE ] );
		if ( $check['ext'] ) {
			return true;
		}
		return $result;
	}

	/**
	 * Make metadata for SVG attachments that lack it.
	 *
	 * @since 0.1.0
	 * @param array $data Existing attachment metadata.
	 * @param int   $attachment_id The attachment ID.
	 * @return array
	 */
	public static function svg_attachment_metadata( $data, $attachment_id ) {
		if ( ! empty( $data['width'] ) and ! empty( $data['height'] ) ) {
			return $data;
		}

		$file = get_attached_file( $attachment_id );

		if ( ! $file ) {
			return $data;
		}

		if ( ! static::file_is_displayable_image( false, $file ) ) {
			return $data;
		}

		$svg = SVGImage::fromFile( $file );

		$data = $data ?: [];
		$data['width'] = intval( $svg->getDocument()->getWidth() );
		$data['height'] = intval( $svg->getDocument()->getHeight() );

		return $data;
	}

	/**
	 * Add a body class when a SVG attachment is being edited in the admin.
	 *
	 * @since 0.1.0
	 * @param string $class Body tag classes to augment.
	 * @return string
	 */
	public static function admin_body_class( $class = '' ) {
		if ( $post = get_post() and MIMEType::SVG_IMAGE === $post->post_mime_type ) {
			$class .= ' edit-attachment-svg ';
		}
		return $class;
	}

	/**
	 * Add styles to hide unsupported SVG image editor buttons.
	 *
	 * @since 0.1.0
	 */
	public static function admin_print_styles() {
		?>
		<style type="text/css">
			.post-type-attachment.edit-attachment-svg .imgedit-flipv,
			.post-type-attachment.edit-attachment-svg .imgedit-fliph,
			.post-type-attachment.edit-attachment-svg .imgedit-rleft,
			.post-type-attachment.edit-attachment-svg .imgedit-rright {
				display: none;
			}
		</style>
		<?php
	}
}
