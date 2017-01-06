<?php

namespace PressBits\UnitTest\ScalableVectorGraphicsEditing;

use PressBits\MediaLibrary\ScalableVectorGraphicsEditing as Editing;

use PressBits\UnitTest\WpTestCase;
use PressBits\UnitTest\WpImageEditor;
use Brain\Monkey\WP;

class Loaded extends WpTestCase {

	public function test_enable() {
		WP\Filters::expectAdded( 'wp_image_editors' )
		->once()
		->with( [ 'PressBits\\MediaLibrary\\ScalableVectorGraphicsEditing', 'add_editor' ] );

		Editing::enable();
	}

	public function test_add_editor() {
		$editors = Editing::add_editor( [] );
		$this->assertCount( 1, $editors, 'Expected one editor.' );
		$this->assertEquals( 'PressBits\\MediaLibrary\\ScalableVectorGraphicsEditor', $editors[0] );
	}
}
