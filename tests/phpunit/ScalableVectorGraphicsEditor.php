<?php

namespace PressBits\UnitTest\MediaLibrary;

use PressBits\MediaLibrary\ScalableVectorGraphicsEditor as Editor;

use PressBits\UnitTest\WpImageEditorMock;
use PressBits\UnitTest\WpErrorMock;


use Mockery;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit_Framework_TestCase;

class ScalableVectorGraphicsEditor extends PHPUnit_Framework_TestCase {


	public function setUp() {
		parent::setUp();
		Monkey::setUp();
		Mockery::mock('WP_Error');
		Mockery::mock('WP_Image_Editor');
	}

	public function tearDown() {
		Monkey::tearDown();
		parent::tearDown();
	}

	public function test_test() {
		$this->assertTrue( Editor::test( [ 'foo' => 'bar' ] ), 'Expected test method to return true.' );
	}

	public function test_supports_svg_mime_type() {
		$this->assertTrue( Editor::supports_mime_type( 'image/svg+xml', 'Expected editor to support SVG MIME type.' ) );
	}

	public function test_does_not_support_jpeg_mime_type() {
		$this->assertFalse( Editor::supports_mime_type( 'image/jpeg', 'Expected editor to support SVG MIME type.' ) );
	}

	public function test_load() {
		$test_path = 'test-path';
		$svg_mock = Mockery::mock('alias:JangoBrick\SVG\SVGImage');
		$svg_mock->shouldReceive( 'fromFile' )
			->with( $test_path )
			->andReturn( $svg_mock );

		$editor = new Editor( $test_path );
		$this->assertTrue( $editor->load(), 'Expected SVG file to load.' );
	}

	public function test_load_exception() {
		$svg_mock = Mockery::mock('alias:JangoBrick\SVG\SVGImage');
		$svg_mock->shouldReceive( 'fromFile' )
			->andThrow( 'RuntimeException' );

		$editor = new Editor( 'test-path' );
		$this->setExpectedException( 'RuntimeException' );
		$editor->load();
	}

}
