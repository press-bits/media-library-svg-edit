<?php

namespace PressBits\UnitTest\ScalableVectorGraphicsEditing;

use PressBits\MediaLibrary\ScalableVectorGraphicsEditing as Editing;

use PressBits\UnitTest\WpTestCase;
use Brain\Monkey\Functions;
use Brain\Monkey\WP;
use Mockery;

class Loaded extends WpTestCase {

	public function test_enable() {
		WP\Filters::expectAdded( 'wp_image_editors' )
		->once()
		->with( [ 'PressBits\\MediaLibrary\\ScalableVectorGraphicsEditing', 'add_editor' ] );

		WP\Filters::expectAdded( 'wp_get_attachment_metadata' )
			->once()
			->with( [ 'PressBits\\MediaLibrary\\ScalableVectorGraphicsEditing', 'svg_attachment_metadata' ], 10, 2 );

		Editing::enable();
	}

	public function test_add_editor() {
		$editors = Editing::add_editor( [] );
		$this->assertCount( 1, $editors, 'Expected one editor.' );
		$this->assertEquals( 'PressBits\\MediaLibrary\\ScalableVectorGraphicsEditor', $editors[0] );
	}

	public function test_attachment_meta_filter_leaves_existing_data() {
		$meta = [ 'foo' => 'bar' ];
		$filtered_meta = Editing::svg_attachment_metadata( $meta, 0 );
		$this->assertEquals( $meta, $filtered_meta );
	}

	public function test_attachment_meta_filter_returns_dimensions() {
		$attachment_id = 3;
		$file = 'test.svg';
		$width = 10;
		$height = 8;

		Functions::expect( 'get_attached_file' )->once()->with( $attachment_id )->andReturn( $file );
		Functions::expect( 'wp_check_filetype' )
			->once()
			->with( $file, [ 'svg' => 'image/svg+xml' ] )
			->andReturn( [
				'ext' => 'svg',
				'type' => 'image/svg+xml',
			] );

		$doc_mock = Mockery::mock( 'JangoBrick\SVG\Nodes\Structures\SVGDocumentFragment' );
		$svg_mock = Mockery::mock( 'alias:JangoBrick\SVG\SVGImage' );

		$svg_mock->shouldReceive( 'fromFile' )->once()->with( $file )->andReturn( $svg_mock );

		$svg_mock->shouldReceive( 'getDocument' )->andReturn( $doc_mock );

		$doc_mock->shouldReceive( 'getWidth' )->once()->andReturn( $width );
		$doc_mock->shouldReceive( 'getHeight' )->once()->andReturn( $height );

		$meta = Editing::svg_attachment_metadata( [], $attachment_id );

		$this->assertEquals( compact( 'width', 'height' ), $meta, 'Expected filtered meta to be SVG dimensions.' );
	}

}
