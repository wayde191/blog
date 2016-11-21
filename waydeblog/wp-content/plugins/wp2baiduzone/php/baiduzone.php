<?php
/*  
   Copyright 2011  Carey Chow (email : zhourunsheng2008@gmail.com)
    
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
class BaiduZone {
	private $bdstoken="";
	private $cookie = "";
	
	public function send($param)
	{		
		$user = $param['user'];
		$password = $param['password'];
		$baiduzoneurl = $param['baiduzoneurl']; 
		$title = $param['title']; 
		$content = $param['content'];
		$category = $param['category']; 
		$blogpower = $param['blogpower'];
		
		//replace "\n" to "<br />"
		$content = str_replace(array("\r\n", "\n", "\r"), "<br />", $content);
		
		//login baidu zone
		$this->loginBaiduZone($user,$password);
		
		//get baidu info
		$this->getBaiduInfo();
		
		//post article
		$this->postArticle($baiduzoneurl, $title, $content, $category, $blogpower);
	}
	
	private function loginBaiduZone($user,$password)
	{
		//get Cookie
		$ret = $this->doHttpPost("https://passport.baidu.com/v2/api/?getapi&class=login&tpl=mn&tangram=false", "", "");
		
		//get token
		$ret = $this->doHttpPost("https://passport.baidu.com/v2/api/?getapi&class=login&tpl=mn&tangram=false", "", "");		
		preg_match_all('/login_token=\'(.+)\'/', $ret, $tokens);
		$login_token = $tokens[1][0];
		
		//login		
		$post_data = array();
		$post_data['username'] = $user;
		$post_data['password'] = $password;
		$post_data['token'] = $login_token;
		$post_data['charset'] = "UTF-8";
		$post_data['callback'] = "parent.bd12Pass.api.login._postCallback";
		$post_data['index'] = "0";
		$post_data['isPhone'] = "false";
		$post_data['mem_pass'] = "on";
		$post_data['loginType'] = "1";
		$post_data['safeflg'] = "0";
		$post_data['staticpage'] = "https://passport.baidu.com/v2Jump.html";
		$post_data['tpl'] = "mn";
		$post_data['u'] = "http://www.baidu.com/";
		$post_data['verifycode'] = "";
		
		$ret = $this->doHttpPost("http://passport.baidu.com/v2/api/?login", $post_data, "https://passport.baidu.com/v2/?login&tpl=mn&u=http%3A%2F%2Fwww.baidu.com%2F");
	}
	
	private function getBaiduInfo()
	{		
		$data = $this->doHttpPost("http://hi.baidu.com/pub/show/createtext", "", "");
		
		//get bdstoken
		preg_match_all( "/bdstoken=([0-9a-z]+)/s",$data, $tokens);
	   	$this->bdstoken=$tokens[1][0];
	}
	
	private function postArticle($baiduzoneurl, $title, $content, $category, $blogpower)
	{
		$post_data = array();
		$post_data["title"] = $title;
		$post_data["tags[]"] = $category;
		$post_data["content"] = $content;
		$post_data["private"] = $blogpower == 0? "" : "1";
		$post_data["imgnum"] = "0";
		$post_data["bdstoken"] = $this->bdstoken;
		$post_data["qbid"] = "";
		$post_data["refer"] = "http://hi.baidu.com/home";
		$post_data["multimedia[]"] = "";
		$post_data["private1"] = $post_data["private"];
		$post_data["synflag"] = "";
		$post_data["qing_request_source"] = "new_request";
		
		$this->doHttpPost("http://hi.baidu.com/pub/submit/createtext", $post_data, "http://hi.baidu.com/pub/show/createtext");
	}
	
	private function doHttpPost($url, $post_data, $referef)
	{
		$mcurl = curl_init();
		curl_setopt($mcurl, CURLOPT_URL, $url);
		
		if ($post_data != "")
		{
			curl_setopt($mcurl, CURLOPT_POST, 1);
			curl_setopt($mcurl, CURLOPT_POSTFIELDS, $post_data);
		}
		
		if ($referef != "")
		{
			curl_setopt($mcurl, CURLOPT_REFERER, $referef);
		}

		curl_setopt($mcurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($mcurl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($mcurl, CURLOPT_HEADER, 1);
		curl_setopt($mcurl,	CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.92 Safari/537.1 LBBROWSER");
		
		if ($this->cookie != "")
		{
			curl_setopt($mcurl, CURLOPT_COOKIE, $this->cookie);
		}
		
		$data = curl_exec($mcurl);
		curl_close($mcurl);
		
		preg_match_all('/Set-Cookie:((.+)=(.+))$/m ', $data, $cookies);
		if(is_array($cookies) && count($cookies) > 1 && count($cookies[1]) > 0)
		{
			foreach($cookies[1] as $i => $k)
			{
				$cookieinfos = explode(";", $k);
				if(is_array($cookieinfos) && count($cookieinfos) > 1)
				{
					$this->cookie .= $cookieinfos[0];
					$this->cookie .= "; ";
				}
			}
		}
		
		return $data;
	}
}
?>