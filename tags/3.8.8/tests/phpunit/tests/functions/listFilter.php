<?php

/**
 * Test wp_filter_object_list(), wp_list_filter(), wp_list_pluck().
 *
 * @group functions.php
 */
class Tests_Functions_ListFilter extends WP_UnitTestCase {
	var $object_list = array();
	var $array_list = array();

	function setUp() {
		parent::setUp();
		$this->array_list['foo'] = array( 'name' => 'foo', 'field1' => true, 'field2' => true, 'field3' => true, 'field4' => array( 'red' ) );
		$this->array_list['bar'] = array( 'name' => 'bar', 'field1' => true, 'field2' => true, 'field3' => false, 'field4' => array( 'green' ) );
		$this->array_list['baz'] = array( 'name' => 'baz', 'field1' => true, 'field2' => false, 'field3' => false, 'field4' => array( 'blue' ) );
		foreach ( $this->array_list as $key => $value ) {
			$this->object_list[ $key ] = (object) $value;
		}
	}

	function test_filter_object_list_and() {
		$list = wp_filter_object_list( $this->object_list, array( 'field1' => true, 'field2' => true ), 'AND' );
		$this->assertEquals( 2, count( $list ) );
		$this->assertArrayHasKey( 'foo', $list );
		$this->assertArrayHasKey( 'bar', $list );
	}

	function test_filter_object_list_or() {
		$list = wp_filter_object_list( $this->object_list, array( 'field1' => true, 'field2' => true ), 'OR' );
		$this->assertEquals( 3, count( $list ) );
		$this->assertArrayHasKey( 'foo', $list );
		$this->assertArrayHasKey( 'bar', $list );
		$this->assertArrayHasKey( 'baz', $list );
	}

	function test_filter_object_list_not() {
		$list = wp_filter_object_list( $this->object_list, array( 'field2' => true, 'field3' => true ), 'NOT' );
		$this->assertEquals( 1, count( $list ) );
		$this->assertArrayHasKey( 'baz', $list );
	}

	function test_filter_object_list_and_field() {
		$list = wp_filter_object_list( $this->object_list, array( 'field1' => true, 'field2' => true ), 'AND', 'name' );
		$this->assertEquals( 2, count( $list ) );
		$this->assertEquals( array( 'foo' => 'foo', 'bar' => 'bar' ) , $list );
	}

	function test_filter_object_list_or_field() {
		$list = wp_filter_object_list( $this->object_list, array( 'field2' => true, 'field3' => true ), 'OR', 'name' );
		$this->assertEquals( 2, count( $list ) );
		$this->assertEquals( array( 'foo' => 'foo', 'bar' => 'bar' ) , $list );
	}

	function test_filter_object_list_not_field() {
		$list = wp_filter_object_list( $this->object_list, array( 'field2' => true, 'field3' => true ), 'NOT', 'name' );
		$this->assertEquals( 1, count( $list ) );
		$this->assertEquals( array( 'baz' => 'baz' ) , $list );
	}

	function test_wp_list_pluck() {
		$list = wp_list_pluck( $this->object_list, 'name' );
		$this->assertEquals( array( 'foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz' ) , $list );

		$list = wp_list_pluck( $this->array_list, 'name' );
		$this->assertEquals( array( 'foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz' ) , $list );
	}

	function test_filter_object_list_nested_array_and() {
		$list = wp_filter_object_list( $this->object_list, array( 'field4' => array( 'blue' ) ), 'AND' );
		$this->assertEquals( 1, count( $list ) );
		$this->assertArrayHasKey( 'baz', $list );
	}

	function test_filter_object_list_nested_array_not() {
		$list = wp_filter_object_list( $this->object_list, array( 'field4' => array( 'red' ) ), 'NOT' );
		$this->assertEquals( 2, count( $list ) );
		$this->assertArrayHasKey( 'bar', $list );
		$this->assertArrayHasKey( 'baz', $list );
	}

	function test_filter_object_list_nested_array_or() {
		$list = wp_filter_object_list( $this->object_list, array( 'field3' => true, 'field4' => array( 'blue' ) ), 'OR' );
		$this->assertEquals( 2, count( $list ) );
		$this->assertArrayHasKey( 'foo', $list );
		$this->assertArrayHasKey( 'baz', $list );
	}

	function test_filter_object_list_nested_array_or_singular() {
		$list = wp_filter_object_list( $this->object_list, array( 'field4' => array( 'blue' ) ), 'OR' );
		$this->assertEquals( 1, count( $list ) );
		$this->assertArrayHasKey( 'baz', $list );
	}


	function test_filter_object_list_nested_array_and_field() {
		$list = wp_filter_object_list( $this->object_list, array( 'field4' => array( 'blue' ) ), 'AND', 'name' );
		$this->assertEquals( 1, count( $list ) );
		$this->assertEquals( array( 'baz' => 'baz' ) , $list );
	}

	function test_filter_object_list_nested_array_not_field() {
		$list = wp_filter_object_list( $this->object_list, array( 'field4' => array( 'green' ) ), 'NOT', 'name' );
		$this->assertEquals( 2, count( $list ) );
		$this->assertEquals( array( 'foo' => 'foo', 'baz' => 'baz' ), $list );
	}

	function test_filter_object_list_nested_array_or_field() {
		$list = wp_filter_object_list( $this->object_list, array( 'field3' => true, 'field4' => array( 'blue' ) ), 'OR', 'name' );
		$this->assertEquals( 2, count( $list ) );
		$this->assertEquals( array( 'foo' => 'foo', 'baz' => 'baz' ), $list );
	}
}
