<?php
/*
Plugin Name: AJAX Login Widget++
Plugin URI: http://dound.com/projects/word-press/ajax-login-widget/
Description: A clean AJAX-based login, registration and lost password retrieval widget.
Version: 1.0.1
Author: David Underhill
Author URI: http://www.dound.com
*/

/*  Copyright 2009 David Underhill (email: dgu@cs.stanford.edu)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once("constants.php");

add_action('wp_head', 'ajax_login_widget_head' );
add_action('admin_menu', 'ajax_login_widget_add_optionsmenu');
add_action('plugins_loaded', 'ajax_login_widget_widget_init');
add_action('activate_ajax-login-widget/ajax-login-widget.php', 'ajax_login_widget_init');

function ajax_login_widget_init() {
	update_option('alw_loadingtimeout', 0);
	update_option('alw_loginredirect', '');
}

function ajax_login_widget_head() {
	wp_print_scripts( array( 'sack' ));
?>
	<script type="text/javascript">
	// ajax_login_widget settings
	var alw_timeout = <?php echo get_option('alw_loadingtimeout'); ?>;
	var alw_redirectOnLogin = '<?php echo get_option('alw_loginredirect'); ?>';

	// constants
	var alw_base_uri = '<?php bloginfo( 'wpurl' ); ?>';
	var alw_success = '<?php echo ALW_SUCCESS; ?>';
	var alw_failure = '<?php echo ALW_FAILURE; ?>';

	</script>
	<script type="text/javascript" src="<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/ajax-login-widget/ajax_login_widget.js"></script>
<?php
}

function ajax_login_widget_add_optionsmenu() {
    add_options_page('AJAX Login Widget++ Settings', 'AJAX Login Widget++', 10, 'ajax_login_widget', 'ajax_login_widget_optionspage');
}

function add_ajax_login_widget() {
	if (file_exists(TEMPLATEPATH . '/alw_template.php')) {
		include(TEMPLATEPATH . '/alw_template.php');
	} else {
		include('alw_template.php');
	}
}

function ajax_login_widget_widget() {
	add_ajax_login_widget();
}

function ajax_login_widget_widget_init() {
	if ( !function_exists('register_sidebar_widget') )
		return;

	register_sidebar_widget('AJAX Login Widget++', 'ajax_login_widget_widget');
}


function ajax_login_widget_optionspage() {
	if (''==get_option('alw_loadingtimeout')) update_option('alw_loadingtimeout', 1000);

	if (!current_user_can('manage_options'))
		wp_die(__('Cheatin&#8217; uh?'));


	if ($_POST['action'] == 'update') {
			$option = 'alw_loadingtimeout';
			$value = trim($_POST[$option]);
			$value = stripslashes($value);
			$value = abs((int) $value);
			update_option($option, $value);

			$option = 'alw_loginredirect';
			$value = trim($_POST[$option]);
			$value = stripslashes($value);
			$value = clean_url($value);
			update_option($option, $value);

			$ajax_login_widget_updated = true;
	}
?>

<?php if ($ajax_login_widget_updated) { ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php } ?>

<div class="wrap">
<h2><?php _e('AJAX Login Widget++ Settings') ?></h2>
<form method="post" action="options-general.php?page=ajax_login_widget">
<?php wp_nonce_field('update-ajax_login_widgetoptions') ?>
<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options &raquo;') ?>" /></p>
<table class="optiontable">
<tr valign="top">
<th scope="row"><?php _e('Fake loading timeout (ms):') ?></th>
<td><input name="alw_loadingtimeout" type="text" id="alw_loadingtimeout" value="<?php form_option('alw_loadingtimeout'); ?>" size="5" />
<br />
<?php _e('Set to 0 to disable fake loading screen.') ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Login redirect URI:') ?></th>
<td><input name="alw_loginredirect" type="text" id="alw_loginredirect" style="width: 95%" value="<?php form_option('alw_loginredirect'); ?>" size="45" />
<br />
<?php _e('Where the user is redirected on successful login. If left blank the user stays on the same page.') ?></td>
</tr>

</table>

<p class="submit">
<input type="hidden" name="action" value="update" />
<input type="submit" name="Submit" value="<?php _e('Update Options &raquo;') ?>" /></p>
</form>

</div>

<?php
}
?>
