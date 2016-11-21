=== AJAX Login Widget++ ===
Contributors: dound, JonasEinarsson
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3461746
Tags: ajax, log in, log out, login, logout, register, registration, lost, password, sidebar, widget++, widget
Requires at least: 2.7
Tested up to: 2.7.1
Stable tag: 1.0.1

A clean AJAX-based login, registration and lost password retrieval widget.

== Description ==

[Plugin Homepage](http://dound.com/projects/word-press/ajax-login-widget/)

= Features =
* **Integrate** login functionality into your site (rather than having it on a separate page).
* User can login, register, and retrieve a lost password **without ever leaving the page** they are on.
* **AJAX**-based.
* *XHTML 1.1 and CSS 3 compliant*.

= Current Ideas for the Next Version =
* Beautify for IE7
* Minify JS and put it in the footer
* Better support for styling via CSS
* Option to include a customizeable title above the widget
* Provide an additional non-table-based template
* Internalization support
* Add login form to page/post with [insert-login] tag
* Options
* - Show gravatar after user logs in
* - Customize welcome message
* - Titles for login form
* - Captcha support

Please continue to give me ideas for the next version
[on my blog](http://dound.com/2009/02/my-first-wordpress-plugin-ajax-login-widget)
or <a href="http://wordpress.org/support/topic/246843">on the forums here</a>.

This plugin provides an XHTML-compliant AJAX login form which can be easily
inserted (one line of code) into your WordPress sidebar.  The form doubles as
lost-password-recovery and registration forms too.

AJAX allows the page to be dynamically updated so that the user does not have to
leave the page they were on when they decided to sign in or sign up.  This means
users do not have to interrupt what they were looking at in order to login!  If
an error occurs (like the entry of an incorrect password), then the user is
alerted with a message box.  The only full page refresh which occurs is when the
user logs in successfully, though it does not change which page the user is
looking at (so as not to interrupt them).

One the user is logged in, the login form is replaced with a "You are logged in
as XYZ" message along with links to go to their profile or logout.  An
administrative user will be presented with the "Site Admin" link instead of a
profile link.

The plugin uses the normal WordPress 2.7 authentication process so it can easily
be added to your WordPress site as a sidebar widget or by adding a single line
of PHP code to your theme.

This plugin extends the former [AJAX
Login](http://wordpress.org/extend/plugins/ajax-login/) plugin written by Jonas
Einarsson.  This version fixes a number of bugs with a previous implementation
(incompatibility with the latest WordPress versions) and contributes a cleaner
user interface with better and more reliable AJAX functionality (including
animated loading icons).


== Installation ==

1. Unzip and then copy the `ajax-login-widget` folder to your `wp-content/plugins/` folder.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Add the widget to your page in one of two ways:

3a) If you are using the dynamic sidebar, you can add the AJAX Login Widget++ widget using the widgets administration menu.

3b) Add it to one of your theme's template with the PHP call "add_ajax_login_widget()" - see below for an example.



*Example*: Adding AJAX Login Widget++ to your theme's `sidebar.php` template page:

1. Open wp-content/themes/your-theme-name/sidebar.php

2. Look for "wp_register()" and "wp_loginout()".  It probably looks like this:
`
        <!-- meta -->
        <div class="widget">
                <h3>Meta</h3>
                <ul>
                        <?php wp_register(); ?>
                        <li><?php wp_loginout(); ?></li>
                </ul>
        </div>
`

2. Replace the section with those two function calls with a call to
     add_ajax_login_widget, so it now looks like this:
`
        <!-- meta -->
        <div class="widget">
             <?php add_ajax_login_widget(); ?>
        </div>
`

== Screenshots ==
1. Before the user logs in
2. Waiting for authentication
3. After the user logs in
4. When the user wants to register
5. After the user has registered
6. When the user has lost their password
7. After the user has requested that a new password be sent to their email

== Frequently Asked Questions ==

= Can I customize the widget? =
Yes.  Just modify the al_template.php in the plugin directory.

= The login form does not work and has some JavaScript error =
If the Firefox Error Console finds an error about 'sack is not defined', then add
"<script type='text/javascript' src='http://dound.com/wp/wp-includes/js/tw-sack.js'></script>"
after the '<title>...</title>' tags in your wp-content/themes/your-theme/templates/start.php
file.  Hopefully we'll come up with less of a hack for this later.

= Why is there an option for loading delay? =
It is set to zero by default.  But it is there in case your site is really fast
and your users are confused by delay-less logins.
