<?php
/*
Plugin Name: WP2HiBaidu
Plugin URI: http://www.starhai.net/wp2hibaidu
Description: 同步发表 WordPress 博客日志到 百度空间,初次安装必须设置后才能使用。
Version: 1.0.4
Author: Starhai
Author URI: http://starhai.net/
*/
/*  Copyright 2010~2011  Starhai   (email : i@starhai.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2.

*/
class baiduhi{
	public  $useragent="Starhai/1.0"; //定义要模拟的浏览器名称
	private $token="";
	private $ch;	//CURL对象句柄
	private $cookie;	//保存Cookie的临时文件
	private $data;	//临时数据保存地址
	public $sblog_class;
	public function login($blogurl,$user,$pass)
	{

		$d = tempnam('../tmp/', 'cookie.txt');  //创建随机临时文件保存cookie.
		$this->cookie=$d;
	    $ch = curl_init("http://hi.baidu.com");
	    $this->ch=$ch;
	    curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
	    curl_setopt($ch, CURLOPT_HEADER, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
	    curl_exec($ch);
	    curl_close($ch);
	    unset($this->ch);


	    $ch = curl_init($this->ch);
		$posturl="https://passport.baidu.com/?login";
		$post="username=".$user."&password=".$pass."&mem_pass=on";

	    curl_setopt($ch, CURLOPT_REFERER, "http://hi.baidu.com/index%2Ehtml");
	    curl_setopt($ch, CURLOPT_URL, $posturl);
		curl_setopt($ch, CURLOPT_POST, 1); // how many parameters to post
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    curl_setopt($ch, CURLOPT_HEADER, 1);
	    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	    curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
	    curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_exec($ch);
 		curl_close($ch);
		unset($this->ch);

		//以上完成登录。
		
		$ch = curl_init($this->ch);
 		$creaturl="http://hi.baidu.com/".$blogurl."/creat/blog/";
 		$reff="http://hi.baidu.com/".$blogurl."/ihome/myblog";
	    curl_setopt($ch, CURLOPT_URL, $creaturl);
	    curl_setopt($ch, CURLOPT_REFERER,$reff);
	    curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 1);
	    curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
	   	$data= curl_exec($ch);
		curl_close($ch);
	   	preg_match_all( "/name=\"bdstoken\" value=\"(.*?)\"\>/s",$data, $tokens );
	   	$this->token=$tokens[1][0];
		unset($this->ch);



	}


public function send($blogurl,$title,$content,$x_rank,$x_cms_flag,$x_shr_flag)
	{

		$creaturl="http://hi.baidu.com/".$blogurl."/creat/blog/";

		$posturl="http://hi.baidu.com/".$blogurl."/commit";
		$post="bdstoken=".urlencode($this->token)."&ct=1&cm=1&spBlogID=&spBlogCatName_o=&edithid=&spBlogTitle=".urlencode($title)."&spBlogText=".$content."&spBlogCatName=%C4%AC%C8%CF%B7%D6%C0%E0&spBlogPower=".urlencode($x_rank)."&spIsCmtAllow=".urlencode($x_cms_flag)."&spShareNotAllow=".urlencode($x_shr_flag)."&spIsCmtAllowObj=on&spShareNotAllowObj=on&spVcode=&spVerifyKey=";
		$ch = curl_init($this->ch);
   		curl_setopt($ch, CURLOPT_URL, $posturl);
		curl_setopt($ch, CURLOPT_POST, 1); // how many parameters to post
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_REFERER, $creaturl);
		curl_setopt($ch, CURLOPT_COOKIEJAR,  $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_exec($ch);
		curl_close($ch);
		unset($this->ch);
	}

	public function logoff()
	{
		unset($this->ch);
		unlink($this->cookie);
	}

}
?>
<?php
// Hook for adding admin menus
add_action('admin_menu', 'mt_add_baidu_pages');
add_action('publish_post', 'publish_post_2_hibaidu');
add_action('xmlrpc_public_post', 'publish_post_2_hibaidu');
// action function for above hook
function mt_add_baidu_pages() {
    //call register settings function
	add_action( 'admin_init', 'register_wpbaidu_settings' );
	// Add a new submenu under Options:
    add_options_page('WP2Baidu Options', 'WP2HiBaidu', 'administrator', 'wpbaidu', 'mt_wpbaidu_page');



}

function register_wpbaidu_settings() {
	//register our settings
	register_setting( 'WP2Baidu-settings-group', 'wp2baiduuser' );
	register_setting( 'WP2Baidu-settings-group', 'wp2baidupass' );
	register_setting( 'WP2Baidu-settings-group', 'wp2baiduhiurl' );
	register_setting( 'WP2Baidu-settings-group', 'baidu_sdurl' );
	register_setting( 'WP2Baidu-settings-group', 'baidu_xmsfg' );
	register_setting( 'WP2Baidu-settings-group', 'baidu_xrank' );
	register_setting( 'WP2Baidu-settings-group', 'baidu_share' );

}


// mt_options_page() displays the page content for the Test Options submenu
function mt_wpbaidu_page() {

 if (!function_exists("curl_init"))
 {
?>

<div class="wrap">
<h2>您的服务器不支持cURL库，插件WP2HiBaidu无法工作，请禁用该插件。</h2><br />
</div>

<?php
 }
 else
 {

?>
<div class="wrap">
<h2>WP2HiBaidu 选项</h2>
设置仅适用于百度空间，不支持Wordpress中<b>private</b>属性的文章发布到百度空间。

<br/><br/>
<form method="post" action="options.php">

  <?php settings_fields( 'WP2Baidu-settings-group' ); ?>
   <table class="form-table">
   		<tr valign="top">
        <th scope="row">百度的登录名</th>
        <td>
			<input name="wp2baiduuser" type="text" id="wp2baiduuser" value="<?php form_option('wp2baiduuser'); ?>" class="regular-text" />

		</td>
		</tr>
		<tr valign="top">
        <th scope="row">百度的登录密码</th>
        <td>
			<input name="wp2baidupass" type="password" id="wp2baiduuser" value="<?php form_option('wp2baidupass'); ?>" class="regular-text" />

		</td>

		</tr>

		<tr valign="top">
        <th scope="row">百度空间的UR</th>
        <td>
			<input name="wp2baiduhiurl" type="text" id="wp2baiduhiurl" value="<?php form_option('wp2baiduhiurl'); ?>" class="regular-text" />
		</td>

		</tr>
		 <tr valign="top">
        <th scope="row">评论权限设置</th>
        <td>

			<input name="baidu_xmsfg"  value="1" <?php checked(1, get_option('baidu_xmsfg')); ?> id="commentRadio1" type="radio">
			<label for="commentRadio1">允许评论</label>
			<input name="baidu_xmsfg" value="0" <?php checked(0, get_option('baidu_xmsfg')); ?> id="commentRadio3" type="radio">
			<label for="commentRadio3">禁止评论</label>
		</td>
		</tr>

		 <tr valign="top">
        <th scope="row">文章访问权限</th>
        <td>
			<input name="baidu_xrank" value="0" id="xRankRadio3" type="radio" <?php checked(0, get_option('baidu_xrank')); ?> >
			<label for="xRankRadio3" style="margin-right: 28px;">公开</label>
			<input name="baidu_xrank" value="1" id="xRankRadio4" type="radio" <?php checked(1, get_option('baidu_xrank')); ?> >
			<label for="xRankRadio4">仅向好友开放见</label>
			<input name="baidu_xrank" value="3" id="xRankRadio5" type="radio" <?php checked(3, get_option('baidu_xrank')); ?> >
			<label for="xRankRadio5">仅自己可见</label>

		</td>
        </tr>
       <tr valign="top">
        <th scope="row">转载权限设置</th>
        <td>

			<input name="baidu_share"  value="0" <?php checked(0, get_option('baidu_share')); ?> id="baidu_shareo1" type="radio">
			<label for="baidu_shareo1">允许转载</label>
			<input name="baidu_share" value="1" <?php checked(1, get_option('baidu_share')); ?> id="baidu_shareo3" type="radio">
			<label for="baidu_shareo3">禁止转载</label>
		</td>
		</tr>

		 <tr valign="top">
        <th scope="row">原文链接设置</th>
        <td>

			<input name="baidu_sdurl"  value="0" <?php checked(0, get_option('baidu_sdurl')); ?> id="cwp2baidusdurl1" type="radio">
			<label for="cwp2baidusdurl1">不发送</label>
			<input name="baidu_sdurl" value="1" <?php checked(1, get_option('baidu_sdurl')); ?> id="cwp2baidusdurl2" type="radio">
			<label for="cwp2baidusdurl2">发送（链接在文章头部)</label>
			<input name="baidu_sdurl" value="2" <?php checked(2, get_option('baidu_sdurl')); ?> id="cwp2baidusdurl3" type="radio">
			<label for="cwp2baidusdurl3">发送（链接在文章尾部)</label>
		</td>
		</tr>

    </table>

  <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
<br/>说明：1.如百度空间地址为<a href="http://hi.baidu.com/%D0%C7%BA%A3%B2%A9%BF%CD">http://hi.baidu.com/%D0%C7%BA%A3%B2%A9%BF%CD</a>
<br/>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;则百度空间的UR应填写<b>%D0%C7%BA%A3%B2%A9%BF%CD</b>
</div>
<?php
 }
}



function publish_post_2_hibaidu($post_ID){

	$post=get_post($post_ID);
	$status=$post->post_status;
	if($post->post_date==$post->post_modified)
	{
		if($post->post_type =="post")
		{


				$title=$post->post_title;
				if (strlen($title)==0)
					{$title="无题  ";}
				$content=$post->post_content;
				$sendurl=get_option('baidu_sdurl');
				if ($sendurl==1)
				{
					$content="查看原文：<a href=".get_permalink($post_ID).">".get_permalink($post_ID)."</a><br/>".$content;
				}
				elseif($sendurl==2)
				{
					$content.="<br/>查看原文：<a href=".get_permalink($post_ID).">".get_permalink($post_ID)."</a>";
				}
				else
				{

					if (strlen($content)==0)
					{$content="a blank ";}
				}

				$x_rank=get_option('baidu_xrank');
				//$catlog=get_option('xs_blog_class');
				//文章是否公开
				if($status=="private")
				{
					$x_rank=1;
					$catlog=0;
				}
				$x_cms_flag=get_option('baidu_xmsfg');

				$x_shr_flag=get_option('baidu_share');


				$wp2baiduuser=get_option('wp2baiduuser');
				$wp2baidupass=get_option('wp2baidupass');
				$wp2baiduhiurl=get_option('wp2baiduhiurl');
				if (strlen($wp2baiduuser)>1)
				{
					if (strlen($wp2baidupass)>3)
					{
							if(!function_exists('iconv'))
							{
								require_once(dirname(__FILE__).'/iconv.php');
							}

					$user=urlencode(iconv('utf-8', 'GBK', $wp2baiduuser));
					$pass=urlencode(iconv('utf-8', 'GBK', $wp2baidupass));
					$blogurl=$wp2baiduhiurl;
					$title=iconv('utf-8', 'GBK', $title);
					$content=iconv('utf-8', 'GBK', $content);
					$content=urlencode($content);
					$arr = array("%0D%0A" => "<br />");
					$content= strtr($content,$arr);					
					$blog=new baiduhi();
					$blog->login($blogurl,$user,$pass);
					$blog->send($blogurl,$title,$content,$x_rank,$x_cms_flag,$x_shr_flag);
					$blog->logoff();
					}
				}

		}
	}
}
?>