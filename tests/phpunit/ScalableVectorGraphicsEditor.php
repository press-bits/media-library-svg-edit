<?php

namespace PressBits\UnitTests\MediaLibrary;

use PressBits\MediaLibrary\ScalableVectorGraphicsEditor as Editor;

use PressBits\UnitTests\WpImageEditorMock;

use PHPUnit_Framework_TestCase;

class ScalableVectorGraphicsEditor extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp();
        WpImageEditorMock::init();
    }

    public function testTest()
    {
        $this->assertTrue(Editor::test(), 'Expected test method to return true.');
    }

    public function testSupportsSvgMimeType()
    {
        $this->assertTrue(Editor::supports_mime_type( 'image/svg+xml', 'Expected editor to support SVG MIME type.'));
    }
}