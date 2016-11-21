<?php
/*
Plugin Name: WP2Sinablog
Plugin URI: http://wpto.starhai.net/
Description: 同步发表 WordPress 博客日志到 新浪博客,初次安装必须设置后才能使用。
Version: 2.0.1
Author: Starhai
Author URI: http://wpto.starhai.net/
*/
/*  Copyright 2010-2012  Starhai   (email : i@starhai.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2.

*/
include_once(ABSPATH.'/wp-includes/class-IXR.php');
include_once("class-wp2sinablog.php");

// Hook for adding admin menus
add_action('admin_menu', 'mt_add_sinapages');
add_action('publish_post', 'publish_post_2_sinablog');
add_action('xmlrpc_public_post', 'publish_post_2_sinablog');
//add_action('future_to_publish ','future_publish_post_2_sinablog');

// action function for above hook
function mt_add_sinapages() {
    //call register settings function
	add_action( 'admin_init', 'register_mysettings' );
	// Add a new submenu under Options:
    add_options_page('WP2Sinablog Options', 'WP2Sinablog', 'administrator', 'wpsinablog', 'mt_options_page');


}

function register_mysettings() {
	//register our settings
	register_setting( 'WP2Sinablog-settings-group', 'wp2sinabloguser' );
	register_setting( 'WP2Sinablog-settings-group', 'wp2sinablogpass' );
	register_setting( 'WP2Sinablog-settings-group', 'wp2sinaxsend_url' );
    register_setting( 'WP2Sinablog-settings-group', 'wp2sina_blog_class' );
}


// mt_options_page() displays the page content for the Test Options submenu
function mt_options_page() {


?>


<div class="wrap">
<h2>WP2Sinablog 选项</h2><br />
设置仅适用于新浪博客，不支持Wordpress中<b>private</b>属性的文章发布到新浪博客。
<br/><br/>
<form method="post" action="options.php">

  <?php settings_fields( 'WP2Sinablog-settings-group' ); ?>
   <table class="form-table">
   		<tr valign="top">
        <th scope="row">新浪博客的登录名</th>
        <td>
			<input name="wp2sinabloguser" type="text" id="wp2sinabloguser" value="<?php form_option('wp2sinabloguser'); ?>" class="regular-text" />
		</td>
		</tr>
		<tr valign="top">
        <th scope="row">新浪博客的登录密码</th>
        <td>
			<input name="wp2sinablogpass" type="password" id="wp2sinabloguser" value="<?php form_option('wp2sinablogpass'); ?>" class="regular-text" />
		</td>
		</tr>
		<tr valign="top">
        <th scope="row">发布文件目录设置</th>
        <td>
			<?php
				$wp2sinabloguser=get_option('wp2sinabloguser');
				$wp2sinablogpass=get_option('wp2sinablogpass');
				if (strlen($wp2sinabloguser)>3)
				{
					if (strlen($wp2sinablogpass)>3)
					{
					$client->debug = false; 	  //开发测试时设置为true，api完成上线后改为false
 
 					$xmlclient = "http://upload.move.blog.sina.com.cn/blog_rebuild/blog/xmlrpc.php";
					$client = new IXR_Client($xmlclient);
					 $params = array(0,$wp2sinabloguser,$wp2sinablogpass);
					 if (!$client->query("metaWeblog.getCategories", $params)) 
					 {
					 	?>
						 <font color="red">尝试登录新浪博客失败，请检查用户名/密码是否正确!</font></b>
						<?php
					 
					 } 
					 else
					 {
					 	$catarrays=$client->getResponse(); 
					 	if (count($catarrays)>0)
					 	{
					 		foreach($catarrays as $catarray)
					 		{
					 			$catarrayid=$catarray['categoryId']; 
					 			$catarrayname=$catarray['categoryName'];
					 			?>
					 		 <input name="wp2sina_blog_class" value="<?php echo $catarrayname; ?>" id="componentSelect<?php echo $catarrayid; ?>" type="radio" <?php checked($catarrayname, get_option('wp2sina_blog_class')); ?> >
						     <label for="componentSelect<?php echo $catarrayid; ?>"><?php echo $catarrayname; ?></label>
						<?php
					 		}
						}
						else
						{
					 		echo "您的新浪博客只存在默认目录。";
					 	}	
					 					
					 }
					 unset($client);
					}					
				}
			?>

		</td>
		</tr>
          <tr valign="top">
        <th scope="row">原文链接设置</th>
        <td>

			<input name="wp2sinaxsend_url"  value="0" <?php checked(0, get_option('wp2sinaxsend_url')); ?> id="cwp2sinaxsend_url1" type="radio">
			<label for="cwp2sinaxsend_url1">不发送</label>
			<input name="wp2sinaxsend_url" value="1" <?php checked(1, get_option('wp2sinaxsend_url')); ?> id="cwp2sinaxsend_url2" type="radio">
			<label for="cwp2sinaxsend_url2">发送（链接在文章头部)</label>
			<input name="wp2sinaxsend_url" value="2" <?php checked(2, get_option('wp2sinaxsend_url')); ?> id="cwp2sinaxsend_url3" type="radio">
			<label for="cwp2sinaxsend_url3">发送（链接在文章尾部)</label>
		</td>
		</tr>

    </table>

  <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>

</div>
<?php

}

?>