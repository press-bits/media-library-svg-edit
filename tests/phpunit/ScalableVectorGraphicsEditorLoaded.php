<?php

namespace PressBits\UnitTest\MediaLibrary;

use PressBits\MediaLibrary\ScalableVectorGraphicsEditor as Editor;

use PressBits\UnitTest\WpImageEditor;

use Mockery;
use Mockery\Mock;
use Brain\Monkey;
use PHPUnit_Framework_TestCase;

class ScalableVectorGraphicsEditorLoaded extends PHPUnit_Framework_TestCase {
	protected $test_path = 'test-path';
	protected $width = 10;
	protected $height = 5;
	/** @var  Mock */
	protected $doc_mock;

	public function setUp() {
		parent::setUp();
		Mockery::mock( 'WP_Error' );
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

}
