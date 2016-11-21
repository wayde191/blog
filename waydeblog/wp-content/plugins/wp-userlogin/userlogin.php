<?php

/*
Plugin Name: WP-UserLogin
Plugin URI: http://wayofthegeek.org/downloads/wp-userlogin/
Description: Adds a UserLogin Widget to display login form or dashboard links depending on user role.
Version: 13.01
Author: Jerry Stephens
Author URI: http://wayofthegeek.org/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


/*  
	Copyright 2013  Jerry Stephens  (email : migo@wayofthegeek.org)

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
	
#// BEGIN add textdomain for localization
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'wp-userlogin', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );
#// END add textdomain for localization

#// BEGIN Add option pages
add_action('admin_menu','wpul_option_page');

function wpul_option_page(){
	add_menu_page(__('UserLogin', 'wp-userlogin'),__('UserLogin', 'wp-userlogin'),'manage_options','wpul_options','wpul_userlogin_options_page');	
	add_submenu_page('wpul_options','','','manage_options','wpul_options','wpul_userlogin_options_page'); // Remove needless extra UserLogin submenu page
	add_submenu_page('wpul_options',__('CSS Editor', 'wp-userlogin'),__('CSS Editor', 'wp-userlogin'),'manage_options','wpul_style_options','wpul_style_editor');	
}
#// END Add option pages

#// BEGIN diff function

function arr_diff( $f1 , $f2 , $show_equal = 0 )
{

    $c1         = 0 ;                   # current line of left
    $c2         = 0 ;                   # current line of right
    $max1       = count( $f1 ) ;        # maximumlines of left
    $max2       = count( $f2 ) ;        # maximum lines of right
    $outcount   = 0;                    # output counter
    $hit1       = "" ;                  # hit in left
    $hit2       = "" ;                  # hit in right

    while ( 
            $c1 < $max1                 # have next line in left
            and                 
            $c2 < $max2                 # have next line in right
            and 
            ($stop++) < 1000            # don-t have more then 1000 ( loop-stopper )
            and 
            $outcount < 20              # output count is less then 20
          )
    {
        /**
        *   is the trimmed line of the current left and current right line
        *   the same ? then this is a hit (no difference)
        */  
        if ( trim( $f1[$c1] ) == trim ( $f2[$c2])  )    
        {
            /**
            *   add to output-string, if "show_equal" is enabled
            */
            $out    .= ($show_equal==1) 
                     ?  formatline ( ($c1) , ($c2), "=", $f1[ $c1 ] ) 
                     : "" ;
            /**
            *   increase the out-putcounter, if "show_equal" is enabled
            *   this ist more for demonstration purpose
            */
            if ( $show_equal == 1 )  
            { 
                $outcount++ ; 
            }
            
            /**
            *   move the current-pointer in the left and right side
            */
            $c1 ++;
            $c2 ++;
        }

        /**
        *   the current lines are different so we search in parallel
        *   on each side for the next matching pair, we walk on both 
        *   sided at the same time comparing with the current-lines
        *   this should be most probable to find the next matching pair
        *   we only search in a distance of 10 lines, because then it
        *   is not the same function most of the time. other algos
        *   would be very complicated, to detect 'real' block movements.
        */
        else
        {
            
            $b      = "" ;
            $s1     = 0  ;      # search on left
            $s2     = 0  ;      # search on right
            $found  = 0  ;      # flag, found a matching pair
            $b1     = "" ;      
            $b2     = "" ;
            $fstop  = 0  ;      # distance of maximum search

            #fast search in on both sides for next match.
            while ( 
                    $found == 0             # search until we find a pair
                    and 
                    ( $c1 + $s1 <= $max1 )  # and we are inside of the left lines
                    and 
                    ( $c2 + $s2 <= $max2 )  # and we are inside of the right lines
                    and     
                    $fstop++  < 10          # and the distance is lower than 10 lines
                  )
            {

                /**
                *   test the left side for a hit
                *
                *   comparing current line with the searching line on the left
                *   b1 is a buffer, which collects the line which not match, to 
                *   show the differences later, if one line hits, this buffer will
                *   be used, else it will be discarded later
                */
                #hit
                if ( trim( $f1[$c1+$s1] ) == trim( $f2[$c2] )  )
                {
                    $found  = 1   ;     # set flag to stop further search
                    $s2     = 0   ;     # reset right side search-pointer
                    $c2--         ;     # move back the current right, so next loop hits
                    $b      = $b1 ;     # set b=output (b)uffer
                }
                #no hit: move on
                else
                {
                    /**
                    *   prevent finding a line again, which would show wrong results
                    *
                    *   add the current line to leftbuffer, if this will be the hit
                    */
                    if ( $hit1[ ($c1 + $s1) . "_" . ($c2) ] != 1 )
                    {   
                        /**
                        *   add current search-line to diffence-buffer
                        */
                        $b1  .= formatline( ($c1 + $s1) , ($c2), "-", $f1[ $c1+$s1 ] );

                        /**
                        *   mark this line as 'searched' to prevent doubles. 
                        */
                        $hit1[ ($c1 + $s1) . "_" . $c2 ] = 1 ;
                    }
                }



                /**
                *   test the right side for a hit
                *
                *   comparing current line with the searching line on the right
                */
                if ( trim ( $f1[$c1] ) == trim ( $f2[$c2+$s2])  )
                {
                    $found  = 1   ;     # flag to stop search
                    $s1     = 0   ;     # reset pointer for search
                    $c1--         ;     # move current line back, so we hit next loop
                    $b      = $b2 ;     # get the buffered difference
                }
                else
                {   
                    /**
                    *   prevent to find line again
                    */
                    if ( $hit2[ ($c1) . "_" . ( $c2 + $s2) ] != 1 )
                    {
                        /**
                        *   add current searchline to buffer
                        */
                        $b2   .= formatline ( ($c1) , ($c2 + $s2), "+", $f2[ $c2+$s2 ] );

                        /**
                        *   mark current line to prevent double-hits
                        */
                        $hit2[ ($c1) . "_" . ($c2 + $s2) ] = 1;
                    }

                 }

                /**
                *   search in bigger distance
                *
                *   increase the search-pointers (satelites) and try again
                */
                $s1++ ;     # increase left  search-pointer
                $s2++ ;     # increase right search-pointer  
            }

            /**
            *   add line as different on both arrays (no match found)
            */
            if ( $found == 0 )
            {
                $b  .= formatline ( ($c1) , ($c2), "-", $f1[ $c1 ] );
                $b  .= formatline ( ($c1) , ($c2), "+", $f2[ $c2 ] );
            }

            /** 
            *   add current buffer to outputstring
            */
            $out        .= $b;
            $outcount++ ;       #increase outcounter

            $c1++  ;    #move currentline forward
            $c2++  ;    #move currentline forward

            /**
            *   comment the lines are tested quite fast, because 
            *   the current line always moves forward
            */

        } /*endif*/

    }/*endwhile*/

    return $out;

}/*end func*/

/**
*   callback function to format the diffence-lines with your 'style'
**/
function formatline( $nr1, $nr2, $stat, &$value )  #change to $value if problems
{
    if ( trim( $value ) == "" )
    {
        return "";
    }

    switch ( $stat )
    {
        case "=":
            return $nr1. " : $nr2 : = ".htmlentities( $value )  ."<br>";
        break;

        case "+":
            return $nr1. " : $nr2 : + <font color='blue' >".htmlentities( $value )  ."</font><br>";
        break;

        case "-":
            return $nr1. " : $nr2 : - <font color='red' >".htmlentities( $value )  ."</font><br>";
        break;
    }

} 

#// END diff function

#// BEGIN Add any missing or updated CSS elements to the stylesheet
$style = dirname(__FILE__).'/style.css';
$style_new = dirname(__FILE__).'/style1.css';
$old = file($style);
$new = file($style_new);
$needle1 = "#";
$needle2 = '.wp';
$diff = implode("",array_diff($new,$old));
if(strstr($diff,$needle1) || strstr($diff,$needle2)):
    $start = "\n";
    $end = '}';
    file_put_contents($style,$start,FILE_APPEND);
    file_put_contents($style,$diff,FILE_APPEND);
    file_put_contents($style,$end,FILE_APPEND);
endif;
#// END Add any missing or updated CSS elements to the stylesheet
 
#// BEGIN sanitize Login Panel Name field
function wpul_sanitize($string) { 
        $sanitize= strip_tags(addslashes($string));
        $string = $sanitize;
        
        return $string;

}
#// END sanitize Login Panel Name field

#// BEGIN register settings
function wpul_init() {
    register_setting('wpul_text','wpul_settings');
}
#// END register settings

#// BEGIN work-around for update notification
add_action('admin_init','wpul_init');
function wpul_notify(){
    echo '<div id="message" class="updated below-h2">Options Saved.</div>';
}
#// END work-around for update notification

#// BEGIN build second page (form options)
function wpul_userlogin_options_page(){
?>
        <div class="wrap">
		<h2><?php _e('UserLogin Options', 'wp-userlogin');?></h2>
<?php if($_GET['updated'] == 'true'):
echo '<div id="message" class="updated fade"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
endif;?>
                <form method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>

        <?php settings_fields('wpul_text');
        $option = get_option('wpul_settings');
?>
<div style="width: 350px; float: left; margin-right: 20px; width: 360px;">        
<div id="submitdiv" class="postbox">
<h3 style="cursor: auto; margin: 0; padding: 5px;" class="hndle"><?php _e('Form Content Options','wp-userlogin');?></h3>
<div style="padding: 5px;">
<h4 style="margin-bottom: 0;"><?php _e('Login Form Name','wp-userlogin');?></h4>
        <input name="wpul_settings[set_nonlog]" type="text" value="<?php echo $option['set_nonlog'];?>" />
<h4 style="margin-bottom: 0;"><?php _e('Login Redirect Page','wp-userlogin');?></h4>
        <?php bloginfo('url');?>/<input name="wpul_settings[redirect]" size="15" type="text" value="<?php echo $option['redirect'];?>" />
<h4 style="margin-bottom: 0;"><?php _e('Logout Redirect Page','wp-userlogin');?></h4>
        <?php bloginfo('url');?>/<input name="wpul_settings[redirect_out]" type="text" size="15" value="<?php echo $option['redirect_out'];?>" />
        
 </div></div>	
 

<div id="submitdiv" class="postbox">
<h3 style="cursor: auto; margin: 0; padding: 5px;" class="hndle"><?php _e('Personalization Options','wp-userlogin');?></h3>
<div style="padding: 5px;">
 <h4 style="margin-bottom: 0;"><?php _e('Gravatars','wp-userlogin');?></h4>
                <input type="checkbox" name="wpul_settings[avatar]" value="CHECKED" <?php echo $option['avatar'];?> /> <?php _e('Display Gravatar','wp-userlogin');?><br />
                <?php _e('Uses avatar settings set on the','wp-userlogin');?> <a href="<?php bloginfo('wpurl');?>/wp-admin/options-discussion.php"><?php _e('Discussion Page','wp-userlogin');?></a>.
 
 <h4 style="margin-bottom: 0;"><?php _e('Welcome Message','wp-userlogin');?></h4>
                <input type="text" name="wpul_settings[welcome]" size="25" value="<?php echo $option['welcome'];?>" />

<br />
                <input type="checkbox" name="wpul_settings[welcomecheck]" value="CHECKED" <?php echo $option['welcomecheck'];?> /> <?php _e('Display Welcome Message','wp-userlogin');?><br />

            <br /><strong><small><?php _e('indicate current user with','wp-userlogin');?></small>:</strong>
            <table cellspacing="6" width="100%">
                <tr><td><strong>%user</strong></td><td>&rarr;</td><td> <?php _e('Display Name','wp-userlogin');?></td></tr>
                <tr><td><strong>%login</strong></td><td>&rarr;</td><td> Username/Login</td></tr>

                <tr><td><strong>%id</strong></td><td>&rarr;</td><td> <?php _e('User ID','wp-userlogin');?></td></tr>
                <tr><td><strong>%email</strong></td><td>&rarr;</td><td> <?php _e('User Email','wp-userlogin');?></td></tr>
                <tr><td colspan="3" align="center"><?php _e('Following defaults to <strong>%user</strong> if profile info is blank','wp-userlogin');?></td></tr>
                <tr><td><strong>%firstname</strong></td><td>&rarr;</td><td> <?php _e('User First Name','wp-userlogin');?></td></tr>
                <tr><td><strong>%lastname</strong></td><td>&rarr;</td><td> <?php _e('User Last Name','wp-userlogin');?></td></tr>
                <tr><td><strong>%fullname</strong></td><td>&rarr;</td><td> <?php _e('User Full Name','wp-userlogin');?></td></tr>
            </table>
           </dt>
</td></tr></table>
</div></div>
</div>
<div  style="float: left; width: 360px;">
<div id="submitdiv" class="postbox">
<h3 style="cursor: auto; margin: 0; padding: 5px;" class="hndle"><?php _e('Control Panel Content Options','wp-userlogin');?></h3>
<div style="padding: 5px;">
 <h4 style="margin-bottom: 0;"><?php _e('Control Panel Name','wp-userlogin');?></h4>
        <input name="wpul_settings[set_log]" type="text" value="<?php echo $option['set_log'];?>" />

 <h4 style="margin-bottom: 0;"><?php _e('Control Stylesheet','wp-userlogin');?></h4>
<input type="checkbox" name="wpul_settings[style]" value="CHECKED" <?php echo $option['style'];?> /> <?php _e('Use default stylesheet','wp-userlogin');?><br />

 <h4 style="margin-bottom: 0;"><?php _e('Available Links','wp-userlogin');?><br />
 <small>(<?php _e('based on user role','wp-userlogin');?>)</small></h4>
 	<input type="checkbox" name="wpul_settings[dashboard]" value="CHECKED" <?php echo $option['dashboard'];?> /> <?php _e('Dashboard','wp-userlogin');?><br />
	<input type="checkbox" name="wpul_settings[newpost]" value="CHECKED" <?php echo $option['newpost'];?> /> <?php _e('New Post','wp-userlogin');?><br />
	<input type="checkbox" name="wpul_settings[editpost]" value="CHECKED" <?php echo $option['editpost'];?> /> <?php _e('Edit Posts','wp-userlogin');?><br />
	<input type="checkbox" name="wpul_settings[managetheme]" value="CHECKED" <?php echo $option['managetheme'];?> /> <?php _e('Manage Themes','wp-userlogin');?><br />
	<input type="checkbox" name="wpul_settings[install_plugins]" value="CHECKED" <?php echo $option['install_plugins'];?> /> <?php _e('Plugins','wp-userlogin');?><br />
	<input type="checkbox" name="wpul_settings[options]" value="CHECKED" <?php echo $option['options'];?> /> <?php _e('Options','wp-userlogin');?><br />
	<input type="checkbox" name="wpul_settings[users]" value="CHECKED" <?php echo $option['users'];?> /> <?php _e('Users','wp-userlogin');?><br />
	<input type="checkbox" name="wpul_settings[profile]" value="CHECKED" <?php echo $option['profile'];?> /> <?php _e('Your Profile','wp-userlogin');?><br />
	<input type="checkbox" name="wpul_settings[logout]" value="CHECKED" <?php echo $option['logout'];?> /> <?php _e('Logout','wp-userlogin');?><br />
</div>
<div style="padding: 5px;">
 <h4 style="margin-bottom: 0;"><?php _e('Extra Optional Links','wp-userlogin');?></h4>
 <span style="float: left; display: block; width: 50%; text-align: center;">URL</span>
 <span style="float: right; display: block; width: 50%; text-align: center;">Name</span>
 <br clear="all" />

<input type="text" name="wpul_settings[link1]" style="float: left;" value="<?php echo $option['link1'];?>" />
<input type="text" name="wpul_settings[name1]" style="float: left;" value="<?php echo $option['name1'];?>" />
<br clear="all" />

<input type="text" name="wpul_settings[link2]" style="float: left;" value="<?php echo $option['link2'];?>" />
<input type="text" name="wpul_settings[name2]" style="float: left;" value="<?php echo $option['name2'];?>" />
<br clear="all" />

<input type="text" name="wpul_settings[link3]" style="float: left;" value="<?php echo $option['link3'];?>" />
<input type="text" name="wpul_settings[name3]" style="float: left;" value="<?php echo $option['name3'];?>" />
<br clear="all" />

<input type="text" name="wpul_settings[link4]" style="float: left;" value="<?php echo $option['link4'];?>" />
<input type="text" name="wpul_settings[name4]" style="float: left;" value="<?php echo $option['name4'];?>" />
<br clear="all" />

<input type="text" name="wpul_settings[link5]" style="float: left;" value="<?php echo $option['link5'];?>" />
<input type="text" name="wpul_settings[name5]" style="float: left;" value="<?php echo $option['name5'];?>" />
<br clear="all" /><br />
	<input type="checkbox" name="wpul_settings[nofollow]" value="CHECKED" <?php echo $option['nofollow'];?> /> <?php _e('Use <strong>rel="nofollow"</strong> on links?','wp-userlogin');?><br />

</div>
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="wpul_settings" />
 <p class="submit" style="clear: both; float: right;">
		<input type="submit" name="submit" value="<?php _e('Save Changes','wp-userlogin')?> " />
	</p>
        </div>
    </form>
        </div>
<?php

}
#// END build second page

#// BEGIN build third page (css editor)
function wpul_style_editor(){

    $changes = $_POST['editor'];

        if(isset($_POST['Submit'])){
            $css = file_put_contents(dirname(__FILE__).'/style.css',$changes);
        }
?>
        <div class="wrap">
<div class="fileedit-sub">

	<form method="post" action="">
        <h2><?php _e('Edit WP-Userlogin CSS', 'wp-userlogin');?></h2>
<?php if(isset($_POST['Submit'])):
echo '<div id="message" class="updated fade"><p><strong>' . _e('Stylesheet Updated','wp-userlogin') . '</strong></p></div>';
endif;?>

<?php
        $css = file_get_contents(dirname(__FILE__).'/style.css');
        echo '<textarea name="editor" rows="25" cols="70" style="width: 80%; height: 100%; margin: 0 auto;">'.$css.'</textarea>';
?>
	<p class="submit">
	<?php wp_nonce_field('update-options'); ?>
	<input type="hidden" name="action" value="update" />
		<input type="submit" name="Submit" value="<?php _e('Edit Stylesheet','wp-userlogin');?> " />
        </p>
    </div></div>
<?php
}
#// END build thir page

#// BEGIN database record install
function wpul_initial_db($args){
	$newvalue = "";
	$autoload = "yes";
	$deprecated = " ";
	$boxvalue = array('CHECKED','CHECKED','CHECKED','CHECKED','CHECKED','CHECKED','CHECKED','CHECKED','CHECKED','CHECKED','CHECKED');
	$option = array('set_nonlog','set_log','redirect','set_checkbox');
	$check = get_option('set_checkbox');
	$redirect = get_option('redirect');
        $redirect_out = get_option('redirect_out');
        $welcome = get_option('welcome');
        $stylesheet = get_option('use_stylesheet');
        $nonlog = get_option('set_nonlog');
        $log = get_option('set_log');
        
$upgrade = array(
                 'set_nonlog'=>$nonlog,
                 'set_log'=>$log,
                 'welcome'=> $welcome,
                 'redirect'=>$redirect,
                 'redirect_out'=>$redirect_out,
                 'dashboard'=>$check[0],
                 'newpost'=>$check[1],
                 'editpost'=>$check[2],
                 'managetheme'=>$check[3],
		 'install_plugins'=>$check[4],
                 'options'=>$check[5],
                 'users'=>$check[6],
                 'profile'=>$check[7],
                 'logout'=>$check[8],
                 'welcomecheck'=>$check[9],
                 'style'=>$check[10],
                 'avatar'=>""
                );
$install = array(
                 'set_nonlog'=>"Login",
                 'set_log'=>"Control Panel",
                 'welcome'=>"Hey %user",
                 'redirect'=>"",
                 'redirect_out'=>"",
                 'dashboard'=>"CHECKED",
                 'newpost'=>"CHECKED",
                 'editpost'=>"CHECKED",
                 'managetheme'=>"CHECKED",
		 'install_plugins'=>"CHECKED",
                 'options'=>"CHECKED",
                 'users'=>"CHECKED",
                 'profile'=>"CHECKED",
                 'logout'=>"CHECKED",
                 'welcomecheck'=>"CHECKED",
                 'style'=>"CHECKED",
                 'avatar'=>"CHECKED"
                );
                
    if($nonlog != ""):
        add_option('wpul_settings',$upgrade);
	delete_option($option[0]);
	delete_option($option[1]);
	delete_option($option[2]);
	delete_option('set_checkbox');
        delete_option('use_stylesheet');

    else:
        add_option('wpul_settings',$install);
    endif;
}
#// END database record install

#// BEGIN initialize sidebar widget
function wpul_widget_userlogin_init() {
	if (!function_exists('register_sidebar_widget')) {
        return;
	}
#// BEGIN set display for links set in backend

function wpul_optional_links() {
    $option = get_option('wpul_settings');
    if($option['nofollow'] == "CHECKED"):
        $follow = ' rel="nofollow"';
    endif;
    $links = array();
    for($i=1;$i<=5;$i++):
			$linki = "link$i";
			$namei = "name$i";
			$link = $option[$linki];
			$name = $option[$namei];
		if($link != ""):
			$link = '<li><a href="'.$link.'"'.$follow.'>'.$name.'</a></li>';
		endif;
        array_push($links,$link);
    endfor;

$links = implode(' ',$links);

return $links;
}
function pluralize($num, $plural = 's', $single = '') {
    if ($num == 1) return $single; else return $plural;
}
#// END set display for links set in backend

#// BEGIN set display based on selected fields & user permission
class wpul_widget extends WP_Widget {

	function wpul_widget() {
		// Instantiate the parent object
			show_admin_bar(false); // Disable admin bar

		parent::__construct( false, 'WP UserLogin');
	}
function wpul_user_permissions($args){
	$wp_url = get_settings('siteurl');
        $check = get_option('wpul_settings');
        $welcome = $check['welcome'];
	$vals = explode(',',$args);
        global $current_user, $user_ID, $wp_admin_bar,$wpdb;
        get_currentuserinfo();
	

        $comments_waiting = $wpdb->get_var("SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");
	$core = get_option('_site_transient_update_core');
	$plugins = get_option('_site_transient_update_plugins');
	$updates['plugins'] = $plugins->response;
	$updates['core'] = $core->updates['0']->response;
	$plugin_update = count($updates['plugins']);
	if($check['dashboard'] == 'CHECKED'){
		$link[] .= '<li><a href="'.admin_url().'">'.__('Dashboard').'</a></li>';
	}
	if($check['newpost'] == 'CHECKED' && current_user_can('publish_posts')){
		$link[] .= '<li><a href="'.admin_url('post-new.php').'">'.__('New Post').'</a></li>';
		$link[] = '<li><a href="'.admin_url('edit.php').'">'.__('Edit Posts').'</a></li>';
	}
	if($comments_waiting > 0){
		$link[] = '<li class="notify"><a href="'.admin_url('edit-comments.php?comment_status=moderated').'"/>'.$comments_waiting.' '.pluralize($comments_waiting,__('Comments'),__('Comment')).(' Pending').'</a></li>'; 
	}

	if($check['managetheme'] == "CHECKED" && current_user_can('update_themes')){
		$link[] .= '<li><a href="'.admin_url('themes.php').'">'.__('Manage Theme').'</a></li>';
	}
	if($check['install_plugins'] == "CHECKED" && current_user_can('install_plugins')){
		$link[] .= '<li><a href="'.admin_url('plugins.php').'">'.__('Manage Plugins').'</a></li>';
		$link[] .= '<li><a href="'.admin_url('plugin-install.php').'">'.__('Install Plugins').'</a></li>';
	}
	if($plugin_update > 0 && current_user_can('update_core')){
		$link[] = '<li class="notify"><a href="'.admin_url('update-core.php').'"/>'.$plugin_update.__(' Plugin ').pluralize($plugin_update,__('Updates'),__('Update')).__(' Available').'</a></li>'; 
	}
	if($updates['core'] == 'upgrade' && current_user_can('update_core')){
		$link[] = '<li class="notify"><a href="'.admin_url('update-core.php').'"/>'.__('Core Update Available').'</a></li>'; 
	}
	if($check['options'] == "CHECKED" &&  current_user_can('manage_options')){
		$link[] .= '<li><a href="'.admin_url('options-general.php').'">'.__('Options').'</a></li>';
	}
	if($check['users'] == "CHECKED" &&  current_user_can('edit_users')){
		$link[] .= '<li><a href="'.admin_url('users.php').'">'.__('Users').'</a></li>';
	}
	if($check['profile'] == "CHECKED" &&  is_user_logged_in()){
		$link[] .= '<li><a href="'.admin_url('profile.php').'">'.__('Edit Your Profile').'</a></li>'.PHP_EOL.
		'<li><a href="'.home_url('?author='.$user_ID).'">'.__('View Your Profile','wp-userlogin').'</a></li>';
		$link[] .= '<li><a href="'.admin_url('tools.php').'">'.__('Your Available Tools').'</a></li>';
	}
	if($check['logout'] == "CHECKED" && is_user_logged_in()){
		if($check['redirect_out'] !== ''){
			$link[] .= '<li><a href="'.wp_logout_url(get_bloginfo('url').'/'.$check['redirect_out']).'">'.__('Logout').'</a></li>';	
		}else{
			$link[] .= '<li><a href="'.wp_logout_url($_SERVER['REQUEST_URI']).'">'.__('Logout').'</a></li>';
		}
	}        
    if($check['welcomecheck'] == "CHECKED"){
        if($current_user->user_firstname != "") :
            $firstname = $current_user->user_firstname;
        else: 
            $firstname = $current_user->display_name;
        endif;

        if($current_user->user_lastname != ""):
            $lastname = $current_user->user_lastname;
        else: 
            $lastname = $current_user->display_name;
        endif;
        
        if($current_user->user_firstname != "" && $current_user->user_lastname != ""):
            $fullname = $current_user->user_firstname.' '.$current_user->user_lastname;
        else :
            $fullname = $current_user->display_name;
        endif;
        
        $head = preg_replace('/\%user/',$current_user->display_name,
        preg_replace('/\%login/',$current_user->user_login,
        preg_replace('/\%id/',$current_user->ID,
        preg_replace('/\%email/',$current_user->user_email,
        preg_replace('/\%firstname/',$firstname,
        preg_replace('/\%lastname/',$lastname,
        preg_replace('/\%fullname/',$fullname,$welcome)))))));
        $head = '<span id="welcome">'.$head.'</span>';
    }
    else{ 
        $head = '';
    }	
    if($check['avatar'] == "CHECKED"):
        $avatar = get_avatar( $current_user->ID, $size, $default, $alt ); 
    else:
        $avatar = "";
    endif;
        echo $avatar;
        $head = $head . '<ul class="wpul_menu">';
        
 	$foot = wpul_optional_links()."</ul>";
$links = implode('',$link);
	return $head.$links.$foot;
}

	function widget( $args, $instance ) {
		// Widget output
		$check = get_option('wpul_settings');
		//~ print_r($check);	
		if(is_user_logged_in()){
			global $current_user;
			get_currentuserinfo();
		$title =$option['set_log'];	
		
            if ( current_user_can('activate_plugins')){
		for($i=0;$i<10;$i++){
			$options[] .=$i;
		}
            }
	    if(current_user_can('edit_posts')){
		$options[] .= 2;
		$options[] .= 0;
	    }
            if(current_user_can('publish_posts')){
		for($i=3;$i<8;$i++){
			$options[] .= $i;
		}
		$options[] .= 0;
	    }
            if(current_user_can('read') ){
                    $options[] .= 0;
                    $options[] .= 6;
                    $options[] .= 7;
            }
	    $options = array_unique($options);
	    $options = implode(',',$options);
	    $options = $this->wpul_user_permissions($options);

		}else{
		$title = $option['set_nonlog'];
		if($option['redirect'] !== ''){
			$redir = get_bloginfo('url').'/'.$option['redirect'];
		}else{
			$redir = $_SERVER['REQUEST_URI'];
		}
                
			$outargs = array(
        'echo' => true,
        'redirect' => $redir, 
        'form_id' => 'loginform',
        'label_username' => __( 'Username' ),
        'label_password' => __( 'Password' ),
        'label_remember' => __( 'Remember Me' ),
        'label_log_in' => __( 'Log In' ),
        'id_username' => 'user_login',
        'id_password' => 'user_pass',
        'id_remember' => 'rememberme',
        'id_submit' => 'wp-submit',
        'remember' => true,
        'value_username' => NULL,
        'value_remember' => false );
			wp_login_form($outargs);
		}
		echo $before_title
		. $title
		. $after_title
		. $options
		.$after_widget;
		
	}



	function update( $new_instance, $old_instance ) {
		// Save widget options
	}

	function form( $instance ) {
		// Output admin widget options form
	}
}

function openid_call(){ // Check for OpenID
    	if(function_exists('openid_input')){
        $openid = '<label for="openid">'.__('OpenID', 'wp-userlogin').'<br />'.openid_input().'</label><br />';
	}else{
	$openid = "";
	}
        return $openid;
}
add_filter( 'login_form_middle', 'openid_call' ); // Add OpenID to form if it exists
function myplugin_register_wpul() {
	register_widget( 'wpul_widget' );
}

add_action( 'widgets_init', 'myplugin_register_wpul' );
#// END build sidebar widget

#// BEGIN register sidebar widget
register_sidebar_widget('User Login','wpul_widget_userlogin');		
#// END register sidebar widget
}
#// END initialize sidebar widget

#// BEGIN set plugin stylesheet

function wpul_style() {
    echo '<link href="'.WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/style.css" type="text/css" rel="stylesheet" />';
    echo '<style type="text/css">
	li.notify a{ color: #F00 !important; font-weight: bold; }
    </style>';
}
#// END set plugin stylesheet

#// BEGIN decide whether to use default stylesheet or not
$option = get_option('wpul_settings');
if($option['style'] == "CHECKED"){
    add_action('wp_head','wpul_style');
}
#// END decide whether to use default stylesheet or not

#// BEGIN uninstall function
function wpul_uninstall() {
    delete_option('wpul_settings');
}
#// END unsinstall function

#/> Load db info on plugin activation
register_activation_hook( __FILE__, 'wpul_initial_db' );
#/> Delete DB entry on deactivation
register_deactivation_hook(__FILE__, 'wpul_uninstall');
#/> Load The WP-UserLogin Widget
add_action('plugins_loaded', 'wpul_widget_userlogin_init');

?>