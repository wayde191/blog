<?php

function make_newpost($post)
{
	$title=$post->post_title;
	if (strlen($title)==0){$title="无题  ";}
	
	$content=$post->post_content;	
	$sendurl=get_option('wp2sinaxsend_url');
	if ($sendurl==1)
		{
			$content="查看原文：<a href=".get_permalink($post->ID).">".get_permalink($post->ID)."</a><br/>".$content;
		}
	elseif($sendurl==2)
		{
			$content.="<br/><br/>查看原文：<a href=".get_permalink($post->ID).">".get_permalink($post->ID)."</a>";
		}
	else
		{	

			if (strlen($content)==0){$content="a blank ";}
		}
		
	$wp2sinabloguser=get_option('wp2sinabloguser');
	$wp2sinablogpass=get_option('wp2sinablogpass');
	if (strlen($wp2sinabloguser)>4)
		{
			if (strlen($wp2sinablogpass)>5)
				{
					$client->debug = false; 
					$xmlclient = "http://upload.move.blog.sina.com.cn/blog_rebuild/blog/xmlrpc.php";
					$client = new IXR_Client($xmlclient);
					
					$content= wp_richedit_pre($content);
					$content=htmlspecialchars_decode($content);
					
				    $catlog=get_option('wp2sina_blog_class');				 
				    $catlog= array($catlog);
					
					$post1=array('title'=>$title,'description'=>$content,'categories'=>$catlog);
					$params = array(1,$wp2sinabloguser,$wp2sinablogpass,$post1,true); // Last parameter is 'true' which means post immideately, to save as draft set it as 'false'
					
					$client->query('metaWeblog.newPost', $params);
					$wp2sinaid=$client->getResponse();
					add_post_meta($post->ID,'_wp2sinaid',$wp2sinaid,true);
					unset($client);
				}
		}

}
function publish_post_2_sinablog($post_ID){

	$post=get_post($post_ID);
	$status=$post->post_status;
	if($post->post_type =="post")
	{
		if($status == "publish")
		{
			$wpsinaid=get_post_meta($post_ID,'_wp2sinaid',true);
			if (strlen($wpsinaid)<4)
			{
		  		make_newpost($post);
			}
	    }
	}
}


?>