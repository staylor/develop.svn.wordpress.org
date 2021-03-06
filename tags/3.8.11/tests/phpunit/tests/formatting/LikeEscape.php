<?php

/**
 * @group formatting
 */
class Tests_Formatting_LikeEscape extends WP_UnitTestCase {
	/**
	 * @ticket 10041
	 */
	function test_like_escape() {
		$this->markTestSkipped( 'Ticket was fixed in a later release.' );
		$inputs = array(
			'howdy%', //Single Percent
			'howdy_', //Single Underscore
			'howdy\\', //Single slash
			'howdy\\howdy%howdy_', //The works
		);
		$expected = array(
			"howdy\\%",
			'howdy\\_',
			'howdy\\\\',
			'howdy\\\\howdy\\%howdy\\_'
		);

		foreach ($inputs as $key => $input) {
			$this->assertEquals($expected[$key], like_escape($input));
		}
	}
}
