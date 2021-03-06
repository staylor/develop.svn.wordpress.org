<?php

/**
 * @group taxonomy
 */
class Tests_Taxonomy extends WP_UnitTestCase {
	function test_get_post_taxonomies() {
		$this->assertEquals(array('category', 'post_tag', 'post_format'), get_object_taxonomies('post'));
	}

	function test_get_link_taxonomies() {
		$this->assertEquals(array('link_category'), get_object_taxonomies('link'));
	}

	/**
	 * @ticket 5417
	 */
	function test_get_unknown_taxonomies() {
		// taxonomies for an unknown object type
		$this->assertEquals( array(), get_object_taxonomies(rand_str()) );
		$this->assertEquals( array(), get_object_taxonomies('') );
		$this->assertEquals( array(), get_object_taxonomies(0) );
		$this->assertEquals( array(), get_object_taxonomies(NULL) );
	}

	function test_get_post_taxonomy() {
		foreach ( get_object_taxonomies('post') as $taxonomy ) {
			$tax = get_taxonomy($taxonomy);
			// should return an object with the correct taxonomy object type
			$this->assertTrue( is_object( $tax ) );
			$this->assertTrue( is_array( $tax->object_type ) );
			$this->assertEquals( array( 'post' ), $tax->object_type );
		}
	}

	function test_get_link_taxonomy() {
		foreach ( get_object_taxonomies('link') as $taxonomy ) {
			$tax = get_taxonomy($taxonomy);
			// should return an object with the correct taxonomy object type
			$this->assertTrue( is_object($tax) );
			$this->assertTrue( is_array( $tax->object_type ) );
			$this->assertEquals( array( 'link' ), $tax->object_type );
		}
	}

	function test_taxonomy_exists_known() {
		$this->assertTrue( taxonomy_exists('category') );
		$this->assertTrue( taxonomy_exists('post_tag') );
		$this->assertTrue( taxonomy_exists('link_category') );
	}

	function test_taxonomy_exists_unknown() {
		$this->assertFalse( taxonomy_exists(rand_str()) );
		$this->assertFalse( taxonomy_exists('') );
		$this->assertFalse( taxonomy_exists(0) );
		$this->assertFalse( taxonomy_exists(NULL) );
	}

	function test_is_taxonomy_hierarchical() {
		$this->assertTrue( is_taxonomy_hierarchical('category') );
		$this->assertFalse( is_taxonomy_hierarchical('post_tag') );
		$this->assertFalse( is_taxonomy_hierarchical('link_category') );
	}

	function test_is_taxonomy_hierarchical_unknown() {
		$this->assertFalse( is_taxonomy_hierarchical(rand_str()) );
		$this->assertFalse( is_taxonomy_hierarchical('') );
		$this->assertFalse( is_taxonomy_hierarchical(0) );
		$this->assertFalse( is_taxonomy_hierarchical(NULL) );
	}

	function test_register_taxonomy() {

		// make up a new taxonomy name, and ensure it's unused
		$tax = rand_str();
		$this->assertFalse( taxonomy_exists($tax) );

		register_taxonomy( $tax, 'post' );
		$this->assertTrue( taxonomy_exists($tax) );
		$this->assertFalse( is_taxonomy_hierarchical($tax) );

		// clean up
		unset($GLOBALS['wp_taxonomies'][$tax]);
	}

	function test_register_hierarchical_taxonomy() {

		// make up a new taxonomy name, and ensure it's unused
		$tax = rand_str();
		$this->assertFalse( taxonomy_exists($tax) );

		register_taxonomy( $tax, 'post', array('hierarchical'=>true) );
		$this->assertTrue( taxonomy_exists($tax) );
		$this->assertTrue( is_taxonomy_hierarchical($tax) );

		// clean up
		unset($GLOBALS['wp_taxonomies'][$tax]);
	}

	/**
	 * @ticket 21593
	 */
	function test_register_long_taxonomy() {
		$this->assertInstanceOf( 'WP_Error', register_taxonomy( 'abcdefghijklmnopqrstuvwxyz0123456789', 'post', array() ) );
	}

	/**
	 * @ticket 11058
	 */
	function test_registering_taxonomies_to_object_types() {
		// Create a taxonomy to test with
		$tax = 'test_tax';
		$this->assertFalse( taxonomy_exists($tax) );
		register_taxonomy( $tax, 'post', array( 'hierarchical' => true ) );

		// Create a post type to test with
		$post_type = 'test_cpt';
		$this->assertFalse( get_post_type( $post_type ) );
		$this->assertObjectHasAttribute( 'name', register_post_type( $post_type ) );

		// Core taxonomy, core post type
		$this->assertTrue( unregister_taxonomy_for_object_type( 'category', 'post' ) );
		$this->assertFalse( unregister_taxonomy_for_object_type( 'category', 'post' ) );
		$this->assertTrue( register_taxonomy_for_object_type( 'category', 'post' ) );

		// Core taxonomy, non-core post type
		$this->assertTrue( register_taxonomy_for_object_type( 'category', $post_type ) );
		$this->assertTrue( unregister_taxonomy_for_object_type( 'category', $post_type ) );
		$this->assertFalse( unregister_taxonomy_for_object_type( 'category', $post_type ) );
		$this->assertTrue( register_taxonomy_for_object_type( 'category', $post_type ) );

		// Core taxonomies, non-post object types
		$this->assertFalse( register_taxonomy_for_object_type( 'category', 'user' ) );
		$this->assertFalse( unregister_taxonomy_for_object_type( 'category', 'user' ) );

		// Non-core taxonomy, core post type
		$this->assertTrue( unregister_taxonomy_for_object_type( $tax, 'post' ) );
		$this->assertFalse( unregister_taxonomy_for_object_type( $tax, 'post' ) );
		$this->assertTrue( register_taxonomy_for_object_type( $tax, 'post' ) );

		// Non-core taxonomy, non-core post type
		$this->assertTrue( register_taxonomy_for_object_type( $tax, $post_type ) );
		$this->assertTrue( unregister_taxonomy_for_object_type( $tax, $post_type ) );
		$this->assertFalse( unregister_taxonomy_for_object_type( $tax, $post_type ) );
		$this->assertTrue( register_taxonomy_for_object_type( $tax, $post_type ) );

		// Non-core taxonomies, non-post object types
		$this->assertFalse( register_taxonomy_for_object_type( $tax, 'user' ) );
		$this->assertFalse( unregister_taxonomy_for_object_type( $tax, 'user' ) );

		unset($GLOBALS['wp_taxonomies'][$tax]);
		_unregister_post_type( $post_type );

	}
}
