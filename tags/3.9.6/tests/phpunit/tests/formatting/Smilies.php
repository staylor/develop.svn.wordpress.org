<?php

/**
 * @group formatting
 */
class Tests_Formatting_Smilies extends WP_UnitTestCase {

	/**
	 * Basic Test Content DataProvider
	 *
	 * array ( input_txt, converted_output_txt)
	 */
	public function get_smilies_input_output() {
		$includes_path = includes_url("images/smilies/");

		return array (
			array (
				'Lorem ipsum dolor sit amet mauris ;-) Praesent gravida sodales. :lol: Vivamus nec diam in faucibus eu, bibendum varius nec, imperdiet purus est, at augue at lacus malesuada elit dapibus a, :eek: mauris. Cras mauris viverra elit. Nam laoreet viverra. Pellentesque tortor. Nam libero ante, porta urna ut turpis. Nullam wisi magna, :mrgreen: tincidunt nec, sagittis non, fringilla enim. Nam consectetuer nec, ullamcorper pede eu dui odio consequat vel, vehicula tortor quis pede turpis cursus quis, egestas ipsum ultricies ut, eleifend velit. Mauris vestibulum iaculis. Sed in nunc. Vivamus elit porttitor egestas. Mauris purus :?:',
				'Lorem ipsum dolor sit amet mauris <img src="' . $includes_path . 'icon_wink.gif" alt=";-)" class="wp-smiley" />  Praesent gravida sodales. <img src="' . $includes_path . 'icon_lol.gif" alt=":lol:" class="wp-smiley" />  Vivamus nec diam in faucibus eu, bibendum varius nec, imperdiet purus est, at augue at lacus malesuada elit dapibus a, <img src="' . $includes_path . 'icon_surprised.gif" alt=":eek:" class="wp-smiley" />  mauris. Cras mauris viverra elit. Nam laoreet viverra. Pellentesque tortor. Nam libero ante, porta urna ut turpis. Nullam wisi magna, <img src="' . $includes_path . 'icon_mrgreen.gif" alt=":mrgreen:" class="wp-smiley" />  tincidunt nec, sagittis non, fringilla enim. Nam consectetuer nec, ullamcorper pede eu dui odio consequat vel, vehicula tortor quis pede turpis cursus quis, egestas ipsum ultricies ut, eleifend velit. Mauris vestibulum iaculis. Sed in nunc. Vivamus elit porttitor egestas. Mauris purus <img src="' . $includes_path . 'icon_question.gif" alt=":?:" class="wp-smiley" /> '
			),
			array (
				'<strong>Welcome to the jungle!</strong> We got fun n games! :) We got everything you want 8-) <em>Honey we know the names :)</em>',
				'<strong>Welcome to the jungle!</strong> We got fun n games! <img src="' . $includes_path . 'icon_smile.gif" alt=":)" class="wp-smiley" />  We got everything you want <img src="' . $includes_path . 'icon_cool.gif" alt="8-)" class="wp-smiley" />  <em>Honey we know the names <img src="' . $includes_path . 'icon_smile.gif" alt=":)" class="wp-smiley" /> </em>'
			),
			array (
				"<strong;)>a little bit of this\na little bit:other: of that :D\n:D a little bit of good\nyeah with a little bit of bad8O",
				"<strong;)>a little bit of this\na little bit:other: of that <img src=\"{$includes_path}icon_biggrin.gif\" alt=\":D\" class=\"wp-smiley\" />  <img src=\"{$includes_path}icon_biggrin.gif\" alt=\":D\" class=\"wp-smiley\" />  a little bit of good\nyeah with a little bit of bad8O"
			),
			array (
				'<strong style="here comes the sun :-D">and I say it\'s allright:D:D',
				'<strong style="here comes the sun :-D">and I say it\'s allright:D:D'
			),
			array (
				'<!-- Woo-hoo, I\'m a comment, baby! :x > -->',
				'<!-- Woo-hoo, I\'m a comment, baby! :x > -->'
			),
			array (
				':?:P:?::-x:mrgreen:::',
				':?:P:?::-x:mrgreen:::'
			),
		);
	}

	/**
	 * @dataProvider get_smilies_input_output
	 *
	 * Basic Validation Test to confirm that smilies are converted to image
	 * when use_smilies = 1 and not when use_smilies = 0
	 */
	function test_convert_standard_smilies( $in_txt, $converted_txt ) {
		global $wpsmiliestrans;

		// standard smilies, use_smilies: ON
		update_option( 'use_smilies', 1 );

		smilies_init();

		$this->assertEquals( $converted_txt, convert_smilies($in_txt) );

		// standard smilies, use_smilies: OFF
		update_option( 'use_smilies', 0 );

		$this->assertEquals( $in_txt, convert_smilies($in_txt) );

		unset( $wpsmiliestrans );
	}

	/**
	 * Custom Smilies Test Content DataProvider
	 *
	 * array ( input_txt, converted_output_txt)
	 */
	public function get_custom_smilies_input_output() {
		$includes_path = includes_url("images/smilies/");

		return array (
			array (
				'Peter Brian Gabriel (born 13 February 1950) is a British singer, musician, and songwriter who rose to fame as the lead vocalist and flautist of the progressive rock group Genesis. :monkey:',
				'Peter Brian Gabriel (born 13 February 1950) is a British singer, musician, and songwriter who rose to fame as the lead vocalist and flautist of the progressive rock group Genesis. <img src="' . $includes_path . 'icon_shock_the_monkey.gif" alt=":monkey:" class="wp-smiley" /> '
			),
			array (
				'Star Wars Jedi Knight :arrow: Jedi Academy is a first and third-person shooter action game set in the Star Wars universe. It was developed by Raven Software and published, distributed and marketed by LucasArts in North America and by Activision in the rest of the world. :nervou:',
				'Star Wars Jedi Knight <img src="' . $includes_path . 'icon_arrow.gif" alt=":arrow:" class="wp-smiley" />  Jedi Academy is a first and third-person shooter action game set in the Star Wars universe. It was developed by Raven Software and published, distributed and marketed by LucasArts in North America and by Activision in the rest of the world. <img src="' . $includes_path . 'icon_nervou.gif" alt=":nervou:" class="wp-smiley" /> '
			),
			array (
				':arrow: monkey: Lorem ipsum dolor sit amet enim. Etiam ullam :PP <br />corper. Suspendisse a pellentesque dui, non felis.<a> :arrow: :arrow</a>',
				' <img src="' . $includes_path . 'icon_arrow.gif" alt=":arrow:" class="wp-smiley" />  monkey: Lorem ipsum dolor sit amet enim. Etiam ullam <img src="' . $includes_path . 'icon_tongue.gif" alt=":PP" class="wp-smiley" />  <br />corper. Suspendisse a pellentesque dui, non felis.<a> <img src="' . $includes_path . 'icon_arrow.gif" alt=":arrow:" class="wp-smiley" />  :arrow</a>'
			),
		);
	}

	/**
	 * @dataProvider get_custom_smilies_input_output
	 *
	 * Validate Custom Smilies are converted to images when use_smilies = 1
	 * @uses $wpsmiliestrans
	 */
	function test_convert_custom_smilies ( $in_txt, $converted_txt ) {
		global $wpsmiliestrans;
		$trans_orig = $wpsmiliestrans; // save original translations array

		// custom smilies, use_smilies: ON
		update_option( 'use_smilies', 1 );
		$wpsmiliestrans = array(
		  ':PP' => 'icon_tongue.gif',
		  ':arrow:' => 'icon_arrow.gif',
		  ':monkey:' => 'icon_shock_the_monkey.gif',
		  ':nervou:' => 'icon_nervou.gif'
		);

		smilies_init();

		$this->assertEquals( $converted_txt, convert_smilies($in_txt) );

		// standard smilies, use_smilies: OFF
		update_option( 'use_smilies', 0 );

		$this->assertEquals( $in_txt, convert_smilies($in_txt) );

		$wpsmiliestrans = $trans_orig; // reset original translations array
	}


	/**
	 * DataProvider of HTML elements/tags that smilie matches should be ignored in
	 *
	 */
	public function get_smilies_ignore_tags() {
		return array (
			array( 'pre' ),
			array( 'code' ),
			array( 'script' ),
			array( 'style' ),
			array( 'textarea'),
		);
	}

	/**
	 * Validate Conversion of Smilies is ignored in pre-determined tags
	 * pre, code, script, style
	 *
	 * @ticket 16448
	 * @dataProvider get_smilies_ignore_tags
	 * @uses $wpsmiliestrans
	 */
	public function test_ignore_smilies_in_tags( $element ) {
		global $wpsmiliestrans;
		$trans_orig = $wpsmiliestrans;  // save original translations array

		$includes_path = includes_url("images/smilies/");

		$in_str = 'Do we ingore smilies ;-) in ' . $element . ' tags <' . $element . '>My Content Here :?: </' . $element . '>';
		$exp_str = 'Do we ingore smilies <img src="' . $includes_path . 'icon_wink.gif" alt=";-)" class="wp-smiley" />  in ' . $element . ' tags <' . $element . '>My Content Here :?: </' . $element . '>';

		// standard smilies, use_smilies: ON
		update_option( 'use_smilies', 1 );
		smilies_init();

		$this->assertEquals( $exp_str, convert_smilies($in_str) );

		// standard smilies, use_smilies: OFF
		update_option( 'use_smilies', 0 );

		$wpsmiliestrans = $trans_orig; // reset original translations array
	}

	/**
	 * DataProvider of Smilie Combinations
	 *
	 */
	public function get_smilies_combinations() {
		$includes_path = includes_url("images/smilies/");

		return array (
			array (
				'8-O :-(',
				' <img src="' . $includes_path . 'icon_eek.gif" alt="8-O" class="wp-smiley" />  <img src="' . $includes_path . 'icon_sad.gif" alt=":-(" class="wp-smiley" /> '
			),
			array (
				'8-) 8-O',
				' <img src="' . $includes_path . 'icon_cool.gif" alt="8-)" class="wp-smiley" />  <img src="' . $includes_path . 'icon_eek.gif" alt="8-O" class="wp-smiley" /> '
			),
			array (
				'8-) 8O',
				' <img src="' . $includes_path . 'icon_cool.gif" alt="8-)" class="wp-smiley" />  <img src="' . $includes_path . 'icon_eek.gif" alt="8O" class="wp-smiley" /> '
			),
			array (
				'8-) :-(',
				' <img src="' . $includes_path . 'icon_cool.gif" alt="8-)" class="wp-smiley" />  <img src="' . $includes_path . 'icon_sad.gif" alt=":-(" class="wp-smiley" /> '
			),
			array (
				'8-) :twisted:',
				' <img src="' . $includes_path . 'icon_cool.gif" alt="8-)" class="wp-smiley" />  <img src="' . $includes_path . 'icon_twisted.gif" alt=":twisted:" class="wp-smiley" /> '
			),
			array (
				'8O :twisted: :( :? :(',
				' <img src="' . $includes_path . 'icon_eek.gif" alt="8O" class="wp-smiley" />  <img src="' . $includes_path . 'icon_twisted.gif" alt=":twisted:" class="wp-smiley" />  <img src="' . $includes_path . 'icon_sad.gif" alt=":(" class="wp-smiley" />  <img src="' . $includes_path . 'icon_confused.gif" alt=":?" class="wp-smiley" />  <img src="' . $includes_path . 'icon_sad.gif" alt=":(" class="wp-smiley" /> '
			),
		);
	}

	/**
	 * Validate Combinations of Smilies separated by single space
	 * are converted correctly
	 *
	 * @ticket 20124
	 * @dataProvider get_smilies_combinations
	 * @uses $wpsmiliestrans
	 */
	public function test_smilies_combinations( $in_txt, $converted_txt ) {
		global $wpsmiliestrans;

		// custom smilies, use_smilies: ON
		update_option( 'use_smilies', 1 );
		smilies_init();

		$this->assertEquals( $converted_txt, convert_smilies($in_txt) );

		// custom smilies, use_smilies: OFF
		update_option( 'use_smilies', 0 );

		$this->assertEquals( $in_txt, convert_smilies($in_txt) );
	}

	/**
	 * DataProvider of Single Smilies input and converted output
	 *
	 */
	public function get_single_smilies_input_output() {
		$includes_path = includes_url("images/smilies/");

		return array (
			array (
				'8-O :-(',
				'8-O :-('
			),
			array (
				'8O :) additional text here :)',
				'8O <img src="' . $includes_path . 'icon_smile.gif" alt=":)" class="wp-smiley" />  additional text here <img src="' . $includes_path . 'icon_smile.gif" alt=":)" class="wp-smiley" /> '
			),
			array (
				':) :) :) :)',
				' <img src="' . $includes_path . 'icon_smile.gif" alt=":)" class="wp-smiley" />  <img src="' . $includes_path . 'icon_smile.gif" alt=":)" class="wp-smiley" />  <img src="' . $includes_path . 'icon_smile.gif" alt=":)" class="wp-smiley" />  <img src="' . $includes_path . 'icon_smile.gif" alt=":)" class="wp-smiley" /> '
			),
		);
	}

	/**
	 * Validate Smilies are converted for single smilie in
	 * the $wpsmiliestrans global array
	 *
	 * @ticket 25303
	 * @dataProvider get_single_smilies_input_output
	 * @uses $wpsmiliestrans
	 */
	public function test_single_smilies_in_wpsmiliestrans( $in_txt, $converted_txt ) {
		global $wpsmiliestrans;
		$orig_trans = $wpsmiliestrans; // save original tranlations array

		// standard smilies, use_smilies: ON
		update_option( 'use_smilies', 1 );

		$wpsmiliestrans = array (
		  ':)' => 'icon_smile.gif'
		);

		smilies_init();

		$this->assertEquals( $converted_txt, convert_smilies($in_txt) );

		// standard smilies, use_smilies: OFF
		update_option( 'use_smilies', 0 );

		$this->assertEquals( $in_txt, convert_smilies($in_txt) );

		$wpsmiliestrans = $orig_trans; // reset original translations array
	}
}