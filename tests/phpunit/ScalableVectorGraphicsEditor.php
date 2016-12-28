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
		WpImageEditorMock::init();
		WpErrorMock::init();
		Monkey::setUp();
	}

	public function tearDown() {
		Monkey::tearDown();
		parent::tearDown();
	}

	public function test_test() {
		$this->assertTrue( Editor::test(), 'Expected test method to return true.' );
	}

	public function test_supports_svg_mime_type() {
		$this->assertTrue( Editor::supports_mime_type( 'image/svg+xml', 'Expected editor to support SVG MIME type.' ) );
	}

	public function test_does_not_support_jpeg_mime_type() {
		$this->assertFalse( Editor::supports_mime_type( 'image/jpeg', 'Expected editor to support SVG MIME type.' ) );
	}

	public function skip_test_load_valid_file() {
		//$svg_mock = Mockery::mock('alias:JangoBrick\SVG\SVGImage');
		//$svg_mock->shouldReceive( 'fromFile' )
		//	->with( 'test-file' )
		//	->andReturn( $svg_mock );

		$test_path = 'test-path';
		Functions::expect('file_exists')->with( $test_path )->andReturn(true);
		$editor = new Editor();
		$this->assertTrue( $editor->load( $test_path ), 'Expected SVG file to load.' );
	}

	public function test_load_nonexistant_file() {
		$editor = new Editor();
		$this->assertInstanceOf(
			'WP_Error',
			$editor->load( 'nonexistant.svg' ),
			'Expected an error loading a nonexistant file.'
		);
	}

}
