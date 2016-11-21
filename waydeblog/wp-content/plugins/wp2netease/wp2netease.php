<?php
/*
Plugin Name: WP2netease
Plugin URI: http://www.hostscheme.com/wp2netease
Description: 同步发表 WordPress 博客日志到 网易博客,初次安装必须设置后才能使用。
Version: 1.1.0
Author: godmagic
Author URI: http://www.hostscheme.com/
*/
/*  Copyright 2010~2011  godmagic   (email : godmagic@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2.

*/
class netease{
	public  $useragent="Hostscheme/1.0"; //定义要模拟的浏览器名称
	private $token="";
	private $thisch;	//CURL对象句柄
	private $cookie;	//保存Cookie的临时文件
	private $data;	//临时数据保存地址
	public $sblog_class;
	public function login($blogurl,$user,$pass)
	{

		$d = tempnam('../tmp/', 'cookie.txt');  //创建随机临时文件保存cookie.
		$this->cookie=$d;
		$ch = curl_init("http://blog.163.com");
		$this->thisch=$ch;
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		$data= curl_exec($ch);
		curl_close($ch);
		//unset($this->thisch);


		$ch = curl_init($this->thisch);
		$posturl="https://reg.163.com:443/logins.jsp";
		$post="savelogin=0"."&username=".$user."&password=".$pass."&url=http://blog.163.com/loginGate.do?blogActivation=true&from=login&type=1&product=blog";
		
		curl_setopt($ch, CURLOPT_REFERER, "http://blog.163.com");
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
		$data= curl_exec($ch);
		curl_close($ch);
		//unset($this->thisch);
		
		//以上完成登录。

	}


	public function send($tags,$blogurl,$title,$content,$allowview,$msyn)
	{
		$ch = curl_init($this->thisch);
		$creaturl="http://".$blogurl.".blog.163.com/blog/getBlog.do?fromString=bloglist";
		$reff="http://".$blogurl.".blog.163.com/blog/";
		curl_setopt($ch, CURLOPT_URL, $creaturl);
		curl_setopt($ch, CURLOPT_REFERER,$reff);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		$data= curl_exec($ch);
		curl_close($ch);
		preg_match_all( "/name=\"NETEASE_BLOG_TOKEN_EDITBLOG\" value=\"(.*?)\"\/\>/s",$data, $tokens );
		$token=$tokens[1][0];
		preg_match_all( "/c:\[{id:\'(.*?)\',/s",$data, $clss );
		$cls=$clss[1][0];
		//unset($this->thisch);

		$refer="http://api.blog.163.com/crossdomain.html?t=20100205";

		$posturl="http://api.blog.163.com/".$blogurl."/editBlogNew.do?p=1&n=1&from=bloglist";
		$post="tag=".urlencode($tags)."&cls=".urlencode($cls)."&allowview=".$allowview."&refurl=&abstract=&bid=&origClassId=&origPublishState=&oldtitle=&todayPublishedCount=0&NETEASE_BLOG_TOKEN_EDITBLOG=".urlencode($token)."&title=".$title."&HEContent=".$content."&copyPhotos=&suggestedSortedIds=&suggestedRecomCnt=&suggestedStyle=0&isSuggestedEachOther=0&photoBookImgUrl=&msyn=".$msyn."&miniBlogCard=0&p=1";
		$ch = curl_init($this->thisch);
		curl_setopt($ch, CURLOPT_URL, $posturl);
		curl_setopt($ch, CURLOPT_POST, 1); // how many parameters to post
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_REFERER, $refer);
		curl_setopt($ch, CURLOPT_COOKIEJAR,  $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_HEADER, 1);
//		curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
//		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		$data= curl_exec($ch);
		curl_close($ch);
		preg_match_all( "/\',sfx:\'(.*?)\'}/s",$data, $nexturls );
		$nexturl=$nexturls[1][0];

		$ch = curl_init("http://".$blogurl.".blog.163.com/".$nexturl);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		$data= curl_exec($ch);
		curl_close($ch);
		
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
add_action('admin_menu', 'mt_add_netease_pages');
add_action('publish_post', 'publish_post_2_netease');
add_action('xmlrpc_public_post', 'publish_post_2_netease');
// action function for above hook
function mt_add_netease_pages() {
    //call register settings function
	add_action( 'admin_init', 'register_wpnetease_settings' );
	// Add a new submenu under Options:
    add_options_page('WP2Netease Options', 'WP2Netease', 'administrator', 'wpnetease', 'mt_wpnetease_page');



}

function register_wpnetease_settings() {
	//register our settings
	register_setting( 'WP2Netease-settings-group', 'wp2neteaseuser' );
	register_setting( 'WP2Netease-settings-group', 'wp2neteasepass' );
	register_setting( 'WP2Netease-settings-group', 'wp2neteaseurl' );
	register_setting( 'WP2Netease-settings-group', 'netease_sdurl' );
	register_setting( 'WP2Netease-settings-group', 'netease_allowview' );
	register_setting( 'WP2Netease-settings-group', 'netease_msyn' );
	
}


// mt_options_page() displays the page content for the Test Options submenu
function mt_wpnetease_page() {

 if (!function_exists("curl_init"))
 {
?>

<div class="wrap">
<h2>您的服务器不支持cURL库，插件WP2Netease无法工作，请禁用该插件。</h2><br />
</div>

<?php
 }
 else
 {

?>
<div class="wrap">
<h2>WP2Netease 选项</h2>
设置仅适用于网易博客，不支持Wordpress中<b>private</b>属性的文章发布到网易博客。

<br/><br/>
<form method="post" action="options.php">

  <?php settings_fields( 'WP2Netease-settings-group' ); ?>
   <table class="form-table">
   		<tr valign="top">
        <th scope="row">网易博客的登录名</th>
        <td>
			<input name="wp2neteaseuser" type="text" id="wp2neteaseuser" value="<?php form_option('wp2neteaseuser'); ?>" class="regular-text" />

		</td>
		</tr>
		<tr valign="top">
        <th scope="row">网易博客的登录密码</th>
        <td>
			<input name="wp2neteasepass" type="password" id="wp2neteaseuser" value="<?php form_option('wp2neteasepass'); ?>" class="regular-text" />

		</td>

		</tr>

		<tr valign="top">
        <th scope="row">网易博客的UR</th>
        <td>
			<input name="wp2neteaseurl" type="text" id="wp2neteaseurl" value="<?php form_option('wp2neteaseurl'); ?>" class="regular-text" />
		</td>

		</tr>

		 <tr valign="top">
        <th scope="row">文章访问权限</th>
        <td>
			<input name="netease_allowview" value="-100" id="xRankRadio3" type="radio" <?php checked(-100, get_option('netease_allowview')); ?> >
			<label for="xRankRadio3" style="margin-right: 28px;">公开</label>
			<input name="netease_allowview" value="10000" id="xRankRadio5" type="radio" <?php checked(10000, get_option('netease_allowview')); ?> >
			<label for="xRankRadio5">仅自己可见</label>

		</td>
        </tr>
		 <tr valign="top">
        <th scope="row">是否同时发往网易微博</th>
        <td>
			<input name="netease_msyn" value="0" id="xRankRadio3" type="radio" <?php checked(0, get_option('netease_msyn')); ?> >
			<label for="xRankRadio3" style="margin-right: 28px;">是</label>
			<input name="netease_msyn" value="1" id="xRankRadio5" type="radio" <?php checked(1, get_option('netease_msyn')); ?> >
			<label for="xRankRadio5">否</label>

		</td>
        </tr>
		 <tr valign="top">
        <th scope="row">原文链接设置</th>
        <td>

			<input name="netease_sdurl"  value="0" <?php checked(0, get_option('netease_sdurl')); ?> id="cwp2neteasesdurl1" type="radio">
			<label for="cwp2neteasesdurl1">不发送</label>
			<input name="netease_sdurl" value="1" <?php checked(1, get_option('netease_sdurl')); ?> id="cwp2neteasesdurl2" type="radio">
			<label for="cwp2neteasesdurl2">发送（链接在文章头部)</label>
			<input name="netease_sdurl" value="2" <?php checked(2, get_option('netease_sdurl')); ?> id="cwp2neteasesdurl3" type="radio">
			<label for="cwp2neteasesdurl3">发送（链接在文章尾部)</label>
		</td>
		</tr>

    </table>

  <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
<br/>说明：1.如网易博客地址为<a href="http://hostscheme.blog.163.com/blog">http://hostscheme.blog.163.com/blog</a>
<br/>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;则网易博客的UR应填写<b>hostscheme</b>，如果为中文请使用urlencode
<br/>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.网易博客限制发文频率，频率高了以后会出现验证码。如果同步不成功，可能是频率太高所致。
</div>
<?php
 }
}



function publish_post_2_netease($post_ID){

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
				$sendurl=get_option('netease_sdurl');
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
				if(tags){
					$tags=substr($tags,1);
				}
				//echo "tags:" . $tags;
				
				$allowview=get_option('netease_allowview');
				$msyn=get_option('netease_msyn');
				//文章是否公开
//				if($status=="private")
//				{
//					$x_rank=10000;
//				}else{
//					$x_rank=100;
//				}

				$wp2neteaseuser=get_option('wp2neteaseuser');
				$wp2neteasepass=get_option('wp2neteasepass');
				$wp2neteaseurl=get_option('wp2neteaseurl');
				if (strlen($wp2neteaseuser)>1)
				{
					if (strlen($wp2neteasepass)>3)
					{
							if(!function_exists('iconv'))
							{
								require_once(dirname(__FILE__).'/iconv.php');
							}

					$user=$wp2neteaseuser;
					$pass=$wp2neteasepass;
					$blogurl=$wp2neteaseurl;
					//$title=iconv('utf-8', 'GBK//IGNORE', $title);
					//$content=iconv('utf-8', 'GBK//IGNORE', $content);
					$content=urlencode($content);
					$title=urlencode($title);
					$title = str_replace("+", "%20", $title);
					$content = str_replace("+", "%20", $content);
					$arr = array("%0D%0A" => "<br />");
					$content= strtr($content,$arr);
					
					$blog=new netease();
					$blog->login($blogurl,$user,$pass);
					$blog->send($tags,$blogurl,$title,$content,$allowview,$msyn);
					$blog->logoff();
					}
				}

		}
	}
}
?>