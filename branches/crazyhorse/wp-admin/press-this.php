<?php
require_once('admin.php');

if ( ! current_user_can('publish_posts') ) wp_die( __( 'Cheatin&#8217; uh?' )); ?>

<?php 
function press_it() {
	#define some basic variables
	$quick['post_status'] = $_REQUEST['post_status'];
	$quick['post_category'] = $_REQUEST['post_category'];
	$quick['tags_input'] = $_REQUEST['tags_input'];
	$quick['post_title'] = $_REQUEST['post_title'];
	$quick['post_content'] = '';
		
	# insert the post with nothing in it, to get an ID
	$post_ID = wp_insert_post($quick, true);
		
	$content = '';
	switch ( $_REQUEST['post_type'] ) {
		case 'text':
		case 'quote':
			$content .= $_REQUEST['content'];
			break;
		
		case 'photo':
			foreach($_REQUEST['photo_src'] as $key => $data) {
				#quote for matching
				$quoted = str_replace('/', '\/', preg_quote($data));
				
				# see if files exist in content - we don't want to upload non-used selected files.
				preg_match('/'.$quoted.'/', $_REQUEST['content'], $matches[0]);
				if($matches[0])
					media_sideload_image($data, $post_ID, $_REQUEST['photo_description'][$key]);	
			}
			$content = $_REQUEST['content'];
			break;
			
		case "video":
			if($_REQUEST['embed_code']) 
				$content .= $_REQUEST['embed_code']."\n\n";
			$content .= $_REQUEST['content'];
			break;	
		}
	# set the post_content
	$quick['post_content'] = str_replace('<br />', "\n", preg_replace('/<\/?p>/','',$content));

	#error handling for $post
	if ( is_wp_error($post_ID) ) {
		wp_die($id);
		wp_delete_post($post_ID);

	#error handling for media_sideload
	} else {	
		$quick['ID'] = $post_ID;
		wp_update_post($quick);
	}
	return $post_ID;
}

# For submitted posts.
if ( 'post' == $_REQUEST['action'] ) { 
	check_admin_referer('press-this'); $post_ID = press_it(); ?>
	
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
		<title><?php _e('Press This') ?></title>
	<?php
		add_thickbox();
		wp_enqueue_style('press-this');
		wp_enqueue_style( 'colors' );
		wp_enqueue_script('post');

		do_action('admin_print_styles');
		do_action('admin_print_scripts');
		do_action('admin_head');
	?>
	</head>
	<body class="press-this">
		<div id="message" class="updated fade"><p><strong><?php _e('Your post has been saved.'); ?></strong> <a onclick="window.opener.location.replace(this.href); window.close();" href="<?php echo get_permalink( $post_ID); ?>"><?php _e('View post'); ?></a> | <a href="post.php?action=edit&amp;post=<?php echo $post_ID; ?>" onclick="window.opener.location.replace(this.href); window.close();"><?php _e('Edit post'); ?></a> | <a href="#" onclick="window.close();">Close Window</a></p></div>
		
		<div id="footer">
		<p><?php
		do_action('in_admin_footer', '');
		$upgrade = apply_filters( 'update_footer', '' );
		echo __('Thank you for creating with <a href="http://wordpress.org/">WordPress</a>');
		?></p>
		</div>
		<?php do_action('admin_footer', ''); ?>
		
	</body>
	</html>
	<?php die;
}


function aposfix($text) {
	$translation_table[chr(34)] = '&quot;';
	$translation_table[chr(38)] = '&';
	$translation_table[chr(39)] = '&apos;';
	return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/","&amp;" , strtr($text, $translation_table));	
}

// Ajax Requests
$title = wp_specialchars(stripslashes($_GET['t']));

$selection = str_replace("\n", "<br />", aposfix( stripslashes($_GET['s']) ) );
$url = clean_url($_GET['u']);
$image = $_GET['i'];

if($_REQUEST['ajax'] == 'thickbox') { ?>
	<h3 id="title"><label for="post_title"><?php _e('Description') ?></label></h3>
	<div class="titlewrap">
		<input id="this_photo_description" name="photo_description" class="text" onkeypress="if(event.keyCode==13) image_selector();" value="<?php echo attribute_escape($title);?>"/>
	</div>
		
	<p><input type="hidden" name="this_photo" value="<?php echo $image; ?>" id="this_photo" />
		<a href="#" class="select"><img src="<?php echo $image; ?>" width="475" alt="Click to insert." title="Click to insert." /></a></p>
	
	<p id="options"><a href="#" class="select">Insert Image</a> | <a href="#" class="cancel">Cancel</a></p>
<?php die; 
}

if($_REQUEST['ajax'] == 'thickbox_url') { ?>
	<h3 id="title"><label for="post_title"><?php _e('URL') ?></label></h3>
	<div class="titlewrap">
		<input id="this_photo" name="this_photo" class="text" onkeypress="if(event.keyCode==13) image_selector();" />
	</div>
	
	
	<h3 id="title"><label for="post_title"><?php _e('Description') ?></label></h3>
	<div class="titlewrap">
		<input id="this_photo_description" name="photo_description" class="text" onkeypress="if(event.keyCode==13) image_selector();" value="<?php echo attribute_escape($title);?>"/>
	</div>
	
	<p id="options"><a href="#" class="select">Insert Image</a> | <a href="#" class="cancel">Cancel</a></p>
<?php die; 
}

if($_REQUEST['ajax'] == 'video') { ?>
	<h2 id="embededcode"><label for="embed_code"><?php _e('Embed Code') ?></label></h2>
	<div class="titlewrap" >
		<textarea name="embed_code" id="embed_code" rows="8" cols="40"><?php echo $selection; ?></textarea>
	</div>
<?php die;
}

if($_REQUEST['ajax'] == 'photo_images') {
	function get_images_from_uri($uri) {
		if(preg_match('/\.(jpg|png|gif)/', $uri) && !strpos($uri,'blogger.com')) 
			return "'".$uri."'";

		$content = wp_remote_fopen($uri);
		if ( false === $content ) return '';
		
		$host = parse_url($uri);
		
		$pattern = '/<img ([^>]*)src=(\"|\')(.+?)(\2)([^>\/]*)\/*>/is';
		preg_match_all($pattern, $content, $matches);
		
		if ( empty($matches[1]) ) return '';
		
		$sources = array();
		foreach ($matches[3] as $src) {
			if(strpos($src, 'http') === false)
				if(strpos($src, '../') === false && strpos($src, './') === false)
					$src = 'http://'.str_replace('//','/', $host['host'].'/'.$src);
				else
					$src = 'http://'.str_replace('//','/', $host['host'].'/'.$host['path'].'/'.$src);
										
			$sources[] = $src;
		}
		return "'" . implode("','", $sources) . "'";
	} 
	
	$url = urldecode($url);
	$url = str_replace(' ', '%20', $url);
	echo 'new Array('.get_images_from_uri($url).')'; 
die;		
}

if($_REQUEST['ajax'] == 'photo_js') { ?>

			tb_init('a.thickbox, area.thickbox, input.thickbox'); //pass where to apply thickbox
			
			function image_selector() {
				desc = jQuery('#this_photo_description').val();
				src = jQuery('#this_photo').val();
				pick(src, desc);
				tb_remove();
				return false;
			}
			
			jQuery(document).ready(function() {
				jQuery('#this_photo').focus();

				jQuery('.cancel').click(function() {
					tb_remove();
				});
				
				jQuery('.select').click(function() {
					image_selector();
				});
				
				jQuery('#photo_add_url').attr('href', '?ajax=thickbox_url&height=200&width=500');
				
			});
			
			
			function pick(img, desc) {
				if (img) { 
					length = jQuery('.photolist input').length;
					if(length == 0) length = 1;
					jQuery('.photolist').append('<input name="photo_src[' + length + ']" value="' + img +'" type="hidden"/>');
					jQuery('.photolist').append('<input name="photo_description[' + length + ']" value="' + desc +'" type="hidden"/>');
					append_editor('<img src="' + img +'" alt="' + desc + '" />'); }
					tinyMCE.activeEditor.resizeToContent();
				return false;
			}

			var last = null
			var my_src, img, img_tag, aspect, w, h, skip, i, strtoappend = "";
			var my_src = eval(
			jQuery.ajax({
			   	type: "GET",
			   	url: "<?php echo clean_url($_SERVER['PHP_SELF']); ?>",
				cache : false,
				async : false,
			   	data: "ajax=photo_images&u=<?php echo urlencode($url); ?>",
				dataType : "script"
				}).responseText);

			for (i = 0; i < my_src.length; i++) {
				img = new Image(); img.src = my_src[i]; img_attr = 'id="img' + i; skip = false;

				if (img.width && img.height) {
					if (img.width * img.height < 2500) 
						skip = true;
					aspect = img.width / img.height;
					scale = (aspect > 1) ? (75 / img.width) : (75 / img.height);
					
					if (scale < 1) {
						w = parseInt(img.width * scale);
						h = parseInt(img.height * scale);
					} else {
						w = img.width;
						h = img.height;
					}
					img_attr += ' style="width: ' + w + 'px; height: ' + h + 'px;"';
				}

				if (!skip) strtoappend += '<a href="?ajax=thickbox&amp;i=' + img.src + '&amp;u=<?php echo $url; ?>&amp;height=400&amp;width=500" title="" class="thickbox"><img src="' + img.src + '" ' + img_attr + '/></a>';

			}
			jQuery('#img_container').html(strtoappend);

<?php die; }

if($_REQUEST['ajax'] == 'photo') { ?>
		<div class="photolist"></div>

		<small id="photo_directions"><?php _e('Click images to select:') ?> <span><a href="#" id="photo_add_url" class="thickbox"><?php _e('Add from URL') ?> +</a></span></small>

		<div class="titlewrap">
			<div id="img_container"></div>
		</div>
<?php die; }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php _e('Press This') ?></title>

<?php
	add_thickbox();
	wp_enqueue_style('press-this');
	wp_enqueue_style( 'colors' );
	wp_enqueue_script( 'post' );

	wp_teeny_mce();

	do_action('admin_print_styles');
	do_action('admin_print_scripts');
	do_action('admin_head');
?>
<script type="text/javascript">
	jQuery('#tags-input').hide();

	tag_update_quickclicks();

	// add the quickadd form
	jQuery('#jaxtag').prepend('<span id="ajaxtag"><input type="text" name="newtag" id="newtag" class="form-input-tip" size="16" autocomplete="off" value="'+postL10n.addTag+'" /><input type="submit" class="button" id="tagadd" value="' + postL10n.add + '" tabindex="3" onclick="return false;" /><input type="hidden"/><input type="hidden"/><span class="howto">'+postL10n.separate+'</span></span>');
		
	jQuery('#tagadd').click( tag_flush_to_text );
	jQuery('#newtag').focus(function() {
		if ( this.value == postL10n.addTag )
			jQuery(this).val( '' ).removeClass( 'form-input-tip' );
	});
	jQuery('#newtag').blur(function() {
		if ( this.value == '' ) 
			jQuery(this).val( postL10n.addTag ).addClass( 'form-input-tip' );
	});

	// auto-save tags on post save/publish
	jQuery('#publish').click( tag_save_on_publish );
	jQuery('#save-post').click( tag_save_on_publish );
		
	function set_menu(type) {
		jQuery('#text_button').removeClass('ui-tabs-selected');
		jQuery('#menu li').removeClass('ui-tabs-selected');
		jQuery('#' + type + '_button').addClass('ui-tabs-selected');
		jQuery("#post_type").val(type);
	}
	function set_editor(text) {

		if(tinyMCE.activeEditor) tinyMCE.activeEditor.setContent('');
		if(tinyMCE.activeEditor) tinyMCE.execCommand('mceInsertContent' ,false, text);
	}
	function append_editor(text) {
		if(tinyMCE.activeEditor) tinyMCE.execCommand('mceInsertContent' ,false, text);
	}
	function set_title(title) { jQuery("#content_type").text(title); }

	function show(tab_name) {
		jQuery('body').removeClass('video_split');
		jQuery('#extra_fields').hide();
		switch(tab_name) {
			case 'text' :
				set_menu('text');
				set_title('<?php _e('Post') ?>');

				return false;
			break;
			case 'quote' :
				set_menu('quote');
				set_title('<?php _e('Quote') ?>');
				set_editor("<blockquote><p><?php echo $selection; ?> </p><p><cite><a href='<?php echo $url; ?>'><?php echo $title; ?> </a> </cite> </p></blockquote>");
				return false;
			break;
			case 'video' :

				set_menu('video');
				set_title('<?php _e('Caption') ?>');
				
				jQuery('#extra_fields').show();
				jQuery('body').addClass('video_split');
				jQuery('#extra_fields').load('<?php echo clean_url($_SERVER['PHP_SELF']); ?>', { ajax: 'video', s: '<?php echo attribute_escape($selection); ?>'}, function() {
					
					<?php 
					if ( preg_match("/youtube\.com\/watch/i", $url) ) {
					list($domain, $video_id) = split("v=", $url);
					$content = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/' . $video_id . '"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/' . $video_id . '" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>'; ?>
					
					<?php } elseif ( preg_match("/vimeo\.com\/[0-9]+/i", $url) ) {
					
					list($domain, $video_id) = split(".com/", $url);
					$content = '<object width="400" height="225"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://www.vimeo.com/moogaloop.swf?clip_id=' . $video_id . '&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" />	<embed src="http://www.vimeo.com/moogaloop.swf?clip_id=' . $video_id . '&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="400" height="225"></embed></object>';
					
					if(trim($selection) == '') $selection = '<a href="http://www.vimeo.com/' . $video_id . '?pg=embed&sec=' . $video_id . '">' . $title . '</a> on <a href="http://vimeo.com?pg=embed&sec=' . $video_id . '">Vimeo</a>';
					}else {
						$content = $selection;
					} ?>
					jQuery('#embed_code').prepend('<?php echo htmlentities($content); ?>');
					
					set_editor("<?php echo $title; ?>");
				});
				
				return false;
			break;
			
			case 'photo' :
				set_menu('photo');
				set_title('Post');
				<?php if($selection) { ?>
					set_editor("<?php echo $selection; ?>");
				<?php } ?>
				jQuery('#extra_fields').show();
				jQuery('#extra_fields').before('<h2 id="waiting"><img src="images/loading.gif" alt="" /> Loading...</h2>');
				jQuery('#extra_fields').load('<?php echo clean_url($_SERVER['PHP_SELF']).'/?ajax=photo&u='.attribute_escape($url); ?>');
				jQuery.ajax({
					type: "GET",
					cache : false,
					url: "<?php echo clean_url($_SERVER['PHP_SELF']); ?>",
					data: "ajax=photo_js&u=<?php echo urlencode($url)?>",
					dataType : "script",
					success : function() {
						jQuery('#waiting').remove();
					}
				});
				
				return false;
			break;

		}
	
	}
	
	jQuery(document).ready(function() {
    	jQuery('#menu li').click(function (){ 
			tab_name = this.id.split('_');
			tab_name = tab_name[0];
			show(tab_name);
		});
		
		<?php if ( preg_match("/youtube\.com\/watch/i", $url) ) { ?>
			show('video');
		<?php } elseif ( preg_match("/vimeo\.com\/[0-9]+/i", $url) ) { ?>
			show('video');
			<?php  } elseif ( preg_match("/flickr\.com/i", $url) ) { ?>
			show('photo');
		<?php } ?>
	});
</script>
</head>
<body class="press-this">
<div id="wphead">
<h1><span id="viewsite"><a href="<?php echo get_option('home'); ?>/"><?php _e('Visit:') ?> <?php bloginfo('name'); ?></a></span></h1>
</div>

		<ul id="menu" class="ui-tabs-nav">
			<li id="text_button" class="ui-tabs-selected"><a href="#"><?php _e('Text') ?></a></li>
		 	<li id="photo_button"><a href="#"><?php _e('Photo') ?></a></li>
			<li id="quote_button"><a href="#"><?php _e('Quote') ?></a></li>
			<li id="video_button"><a href="#"><?php _e('Video') ?></a></li>
		</ul>

			<form action="press-this.php?action=post" method="post">

				<?php wp_nonce_field('press-this') ?>
				<input type="hidden" name="post_type" id="post_type" value="text"/>
				<div id="posting">
					
					<h2 id="title"><label for="post_title"><?php _e('Title') ?></label></h2>
					<div class="titlewrap">
						<input name="post_title" id="post_title" class="text" value="<?php echo attribute_escape($title);?>"/>
					</div>
					
					<div id="extra_fields" style="display: none"></div>
					<div class="editor_area">
					<h2 id="content_type"><label for="content"><?php _e('Post') ?></label></h2>
					<div class="editor-container">
						<textarea name="content" id="content" style="width:100%;" class="mceEditor"><?php if($selection) { ?><a href='<?php echo $url ?>'><?php echo $selection ?></a><?php } else { ?><a href='<?php echo $url ?>'><?php echo $title; ?></a><?php } ?></textarea>
					</div>
					</div>
					
				</div>
				<div id="categories">
					<div class="submitbox" id="submitpost">
					<div id="previewview"></div>
					<div class="inside">
						<h2><?php _e('Categories') ?></h2>
						<div id="categories-all">
							<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
								<?php wp_category_checklist() ?>
							</ul>
						</div>
						<h2><?php _e('Tags') ?></h2>
						<p id="jaxtag"><label class="hidden" for="newtag"><?php _e('Tags'); ?></label><input type="text" name="tags_input" class="tags-input" id="tags-input" size="40" tabindex="3" value="<?php echo get_tags_to_edit( $post->ID ); ?>" /></p>
						<div id="tagchecklist"></div>
					</div>
					<label for="post_status" id="post_status"><input type="radio" name="post_status" value="publish" checked="checked" id="published" />Published <input type="radio" name="post_status" value="draft" id="unpubplished" /> Unpublished</label>
					
					<p class="submit">         
						<input type="submit" value="<?php _e('Publish') ?>" onclick="document.getElementById('photo_saving').style.display = '';"/>
						<img src="images/loading-publish.gif" alt="" id="photo_saving" style="display:none;"/>
					</p>
				</div>
				
				
			</form>		
					
</body>
</html>