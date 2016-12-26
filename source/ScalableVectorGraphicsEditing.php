<?php

namespace PressBits\MediaLibrary;

/**
 * Scalable Vector Graphics Editing.
 *
 * @since 0.1.0
 */
class ScalableVectorGraphicsEditing
{
    protected static $enabled = false;

    /**
     * Enable by hooking into the WordPress lifecycle.
     *
     * @since 0.1.0
     */
    public static function enable()
    {
        if (static::$enabled) {
            return;
        }
        add_filter('wp_image_editors', [__CLASS__, 'addEditor']);
        add_filter('file_is_displayable_image', [__CLASS__, 'fileIsDisplayableImage'], 10, 2);
        static::$enabled = true;
    }

    /**
     * Add the SVG editor to available image editors.
     *
     * @since 0.1.0
     * @param array $editors
     * @return array
     */
    public static function addEditor($editors)
    {
        return $editors;
    }

    /**
     * Make WordPress treat SVGs as displayable images.
     *
     * @since 0.1.0
     * @param bool $result
     * @param string $path
     * @return bool
     */
    public static function fileIsDisplayableImage($result, $path)
    {
        $check = wp_check_filetype($path, ['svg' => 'image/svg+xml']);
        if ($check['ext']) {
            return true;
        }
        return $result;
    }
}
