<?php

/**
 * @group content
 */
class WP_Gistpen_Content_Test extends WP_Gistpen_UnitTestCase {

	public $gistpen;
	public $gistpenfiles;

	function setUp() {
		parent::setUp();

		$this->create_post_and_children();
	}

	function test_get_post_content() {
		global $post;

		$post = $this->gistpen;
		$content = WP_Gistpen_Content::post_content();

		$sub_str_count = substr_count( $content, '<h2 class="wp-gistpenfile-title">' );
		$this->assertEquals( 3, $sub_str_count );
	}

	function test_get_shortcode_content_child() {
		$content = WP_Gistpen_Content::add_shortcode( array( 'id' => $this->files[0], 'highlight' => null ) );

		$sub_str_count = substr_count( $content, '<h2 class="wp-gistpenfile-title">' );
		$this->assertEquals( 1, $sub_str_count );
		$sub_str_count = substr_count( $content, 'Post content' );
		$this->assertEquals( 1, $sub_str_count );
		$sub_str_count = substr_count( $content, 'data-line=' );
		$this->assertEquals( 0, $sub_str_count );
	}

	function test_get_shortcode_content_parent() {
		$content = WP_Gistpen_Content::add_shortcode( array( 'id' => $this->gistpen->ID, 'highlight' => null ) );

		$sub_str_count = substr_count( $content, '<h2 class="wp-gistpenfile-title">' );
		$this->assertEquals( 3, $sub_str_count );
		$sub_str_count = substr_count( $content, 'Post content' );
		$this->assertEquals( 3, $sub_str_count );
		$sub_str_count = substr_count( $content, 'data-line=' );
		$this->assertEquals( 0, $sub_str_count );
	}

	function test_get_shortcode_with_highlight() {
		$content = WP_Gistpen_Content::add_shortcode( array( 'id' => $this->files[0], 'highlight' => '1' ) );

		$sub_str_count = substr_count( $content, 'data-line=' );
		$this->assertEquals( 1, $sub_str_count );
	}

	function tearDown() {
		parent::tearDown();
	}
}
