<?php

namespace PressBits\UnitTest\ScalableVectorGraphicsEditor;

use PressBits\MediaLibrary\ScalableVectorGraphicsEditor as Editor;

use PressBits\UnitTest\WpImageEditor;
use PressBits\UnitTest\WpError;

use Mockery;
use Mockery\Mock;
use Brain\Monkey;
use PHPUnit_Framework_TestCase;

class WithValidFile extends PHPUnit_Framework_TestCase {
	protected $test_path = 'test-path';
	protected $width = 10;
	protected $height = 5;
	/** @var  Mock */
	protected $doc_mock;

	public function setUp() {
		parent::setUp();
		WpError::alias();
		WpImageEditor::alias();
		Monkey::setUp();
		$this->doc_mock = Mockery::mock( 'JangoBrick\SVG\Nodes\Structures\SVGDocumentFragment' );
		$this->doc_mock->shouldReceive( 'getWidth' )
			->once()
			->andReturn( $this->width );
		$this->doc_mock->shouldReceive( 'getHeight' )
			->once()
			->andReturn( $this->height );

		$svg_mock = Mockery::mock( 'alias:JangoBrick\SVG\SVGImage' );

		$svg_mock->shouldReceive( 'getDocument' )->andReturn( $this->doc_mock );
		$svg_mock->shouldReceive( 'fromFile' )
			->once()
			->with( $this->test_path )
			->andReturn( $svg_mock );
	}

	public function tearDown() {
		Monkey::tearDown();
		parent::tearDown();
	}

	public function test_load_returns_true() {
		$editor = new Editor( $this->test_path );
		$this->assertTrue( $editor->load(), 'Expected SVG file to load.' );
	}

	public function test_resize_sets_document_width_and_height() {
		$new_width = 12;
		$new_height = 8;

		$editor = new Editor( $this->test_path );
		$editor->load();

		$this->doc_mock->shouldReceive( 'setWidth' )->once()->with( $new_width )->andReturn( $this->doc_mock );
		$this->doc_mock->shouldReceive( 'setHeight' )->once()->with( $new_height )->andReturn( $this->doc_mock );

		Monkey\Functions::expect( 'image_resize_dimensions' )
			->once()
			->with( $this->width, $this->height, $new_width, $new_height, false )
			->andReturn( [ 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height ] );

		$editor->resize( $new_width, $new_height );
	}

	public function test_crop_sets_width_height_and_view_box() {
		$crop_x = 1;
		$crop_y = 1;
		$crop_width = 8;
		$crop_height = 4;
		$view_box = '1 1 8 4';

		$editor = new Editor( $this->test_path );
		$editor->load();

		$this->doc_mock->shouldReceive( 'setWidth' )->once()->with( $crop_width )->andReturn( $this->doc_mock );
		$this->doc_mock->shouldReceive( 'setHeight' )->once()->with( $crop_height )->andReturn( $this->doc_mock );
		$this->doc_mock->shouldReceive( 'getAttribute' )->once()->with( 'viewBox' )->andReturn( null );
		$this->doc_mock->shouldReceive( 'setAttribute' )->once()->with( 'viewBox', $view_box )->andReturn( $this->doc_mock );

		Monkey\Functions::expect( 'image_resize_dimensions' )
			->once()
			->with( $this->width, $this->height, $crop_width, $crop_height, false )
			->andReturn( [ 0, 0, 0, 0, $crop_width, $crop_height, $this->width, $this->height ] );

		$editor->crop( $crop_x, $crop_y, $crop_width, $crop_height );
	}

	public function test_resize_with_crop_sets_width_height_and_view_box() {
		$resize_width = 8;
		$resize_height = 4;
		$view_box = '0 0 10 5';

		$editor = new Editor( $this->test_path );
		$editor->load();

		$this->doc_mock->shouldReceive( 'setWidth' )->once()->with( $resize_width )->andReturn( $this->doc_mock );
		$this->doc_mock->shouldReceive( 'setHeight' )->once()->with( $resize_height )->andReturn( $this->doc_mock );
		$this->doc_mock->shouldReceive( 'getAttribute' )->once()->with( 'viewBox' )->andReturn( null );
		$this->doc_mock->shouldReceive( 'setAttribute' )->once()->with( 'viewBox', $view_box )->andReturn( $this->doc_mock );

		Monkey\Functions::expect( 'image_resize_dimensions' )
			->twice()
			->with( $this->width, $this->height, $resize_width, $resize_height, Mockery::type( 'bool' ) )
			->andReturn( [ 0, 0, 0, 0, $resize_width, $resize_height, $this->width, $this->height ] );

		$editor->resize( $resize_width, $resize_height, $crop = true );
	}

	public function test_rotate_error() {
		$editor = new Editor( $this->test_path );
		$editor->load();
		Monkey\Functions::expect( '__' )->once()->andReturn( 'error message' );
		$error = $editor->rotate( 90 );
		$this->assertInstanceOf( 'WP_Error', $error );
	}

	public function test_flip_error() {
		$editor = new Editor( $this->test_path );
		$editor->load();
		Monkey\Functions::expect( '__' )->once()->andReturn( 'error message' );
		$error = $editor->flip( true, false );
		$this->assertInstanceOf( 'WP_Error', $error );
	}
}
