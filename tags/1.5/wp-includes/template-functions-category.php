<?php

function get_the_category($id = false) {
    global $post, $wpdb, $category_cache;

    if (! $id) {
        $id = $post->ID;
    }

    if ($category_cache[$id]) {
			 $categories = $category_cache[$id];
    } else {
        $categories = $wpdb->get_results("
            SELECT category_id, cat_name, category_nicename, category_description, category_parent
            FROM  $wpdb->categories, $wpdb->post2cat
            WHERE $wpdb->post2cat.category_id = cat_ID AND $wpdb->post2cat.post_id = '$id'
            ");
    
    }

		if (!empty($categories))
			sort($categories);

		return $categories;
}

function get_category_link($category_id) {
	global $wpdb, $wp_rewrite, $querystring_start, $querystring_equal, $cache_categories;
	$catlink = $wp_rewrite->get_category_permastruct();

	if ( empty($catlink) ) {
		$file = get_settings('home') . '/' . get_settings('blogfilename');
		$catlink = $file . '?cat=' . $category_id;
	} else {
		if ($cache_categories[$category_id]->category_nicename)
			$category_nicename = $cache_categories[$category_id]->category_nicename;
		else
			$category_nicename = $wpdb->get_var('SELECT category_nicename FROM ' . $wpdb->categories . ' WHERE cat_ID=' . $category_id);

		if ($parent = $cache_categories[$category_id]->category_parent) 
			$category_nicename = get_category_parents($parent, false, '/', true) . $category_nicename . '/';

		$catlink = str_replace('%category%', $category_nicename, $catlink);
		$catlink = get_settings('home') . trailingslashit($catlink);
	}
	return apply_filters('category_link', $catlink, $category_id);
}

function the_category($separator = '', $parents='') {
    $categories = get_the_category();
    if (empty($categories)) {
        _e('Uncategorized');
        return;
    }

    $thelist = '';
    if ('' == $separator) {
        $thelist .= '<ul class="post-categories">';
        foreach ($categories as $category) {
            $category->cat_name = $category->cat_name;
            $thelist .= "\n\t<li>";
            switch(strtolower($parents)) {
                case 'multiple':
                    if ($category->category_parent) {
                        $thelist .= get_category_parents($category->category_parent, TRUE);
                    }
                    $thelist .= '<a href="' . get_category_link($category->category_id) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . '" rel="category tag">'.$category->cat_name.'</a></li>';
                    break;
                case 'single':
                    $thelist .= '<a href="' . get_category_link($category->category_id) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . ' rel="category tag">';
                    if ($category->category_parent) {
                        $thelist .= get_category_parents($category->category_parent, FALSE);
                    }
                    $thelist .= $category->cat_name.'</a></li>';
                    break;
                case '':
                default:
                    $thelist .= '<a href="' . get_category_link($category->category_id) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . '" rel="category tag">'.$category->cat_name.'</a></li>';
            }
        }
        $thelist .= '</ul>';
    } else {
        $i = 0;
        foreach ($categories as $category) {
            $category->cat_name = $category->cat_name;
            if (0 < $i) $thelist .= $separator . ' ';
            switch(strtolower($parents)) {
                case 'multiple':
                    if ($category->category_parent)    $thelist .= get_category_parents($category->category_parent, TRUE);
                    $thelist .= '<a href="' . get_category_link($category->category_id) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . '" rel="category tag">'.$category->cat_name.'</a>';
                    break;
                case 'single':
                    $thelist .= '<a href="' . get_category_link($category->category_id) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . '" rel="category tag">';
                    if ($category->category_parent)    $thelist .= get_category_parents($category->category_parent, FALSE);
                    $thelist .= "$category->cat_name</a>";
                    break;
                case '':
                default:
                    $thelist .= '<a href="' . get_category_link($category->category_id) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . '" rel="category tag">'.$category->cat_name.'</a>';
            }
            ++$i;
        }
    }
    echo apply_filters('the_category', $thelist, $separator, $parents);
}

function get_the_category_by_ID($cat_ID) {
    global $cache_categories, $wpdb;
    if ( !$cache_categories[$cat_ID] ) {
        $cat_name = $wpdb->get_var("SELECT cat_name FROM $wpdb->categories WHERE cat_ID = '$cat_ID'");
        $cache_categories[$cat_ID]->cat_name = $cat_name;
    } else {
        $cat_name = $cache_categories[$cat_ID]->cat_name;
    }
    return($cat_name);
}

function get_category_parents($id, $link = FALSE, $separator = '/', $nicename = FALSE){
    global $cache_categories;
    $chain = '';
    $parent = $cache_categories[$id];
    if ($nicename) {
        $name = $parent->category_nicename;
    } else {
        $name = $parent->cat_name;
    }
    if ($parent->category_parent) $chain .= get_category_parents($parent->category_parent, $link, $separator, $nicename);
    if ($link) {
        $chain .= '<a href="' . get_category_link($parent->cat_ID) . '" title="' . sprintf(__("View all posts in %s"), $parent->cat_name) . '">'.$name.'</a>' . $separator;
    } else {
        $chain .= $name.$separator;
    }
    return $chain;
}

function get_category_children($id, $before = '/', $after = '') {
    global $cache_categories;
    $c_cache = $cache_categories; // Can't do recursive foreach on a global, have to make a copy
    $chain = '';
    foreach ($c_cache as $category){
        if ($category->category_parent == $id){
            $chain .= $before.$category->cat_ID.$after;
            $chain .= get_category_children($category->cat_ID, $before, $after);
        }
    }
    return $chain;
}

// Deprecated.
function the_category_ID($echo = true) {
    // Grab the first cat in the list.
    $categories = get_the_category();
    $cat = $categories[0]->category_id;
    
    if ($echo) echo $cat;

    return $cat;
}

// Deprecated.
function the_category_head($before='', $after='') {
    global $currentcat, $previouscat;
    // Grab the first cat in the list.
    $categories = get_the_category();
    $currentcat = $categories[0]->category_id;
    if ($currentcat != $previouscat) {
        echo $before;
        echo get_the_category_by_ID($currentcat);
        echo $after;
        $previouscat = $currentcat;
    }
}

function category_description($category = 0) {
    global $cat, $wpdb, $cache_categories;
    if (!$category) $category = $cat;
    $category_description = $cache_categories[$category]->category_description;
    $category_description = apply_filters('category_description', $category_description, $category);
    return $category_description;
}

// out of the WordPress loop
function dropdown_cats($optionall = 1, $all = 'All', $sort_column = 'ID', $sort_order = 'asc',
        $optiondates = 0, $optioncount = 0, $hide_empty = 1, $optionnone=FALSE,
        $selected=0, $hide=0) {
    global $wpdb;
    if (($file == 'blah') || ($file == '')) $file = get_settings('home') . '/';
    if (!$selected) $selected=$cat;
    $sort_column = 'cat_'.$sort_column;

    $query = "
        SELECT cat_ID, cat_name, category_nicename,category_parent,
        COUNT($wpdb->post2cat.post_id) AS cat_count,
        DAYOFMONTH(MAX(post_date)) AS lastday, MONTH(MAX(post_date)) AS lastmonth
        FROM $wpdb->categories LEFT JOIN $wpdb->post2cat ON (cat_ID = category_id)
        LEFT JOIN $wpdb->posts ON (ID = post_id)
        WHERE cat_ID > 0
        ";
    if ($hide) {
        $query .= " AND cat_ID != $hide";
        $query .= get_category_children($hide, " AND cat_ID != ");
    }
    $query .=" GROUP BY cat_ID";
    if (intval($hide_empty) == 1) $query .= " HAVING cat_count > 0";
    $query .= " ORDER BY $sort_column $sort_order, post_date DESC";

    $categories = $wpdb->get_results($query);
    echo "<select name='cat' class='postform'>\n";
    if (intval($optionall) == 1) {
        $all = apply_filters('list_cats', $all);
        echo "\t<option value='all'>$all</option>\n";
    }
    if (intval($optionnone) == 1) echo "\t<option value='0'>None</option>\n";
    if ($categories) {
        foreach ($categories as $category) {
            $cat_name = apply_filters('list_cats', $category->cat_name, $category);
            echo "\t<option value=\"".$category->cat_ID."\"";
            if ($category->cat_ID == $selected)
                echo ' selected="selected"';
            echo '>';
            echo $cat_name;
            if (intval($optioncount) == 1) echo '&nbsp;&nbsp;('.$category->cat_count.')';
            if (intval($optiondates) == 1) echo '&nbsp;&nbsp;'.$category->lastday.'/'.$category->lastmonth;
            echo "</option>\n";
        }
    }
    echo "</select>\n";
}

// out of the WordPress loop
function wp_list_cats($args = '') {
	parse_str($args, $r);
	if (!isset($r['optionall'])) $r['optionall'] = 0;
    if (!isset($r['all'])) $r['all'] = 'All';
	if (!isset($r['sort_column'])) $r['sort_column'] = 'ID';
	if (!isset($r['sort_order'])) $r['sort_order'] = 'asc';
	if (!isset($r['file'])) $r['file'] = '';
	if (!isset($r['list'])) $r['list'] = true;
	if (!isset($r['optiondates'])) $r['optiondates'] = 0;
	if (!isset($r['optioncount'])) $r['optioncount'] = 0;
	if (!isset($r['hide_empty'])) $r['hide_empty'] = 1;
	if (!isset($r['use_desc_for_title'])) $r['use_desc_for_title'] = 1;
	if (!isset($r['children'])) $r['children'] = true;
	if (!isset($r['child_of'])) $r['child_of'] = 0;
	if (!isset($r['categories'])) $r['categories'] = 0;
	if (!isset($r['recurse'])) $r['recurse'] = 0;
	if (!isset($r['feed'])) $r['feed'] = '';
	if (!isset($r['feed_image'])) $r['feed_image'] = '';
	if (!isset($r['exclude'])) $r['exclude'] = '';
	if (!isset($r['hierarchical'])) $r['hierarchical'] = true;

	list_cats($r['optionall'], $r['all'], $r['sort_column'], $r['sort_order'], $r['file'],	$r['list'], $r['optiondates'], $r['optioncount'], $r['hide_empty'], $r['use_desc_for_title'], $r['children'], $r['child_of'], $r['categories'], $r['recurse'], $r['feed'], $r['feed_image'], $r['exclude'], $r['hierarchical']);
}

function list_cats($optionall = 1, $all = 'All', $sort_column = 'ID', $sort_order = 'asc', $file = '', $list = true, $optiondates = 0, $optioncount = 0, $hide_empty = 1, $use_desc_for_title = 1, $children=FALSE, $child_of=0, $categories=0, $recurse=0, $feed = '', $feed_image = '', $exclude = '', $hierarchical=FALSE) {
	global $wpdb, $category_posts;
	// Optiondates now works
	if ('' == $file) {
		$file = get_settings('home') . '/';
	}

	$exclusions = '';
	if (!empty($exclude)) {
		$excats = preg_split('/[\s,]+/',$exclude);
		if (count($excats)) {
			foreach ($excats as $excat) {
				$exclusions .= ' AND cat_ID <> ' . intval($excat) . ' ';
			}
		}
	}

	if ($hide_empty) {
		$categories = array();
		$extra_fields = 'cat_name, category_nicename, category_description,';
	} else {
		if (intval($categories)==0 && !$hide_empty){
			$sort_column = 'cat_'.$sort_column;

			$query  = "
				SELECT cat_ID, cat_name, category_nicename, category_description, category_parent
				FROM $wpdb->categories
				WHERE cat_ID > 0 $exclusions
				ORDER BY $sort_column $sort_order";

			$categories = $wpdb->get_results($query);
		}
		$extra_fields = '';
	}

	if (!count($category_posts)) {
		$now = current_time('mysql', 1);
		$cat_counts = $wpdb->get_results("	SELECT cat_ID, $extra_fields
		COUNT($wpdb->post2cat.post_id) AS cat_count
		FROM $wpdb->categories 
		INNER JOIN $wpdb->post2cat ON (cat_ID = category_id)
		INNER JOIN $wpdb->posts ON (ID = post_id)
		WHERE post_status = 'publish'
		AND post_date_gmt < '$now' $exclusions
		GROUP BY category_id");
        if (! empty($cat_counts)) {
            foreach ($cat_counts as $cat_count) {
                if (1 != intval($hide_empty) || $cat_count > 0) {
                    $category_posts["$cat_count->cat_ID"] = $cat_count->cat_count;
                    if ($hide_empty) {
                    	$categories[] = $cat_count;
                    }
                }
            }
        }
	}
	
	if ( $optiondates ) {
		$cat_dates = $wpdb->get_results("	SELECT category_id,
		UNIX_TIMESTAMP( MAX(post_date) ) AS ts
		FROM $wpdb->posts, $wpdb->post2cat
		WHERE post_status = 'publish' AND post_id = ID $exclusions
		GROUP BY category_id");
		foreach ($cat_dates as $cat_date) {
			$category_timestamp["$cat_date->category_id"] = $cat_date->ts;
		}
	}
	
	$num_found=0;
	$thelist = "";
	
	foreach ($categories as $category) {
		if ((intval($hide_empty) == 0 || isset($category_posts["$category->cat_ID"])) && (!$hierarchical || $category->category_parent == $child_of) ) {
			$num_found++;
			$link = '<a href="'.get_category_link($category->cat_ID).'" ';
			if ($use_desc_for_title == 0 || empty($category->category_description)) {
				$link .= 'title="'. sprintf(__("View all posts filed under %s"), wp_specialchars($category->cat_name)) . '"';
			} else {
				$link .= 'title="' . wp_specialchars(apply_filters('category_description',$category->category_description,$category)) . '"';
			}
			$link .= '>';
			$link .= apply_filters('list_cats', $category->cat_name, $category).'</a>';

			if ( (! empty($feed_image)) || (! empty($feed)) ) {
				
				$link .= ' ';

				if (empty($feed_image)) {
					$link .= '(';
				}

				$link .= '<a href="' . get_category_rss_link(0, $category->cat_ID, $category->category_nicename)  . '"';

				if ( !empty($feed) ) {
					$title =  ' title="' . $feed . '"';
					$alt = ' alt="' . $feed . '"';
					$name = $feed;
					$link .= $title;
				}

				$link .= '>';

				if (! empty($feed_image)) {
					$link .= "<img src='$feed_image' $alt$title" . ' />';
				} else {
					$link .= $name;
				}
				
				$link .= '</a>';

				if (empty($feed_image)) {
					$link .= ')';
				}
			}

			if (intval($optioncount) == 1) {
				$link .= ' ('.intval($category_posts["$category->cat_ID"]).')';
			}
			if ( $optiondates ) {
				if ( $optiondates == 1 ) $optiondates = 'Y-m-d';
				$link .= ' ' . gmdate($optiondates, $category_timestamp["$category->cat_ID"]);
			}
			if ($list) {
				$thelist .= "\t<li>$link\n";
			} else {
				$thelist .= "\t$link<br />\n";
			}
			if ($hierarchical && $children) $thelist .= list_cats($optionall, $all, $sort_column, $sort_order, $file, $list, $optiondates, $optioncount, $hide_empty, $use_desc_for_title, $hierarchical, $category->cat_ID, $categories, 1, $feed, $feed_image, $exclude, $hierarchical);
			if ($list) $thelist .= "</li>\n";
			}
	}
	if (!$num_found && !$child_of){
		if ($list) {
			$before = '<li>';
			$after = '</li>';
		}
		echo $before . __("No categories") . $after . "\n";
		return;
	}
	if ($list && $child_of && $num_found && $recurse) {
		$pre = "\t\t<ul class='children'>";
		$post = "\t\t</ul>\n";
	} else {
		$pre = $post = '';
	}
	$thelist = $pre . $thelist . $post;
	if ($recurse) {
		return $thelist;
	}
	echo apply_filters('list_cats', $thelist);
}

function in_category($category) { // Check if the current post is in the given category
	global $post, $category_cache;
	$cats = '';
	foreach ($category_cache[$post->ID] as $cat) :
		$cats[] = $cat->category_id;
	endforeach;

	if ( in_array($category, $cats) )
		return true;
	else
		return false;
}
?>