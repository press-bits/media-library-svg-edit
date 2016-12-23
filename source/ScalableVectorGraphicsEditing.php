<?php

namespace PressBits\MediaLibrary;

/**
 * Scalable Vector Graphics Editing.
 *
 * When editing is enabled, hook into the WordPress lifecycle.
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
}
