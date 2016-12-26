<?php

namespace PressBits\MediaLibrary;

use WP_Image_Editor;

class ScalableVectorGraphicsEditor extends WP_Image_Editor
{
    public static function test()
    {
        return true;
    }

    public static function supports_mime_type($mime_type)
    {
        return 'image/svg+xml' == $mime_type;
    }
}
