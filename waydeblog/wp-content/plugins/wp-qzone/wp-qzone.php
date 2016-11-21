<?php
/*
Plugin Name: Post to Qzone 
Plugin URI: http://liguangming.com
Description: publish post to Qzone,<a href="http://service.mail.qq.com/cgi-bin/help?subtype=1&id=23&no=242" target="_blank">如何通过发送邮件的方式发表Qzone</a>.
Version: 0.4
Author: Ian Lee
Author URI: http://liguangming.com
*/

defined('ABSPATH') or exit('access denied');

class Mailer
{
	var $qq = null;
	var $From = null;
	var $FromName = null;
	var $Host = "smtp.qq.com";
	var $Password = null;
	var $Address = null;

	function Mailer($qq,$psw){
		$this->qq = $qq;
		$this->From	 = "{$qq}@qq.com";
		$this->FromName = $qq;
		$this->Host	 = "smtp.qq.com";
		$this->Password = $psw;
		$this->Address = array();
	}

	function AddAddress($address, $name){
		$this->Address[$name] = $address;
	}

	function Halo($subject, $body){

		$this->AddAddress("{$this->qq}@qzone.qq.com", "{$this->qq}@qzone.qq.com");

		$port = "25"; // should be 25 by default
		$timeout = "30"; //typical timeout. try 45 for slow servers
		$localhost = $this->Host; //this seems to work always
		$newLine = "\r\n"; //var just for newlines in MS

		$result = true;

		//connect to the host and port
		$connection = fsockopen($this->Host, $port, $errno, $errstr, $timeout);
		$response = fgets($connection, 4096);
		if(empty($connection))
		{
		   $output = "Failed to connect: $response";
		   return false;
		}
		else
		{
		   $logArray['connection'] = "Connected to: $response";
		}

		//say HELO to our little friend
		fputs($connection, "HELO $localhost". $newLine);
		$response = fgets($connection, 4096);
		$logArray['heloresponse'] = $response;

		//request for auth login
		fputs($connection,"AUTH LOGIN" . $newLine);
		$response = fgets($connection, 4096);
		$logArray['authrequest'] = $response;

		//send the username
		fputs($connection, base64_encode($this->qq) . $newLine);
		$response = fgets($connection, 4096);
		$logArray['authusername'] = $response;

		//send the password
		fputs($connection, base64_encode($this->Password) . $newLine);
		$response = fgets($connection, 4096);
		$logArray['authpassword'] = $response;

		//email from
		fputs($connection, "MAIL FROM: " . $this->From . $newLine);
		$response = fgets($connection, 4096);
		$logArray['mailfromresponse'] = $response;

		//email to
		$i = 1;
		foreach($this->Address as $to)
		{
			fputs($connection, "RCPT TO: $to" . $newLine);
			$response = fgets($connection, 4096);
			$logArray["mailtoresponse$i"] = $response;
			$i++;
		}

		//the email
		fputs($connection, "DATA" . $newLine);
		$response = fgets($connection, 4096);
		$logArray['data1response'] = $response;

		//construct headers
		$headers = "MIME-Version: 1.0" . $newLine;
		$headers .= "Content-type: text/html; charset=utf-8" . $newLine;
		$headers_to = "To: ";
		$i = 1;
		$m = count($this->Address);
		foreach($this->Address as $toName => $to)
		{
			$headers_to .= " $toName <$to>";
			if ($i < $m)
			{
				$headers_to .= ",";
			}
			$i++;
		}
		$headers .= $headers_to . $newLine;
		$headers .= "From: " . $this->FromName ."<".$this->From.">" . $newLine;

		//strip new line
		$body = str_replace("\r\n", "\n", $body);
		$body = str_replace("\r", "\n", $body);
		$body = str_replace("\n", "<br />", $body);
		$body = str_replace("\\'", "'", $body);
		$body = str_replace("\\\"", "\"", $body);
		$body = str_replace("\\\\", "\\", $body);

		//observe the . after the newline, it signals the end of message
		fputs($connection, "To: {$this->qq}@qzone.qq.com\r\nFrom: {$this->From}\r\nSubject: $subject\r\n$headers\r\n\r\n$body\r\n.\r\n");
		$response = fgets($connection, 4096);
		$logArray['data2response'] = $response;

		// say goodbye
		fputs($connection,"QUIT" . $newLine);
		$response = fgets($connection, 4096);
		$logArray['quitresponse'] = $response;
		$logArray['quitcode'] = substr($response,0,3);
		fclose($connection);
		//a return value of 221 in $retVal["quitcode"] is a success 

		//error_log(print_r($logArray, true));
		return true;
	}
}

class Crypter{

   var $key;

   function Crypter($clave){
	  $this->key = $clave;
   }

   function keyED($txt) { 
	  $encrypt_key = md5($this->key); 
	  $ctr=0; 
	  $tmp = ""; 
	  for ($i=0;$i<strlen($txt);$i++) { 
		 if ($ctr==strlen($encrypt_key)) $ctr=0; 
		 $tmp.= substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1); 
		 $ctr++; 
	  } 
	  return $tmp; 
   } 
   
   function encrypt($txt){ 
	  srand((double)microtime()*1000000); 
	  $encrypt_key = md5(rand(0,32000)); 
	  $ctr=0; 
	  $tmp = ""; 
	  for ($i=0;$i<strlen($txt);$i++){ 
		 if ($ctr==strlen($encrypt_key)) $ctr=0; 
		 $tmp.= substr($encrypt_key,$ctr,1) . 
			 (substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1)); 
		 $ctr++; 
	  } 
	  return base64_encode($this->keyED($tmp)); 
   } 

   function decrypt($txt) { 
	  $txt = $this->keyED(base64_decode($txt)); 
	  $tmp = ""; 
	  for ($i=0;$i<strlen($txt);$i++){ 
		 $md5 = substr($txt,$i,1); 
		 $i++; 
		 $tmp.= (substr($txt,$i,1) ^ $md5); 
	  } 
	  return $tmp; 
   } 
}


function post_to_qzone_admin()
{
	if (function_exists('add_options_page')) {
		add_options_page('post_to_qzone', 'Post2Qzone',8, basename(__FILE__), 'post_to_qzone_option');	
	}
}

function post_to_qzone_default($qzone){
	if(strpos($qzone->title,'{post_title}')===false){
		$qzone->title = '{post_title}';
	}

	if(strpos($qzone->content,'{post_content}')===false){
		$qzone->content = '{post_content}<br />Original:{post_link}';
	}
	return $qzone;
}

function post_to_qzone_option()
{
	$qzone = get_option('post_to_qzone');

	$crypter = new Crypter(get_option('secret'));

	if($qzone !== false){
		$qzone = unserialize($qzone);
	}else{
		$qzone = new stdClass;
	}

	if(isset($_POST['p2q_title'])  && isset($_POST['p2q_content'])){
		$n = trim($_POST['p2q_number']);
		$p = trim($_POST['p2q_password']);
		$t = trim($_POST['p2q_title']);
		$c = trim($_POST['p2q_content']);
		$r = trim($_POST['p2q_receive']);

		$c = str_replace("\\'", "'", $c);
		$c = str_replace("\\\"", "\"", $c);
		$c = str_replace("\\\\", "\\", $c);

		$qzone->number = $n;
		$qzone->title = $t;
		$qzone->content = $c;
		$qzone->receive = $r;

		if(strlen($p)>=6){
			$crypter = new Crypter(get_option('secret'));
			$qzone->password = $crypter->encrypt($p);
		}else{
			$qzone->password = '';
		}
		$qzone = post_to_qzone_default($qzone);
		update_option('post_to_qzone', serialize($qzone));
	}else{
		$qzone = post_to_qzone_default($qzone);
	}
?>
	<div class="wrap" style="padding:5px 0 0 5px;text-align:left" id="poststuff">
	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" id="post">
	<h3>QQ Number</h3>
	<div id="titlewrap">
		<input type="text" name="p2q_number" size="30" tabindex="1" value="<?php echo $qzone->number;?>" id="p2q_number" autocomplete="off" /> 
	</div><br />
	<h3>QQ Mail Password</h3>
	<div id="titlewrap">
		<input type="password" name="p2q_password" size="30" tabindex="2" value="<?php echo $crypter->decrypt($qzone->password);?>" id="p2q_password" autocomplete="off" />
	</div><br />
	<h3>Publish to other emails(eg:<a href="http://spaces.live.com/" target="_blank">Live Space</a>)</h3>
	<div id="titlewrap">
		<input type="text" name="p2q_receive" size="90" tabindex="2" value="<?php echo $qzone->receive;?>" id="p2q_receive" autocomplete="off" />
		<br />Separate multiple email addresses with spaces
	</div><br />
	<h3>Post Title Template </h3>
	<div id="titlewrap">
		<input type="text" name="p2q_title" size="30" tabindex="3" value="<?php echo $qzone->title;?>" id="p2q_title" autocomplete="off" />
	</div><br />
	<h3>Post Content Template</h3>
	<div id="titlewrap"><textarea name="p2q_content" id="p2q_content" tabindex="4" rows="6" cols="100%" autocomplete="off"><?php echo $qzone->content;?></textarea>
	</div>
	<p>Above the fields are not required!if not,you will input information when publish post to qzone everytime!</p>
	<p class="submit"><input type="submit" value="<?php _e("Save Qzone Account Information");?>" name="submit" /></p>
	</form>
	</div>
<?php
}

function post_qzone_config(){
	$qzone = get_option('post_to_qzone');
	$post_id = intval($_GET['post']);
	$p2q_stat = get_post_meta($post_id,'_p2q_stat',true);

	if($qzone !== false){
		$qzone = unserialize($qzone);
	}else{
		$qzone = new stdClass;
	}
	
	$action = $_GET['action'];

	if(extension_loaded("sockets")){
		echo '<div id="qzonediv" class="postbox"><h3>Post to Qzone</h3><div class="inside"><p><strong>QQ number:</strong> &nbsp; &nbsp;<a href="options-general.php?page='.basename(__FILE__).'">Config Account?</a></p><input name="qqnumber" type="text" size="50" id="qqnumber" value="'.$qzone->number.'" /></div><div class="inside"><p><strong>QQ email password:</strong></p><input name="qqpsw" type="password" size="50" id="qqpsw" value="'.$qzone->password.'" />';

		if(strlen($qzone->password) > 0){
			echo '<input name="qqed" type="hidden" id="qqed" value="1" />';
		}

		echo '</div><div class="inside">';
		echo '<p><input name="qqpub" type="checkbox" id="qqpub" value="1" />';

		if($p2q_stat =='OK'){
			echo '<strong>This post has been already sent to Qzone.Resend it?</strong>';
		}else{
			echo '<strong>Confirm publish!</strong>';

			if($p2q_stat=='Failed'){
				echo '<span style="color:#f60">Just publish failed!</span>';
			}
		}
		echo '</p></div></div>';
	}else{
		echo '<div id="qzonediv" class="postbox"><h3>Post to Qzone</h3><div class="inside"><p>Sorry,Your webserver do <strong>not</strong> support socket!</p></div></div>';
	}
}


function post_qzone_stat($post_id,$value){
	if ( !add_post_meta( $post_id, '_p2q_stat', $value, true ) ){
		update_post_meta( $post_id, '_p2q_stat', $value );
	}
}

function post_qzone_publish(){
	global $wpdb;

	if(isset($_POST['qqnumber']) 
		&& isset($_POST['qqpsw']) 
		&& isset($_POST['qqpub'])){
		$qq = intval($_POST['qqnumber']);
		$psw = trim($_POST['qqpsw']);
		
		$post_id = intval($_POST['ID']);

		if(strlen($psw)<6 or $post_id ==0){
			return false;
		}

		if(isset($_POST['qqed'])){
			$crypter = new Crypter(get_option('secret'));
			$psw = $crypter->decrypt($psw);
		}

		$qzone = get_option('post_to_qzone');
		if($qzone !== false){
			$qzone = unserialize($qzone);
		}else{
			$qzone = new stdClass;
		}

		$qzone = post_to_qzone_default($qzone);

		if (get_magic_quotes_gpc()) {
			$post_title=stripslashes($_POST['post_title']);
			$post_content=stripslashes($_POST['post_content']);
		}else{
			$post_title=trim($_POST['post_title']);
			$post_content=trim($_POST['post_content']);
		}

		if($qq>1000 && !empty($post_title)  && !empty($post_content) ){

			$post_content = str_replace('{post_content}',$post_content,$qzone->content);
			$post_content = str_replace('{post_link}',get_permalink($post_id),$post_content);
			$post_content = str_replace('{post_title}',$post_title,$post_content);

			$post_title = str_replace('{post_title}',$post_title,$qzone->title);
			
			$m = new Mailer($qq,$psw);
			$addz = preg_split('#(\s+)#',trim($qzone->receive));
			
			if(count($addz)>0){
				foreach($addz as $add){
					if(is_email($add)){
						$m->AddAddress($add, $add);
					}
				}
			}

			$result = $m->Halo($post_title,$post_content);
			if($result){
				post_qzone_stat($post_id, 'OK');
			}else{
				post_qzone_stat($post_id, 'Failed');
			}

		}

		unset($_POST['qqnumber'],$_POST['qqpsw'],$_POST['qqpub'],$_POST['qqed']);
	}
}

function post_qzone_xmlrpc($post_id = 0){
	//check id
	if($post_id == 0){
		return false;
	}

	//check p2p_stat
	$p2q_stat = get_post_meta($post_id,'_p2q_stat',true);
	if($p2q_stat =='OK'){
		return false;
	}
	
	//check option
	$qzone = get_option('post_to_qzone');
	if($qzone !== false){
		$qzone = unserialize($qzone);
	}else{
		return false;
	}

	//check number password
	if (empty($qzone->number) or empty($qzone->password)){
		return false;
	}
	
	//reset default option
	$qzone = post_to_qzone_default($qzone);
	$crypter = new Crypter(get_option('secret'));

	//number password
	$psw = $crypter->decrypt($qzone->password);
	$qq = intval($qzone->number);

	$post = & get_post($post_id);

	if($qq > 10000 && $qq < 9999999999){

		$post_content = str_replace('{post_content}',$post->post_content,$qzone->content);
		$post_content = str_replace('{post_link}',get_permalink($post_id),$post_content);
		$post_content = str_replace('{post_title}',$post->post_title,$post_content);

		$post_title = str_replace('{post_title}',$post->post_title,$qzone->title);

		$m = new Mailer($qq,$psw);
		$addz = preg_split('#(\s+)#',trim($qzone->receive));

		if(count($addz) >0){
			foreach($addz as $add){
				if(is_email($add)){
					$m->AddAddress($add, $add);
				}
			}
		}
		
		$result = $m->Halo($post_title,$post_content);
		if($result){
			post_qzone_stat($post_id, 'OK');
		}else{
			post_qzone_stat($post_id, 'Failed');
		}
		
	}
}

add_action('admin_menu', 'post_to_qzone_admin');
add_action('dbx_post_sidebar', 'post_qzone_config');
add_action('publish_post', 'post_qzone_publish');
add_action('xmlrpc_publish_post', 'post_qzone_xmlrpc');


