<?php
require_once('admin.php');

$wpvarstoreset = array('action', 'safe_mode', 'withcomments', 'posts', 'content', 'edited_post_title', 'comment_error', 'profile', 'trackback_url', 'excerpt', 'showcomments', 'commentstart', 'commentend', 'commentorder' );

for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST["$wpvar"])) {
			if (empty($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
			$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}

if (isset($_POST['deletepost'])) {
$action = "delete";
}

// Fix submenu highlighting for pages.
if ( isset($_REQUEST['post']) && 'static' == get_post_status($_REQUEST['post']) )
	$submenu_file = 'page-new.php';

$editing = true;

switch($action) {
case 'post':
	check_admin_referer('add-post');
	
	$post_ID = write_post();

	// Redirect.
	if (!empty($_POST['mode'])) {
	switch($_POST['mode']) {
		case 'bookmarklet':
			$location = $_POST['referredby'];
			break;
		case 'sidebar':
			$location = 'sidebar.php?a=b';
			break;
		default:
			$location = 'post.php';
			break;
		}
	} else {
		$location = "post.php?posted=$post_ID";
	}

	if ( 'static' == $_POST['post_status'] )
		$location = "page-new.php?saved=$post_ID";

	if ( isset($_POST['save']) )
		$location = "post.php?action=edit&post=$post_ID";

	wp_redirect($location);
	exit();
	break;

case 'edit':
	$title = __('Edit');

	require_once('admin-header.php');

	$post_ID = $p = (int) $_GET['post'];

	if ( !current_user_can('edit_post', $post_ID) )
		die ( __('You are not allowed to edit this post.') );

	$post = get_post_to_edit($post_ID);
	
	if ($post->post_status == 'static')
		include('edit-page-form.php');
	else
		include('edit-form-advanced.php');

	?>
	<div id='preview' class='wrap'>
	<h2 id="preview-post"><?php _e('Post Preview (updated when post is saved)'); ?> <small class="quickjump"><a href="#write-post"><?php _e('edit &uarr;'); ?></a></small></h2>
		<iframe src="<?php echo clean_url(apply_filters('preview_post_link', add_query_arg('preview', 'true', get_permalink($post->ID)))); ?>" width="100%" height="600" ></iframe>
	</div>
	<?php
	break;

case 'editattachment':
	$post_id = (int) $_POST['post_ID'];

	check_admin_referer('update-attachment_' . $post_id);

	// Don't let these be changed
	unset($_POST['guid']);
	$_POST['post_status'] = 'attachment';

	// Update the thumbnail filename
	$oldmeta = $newmeta = get_post_meta($post_id, '_wp_attachment_metadata', true);
	$newmeta['thumb'] = $_POST['thumb'];

	if ( '' !== $oldmeta )
		update_post_meta($post_id, '_wp_attachment_metadata', $newmeta, $oldmeta);
	else
		add_post_meta($post_id, '_wp_attachment_metadata', $newmeta);

case 'editpost':
	$post_ID = (int) $_POST['post_ID'];
	check_admin_referer('update-post_' . $post_ID);
	
	$post_ID = edit_post();

	$referredby = '';
	if ( !empty($_POST['referredby']) )
		$referredby = preg_replace('|https?://[^/]+|i', '', $_POST['referredby']);
	$referer = preg_replace('|https?://[^/]+|i', '', wp_get_referer());
	
	if ($_POST['save']) {
		$location = wp_get_referer();
	} elseif ($_POST['updatemeta']) {
		$location = wp_get_referer() . '&message=2#postcustom';
	} elseif ($_POST['deletemeta']) {
		$location = wp_get_referer() . '&message=3#postcustom';
	} elseif (!empty($referredby) && $referredby != $referer) {
		$location = $_POST['referredby'];
		if ( $_POST['referredby'] == 'redo' )
			$location = get_permalink( $post_ID );
	} elseif ($action == 'editattachment') {
		$location = 'attachments.php';
	} else {
		$location = 'post.php';
	}

	wp_redirect($location); // Send user on their way while we keep working

	exit();
	break;

case 'delete':
	$post_id = (isset($_GET['post']))  ? intval($_GET['post']) : intval($_POST['post_ID']);

	$post = & get_post($post_id);
	if ( 'static' == $post->post_status )
		check_admin_referer('delete-page_' . $post_id);
	else
		check_admin_referer('delete-post_' . $post_id);

	if ( !current_user_can('edit_post', $post_id) )	
		die( __('You are not allowed to delete this post.') );

	if ( $post->post_status == 'attachment' ) {
		if ( ! wp_delete_attachment($post_id) )
			die( __('Error in deleting...') );
	} else {
		if ( !wp_delete_post($post_id) ) 
			die( __('Error in deleting...') );
	}

	$sendback = wp_get_referer();
	if ( 'static' == $post->post_status )
		$sendback = get_option('siteurl') . '/wp-admin/edit-pages.php';
	elseif ( strstr($sendback, 'post.php') )
		$sendback = get_option('siteurl') .'/wp-admin/post.php';
	elseif ( strstr($sendback, 'attachments.php') )
		$sendback = get_option('siteurl') .'/wp-admin/attachments.php';
	wp_redirect($sendback);
	break;

case 'editcomment':
	$title = __('Edit Comment');
	$parent_file = 'edit.php';
	require_once ('admin-header.php');

	get_currentuserinfo();

	$comment = (int) $_GET['comment'];

	if ( ! $comment = get_comment($comment) )
		die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'javascript:history.go(-1)'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )	
		die( __('You are not allowed to edit comments on this post.') );

	$comment = get_comment_to_edit($comment);

	include('edit-form-comment.php');

	break;

case 'confirmdeletecomment':

	require_once('./admin-header.php');

	$comment = (int) $_GET['comment'];
	$p = (int) $_GET['p'];

	if ( ! $comment = get_comment_to_edit($comment) )
		die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )	
		die( __('You are not allowed to delete comments on this post.') );

	echo "<div class='wrap'>\n";
	echo "<p>" . __('<strong>Caution:</strong> You are about to delete the following comment:') . "</p>\n";
	echo "<table border='0'>\n";
	echo "<tr><td>" . __('Author:') . "</td><td>$comment->comment_author</td></tr>\n";
	echo "<tr><td>" . __('E-mail:') . "</td><td>$comment->comment_author_email</td></tr>\n";
	echo "<tr><td>". __('URL:') . "</td><td>$comment->comment_author_url</td></tr>\n";
	echo "<tr><td>". __('Comment:') . "</td><td>$comment->comment_content</td></tr>\n";
	echo "</table>\n";
	echo "<p>" . __('Are you sure you want to do that?') . "</p>\n";

	echo "<form action='".get_settings('siteurl')."/wp-admin/post.php' method='get'>\n";
	echo "<input type='hidden' name='action' value='deletecomment' />\n";
	echo "<input type='hidden' name='p' value='$p' />\n";
	echo "<input type='hidden' name='comment' value='{$comment->comment_ID}' />\n";
	echo "<input type='hidden' name='noredir' value='1' />\n";
	wp_nonce_field('delete-comment_' .  $comment->comment_ID);
	echo "<input type='submit' value='" . __('Yes') . "' />";
	echo "&nbsp;&nbsp;";
	echo "<input type='button' value='" . __('No') . "' onclick=\"self.location='". get_settings('siteurl') ."/wp-admin/edit.php?p=$p&amp;c=1#comments';\" />\n";
	echo "</form>\n";
	echo "</div>\n";

	break;

case 'deletecomment':
	$comment = (int) $_GET['comment'];
	check_admin_referer('delete-comment_' . $comment);

	$p = (int) $_GET['p'];
	if (isset($_GET['noredir'])) {
		$noredir = true;
	} else {
		$noredir = false;
	}

	$postdata = get_post($p) or die(sprintf(__('Oops, no post with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if ( ! $comment = get_comment($comment) )
			 die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'post.php'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )	
		die( __('You are not allowed to edit comments on this post.') );

	wp_set_comment_status($comment->comment_ID, "delete");
	do_action('delete_comment', $comment->comment_ID);

	if ((wp_get_referer() != "") && (false == $noredir)) {
		wp_redirect(wp_get_referer());
	} else {
		wp_redirect(get_settings('siteurl') .'/wp-admin/edit.php?p='.$p.'&c=1#comments');
	}

	break;

case 'unapprovecomment':
	$comment = (int) $_GET['comment'];
	check_admin_referer('unapprove-comment_' . $comment);

	$p = (int) $_GET['p'];
	if (isset($_GET['noredir'])) {
		$noredir = true;
	} else {
		$noredir = false;
	}

	if ( ! $comment = get_comment($comment) )
		die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )	
		die( __('You are not allowed to edit comments on this post, so you cannot disapprove this comment.') );

	wp_set_comment_status($comment->comment_ID, "hold");

	if ((wp_get_referer() != "") && (false == $noredir)) {
		wp_redirect(wp_get_referer());
	} else {
		wp_redirect(get_settings('siteurl') .'/wp-admin/edit.php?p='.$p.'&c=1#comments');
	}

	break;

case 'mailapprovecomment':
	$comment = (int) $_GET['comment'];
	check_admin_referer('approve-comment_' . $comment);

	if ( ! $comment = get_comment($comment) )
			 die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )	
		die( __('You are not allowed to edit comments on this post, so you cannot approve this comment.') );

	if ('1' != $comment->comment_approved) {
		wp_set_comment_status($comment->comment_ID, 'approve');
		if (true == get_option('comments_notify'))
			wp_notify_postauthor($comment->comment_ID);
	}

	wp_redirect(get_option('siteurl') . '/wp-admin/moderation.php?approved=1');

	break;

case 'approvecomment':
	$comment = (int) $_GET['comment'];
	check_admin_referer('approve-comment_' . $comment);

	$p = (int) $_GET['p'];
	if (isset($_GET['noredir'])) {
		$noredir = true;
	} else {
		$noredir = false;
	}

	if ( ! $comment = get_comment($comment) )
		die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )	
		die( __('You are not allowed to edit comments on this post, so you cannot approve this comment.') );

	wp_set_comment_status($comment->comment_ID, "approve");
	if (get_settings("comments_notify") == true) {
		wp_notify_postauthor($comment->comment_ID);
	}


	if ((wp_get_referer() != "") && (false == $noredir)) {
		wp_redirect(wp_get_referer());
	} else {
		wp_redirect(get_settings('siteurl') .'/wp-admin/edit.php?p='.$p.'&c=1#comments');
	}

	break;

case 'editedcomment':

	$comment_ID = (int) $_POST['comment_ID'];
	$comment_post_ID = (int)  $_POST['comment_post_ID'];

	check_admin_referer('update-comment_' . $comment_ID);

	edit_comment();

	$location = ( empty($_POST['referredby']) ? "edit.php?p=$comment_post_ID&c=1" : $_POST['referredby'] ) . '#comment-' . $comment_ID;
	$location = apply_filters('comment_edit_redirect', $location, $comment_ID);
	wp_redirect($location);
	exit();
	break;

default:
	$title = __('Create New Post');
	require_once ('./admin-header.php');
?>
<?php if ( isset($_GET['posted']) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Post saved.'); ?></strong> <a href="<?php echo get_permalink( $_GET['posted'] ); ?>"><?php _e('View post'); ?> &raquo;</a></p></div>
<?php endif; ?>
<?php
	if ( current_user_can('edit_posts') ) {
		$action = 'post';
		get_currentuserinfo();
		if ( $drafts = get_users_drafts( $user_ID ) ) {
			?>
			<div class="wrap">
			<p><strong><?php _e('Your Drafts:') ?></strong>
			<?php
			$num_drafts = count($drafts);
			if ( $num_drafts > 15 ) $num_drafts = 15;
			for ( $i = 0; $i < $num_drafts; $i++ ) {
				$draft = $drafts[$i];
				if ( 0 != $i )
					echo ', ';
				$draft->post_title = stripslashes($draft->post_title);
				if ( empty($draft->post_title) )
					$draft->post_title = sprintf(__('Post # %s'), $draft->ID);
				echo "<a href='post.php?action=edit&amp;post=$draft->ID' title='" . __('Edit this draft') . "'>$draft->post_title</a>";
			}
			?>
			<?php if ( 15 < count($drafts) ) { ?>
			, <a href="edit.php"><?php echo sprintf(__('and %s more'), (count($drafts) - 15) ); ?> &raquo;</a>
			<?php } ?>
			.</p>
			</div>
			<?php
		}

		$post = get_default_post_to_edit();

		include('edit-form-advanced.php');
?>
<div id="wp-bookmarklet" class="wrap">
<?php echo '<h3>'.__('WordPress bookmarklet').'</h3>
<p>'.__('Right click on the following link and choose "Add to favorites" to create a posting shortcut.').'</p>'; ?>
<p>

<?php
if ($is_NS4 || $is_gecko) {
?>
<a href="javascript:if(navigator.userAgent.indexOf('Safari') >= 0){Q=getSelection();}else{Q=document.selection?document.selection.createRange().text:document.getSelection();}location.href='<?php echo get_settings('siteurl') ?>/wp-admin/post.php?text='+encodeURIComponent(Q)+'&amp;popupurl='+encodeURIComponent(location.href)+'&amp;popuptitle='+encodeURIComponent(document.title);"><?php printf(__('Press It - %s'), wp_specialchars(get_settings('blogname'))); ?></a> 
<?php
} else if ($is_winIE) {
?>
<a href="javascript:Q='';if(top.frames.length==0)Q=document.selection.createRange().text;location.href='<?php echo get_settings('siteurl') ?>/wp-admin/post.php?text='+encodeURIComponent(Q)+'&amp;popupurl='+encodeURIComponent(location.href)+'&amp;popuptitle='+encodeURIComponent(document.title);"><?php printf(__('Press it - %s'), get_settings('blogname')); ?></a>
<script type="text/javascript">
<!--
function oneclickbookmarklet(blah) {
window.open ("profile.php?action=IErightclick", "oneclickbookmarklet", "width=500, height=450, location=0, menubar=0, resizable=0, scrollbars=1, status=1, titlebar=0, toolbar=0, screenX=120, left=120, screenY=120, top=120");
}
// -->
</script>
<br />
<br />
<?php _e('One-click bookmarklet:') ?><br />
<a href="javascript:oneclickbookmarklet(0);"><?php _e('click here') ?></a> 
<?php
} else if ($is_opera) {
?>
<a href="javascript:location.href='<?php echo get_settings('siteurl'); ?>/wp-admin/post.php?popupurl='+escape(location.href)+'&popuptitle='+escape(document.title);"><?php printf(__('Press it - %s'), get_settings('blogname')); ?></a> 
<?php
} else if ($is_macIE) {
?>
<a href="javascript:Q='';location.href='<?php echo get_settings('siteurl'); ?>/wp-admin/bookmarklet.php?text='+escape(document.getSelection())+'&popupurl='+escape(location.href)+'&popuptitle='+escape(document.title);"><?php printf(__('Press it - %s'), get_settings('blogname')); ?></a> 
<?php
}
?>
</p>
</div>
<?php
} else {
?>
<div class="wrap">
<p><?php printf(__('Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 1, in order to be authorized to post.<br />
You can also <a href="mailto:%s?subject=Promotion?">e-mail the admin</a> to ask for a promotion.<br />
When you&#8217;re promoted, just reload this page and you&#8217;ll be able to blog. :)'), get_settings('admin_email')); ?>
</p>
</div>
<?php
}

	break;
} // end switch
/* </Edit> */
include('admin-footer.php');
?>
