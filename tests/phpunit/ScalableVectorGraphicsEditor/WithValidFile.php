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
	/** @var  Mock */
	protected $svg_mock;
	/** @var  Editor */
	protected $editor;

	public function setUp() {
		parent::setUp();
		WpError::alias();
		WpImageEditor::alias();
		Monkey::setUpWP();
		$this->doc_mock = Mockery::mock( 'JangoBrick\SVG\Nodes\Structures\SVGDocumentFragment' );
		$this->doc_mock->shouldReceive( 'getWidth' )
			->once()
			->andReturn( $this->width . 'px' );
		$this->doc_mock->shouldReceive( 'getHeight' )
			->once()
			->andReturn( $this->height . 'px' );

		$this->svg_mock = Mockery::mock( 'alias:JangoBrick\SVG\SVGImage' );

		$this->svg_mock->shouldReceive( 'getDocument' )->andReturn( $this->doc_mock );
		$this->svg_mock->shouldReceive( 'fromFile' )
			->once()
			->with( $this->test_path )
			->andReturn( $this->svg_mock );

		$this->editor = new Editor( $this->test_path );
	}

	public function tearDown() {
		Monkey::tearDownWP();
		parent::tearDown();
	}

	public function test_load_returns_true() {
		$this->assertTrue( $this->editor->load(), 'Expected SVG file to load.' );
	}

	public function test_resize_sets_document_width_and_height() {
		$new_width = 12;
		$new_height = 8;

		$this->editor->load();

		$this->doc_mock->shouldReceive( 'setWidth' )->once()->with( $new_width )->andReturn( $this->doc_mock );
		$this->doc_mock->shouldReceive( 'setHeight' )->once()->with( $new_height )->andReturn( $this->doc_mock );

		$this->editor->resize( $new_width, $new_height );
	}

	public function test_crop_sets_width_height_and_view_box() {
		$crop_x = 1;
		$crop_y = 1;
		$crop_width = 8;
		$crop_height = 4;
		$view_box = '1 1 8 4';

		$this->editor->load();

		$this->doc_mock->shouldReceive( 'setWidth' )->once()->with( $crop_width )->andReturn( $this->doc_mock );
		$this->doc_mock->shouldReceive( 'setHeight' )->once()->with( $crop_height )->andReturn( $this->doc_mock );
		$this->doc_mock->shouldReceive( 'getAttribute' )->once()->with( 'viewBox' )->andReturn( null );
		$this->doc_mock->shouldReceive( 'setAttribute' )->once()->with( 'viewBox', $view_box )->andReturn( $this->doc_mock );

		$this->editor->crop( $crop_x, $crop_y, $crop_width, $crop_height );
	}

	public function test_resize_with_crop_sets_width_height_and_view_box() {
		$resize_width = 8;
		$resize_height = 4;
		$view_box = '0 0 10 5';

		$this->editor->load();

		$this->doc_mock->shouldReceive( 'setWidth' )->once()->with( $resize_width )->andReturn( $this->doc_mock );
		$this->doc_mock->shouldReceive( 'setHeight' )->once()->with( $resize_height )->andReturn( $this->doc_mock );
		$this->doc_mock->shouldReceive( 'getAttribute' )->once()->with( 'viewBox' )->andReturn( null );
		$this->doc_mock->shouldReceive( 'setAttribute' )->once()->with( 'viewBox', $view_box )->andReturn( $this->doc_mock );

		Monkey\Functions::expect( 'image_resize_dimensions' )
			->once()
			->with( $this->width, $this->height, $resize_width, $resize_height, true )
			->andReturn( [ 0, 0, 0, 0, $resize_width, $resize_height, $this->width, $this->height ] );

		$this->editor->resize( $resize_width, $resize_height, $crop = true );
	}

	public function test_rotate_error() {
		$this->editor->load();
		Monkey\Functions::expect( '__' )->once()->andReturn( 'error message' );
		$error = $this->editor->rotate( 90 );
		$this->assertInstanceOf( 'WP_Error', $error );
	}

	public function test_flip_error() {
		$this->editor->load();
		Monkey\Functions::expect( '__' )->once()->andReturn( 'error message' );
		$error = $this->editor->flip( true, false );
		$this->assertInstanceOf( 'WP_Error', $error );
	}

	public function test_save_writes_xml_to_file() {
		$this->editor->load();

		$xml = '<svg attr="test" />';

		$this->svg_mock->shouldReceive( 'toXMLString' )->once()->andReturn( $xml );

		$test_file = 'test-dir/test-file.svg';

		$fs_mock = Mockery::mock( 'WP_Filesystem_Base' );
		$fs_mock->shouldReceive( 'is_dir' )
			->with( dirname( $test_file ) )
			->andReturn( true );
		$fs_mock->shouldReceive( 'put_contents' )
			->with( $test_file, $xml, 0000666 )
			->andReturn( true );

		$GLOBALS['wp_filesystem'] = $fs_mock;

		Monkey::filters()->expectApplied( 'image_make_intermediate_size' )->with( $test_file )->andReturn( $test_file );
		Monkey\Functions::expect( 'wp_basename' )->with( $test_file )->andReturn( basename( $test_file ) );

		$info = $this->editor->save( $test_file );
		$this->assertEquals(
			[
				'path' => $test_file,
				'file' => basename( $test_file ),
				'width' => $this->width,
				'height' => $this->height,
				'mime-type' => 'image/svg+xml',
			],
			$info
		);
	}

	public function test_multi_resize_resets_width_height_and_viewbox() {
		$resize_width = 8;
		$resize_height = 4;

		$this->editor->load();

		$this->svg_mock->shouldReceive( 'toXMLString' )->once()->andReturn( 'XML' );

		$this->doc_mock->shouldReceive( 'setWidth' )->once()->with( $resize_width )->andReturn( $this->doc_mock );
		$this->doc_mock->shouldReceive( 'setHeight' )->once()->with( $resize_height )->andReturn( $this->doc_mock );

		$fs_mock = Mockery::mock( 'WP_Filesystem_Base' );
		$fs_mock->shouldReceive( 'is_dir' )->andReturn( true );
		$fs_mock->shouldReceive( 'put_contents' )->andReturn( true );

		$GLOBALS['wp_filesystem'] = $fs_mock;

		Monkey::filters()->expectApplied( 'image_make_intermediate_size' )->with( '8x4.svg' )->andReturn( '8x4.svg' );
		Monkey\Functions::expect( 'wp_basename' )->with( '8x4.svg' )->andReturn( '8x4.svg' );
		Monkey\Functions::expect( 'is_wp_error' )->andReturn( false );

		$resized_data = $this->editor->multi_resize( [ 'test' => [ 'width' => $resize_width ] ] );

		$this->assertEquals(
			[
			'test' =>
				[
				'file' => "{$resize_width}x{$resize_height}.svg",
				'width' => $resize_width,
				'height' => $resize_height,
				'mime-type' => 'image/svg+xml',
				],
			],
			$resized_data
		);
	}

	public function test_stream() {
		$this->editor->load();

		$xml = 'XML';

		$this->svg_mock->shouldReceive( 'toXMLString' )->once()->andReturn( $xml );

		ob_start();
		$this->editor->stream();
		$content = ob_get_clean();

		$this->assertEquals( $xml, $content );
		$this->assertContains(
			'Content-Type: image/svg+xml',
			xdebug_get_headers(),
			'Expected SVG content type header.'
		);
	}
}
