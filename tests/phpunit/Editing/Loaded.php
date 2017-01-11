<?php

namespace PressBits\UnitTest\ScalableVectorGraphics\Editing;

use PressBits\MediaLibrary\ScalableVectorGraphics\Editing;
use PressBits\MediaLibrary\ScalableVectorGraphics\MIMEType;

use PressBits\UnitTest\ScalableVectorGraphics\WpTestCase;
use Brain\Monkey\Functions;
use Brain\Monkey\WP;
use Mockery;

class Loaded extends WpTestCase {

	public function test_enable() {
		WP\Filters::expectAdded( 'wp_image_editors' )
			->once()
			->with( [ 'PressBits\\MediaLibrary\\ScalableVectorGraphics\\Editing', 'add_editor' ] );

		WP\Filters::expectAdded( 'file_is_displayable_image' )
			->once()
			->with( [ 'PressBits\\MediaLibrary\\ScalableVectorGraphics\\Editing', 'file_is_displayable_image' ], 10, 2 );

		WP\Filters::expectAdded( 'wp_get_attachment_metadata' )
			->once()
			->with( [ 'PressBits\\MediaLibrary\\ScalableVectorGraphics\\Editing', 'svg_attachment_metadata' ], 10, 2 );

		WP\Actions::expectAdded( 'wp_ajax_image-editor' )
			->once()
			->with( [ 'PressBits\\MediaLibrary\\ScalableVectorGraphics\\Editing', 'edit_attachment_inline_styles' ], -1 );

		Editing::enable();
	}

	public function test_add_editor() {
		$editors = Editing::add_editor( [] );
		$this->assertCount( 1, $editors, 'Expected one editor.' );
		$this->assertEquals( 'PressBits\\MediaLibrary\\ScalableVectorGraphics\\Editor', $editors[0] );
	}

	public function test_attachment_meta_filter_leaves_existing_data() {
		$meta = [ 'width' => 'foo', 'height' => 'bar' ];
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
			->with( $file, [ 'svg' => MIMEType::SVG_IMAGE ] )
			->andReturn( [
				'ext' => 'svg',
				'type' => MIMEType::SVG_IMAGE,
			] );

		$doc_mock = Mockery::mock( 'JangoBrick\SVG\Nodes\Structures\SVGDocumentFragment' );
		$svg_mock = Mockery::mock( 'alias:JangoBrick\SVG\SVGImage' );

		$svg_mock->shouldReceive( 'fromFile' )->once()->with( $file )->andReturn( $svg_mock );

		$svg_mock->shouldReceive( 'getDocument' )->andReturn( $doc_mock );

		$doc_mock->shouldReceive( 'getWidth' )->once()->andReturn( $width . 'px' );
		$doc_mock->shouldReceive( 'getHeight' )->once()->andReturn( $height . 'px' );

		$meta = [ 'file' => $file, 'width' => null, 'height' => null ];
		$meta = Editing::svg_attachment_metadata( $meta, $attachment_id );

		$this->assertEquals( compact( 'file', 'width', 'height' ), $meta, 'Expected filtered meta to be SVG dimensions.' );
	}

	public function test_edit_attachment_print_styles() {
		$attachment = (object) [ 'ID' => 3, 'post_mime_type' => MIMEType::SVG_IMAGE ];

		$_POST['postid'] = $attachment->ID;

		Functions::expect( 'get_post' )->once()->with( 3 )->andReturn( $attachment );
		Functions::expect( 'wp_verify_nonce' )->once()->with( 'image-editor-3' )->andReturn( true );

		ob_start();
		Editing::edit_attachment_inline_styles();
		$css = ob_get_clean();

		$this->assertContains(
			'#image-editor-3 .imgedit-flipv',
			$css,
			'Expected vertical flip button selector.'
		);
	}
}
