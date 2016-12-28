<?php

namespace PressBits\UnitTest\MediaLibrary;

use PressBits\MediaLibrary\ScalableVectorGraphicsEditor as Editor;

use PressBits\UnitTest\WpImageEditor;

use Mockery;
use PHPUnit_Framework_TestCase;

class ScalableVectorGraphicsEditorLoaded extends PHPUnit_Framework_TestCase {
	protected $test_path = 'test-path';
	protected $width = 10;
	protected $height = 5;

	public function setUp() {
		parent::setUp();
		Mockery::mock( 'WP_Error' );
		WpImageEditor::alias();
		$doc_mock = Mockery::mock( 'JangoBrick\SVG\Nodes\Structures\SVGDocumentFragment' );
		$doc_mock->shouldReceive( 'getWidth' )
			->once()
			->andReturn( $this->width );
		$doc_mock->shouldReceive( 'getHeight' )
			->once()
			->andReturn( $this->height );

		$svg_mock = Mockery::mock( 'alias:JangoBrick\SVG\SVGImage' );

		$svg_mock->shouldReceive( 'getDocument' )->andReturn( $doc_mock );
		$svg_mock->shouldReceive( 'fromFile' )
			->once()
			->with( $this->test_path )
			->andReturn( $svg_mock );
	}

	public function tearDown() {
		Mockery::close();
		parent::tearDown();
	}

	public function test_load() {
		$editor = new Editor( $this->test_path );
		$this->assertTrue( $editor->load(), 'Expected SVG file to load.' );
	}

}
