<?php

function bloginfo_rss($show='') {
    $info = strip_tags(get_bloginfo($show));
    echo convert_chars($info);
}

function the_title_rss() {
	$title = get_the_title();
	$title = apply_filters('the_title', $title);
	$title = apply_filters('the_title_rss', $title);
	echo $title;
}

function the_content_rss($more_link_text='(more...)', $stripteaser=0, $more_file='', $cut = 0, $encode_html = 0) {
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	$content = apply_filters('the_content', $content);
	if ($cut && !$encode_html) {
		$encode_html = 2;
	}
	if ($encode_html == 1) {
		$content = wp_specialchars($content);
		$cut = 0;
	} elseif ($encode_html == 0) {
		$content = make_url_footnote($content);
	} elseif ($encode_html == 2) {
		$content = strip_tags($content);
	}
	if ($cut) {
		$blah = explode(' ', $content);
		if (count($blah) > $cut) {
			$k = $cut;
			$use_dotdotdot = 1;
		} else {
			$k = count($blah);
			$use_dotdotdot = 0;
		}
		for ($i=0; $i<$k; $i++) {
			$excerpt .= $blah[$i].' ';
		}
		$excerpt .= ($use_dotdotdot) ? '...' : '';
		$content = $excerpt;
	}
	$content = str_replace(']]>', ']]&gt;', $content);
	echo $content;
}

function the_excerpt_rss() {
	$output = get_the_excerpt(true);
	echo apply_filters('the_excerpt_rss', $output);
}

function permalink_single_rss($file = '') {
    echo get_permalink();
}

function comment_link() {
	echo get_comment_link();
}

function comment_author_rss() {
	$author = apply_filters('comment_author_rss', get_comment_author() );
	echo $author;
}

function comment_text_rss() {
	$comment_text = get_comment_text();
	$comment_text = apply_filters('comment_text_rss', $comment_text);
	echo $comment_text;
}

function comments_rss_link($link_text = 'Comments RSS', $commentsrssfilename = 'wp-commentsrss2.php') {
	$url = comments_rss($commentsrssfilename);
	echo "<a href='$url'>$link_text</a>";
}

function comments_rss($commentsrssfilename = 'wp-commentsrss2.php') {
	global $id;

	if ('' != get_settings('permalink_structure'))
		$url = trailingslashit( get_permalink() ) . 'feed/';
	else
		$url = get_settings('siteurl') . "/$commentsrssfilename?p=$id";

	return $url;
}

function get_author_rss_link($echo = false, $author_id, $author_nicename) {
       $auth_ID = $author_id;
       $permalink_structure = get_settings('permalink_structure');

       if ('' == $permalink_structure) {
           $file = get_settings('siteurl') . '/wp-rss2.php';
           $link = $file . '?author=' . $author_id;
       } else {
           $link = get_author_link(0, $author_id, $author_nicename);
           $link = $link . "feed/";
       }

       if ($echo) echo $link;
       return $link;
}

function get_category_rss_link($echo = false, $category_id, $category_nicename) {
       $cat_ID = $category_id;
       $permalink_structure = get_settings('permalink_structure');

       if ('' == $permalink_structure) {
               $file = get_settings('siteurl') . '/wp-rss2.php';
        $link = $file . '?cat=' . $category_id;
       } else {
        $link = get_category_link($category_id);
               $link = $link . "feed/";
       }

       if ($echo) echo $link;
       return $link;
}

function the_category_rss($type = 'rss') {
    $categories = get_the_category();
    $the_list = '';
    foreach ($categories as $category) {
        $category->cat_name = convert_chars($category->cat_name);
        if ('rdf' == $type) {
            $the_list .= "\n\t<dc:subject>$category->cat_name</dc:subject>";
        } else {
            $the_list .= "\n\t<category>$category->cat_name</category>";
        }
    }
    echo apply_filters('the_category_rss', $the_list, $type);
}

function rss_enclosure() {
	global $id;
	$custom_fields = get_post_custom();
	if( is_array( $custom_fields ) ) {
		while( list( $key, $val ) = each( $custom_fields ) ) { 
			if( $key == 'enclosure' ) {
				if (is_array($val)) {
					foreach($val as $enc) {
						$enclosure = split( "\n", $enc );
						print "<enclosure url='".trim( $enclosure[ 0 ] )."' length='".trim( $enclosure[ 1 ] )."' type='".trim( $enclosure[ 2 ] )."'/>\n";
					}
				}
			}
		}
	}
}

?>