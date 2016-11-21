<?php
/*
 This is a template for AJAX Login Widget++.

 Elements which always must exist:
  - The forms alw_registerForm, alw_loginForm and alw_lostPassword
  - The message spans alw_registerMessage, alw_loginMessage and alw_lostPasswordMessage

 Elements that must exist for the alw_show*()-functions to work:
  - alw_login, alw_loading_login
  - alw_register, alw_loading_register
  - alw_lostPassword, alw_loading_lost
*/
?>

<div>

<?php
  global $user_ID, $user_identity;
  get_currentuserinfo();
  if (!$user_ID) {
      /* This part is for when the user is NOT logged in. */
?>
<div id="alw_login" style="padding-left:10px">
    <form onsubmit="return false;" id="alw_loginForm" action="#" method="post">
        <table>
        <tr>
            <td><?php _e('User') ?>:</td>
            <td><input onkeypress="return alw_loginOnEnter(event);" type="text" name="log" size="20" style="height:18px;"/></td>
        </tr>
        <tr>
            <td><?php _e('Password') ?>:</td>
            <td><input onkeypress="return alw_loginOnEnter(event);" type="password" name="pwd" size="20" style="height:18px;"/></td>
        </tr>
        </table>
        <p id="alw_login_p">
            <label><input onkeypress="return alw_loginOnEnter(event);" type="checkbox" name="rememberme" value="forever"/> <?php _e("Remember me"); ?></label>
             | <a href="javascript:alw_showRegister();">Register</a>
             | <a href="javascript:alw_showLostPassword();">Lost password?</a><br/>
            <input type="button" name="submit" value="<?php _e('Log In'); ?> &raquo;" onclick="alw_login();" style="font-size:10px;height:24px;"/>
            <span id="alw_loginMessage"></span>
            <span id="alw_loading_login" style="display:none; height:22px; width:22px; vertical-align:bottom">
               <img src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/ajax-login-widget/alw_loading.gif" alt="Loading"/>
               Logging in ...
            </span>
        </p>
    </form>
</div>

<div id="alw_register" style="padding-left:10px; display:none">
<div>
    <form onsubmit="return false;" id="alw_registerForm" action="#" method="post">
        <table>
        <tr>
            <td><?php _e('User') ?>:</td>
            <td><input onkeypress="return alw_registerOnEnter(event);" type="text" name="user_login" size="20" style="height:18px;"/></td>
        </tr>
        <tr>
            <td><?php _e('E-mail') ?>:</td>
            <td><input onkeypress="return alw_registerOnEnter(event);" type="text" name="user_email" size="20"  style="height:18px;"/></td>
        </tr>
        </table>
        <p>
            <span id="alw_registerMessage">A password will be mailed to you.<br/></span>
            <a href="javascript:alw_showLogin();">Log In</a>
             | <a href="javascript:alw_showLostPassword();">Lost password?</a><br/>
            <input type="button" name="submit" value="<?php _e('Register'); ?> &raquo;" onclick="alw_register();" style="font-size:10px;height:22px;"/>
            <span id="alw_loading_register" style="display:none; height:22px; width:22px; vertical-align:bottom">
               <img src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/ajax-login-widget/alw_loading.gif" alt="Loading"/>
               Registering ...
            </span>
        </p>
    </form>
</div>
</div>

<div id="alw_lostPassword" style="padding-left:10px; display:none">
<div>
    <form onsubmit="return false;" id="alw_lostPasswordForm" action="#" method="post">
        <table>
        <tr>
            <td><?php _e('User') ?>:</td>
            <td><input onkeypress="return alw_retrievePasswordOnEnter(event);" type="text" name="user_login" size="20" style="height:18px;"/></td>
        </tr>
        <tr>
            <td><?php _e('E-mail') ?>:</td>
            <td><input onkeypress="return alw_retrievePasswordOnEnter(event);" type="text" name="user_email" size="20" style="height:18px;"/></td>
        </tr>
        </table>
        <p>
            <span id="alw_lostPasswordMessage">A message will be sent to your e-mail address.<br/></span>
            <a href="javascript:alw_showLogin();">Log In</a>
             | <a href="javascript:alw_showRegister();">Register</a><br/>
            <input type="button" name="submit" value="<?php _e('Retrieve'); ?> &raquo;" onclick="alw_retrievePassword();" style="font-size:10px;height:22px;"/>
            <span id="alw_loading_lost" style="display:none; height:22px; width:22px; vertical-align:bottom">
               <img src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/ajax-login-widget/alw_loading.gif" alt="Loading"/>
               Looking up your credentials ...
            </span>
        </p>
    </form>
</div>
</div>

<?php
  } else {
      /* This part is for when the user IS logged in. */
?>

<div>
    <span class="ajax_login_widget">Logged in as <?php echo $user_identity; ?></span>
     (<a href="<?php echo wp_logout_url('/wp-login.php?action=logout&amp;redirect_to=' . $_SERVER['REQUEST_URI']); ?>">log out</a>)
<?php
    if ( current_user_can('manage_options') ) {
        echo '(<a href="' . admin_url() . '">' . __('admin') . '</a>)';
    } else {
        echo '(<a href="' . admin_url() . 'profile.php">' . __('profile') . '</a>)';
    }
?>
</div>

<?php
  }
?>

</div>
