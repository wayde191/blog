<?php
/*
Plugin Name: WP2tianya
Plugin URI: http://www.hostscheme.com/wp2tianya
Description: 同步发表 WordPress 博客日志到 天涯博客,初次安装必须设置后才能使用。
Version: 1.0.0
Author: godmagic
Author URI: http://www.hostscheme.com/
*/
/*  Copyright 2010~2011  godmagic   (email : godmagic@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2.

*/
class tianya{
	public  $useragent="Hostscheme/1.0"; //定义要模拟的浏览器名称
	private $token="";
	private $thisch;	//CURL对象句柄
	private $thiscookie;	//保存Cookie的临时文件
	private $jscookies="";	//保存js设置的Cookies
	private $data;	//临时数据保存地址
	public $sblog_class;
	public function login($user,$pass)
	{

		$d = tempnam('../tmp/', 'cookie.txt');  //创建随机临时文件保存cookie.
		$this->thiscookie=$d;
		$ch = curl_init();
		$posturl="http://passport.tianya.cn:80/login";
		$post="vwriter=".$user."&vpassword=".$pass."&rmflag=1&fowardURL=http%3A%2F%2Fmy.tianya.cn&returnURL=&from=&Submit=%E7%99%BB%E5%BD%95";		
		curl_setopt($ch, CURLOPT_REFERER, "http://focus.tianya.cn/");
		curl_setopt($ch, CURLOPT_URL, $posturl);
		curl_setopt($ch, CURLOPT_POST, 1); // how many parameters to post
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->thiscookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->thiscookie);
		$data= curl_exec($ch);
		curl_close($ch);
		//unset($this->thisch);
		
		 preg_match_all( "/document.cookie=\'(.*?);expires=/s",$data, $cookies );
		  $times=floor(time()/1000);
		  if ($cookies[1]) {
			  foreach($cookies[1] as $cookie) {
				  $cookie = str_replace("'+parseInt(new Date().getTime()/1000)+'", $times, $cookie);
				  $cookie = str_replace("'+(parseInt(new Date().getTime()/1000)+2592000)+'", $times+2592000, $cookie);
				  $this->jscookies = $this->jscookies . ";" . $cookie;
			  }
		  }
		if($this->jscookies){
			$this->jscookies=substr($this->jscookies,1);
		}
		  //以上完成登录。

	}


	public function send($title,$content)
	{
		$ch = curl_init($this->thisch);
		$geturl="http://blog.tianya.cn:80/myblog/mybloglist.asp?var=oBlogCatalog&_r=";
		$reff="http://my.tianya.cn/";
		curl_setopt($ch, CURLOPT_URL, $geturl);
		curl_setopt($ch, CURLOPT_REFERER,$reff);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->thiscookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIE, $this->jscookies);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->thiscookie);
		$data= curl_exec($ch);
		curl_close($ch);
		preg_match_all( "/\"default\":(.*?),/s",$data, $tokens );
		$token=$tokens[1][0];
		//unset($this->thisch);

		$refer="http://blog.tianya.cn/includes/twitterProxy.html";
		$posturl="http://blog.tianya.cn:80/myblog/weibo_post.asp?act=postBlog";
		$post="act=postBlog&content=%3Cp%3E".$content."%3C/p%3E&title=".$title."&blogid=".urlencode($token)."";
		
		$ch = curl_init($this->thisch);
		curl_setopt($ch, CURLOPT_URL, $posturl);
		curl_setopt($ch, CURLOPT_POST, 1); // how many parameters to post
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_REFERER, $refer);
		curl_setopt($ch, CURLOPT_COOKIEJAR,  $this->thiscookie);
		curl_setopt($ch, CURLOPT_COOKIE, $this->jscookies);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->thiscookie);
		curl_setopt($ch, CURLOPT_HEADER, 1);
//		curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
//		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		$data= curl_exec($ch);
		//print($data);
		unset($this->thisch);
	}

	public function logoff()
	{
		unset($this->thisch);
		unlink($this->cookie);
	}

}
?>
<?php
// Hook for adding admin menus
add_action('admin_menu', 'mt_add_tianya_pages');
add_action('publish_post', 'publish_post_2_tianya');
add_action('xmlrpc_public_post', 'publish_post_2_tianya');
// action function for above hook
function mt_add_tianya_pages() {
    //call register settings function
	add_action( 'admin_init', 'register_wptianya_settings' );
	// Add a new submenu under Options:
    add_options_page('WP2Tianya Options', 'WP2Tianya', 'administrator', 'wptianya', 'mt_wptianya_page');



}

function register_wptianya_settings() {
	//register our settings
	register_setting( 'WP2Tianya-settings-group', 'wp2tianyauser' );
	register_setting( 'WP2Tianya-settings-group', 'wp2tianyapass' );
	register_setting( 'WP2Tianya-settings-group', 'tianya_sdurl' );
	
}


// mt_options_page() displays the page content for the Test Options submenu
function mt_wptianya_page() {

 if (!function_exists("curl_init"))
 {
?>

<div class="wrap">
<h2>您的服务器不支持cURL库，插件WP2Tianya无法工作，请禁用该插件。</h2><br />
</div>

<?php
 }
 else
 {

?>
<div class="wrap">
<h2>WP2Tianya 选项</h2>
设置仅适用于天涯博客，不支持Wordpress中<b>private</b>属性的文章发布到天涯博客。

<br/><br/>
<form method="post" action="options.php">

  <?php settings_fields( 'WP2Tianya-settings-group' ); ?>
   <table class="form-table">
   		<tr valign="top">
        <th scope="row">天涯博客的登录名</th>
        <td>
			<input name="wp2tianyauser" type="text" id="wp2tianyauser" value="<?php form_option('wp2tianyauser'); ?>" class="regular-text" />

		</td>
		</tr>
		<tr valign="top">
        <th scope="row">天涯博客的登录密码</th>
        <td>
			<input name="wp2tianyapass" type="password" id="wp2tianyauser" value="<?php form_option('wp2tianyapass'); ?>" class="regular-text" />

		</td>

		</tr>

		 <tr valign="top">
        <th scope="row">原文链接设置</th>
        <td>

			<input name="tianya_sdurl"  value="0" <?php checked(0, get_option('tianya_sdurl')); ?> id="cwp2tianyasdurl1" type="radio">
			<label for="cwp2tianyasdurl1">不发送</label>
			<input name="tianya_sdurl" value="1" <?php checked(1, get_option('tianya_sdurl')); ?> id="cwp2tianyasdurl2" type="radio">
			<label for="cwp2tianyasdurl2">发送（链接在文章头部)</label>
			<input name="tianya_sdurl" value="2" <?php checked(2, get_option('tianya_sdurl')); ?> id="cwp2tianyasdurl3" type="radio">
			<label for="cwp2tianyasdurl3">发送（链接在文章尾部)</label>
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
}



function publish_post_2_tianya($post_ID){

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
				$sendurl=get_option('tianya_sdurl');
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
				
				$tags="";
				$posttags = get_the_tags();
				if ($posttags) {
				  foreach($posttags as $tag) {
				    $tags=$tags . ',' . $tag->name ; 
				  }
				}
				if($tags){
					$tags=substr($tags,1);
				}
				//echo "tags:" . $tags;
				
				$wp2tianyauser=get_option('wp2tianyauser');
				$wp2tianyapass=get_option('wp2tianyapass');
				if (strlen($wp2tianyauser)>1)
				{
					if (strlen($wp2tianyapass)>3)
					{
							if(!function_exists('iconv'))
							{
								require_once(dirname(__FILE__).'/iconv.php');
							}

					$user=$wp2tianyauser;
					$pass=$wp2tianyapass;
					$title=iconv('utf-8', 'GBK//IGNORE', $title);
					$content=iconv('utf-8', 'GBK//IGNORE', $content);
					$content=urlencode($content);
					$title=urlencode($title);
					$title = str_replace("+", "%20", $title);
					$content = str_replace("+", "%20", $content);
					$arr = array("%0D%0A" => "<br />");
					$content= strtr($content,$arr);
					
					$blog=new tianya();
					$blog->login($user,$pass);
					$blog->send($title,$content);
					$blog->logoff();
					}
				}

		}
	}
}
?>