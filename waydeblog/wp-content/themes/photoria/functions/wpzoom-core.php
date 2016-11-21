<?php 

/* Enabling Localization */
load_theme_textdomain( 'wpzoom', TEMPLATEPATH.'/languages' );

$locale = get_locale();
$locale_file = TEMPLATEPATH."/languages/$locale.php";
if ( is_readable($locale_file) )
	require_once($locale_file);

if (function_exists('register_nav_menus')) {
register_nav_menus( array(
		'primary' => __( 'Main Menu', 'wpzoom' ),
 	) );
}

 	if ( function_exists( 'add_theme_support'  ) ) { 

  	// This theme allows users to set a Post Thumbnail. Added in 2.9
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 150, 120, true ); // Normal post thumbnails
	add_image_size( 'homecat', 9999, 175 ); // Permalink thumbnail size
	
	}
	
	if ( function_exists( 'add_custom_background'  ) ) { 
	// This theme allows users to set a custom background. Added in 3.0
	add_custom_background();
	}
	
	if (is_admin() && $_GET['activated'] == 'true') {
	header("Location: admin.php?page=wpzoom_options");
	}


	if (is_admin() && $_GET['page'] == 'wpzoom_options') {
		add_action('admin_head', 'wpzoom_admin_css');
		// wp_enqueue_script('jquery');
		wp_enqueue_script('tabs', get_bloginfo('template_directory').'/wpzoom_admin/simpletabs.js');
	}
	
function wpzoom_admin_css() {
	echo '
	<link rel="stylesheet" type="text/css" media="screen" href="'.get_bloginfo('template_directory').'/wpzoom_admin/options.css" />
	';
}
 
$functions_path = TEMPLATEPATH . '/wpzoom_admin/';
require_once ($functions_path . 'admin_functions.php');
$homepath = get_bloginfo('template_directory');

add_action('admin_menu', 'wpzoom_add_admin');

if ( function_exists('register_sidebar') )

register_sidebar(array('name'=>'Sidebar',
'before_widget' => '<div class="widget">',
'after_widget' => '<div class="cleaner">&nbsp;</div>
  </div>',
'before_title' => '<h2>',
'after_title' => '</h2>',
));

register_sidebar(array('name'=>'Footer: Column 1',
'before_widget' => '<div class="widget">',
'after_widget' => '<div class="cleaner">&nbsp;</div>
  </div>',
'before_title' => '<h2>',
'after_title' => '</h2>',
));

register_sidebar(array('name'=>'Footer: Column 2',
'before_widget' => '<div class="widget">',
'after_widget' => '<div class="cleaner">&nbsp;</div>
  </div>',
'before_title' => '<h2>',
'after_title' => '</h2>',
));

register_sidebar(array('name'=>'Footer: Column 3',
'before_widget' => '<div class="widget">',
'after_widget' => '<div class="cleaner">&nbsp;</div>
  </div>',
'before_title' => '<h2>',
'after_title' => '</h2>',
));

register_sidebar(array('name'=>'Footer: Column 4',
'before_widget' => '<div class="widget">',
'after_widget' => '<div class="cleaner">&nbsp;</div>
  </div>',
'before_title' => '<h2>',
'after_title' => '</h2>',
));
?>