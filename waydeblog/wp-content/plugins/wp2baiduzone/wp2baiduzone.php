<?php
/*
Plugin Name: WP2BaiduZone
Plugin URI:  http://code.google.com/p/wp2baiduzone/
Version:     1.3
Author:      Carey Chow
Author URI:  http://blog.zhourunsheng.com
Description: 同步发表 WordPress 博客日志到 百度空间
*/

/*  Copyright 2011  Carey Chow (email : zhourunsheng2008@gmail.com)

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
?>
<?php
add_action('admin_menu', 'menu_add_wp2baiduzone_setting');
add_action('publish_post', 'publish_article_to_baiduzone');
add_action('xmlrpc_public_post', 'publish_article_to_baiduzone');

function menu_add_wp2baiduzone_setting() 
{
	add_action( 'admin_init', 'register_wp2baiduzone_settings' );
	add_options_page('WP2BaiduZone Options', 'WP2BaiduZone', 'administrator', 'wp2baiduzone', 'wp2baiduzone_setting_page');
}

function register_wp2baiduzone_settings() 
{
	register_setting( 'WP2BaiduZone-Settings', 'wp2baiduzone_user' );
	register_setting( 'WP2BaiduZone-Settings', 'wp2baiduzone_password' );
	register_setting( 'WP2BaiduZone-Settings', 'wp2baiduzone_url' );
	register_setting( 'WP2BaiduZone-Settings', 'wp2baiduzone_blogpower' );
	register_setting( 'WP2BaiduZone-Settings', 'wp2baiduzone_isaddlink' );
    register_setting( 'WP2BaiduZone-Settings', 'wp2baiduzone_issync' );
}

function wp2baiduzone_setting_page() {

 if (!function_exists("curl_init"))
 {
?>

	<div class="wrap">
	<h2>很抱歉，您的服务器配置不支持cURL库，插件 WP2BaiduZone 无法正常工作，请禁用该插件。</h2><br />
	<h2>联系作者：<a href="mailto:zhourunsheng2008@gmail.com?subject=意见反馈（WP2BaiduZone）">意见反馈</a></h2><br />
	</div>

<?php
 }
 else
 {
?>

	<div class="wrap">
	<h2>WP2BaiduZone 配置选项</h2>

	<form method="post" action="options.php">
		<?php settings_fields( 'WP2BaiduZone-Settings' ); ?>
		<?php do_settings_sections('wp2baiduzone'); ?>

		<table class="form-table">
			<tr valign="top">
			<th scope="row">百度空间登录名</th>
			<td>
				<input name="wp2baiduzone_user" type="text" id="wp2baiduzone_user" value="<?php form_option('wp2baiduzone_user'); ?>" class="regular-text" />
			</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">百度空间登录密码</th>
			<td>
				<input name="wp2baiduzone_password" type="password" id="wp2baiduzone_password" value="<?php form_option('wp2baiduzone_password'); ?>" class="regular-text" />
			</td>
			</tr>

			<tr valign="top">
			<th scope="row">百度空间地址(URL)</th>
			<td>
				<input name="wp2baiduzone_url" type="text" id="wp2baiduzone_url" value="<?php form_option('wp2baiduzone_url'); ?>" class="regular-text" />
			</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">文章访问权限</th>
			<td>
				<input name="wp2baiduzone_blogpower" value="0" id="blogpower0" type="radio" <?php checked(0, get_option('wp2baiduzone_blogpower')); ?> >
				<label for="blogpower0">所有人可见</label>
				<input name="wp2baiduzone_blogpower" value="1" id="blogpower1" type="radio" <?php checked(1, get_option('wp2baiduzone_blogpower')); ?> >
				<label for="blogpower1">仅自己可见</label>
			</td>
			</tr>

			<tr valign="top">
			<th scope="row">原文链接设置</th>
			<td>
				<input name="wp2baiduzone_isaddlink" value="1" <?php checked(1, get_option('wp2baiduzone_isaddlink')); ?> id="wp2baiduzone_isaddlink" type="checkbox">
				<label for="wp2baiduzone_isaddlink">是否添加原文链接</label>
			</td>
			</tr>
		</table>

	  <p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	  </p>
	</form>

   <br/><b>说明：</b>一般百度空间的地址(URL)同百度空间用户名一致，如果不一样的话，可按如下规则来改：
   <br/>例如：百度空间地址为<a http://hi.baidu.com/runsheng2005">http://hi.baidu.com/runsheng2005</a>，则百度空间的地址(URL)应填写 <b>runsheng2005</b>

</div>
<?php
 }
}

function publish_article_to_baiduzone($post_ID)
{
    if (get_option('wp2baiduzone_issync') != 'yes') { //该文章不同步
        return $post_ID;
    }
    
    $post = get_post($post_ID);
	
	//new article
	if(($post->post_type == "post") && ($post->post_date == $post->post_modified))
	{
		//get title
		$title = $post->post_title;
		if(strlen($title) == 0)
		{
		   //no title
		   return $post_ID;
		}
		
		//get content
		$content = $post->post_content;
		if (strlen($content) == 0)
		{
			//no content
			return $post_ID;
		}
		
		//add link or not
		$isaddlink = get_option('wp2baiduzone_isaddlink');
		if ($isaddlink == 1)
		{
			$content .= "<br/><br/>文章来源：<a href=".get_permalink($post_ID).">".get_permalink($post_ID)."</a>";
		}
		//<p> content </p>
		$content = "<p>" . $content . "</p>";
		
		$blogpower = get_option('wp2baiduzone_blogpower');
		if($post->post_status == "private")
		{
			//私有文章，仅自己可见
			$blogpower = 1;
		}
		
		$categories = get_the_category($post_ID);
		if (is_array($categories) && count($categories) > 0)
		{
			$category = $categories[0]->cat_name;
		}
		else 
		{
			$category = "默认分类";
		}
		
		$username = get_option('wp2baiduzone_user');
		$password = get_option('wp2baiduzone_password');
		$blogurl = get_option('wp2baiduzone_url');
		
		//检查账户是否已设置
		if(strlen($username) > 3 && strlen($password) > 3) 
		{
			require_once(dirname(__FILE__).'/php/baiduzone.php');
			$baiduzone = new BaiduZone();
			$param = array(
				"user" => $username,
				"password" => $password,
				"baiduzoneurl" => $blogurl,
				"title" => $title,
				"content" => $content,
				"category" => $category,
				"blogpower" => $blogpower,
			);
			
			$baiduzone->send($param);
		}
	}
	
	return $post_ID;
}
?>
<?php
add_action('add_meta_boxes', 'wp2bz_add_custom_box');

function wp2bz_add_custom_box(){
	add_meta_box( 'wp2bz-meta_box','百度空间博文同步','wp2bz_box_set_sync', 'post', 'side', 'high');
}

function wp2bz_box_set_sync(){
	global $post;
    
    $username = get_option('wp2baiduzone_user');
	$password = get_option('wp2baiduzone_password');
    if(strlen($username) < 3 || strlen($password) < 3) {
         echo '<div class="misc-pub-section" style="line-height:18px;">';
         echo '<b>配置信息: </b><br/>';
         echo '您还未配置百度空间的帐号信息，如果要同步文章，请首先到 <a href="'.site_url('/wp-admin/options-general.php?page=wp2baiduzone').'">wp2baiduzone</a> 进行配置。';
         echo '</div>';
         return false;
    } else {
        echo '<div class="misc-pub-section" style="line-height:18px;">';
        echo '<b>配置信息: </b><br/>';
        echo '您百度空间的同步帐号为 <b>'.$username.'</b>';
        echo '</div>';
    }
    
    $synced = get_option( 'wp2baiduzone_issync', 'yes');
	$post_status = get_post_status($post->ID);
	switch($post_status){
		case 'publish':
                $status_info='这篇文章已经发布，默认不会同步。';$synced='no';
                break;
		case 'private':
                $status_info='这篇文章是私密文章，默认不会同步。';$synced='no';
                break;
		case 'future':
                $status_info='这篇文章是定时发布，将在设定的时间同步。';
                break;
		case 'auto-draft':
                $status_info='这篇文章是您新建的，点击“发布”将会同步到您的百度空间。';$synced='yes';
                break;
		case 'draft':
                $status_info='这篇文章是您之前保存的草稿，点击“发布”将会同步到您的百度空间。';
                break;
		case 'pending':
                $status_info='这篇文章等待复审，通过后将会根据选择情况进行同步';
		        break;
		default:
               $synced='yes';
               break;
	}
    
	if($status_info) {
       echo '<div class="misc-pub-section" style="line-height:18px;"><b>温馨提示: </b><br/>'.$status_info.'</div>';
    }
    
    echo '<div class="misc-pub-section" style="background:#EAF2FA;line-height:18px;">';
    echo '<b>同步到百度空间: </b><br/>';
    
    echo '<input type="radio" name="wp2baiduzone_issync" value="no"';
	if($synced == 'no') {
	   echo ' checked="checked"';
	}
	echo '/><label>不同步</label> <br/>';
    
	echo '<input type="radio" name="wp2baiduzone_issync" value="yes"';
	if($synced == 'yes') {
	   echo ' checked="checked"';
	}
	echo '/><label>同步</label> <br/>';
    echo '</div>';
	echo '<div class="clear"></div>';
}

/* Use the save_post action to do something with the data entered */
add_action('save_post', 'wp2bz_save_custom_box_data');

/* When the post is saved, saves our custom data */
function wp2bz_save_custom_box_data( $post_id ) {
    if (isset($_POST['wp2baiduzone_issync'])) {
        update_option( 'wp2baiduzone_issync', $_POST['wp2baiduzone_issync'] );
    }
}

?>