<?php

namespace PressBits\UnitTest\ScalableVectorGraphicsEditor;

use PressBits\MediaLibrary\ScalableVectorGraphicsEditor as Editor;

use PressBits\UnitTest\WpImageEditor;

use Mockery;
use PHPUnit_Framework_TestCase;

class WithoutValidFile extends PHPUnit_Framework_TestCase {

	public function setUp() {
		parent::setUp();
		Mockery::mock( 'WP_Error' );
		WpImageEditor::alias();
	}

	public function tearDown() {
		Mockery::close();
		parent::tearDown();
	}

	public function test_test() {
		$this->assertTrue( Editor::test( [ 'foo' => 'bar' ] ), 'Expected test method to return true by default.' );
	}

	public function test_test_unsupported_methods() {
		$this->assertFalse( Editor::test( [ 'methods' => [ 'flip' ] ] ), 'Expected flip method to be unsupported.' );
		$this->assertFalse( Editor::test(
			[ 'methods' => [ 'rotate', 'resize' ] ] ),
			'Expected rotate method to be unsupported.'
		);
	}

	public function test_supports_svg_mime_type() {
		$this->assertTrue( Editor::supports_mime_type( 'image/svg+xml', 'Expected editor to support SVG MIME type.' ) );
	}

	public function test_does_not_support_jpeg_mime_type() {
		$this->assertFalse( Editor::supports_mime_type( 'image/jpeg', 'Expected editor to support SVG MIME type.' ) );
	}

	public function test_load_exception() {
		$svg_mock = Mockery::mock( 'alias:JangoBrick\SVG\SVGImage' );
		$svg_mock->shouldReceive( 'fromFile' )->andThrow( 'RuntimeException' );

		$editor = new Editor( 'test-path' );
		$this->setExpectedException( 'RuntimeException' );
		$editor->load();
	}
}
