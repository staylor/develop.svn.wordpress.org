<?php
require_once('admin.php');

if ( !is_multisite() )
	wp_die( __('Multisite support is not enabled.') );

$title = __('Sites');
$parent_file = 'ms-admin.php';

wp_enqueue_script( 'admin-forms' );

require_once('admin-header.php');

if ( ! current_user_can( 'manage_sites' ) )
	wp_die( __('You do not have permission to access this page.') );

$id = isset($_GET['id']) ? intval( $_GET['id'] ) : 0;
$protocol = is_ssl() ? 'https://' : 'http://';

if ( isset($_GET['updated']) && $_GET['updated'] == 'true' ) {
	?>
	<div id="message" class="updated fade"><p>
		<?php
		switch ($_GET['action']) {
			case 'all_notspam':
				_e('Blogs mark as not spam !');
			break;
			case 'all_spam':
				_e('Blogs mark as spam !');
			break;
			case 'all_delete':
				_e('Blogs deleted !');
			break;
			case 'delete':
				_e('Blog deleted !');
			break;
			case 'add-blog':
				_e('Blog added !');
			break;
			case 'archive':
				_e('Blog archived !');
			break;
			case 'unarchive':
				_e('Blog unarchived !');
			break;
			case 'activate':
				_e('Blog activated !');
			break;
			case 'deactivate':
				_e('Blog deactivated !');
			break;
			case 'unspam':
				_e('Blog mark as not spam !');
			break;
			case 'spam':
				_e('Blog mark as spam !');
			break;
			case 'umature':
				_e('Blog mark as not mature !');
			break;
			case 'mature':
				_e('Blog mark as mature !');
			break;
			default:
				_e('Options saved !');
			break;
		}
		?>
	</p></div>
	<?php
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

switch ( $action ) {
	// Edit blog
	case "editblog":
		$blog_prefix = $wpdb->get_blog_prefix( $id );
		$options = $wpdb->get_results( "SELECT * FROM {$blog_prefix}options WHERE option_name NOT LIKE '\_%' AND option_name NOT LIKE '%user_roles'" );
		$details = get_blog_details($id);
		$editblog_roles = get_blog_option( $id, "{$blog_prefix}user_roles" );
		$is_main_site = is_main_site( $id );
		?>
		<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php _e('Edit Site'); ?> - <a href='<?php echo get_home_url($id); ?>'><?php echo get_home_url($id); ?></a></h2>
		<form method="post" action="ms-edit.php?action=updateblog">
			<?php wp_nonce_field('editblog'); ?>
			<input type="hidden" name="id" value="<?php echo esc_attr($id) ?>" />
			<div class='metabox-holder' style='width:49%;float:left;'>
			<div id="blogedit_bloginfo" class="postbox " >
			<h3 class='hndle'><span><?php _e('Blog info (wp_blogs)'); ?></span></h3>
			<div class="inside">
				<table class="form-table">
							<tr class="form-field form-required">
								<th scope="row"><?php _e('Domain') ?></th>
<?php if ( $is_main_site ) { ?>
								<td>http://<?php echo esc_attr($details->domain) ?></td>
<?php } else { ?>
								<td>http://<input name="blog[domain]" type="text" id="domain" value="<?php echo esc_attr($details->domain) ?>" size="33" /></td>
<?php } ?>
							</tr>
							<tr class="form-field form-required">
								<th scope="row"><?php _e('Path') ?></th>
<?php if ( $is_main_site ) { ?>
								<td><?php echo esc_attr($details->path) ?></td>
<?php } else { ?>
								<td><input name="blog[path]" type="text" id="path" value="<?php echo esc_attr($details->path) ?>" size="40" style='margin-bottom:5px;' />
								<br /><input type='checkbox' style='width:20px;' name='update_home_url' value='update' <?php if ( get_blog_option( $id, 'siteurl' ) == untrailingslashit( get_blogaddress_by_id($id) ) || get_blog_option( $id, 'home' ) == untrailingslashit( get_blogaddress_by_id($id) ) ) echo 'checked="checked"'; ?> /> <?php _e( "Update 'siteurl' and 'home' as well." ); ?></td>
<?php } ?>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e('Registered') ?></th>
								<td><input name="blog[registered]" type="text" id="blog_registered" value="<?php echo esc_attr($details->registered) ?>" size="40" /></td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e('Last Updated') ?></th>
								<td><input name="blog[last_updated]" type="text" id="blog_last_updated" value="<?php echo esc_attr($details->last_updated) ?>" size="40" /></td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e('Public') ?></th>
								<td>
									<input type='radio' style='width:20px;' name='blog[public]' value='1' <?php if ( $details->public == '1' ) echo 'checked="checked"'; ?> /> <?php _e('Yes') ?>
									<input type='radio' style='width:20px;' name='blog[public]' value='0' <?php if ( $details->public == '0' ) echo 'checked="checked"'; ?> /> <?php _e('No') ?>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e( 'Archived' ); ?></th>
								<td>
									<input type='radio' style='width:20px;' name='blog[archived]' value='1' <?php if ( $details->archived == '1' ) echo 'checked="checked"'; ?> /> <?php _e('Yes') ?>
									<input type='radio' style='width:20px;' name='blog[archived]' value='0' <?php if ( $details->archived == '0' ) echo 'checked="checked"'; ?> /> <?php _e('No') ?>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e( 'Mature' ); ?></th>
								<td>
									<input type='radio' style='width:20px;' name='blog[mature]' value='1' <?php if ( $details->mature == '1' ) echo 'checked="checked"'; ?> /> <?php _e('Yes') ?>
									<input type='radio' style='width:20px;' name='blog[mature]' value='0' <?php if ( $details->mature == '0' ) echo 'checked="checked"'; ?> /> <?php _e('No') ?>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e( 'Spam' ); ?></th>
								<td>
									<input type='radio' style='width:20px;' name='blog[spam]' value='1' <?php if ( $details->spam == '1' ) echo 'checked="checked"'; ?> /> <?php _e('Yes') ?>
									<input type='radio' style='width:20px;' name='blog[spam]' value='0' <?php if ( $details->spam == '0' ) echo 'checked="checked"'; ?> /> <?php _e('No') ?>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e( 'Deleted' ); ?></th>
								<td>
									<input type='radio' style='width:20px;' name='blog[deleted]' value='1' <?php if ( $details->deleted == '1' ) echo 'checked="checked"'; ?> /> <?php _e('Yes') ?>
									<input type='radio' style='width:20px;' name='blog[deleted]' value='0' <?php if ( $details->deleted == '0' ) echo 'checked="checked"'; ?> /> <?php _e('No') ?>
								</td>
							</tr>
						</table>
						<p class="submit" style="margin:-15px 0 -5px 230px;"><input type="submit" name="Submit" value="<?php esc_attr_e('Update Options') ?>" /></p>
			</div></div>

			<div id="blogedit_blogoptions" class="postbox " >
			<h3 class='hndle'><span><?php printf( __('Blog options (%soptions)'), $blog_prefix ); ?></span></h3>
			<div class="inside">
				<table class="form-table">
							<?php
							$editblog_default_role = 'subscriber';
							foreach ( $options as $option ) {
								if ( $option->option_name == 'default_role' )
									$editblog_default_role = $option->option_value;
								$disabled = '';
								if ( is_serialized($option->option_value) ) {
									if ( is_serialized_string($option->option_value) ) {
										$option->option_value = esc_html(maybe_unserialize($option->option_value), 'single');
									} else {
										$option->option_value = "SERIALIZED DATA";
										$disabled = ' disabled="disabled"';
									}
								}
								if ( stristr($option->option_value, "\r") || stristr($option->option_value, "\n") || stristr($option->option_value, "\r\n") ) {
								?>
									<tr class="form-field">
										<th scope="row"><?php echo ucwords( str_replace( "_", " ", $option->option_name ) ) ?></th>
										<td><textarea rows="5" cols="40" name="option[<?php echo esc_attr($option->option_name) ?>]" type="text" id="<?php echo esc_attr($option->option_name) ?>"<?php echo $disabled ?>><?php echo esc_html( $option->option_value ) ?></textarea></td>
									</tr>
								<?php
								} else {
								?>
									<tr class="form-field">
										<th scope="row"><?php echo ucwords( str_replace( "_", " ", $option->option_name ) ) ?></th>
<?php if ( $is_main_site && in_array( $option->option_name, array( 'siteurl', 'home' ) ) ) { ?>
										<td><?php echo esc_attr( $option->option_value ) ?></td>
<?php } else { ?>
										<td><input name="option[<?php echo esc_attr($option->option_name) ?>]" type="text" id="<?php echo esc_attr($option->option_name) ?>" value="<?php echo esc_attr( $option->option_value ) ?>" size="40" <?php echo $disabled ?> /></td>
<?php } ?>
									</tr>
								<?php
								}
							} // End foreach
							?>
						</table>
						<p class="submit" style="margin:-15px 0 -5px 230px;"><input type="submit" name="Submit" value="<?php esc_attr_e('Update Options') ?>" /></p>
			</div></div>
			</div>

			<div class='metabox-holder' style='width:49%;float:right;'>
			<?php
					// Blog Themes
					$themes = get_themes();
					$blog_allowed_themes = wpmu_get_blog_allowedthemes( $id );
					$allowed_themes = get_site_option( "allowedthemes" );
					if ( ! $allowed_themes )
						$allowed_themes = array_keys( $themes );
					$out = '';
					foreach ( $themes as $key => $theme ) {
						$theme_key = esc_html( $theme['Stylesheet'] );
						if ( ! isset($allowed_themes[$theme_key] ) ) {
							$checked = ( isset($blog_allowed_themes[ $theme_key ]) ) ? 'checked="checked"' : '';
							$out .= '<tr class="form-field form-required">
									<th title="' . esc_attr( $theme["Description"] ).'" scope="row">' . esc_html($key) . '</th>
									<td><input name="theme[' . esc_attr($theme_key) . ']" type="checkbox" style="width:20px;" value="on" '.$checked.'/>' . __( 'Active' ) . '</td>
								</tr>';
						}
					}

					if ( $out != '' ) {
			?>
			<div id="blogedit_blogthemes" class="postbox">
			<h3 class='hndle'><span><?php esc_html_e('Blog Themes'); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr><th style="font-weight:bold;"><?php esc_html_e('Theme'); ?></th></tr>
					<?php echo $out; ?>
				</table>
				<p class="submit" style="margin:-15px 0 -5px 230px;"><input type="submit" name="Submit" value="<?php esc_attr_e('Update Options') ?>" /></p>
			</div></div>
			<?php } ?>

			<?php
					// Blog users
					$blogusers = get_users_of_blog( $id );
					if ( is_array( $blogusers ) ) {
						echo '<div id="blogedit_blogusers" class="postbox"><h3 class="hndle"><span>' . __('Blog Users') . '</span></h3><div class="inside">';
						echo '<table class="form-table">';
						echo "<tr><th>" . __('User') . "</th><th>" . __('Role') . "</th><th>" . __('Password') . "</th><th>" . __('Remove') . "</th></tr>";
						reset($blogusers);
						foreach ( (array) $blogusers as $key => $val ) {
							$t = @unserialize( $val->meta_value );
							if ( is_array( $t ) ) {
								reset( $t );
								$existing_role = key( $t );
							}
							echo '<tr><td><a href="user-edit.php?user_id=' . $val->user_id . '">' . $val->user_login . '</a></td>';
							if ( $val->user_id != $current_user->data->ID ) {
								?>
								<td>
									<select name="role[<?php echo $val->user_id ?>]" id="new_role"><?php
										foreach ( $editblog_roles as $role => $role_assoc ){
											$name = translate_user_role($role_assoc['name']);
											$selected = ( $role == $existing_role ) ? 'selected="selected"' : '';
											echo "<option {$selected} value=\"" . esc_attr($role) . "\">{$name}</option>";
										}
										?>
									</select>
								</td>
								<td>
										<input type='text' name='user_password[<?php echo esc_attr($val->user_id) ?>]' />
								</td>
								<?php
								echo '<td><input title="' . __('Click to remove user') . '" type="checkbox" name="blogusers[' . esc_attr($val->user_id) . ']" /></td>';
							} else {
								echo "<td><strong>" . __ ('N/A') . "</strong></td><td><strong>" . __ ('N/A') . "</strong></td><td><strong>" . __('N/A') . "</strong></td>";
							}
							echo '</tr>';
						}
						echo "</table>";
						echo '<p class="submit" style="margin:-15px 0 -5px 230px;"><input type="submit" name="Submit" value="' . esc_attr__('Update Options') . '" /></p>';
						echo "</div></div>";
					}
			?>

			<div id="blogedit_blogadduser" class="postbox">
			<h3 class='hndle'><span><?php _e('Add a new user'); ?></span></h3>
			<div class="inside">
				<p style="margin:10px 0 0px;padding:0px 10px 10px;border-bottom:1px solid #DFDFDF;"><?php _e('Enter the username of an existing user and hit <em>Update Options</em> to add the user.') ?></p>
				<table class="form-table">
						<tr>
							<th scope="row"><?php _e('User&nbsp;Login:') ?></th>
							<td><input type="text" name="newuser" id="newuser" /></td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Role:') ?></th>
							<td>
								<select name="new_role" id="new_role">
								<?php
								reset( $editblog_roles );
								foreach ( $editblog_roles as $role => $role_assoc ){
									$name = translate_with_context($role_assoc['name']);
									$selected = ( $role == $editblog_default_role ) ? 'selected="selected"' : '';
									echo "<option {$selected} value=\"" . esc_attr($role) . "\">{$name}</option>";
								}
								?>
								</select>
							</td>
						</tr>
					</table>
				<p class="submit" style="margin:-15px 0 -5px 230px;"><input type="submit" name="Submit" value="<?php esc_attr_e('Update Options') ?>" /></p>
			</div></div>

			<div id="blogedit_miscoptions" class="postbox">
			<h3 class='hndle'><span><?php _e('Misc Blog Actions') ?></span></h3>
			<div class="inside">
				<table class="form-table">
						<?php do_action( 'wpmueditblogaction', $id ); ?>
					</table>
				<p class="submit" style="margin:-15px 0 -5px 230px;"><input type="submit" name="Submit" value="<?php esc_attr_e('Update Options') ?>" /></p>
			</div></div>

			</div>

			<div style="clear:both;"></div>
		</form>
		</div>
		<?php
	break;

	// List blogs
	case 'list':
	default:
		$apage = ( isset($_GET['apage'] ) && intval( $_GET['apage'] ) ) ? absint( $_GET['apage'] ) : 1;
		$num = ( isset($_GET['num'] ) && intval( $_GET['num'] ) ) ? absint( $_GET['num'] ) : 15;
		$s = isset($_GET['s']) ? esc_attr( trim( $_GET[ 's' ] ) ) : '';
		$like_s = like_escape($s);

		$query = "SELECT * FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' ";

		if ( isset($_GET['blog_name']) ) {
			$query .= " AND ( {$wpdb->blogs}.domain LIKE '%{$like_s}%' OR {$wpdb->blogs}.path LIKE '%{$like_s}%' ) ";
		} elseif ( isset($_GET['blog_id']) ) {
			$query .= " AND   blog_id = '". absint( $_GET['blog_id'] )."' ";
		} elseif ( isset($_GET['blog_ip']) ) {
			$query = "SELECT *
				FROM {$wpdb->blogs}, {$wpdb->registration_log}
				WHERE site_id = '{$wpdb->siteid}'
				AND {$wpdb->blogs}.blog_id = {$wpdb->registration_log}.blog_id
				AND {$wpdb->registration_log}.IP LIKE ('%{$like_s}%')";
		}

		$order_by = isset( $_GET['sortby'] ) ? $_GET['sortby'] : 'id';

		if ( $order_by == 'registered' ) {
			$query .= ' ORDER BY registered ';
		} elseif ( $order_by == 'lastupdated' ) {
			$query .= ' ORDER BY last_updated ';
		} elseif ( $order_by == 'blogname' ) {
			$query .= ' ORDER BY domain ';
		} else {
			$order_by = 'id';
			$query .= ' ORDER BY ' . $wpdb->blogs . '.blog_id ';
		}

		$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
		$order = ( 'DESC' == $order ) ? 'DESC' : 'ASC';
		$query .= $order;

		if ( !empty($s) )
			$total = $wpdb->get_var( str_replace('SELECT *', 'SELECT COUNT(blog_id)', $query) );
		else
			$total = $wpdb->get_var( "SELECT COUNT(blog_id) FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' ");

		$query .= " LIMIT " . intval( ( $apage - 1 ) * $num) . ", " . intval( $num );
		$blog_list = $wpdb->get_results( $query, ARRAY_A );

		// Pagination
		$url2 = "&amp;order=" . $order . "&amp;sortby=" . $order_by . "&amp;s=";
		if ( !empty($_GET[ 'blog_ip' ])  )
			$url2 .= "&amp;ip_address=" . urlencode( $s );
		else
			$url2 .= $s . "&amp;ip_address=" . urlencode( $s );

		$blog_navigation = paginate_links( array(
			'base' => add_query_arg( 'apage', '%#%' ).$url2,
			'format' => '',
			'total' => ceil($total / $num),
			'current' => $apage
		));
		?>

		<div class="wrap" style="position:relative;">
		<?php screen_icon(); ?>
		<h2><?php _e('Sites') ?></h2>

		<form action="ms-sites.php" method="get" id="ms-search">
			<input type="hidden" name="action" value="blogs" />
			<input type="text" name="s" value="<?php echo($s); ?>" size="17" />
			<input type="submit" class="button" name="blog_name" value="<?php esc_attr_e('Search blogs by name') ?>" />
			<input type="submit" class="button" name="blog_id" value="<?php esc_attr_e('by blog ID') ?>" />
			<input type="submit" class="button" name="blog_ip" value="<?php esc_attr_e('by IP address') ?>" />
		</form>

		<form id="form-blog-list" action="ms-edit.php?action=allblogs" method="post">

		<div class="tablenav">
			<?php if ( $blog_navigation ) echo "<div class='tablenav-pages'>$blog_navigation</div>"; ?>

			<div class="alignleft">
				<input type="submit" value="<?php esc_attr_e('Delete') ?>" name="allblog_delete" class="button-secondary delete" />
				<input type="submit" value="<?php esc_attr_e('Mark as Spam') ?>" name="allblog_spam" class="button-secondary" />
				<input type="submit" value="<?php esc_attr_e('Not Spam') ?>" name="allblog_notspam" class="button-secondary" />
				<?php wp_nonce_field( 'allblogs' ); ?>
				<br class="clear" />
			</div>
		</div>

		<br class="clear" />

		<?php if ( isset($_GET['s']) && !empty($_GET['s']) ) : ?>
			<p><a href="ms-users.php?action=users&s=<?php echo urlencode( stripslashes( $s ) ) ?>"><?php _e('Search Users:') ?> <strong><?php echo stripslashes( $s ); ?></strong></a></p>
		<?php endif; ?>

		<?php
		// define the columns to display, the syntax is 'internal name' => 'display name'
		$blogname_columns = ( is_subdomain_install() ) ? __('Domain') : __('Path');
		$posts_columns = array(
			'id'           => __('ID'),
			'blogname'     => $blogname_columns,
			'lastupdated'  => __('Last Updated'),
			'registered'   => __('Registered'),
			'users'        => __('Users')
		);

		if ( has_filter( 'wpmublogsaction' ) )
			$posts_columns['plugins'] = __('Actions');

		$posts_columns = apply_filters('wpmu_blogs_columns', $posts_columns);

		$sortby_url = "s=";
		if ( !empty($_GET[ 'blog_ip' ]) )
			$sortby_url .= "&ip_address=" . urlencode( $s );
		else
			$sortby_url .= urlencode( $s ) . "&ip_address=" . urlencode( $s );
		?>

		<table width="100%" cellpadding="3" cellspacing="3" class="widefat">
			<thead>
				<tr>
				<th scope="col" class="check-column"></th>
				<?php foreach($posts_columns as $column_id => $column_display_name) {
					$column_link = "<a href='ms-sites.php?{$sortby_url}&amp;sortby={$column_id}&amp;";
					if ( $order_by == $column_id ) {
						$column_link .= ($order_by == 'DESC') ? 'order=ASC&amp;' : 'order=DESC&amp;';
					}
					$column_link .= "apage={$apage}'>{$column_display_name}</a>";

					$col_url = ($column_id == 'users' || $column_id == 'plugins') ? $column_display_name : $column_link;
					?>
					<th scope="col"><?php echo $col_url ?></th>
				<?php } ?>
				</tr>
			</thead>
			<tbody id="the-list">
			<?php
			if ( $blog_list ) {
				$status_list = array( 'archived' => array( 'site-archived', __('Archived') ), 'spam' => array( 'site-spammed', __('Spam') ), 'deleted' => array( 'site-deleted', __('Deleted') ) );
				$class = '';
				foreach ( $blog_list as $blog ) {
					$class = ('alternate' == $class) ? '' : 'alternate';
					reset( $status_list );

					$blog_states = array();
					foreach ( $status_list as $status => $col ) {
						if ( get_blog_status( $blog['blog_id'], $status ) == 1 ) {
							$class = $col[0];
							$blog_states[] = $col[1];
						}
					}
					$blog_state = '';
					if ( ! empty($blog_states) ) {
						$state_count = count($blog_states);
						$i = 0;
						$blog_state .= ' - ';
						foreach ( $blog_states as $state ) {
							++$i;
							( $i == $state_count ) ? $sep = '' : $sep = ', ';
							$blog_state .= "<span class='post-state'>$state$sep</span>";
						}
					}
					echo "<tr class='$class'>";

					$blogname = ( is_subdomain_install() ) ? str_replace('.'.$current_site->domain, '', $blog['domain']) : $blog['path'];
					foreach ( $posts_columns as $column_name=>$column_display_name ) {
						switch ( $column_name ) {
							case 'id': ?>
								<th scope="row" class="check-column">
									<input type='checkbox' id='blog_<?php echo $blog['blog_id'] ?>' name='allblogs[]' value='<?php echo esc_attr($blog['blog_id']) ?>' />
								</th>
								<th scope="row">
									<?php echo $blog['blog_id'] ?>
								</th>
							<?php
							break;

							case 'blogname': ?>
								<td valign="top">
									<a href="ms-sites.php?action=editblog&amp;id=<?php echo $blog['blog_id'] ?>" class="edit"><?php echo $blogname; echo $blog_state?></a>
									<br/>
									<?php
									$actions	= array();
									$actions[]	= '<a href="ms-sites.php?action=editblog&amp;id=' . $blog['blog_id'] . '" class="edit">' . __('Edit') . '</a>';
									$actions[]	= "<a href='" . get_admin_url($blog['blog_id']) . "' class='edit'>" . __('Backend') . '</a>';

									if ( get_blog_status( $blog['blog_id'], "deleted" ) == '1' )
										$actions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=activateblog&amp;ref=' . urlencode( $_SERVER['REQUEST_URI'] ) . '&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to activate the blog %s" ), $blogname ) ) . '">' . __('Activate') . '</a>';
									else
										$actions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=deactivateblog&amp;ref=' . urlencode( $_SERVER['REQUEST_URI'] ) . '&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to deactivate the blog %s" ), $blogname ) ) . '">' . __('Deactivate') . '</a>';

									if ( get_blog_status( $blog['blog_id'], "archived" ) == '1' )
										$actions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=unarchiveblog&amp;id=' .  $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to unarchive the blog %s" ), $blogname ) ) . '">' . __('Unarchive') . '</a>';
									else
										$actions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=archiveblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to archive the blog %s" ), $blogname ) ) . '">' . __('Archive') . '</a>';

									if ( get_blog_status( $blog['blog_id'], "spam" ) == '1' )
										$actions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=unspamblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to unspam the blog %s" ), $blogname ) ) . '">' . __('Not Spam') . '</a>';
									else
										$actions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=spamblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to mark the blog %s as spam" ), $blogname ) ) . '">' . __("Spam") . '</a>';

									$actions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=deleteblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to delete the blog %s" ), $blogname ) ) . '">' . __("Delete") . '</a>';

									$actions[]	= "<a href='" . get_home_url($blog['blog_id']) . "' rel='permalink'>" . __('Visit') . '</a>';
									?>

									<?php if ( count($actions) ) : ?>
									<div class="row-actions">
										<?php echo implode(' | ', $actions); ?>
									</div>
									<?php endif; ?>
								</td>
							<?php
							break;

							case 'lastupdated': ?>
								<td valign="top">
									<?php echo ( $blog['last_updated'] == '0000-00-00 00:00:00' ) ? __("Never") : mysql2date(__('Y-m-d \<\b\r \/\> g:i:s a'), $blog['last_updated']); ?>
								</td>
							<?php
							break;
							case 'registered': ?>
								<td valign="top">
									<?php echo mysql2date(__('Y-m-d \<\b\r \/\> g:i:s a'), $blog['registered']); ?>
								</td>
							<?php
							break;

							case 'users': ?>
								<td valign="top">
									<?php
									$blogusers = get_users_of_blog( $blog['blog_id'] );
									if ( is_array( $blogusers ) ) {
										$blogusers_warning = '';
										if ( count( $blogusers ) > 5 ) {
											$blogusers = array_slice( $blogusers, 0, 5 );
											$blogusers_warning = __( 'Only showing first 5 users.' ) . ' <a href="' . get_admin_url($blog['blog_id'], 'users.php') . '">' . __( 'More' ) . '</a>';
										}
										foreach ( $blogusers as $key => $val )
											echo '<a href="user-edit.php?user_id=' . $val->user_id . '">' . $val->user_login . '</a> ('.$val->user_email.')<br />';
										if ( $blogusers_warning != '' )
											echo '<strong>' . $blogusers_warning . '</strong><br />';
									}
									?>
								</td>
							<?php
							break;

							case 'plugins': ?>
								<?php if ( has_filter( 'wpmublogsaction' ) ) { ?>
								<td valign="top">
									<?php do_action( "wpmublogsaction", $blog['blog_id'] ); ?>
								</td>
								<?php } ?>
							<?php break;

							default: ?>
								<?php if ( has_filter( 'manage_blogs_custom_column' ) ) { ?>
								<td valign="top">
									<?php do_action('manage_blogs_custom_column', $column_name, $blog['blog_id']); ?>
								</td>
								<?php } ?>
							<?php break;
						}
					}
					?>
					</tr>
					<?php
				}
			} else { ?>
				<tr style='background-color: <?php echo $bgcolor; ?>'>
					<td colspan="8"><?php _e('No blogs found.') ?></td>
				</tr>
			<?php
			} // end if ($blogs)
			?>

			</tbody>
		</table>
		</form>
		</div>

		<div class="wrap">
			<a name="form-add-blog"></a>
			<h2><?php _e('Add Site') ?></h2>
			<form method="post" action="ms-edit.php?action=addblog">
				<?php wp_nonce_field('add-blog') ?>
				<table class="form-table">
					<tr class="form-field form-required">
						<th style="text-align:center;" scope='row'><?php _e('Site Address') ?></th>
						<td>
						<?php if ( is_subdomain_install() ) { ?>
							<input name="blog[domain]" type="text" title="<?php _e('Domain') ?>"/>.<?php echo $current_site->domain;?>
						<?php } else {
							echo $current_site->domain . $current_site->path ?><input name="blog[domain]" type="text" title="<?php _e('Domain') ?>"/>
						<?php }
						echo "<p>" . __( 'Only the characters a-z and 0-9 recommended.' ) . "</p>";
						?>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th style="text-align:center;" scope='row'><?php _e('Site Title') ?></th>
						<td><input name="blog[title]" type="text" size="20" title="<?php _e('Title') ?>"/></td>
					</tr>
					<tr class="form-field form-required">
						<th style="text-align:center;" scope='row'><?php _e('Admin Email') ?></th>
						<td><input name="blog[email]" type="text" size="20" title="<?php _e('Email') ?>"/></td>
					</tr>
					<tr class="form-field">
						<td colspan='2'><?php _e('A new user will be created if the above email address is not in the database.') ?><br /><?php _e('The username and password will be mailed to this email address.') ?></td>
					</tr>
				</table>
				<p class="submit">
					<input class="button" type="submit" name="go" value="<?php esc_attr_e('Add Site') ?>" /></p>
			</form>
		</div>
		<?php
	break;
} // end switch( $action )

include('admin-footer.php'); ?>
