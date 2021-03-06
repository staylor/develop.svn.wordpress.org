<?php

// Test the output of Comment Querying functions

/**
 * @group comment
 */
class Tests_Comment_Query extends WP_UnitTestCase {
	protected $post_id;
	protected $comment_id;

	function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();
	}

	/**
	 * @ticket 21101
	 */
	function test_get_comment_comment_approved_0() {
		$comment_id = $this->factory->comment->create( array( 'comment_post_ID' => $this->post_id ) );
		$comments_approved_0 = get_comments( array( 'status' => 'hold' ) );
		$this->assertEquals( 0, count( $comments_approved_0 ) );
	}

	/**
	 * @ticket 21101
	 */
	function test_get_comment_comment_approved_1() {
		$comment_id = $this->factory->comment->create( array( 'comment_post_ID' => $this->post_id ) );
		$comments_approved_1 = get_comments( array( 'status' => 'approve' ) );

		$this->assertEquals( 1, count( $comments_approved_1 ) );
		$result = $comments_approved_1[0];

		$this->assertEquals( $comment_id, $result->comment_ID );
	}

	function test_get_comments_for_post() {
		$post_id = $this->factory->post->create();
		$this->factory->comment->create_post_comments( $post_id, 10 );
		$comments = get_comments( array( 'post_id' => $post_id ) );
		$this->assertEquals( 10, count( $comments ) );
		foreach ( $comments as $comment ) {
			$this->assertEquals( $post_id, $comment->comment_post_ID );
		}

		$post_id2 = $this->factory->post->create();
		$this->factory->comment->create_post_comments( $post_id2, 10 );
		$comments = get_comments( array( 'post_id' => $post_id2 ) );
		$this->assertEquals( 10, count( $comments ) );
		foreach ( $comments as $comment ) {
			$this->assertEquals( $post_id2, $comment->comment_post_ID );
		}

		$post_id3 = $this->factory->post->create();
		$this->factory->comment->create_post_comments( $post_id3, 10, array( 'comment_approved' => '0' ) );
		$comments = get_comments( array( 'post_id' => $post_id3 ) );
		$this->assertEquals( 10, count( $comments ) );
		foreach ( $comments as $comment ) {
			$this->assertEquals( $post_id3, $comment->comment_post_ID );
		}

		$comments = get_comments( array( 'post_id' => $post_id3, 'status' => 'hold' ) );
		$this->assertEquals( 10, count( $comments ) );
		foreach ( $comments as $comment ) {
			$this->assertEquals( $post_id3, $comment->comment_post_ID );
		}

		$comments = get_comments( array( 'post_id' => $post_id3, 'status' => 'approve' ) );
		$this->assertEquals( 0, count( $comments ) );

		$this->factory->comment->create_post_comments( $post_id3, 10, array( 'comment_approved' => '1' ) );
		$comments = get_comments( array( 'post_id' => $post_id3 ) );
		$this->assertEquals( 20, count( $comments ) );
		foreach ( $comments as $comment ) {
			$this->assertEquals( $post_id3, $comment->comment_post_ID );
		}
	}

	/**
	 * @ticket 21003
	 */
	function test_orderby_meta() {
		$comment_id = $this->factory->comment->create( array( 'comment_post_ID' => $this->post_id ) );
		$comment_id2 = $this->factory->comment->create( array( 'comment_post_ID' => $this->post_id ) );
		$comment_id3 = $this->factory->comment->create( array( 'comment_post_ID' => $this->post_id ) );

		add_comment_meta( $comment_id, 'key', 'value1', true );
		add_comment_meta( $comment_id, 'key1', 'value1', true );
		add_comment_meta( $comment_id, 'key3', 'value3', true );
		add_comment_meta( $comment_id2, 'key', 'value2', true );
		add_comment_meta( $comment_id2, 'key2', 'value2', true );
		add_comment_meta( $comment_id3, 'key3', 'value3', true );

		$comments = get_comments( array( 'meta_key' => 'key', 'orderby' => array( 'key' ) ) );
		$this->assertEquals( 2, count( $comments ) );
		$this->assertEquals( $comment_id2, $comments[0]->comment_ID );
		$this->assertEquals( $comment_id, $comments[1]->comment_ID );

		$comments = get_comments( array( 'meta_key' => 'key', 'orderby' => array( 'meta_value' ) ) );
		$this->assertEquals( 2, count( $comments ) );
		$this->assertEquals( $comment_id2, $comments[0]->comment_ID );
		$this->assertEquals( $comment_id, $comments[1]->comment_ID );

		$comments = get_comments( array( 'meta_key' => 'key', 'orderby' => array( 'key' ), 'order' => 'ASC' ) );
		$this->assertEquals( 2, count( $comments ) );
		$this->assertEquals( $comment_id, $comments[0]->comment_ID );
		$this->assertEquals( $comment_id2, $comments[1]->comment_ID );

		$comments = get_comments( array( 'meta_key' => 'key', 'orderby' => array( 'meta_value' ), 'order' => 'ASC' ) );
		$this->assertEquals( 2, count( $comments ) );
		$this->assertEquals( $comment_id, $comments[0]->comment_ID );
		$this->assertEquals( $comment_id2, $comments[1]->comment_ID );

		$comments = get_comments( array( 'meta_value' => 'value3', 'orderby' => array( 'key' ) ) );
		$this->assertEquals( 2, count( $comments ) );
		$this->assertEquals( $comment_id, $comments[0]->comment_ID );
		$this->assertEquals( $comment_id3, $comments[1]->comment_ID );

		$comments = get_comments( array( 'meta_value' => 'value3', 'orderby' => array( 'meta_value' ) ) );
		$this->assertEquals( 2, count( $comments ) );
		$this->assertEquals( $comment_id, $comments[0]->comment_ID );
		$this->assertEquals( $comment_id3, $comments[1]->comment_ID );

		// value1 is present on two different keys for $comment_id yet we should get only one instance
		// of that comment in the results
		$comments = get_comments( array( 'meta_value' => 'value1', 'orderby' => array( 'key' ) ) );
		$this->assertEquals( 1, count( $comments ) );

		$comments = get_comments( array( 'meta_value' => 'value1', 'orderby' => array( 'meta_value' ) ) );
		$this->assertEquals( 1, count( $comments ) );
	}

	function test_get_status() {
		$post_id = $this->factory->post->create();
		$this->factory->comment->create_post_comments( $post_id, 10 );

		$post_id2 = $this->factory->post->create();
		$this->factory->comment->create_post_comments( $post_id2, 10 );

		$post_id3 = $this->factory->post->create();
		$this->factory->comment->create_post_comments( $post_id3, 10, array( 'comment_approved' => '0' ) );

		$post_id4 = $this->factory->post->create();
		$this->factory->comment->create_post_comments( $post_id4, 10, array( 'comment_approved' => 'trash' ) );

		$post_id5 = $this->factory->post->create();
		$this->factory->comment->create_post_comments( $post_id5, 10, array( 'comment_approved' => 'spam' ) );

		$this->assertEquals( 30, count( get_comments() ) );
		$this->assertEquals( 30, count( get_comments( array( 'status' => 'all' ) ) ) );
		$this->assertEquals( 20, count( get_comments( array( 'status' => 'approve' ) ) ) );
		$this->assertEquals( 10, count( get_comments( array( 'status' => 'hold' ) ) ) );
		$this->assertEquals( 10, count( get_comments( array( 'status' => 'trash' ) ) ) );
		$this->assertEquals( 10, count( get_comments( array( 'status' => 'spam' ) ) ) );
	}
}
