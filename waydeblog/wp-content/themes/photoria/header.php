<?php 
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<title><?php if ($wpzoom_seo_enable == 'Enable') { wpzoom_titles(); } else { ?> <?php bloginfo('name'); wp_title('-'); } ?></title>
<meta http-equiv="content-type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php if ($wpzoom_seo_enable == 'Enable') { 
if (is_single() || is_page() ) : if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<meta name="description" content="<?php echo strip_tags(get_the_excerpt()); ?>" />
<?php meta_post_keywords(); ?>
<?php endwhile; endif; elseif(is_home()) : ?>
<meta name="description" content="<?php if (strlen($wpzoom_meta_desc) < 1) { bloginfo('description');} else {echo"$wpzoom_meta_desc";}?>" />
<?php meta_home_keywords(); ?>
<?php endif; ?>
<?php wpzoom_index(); ?>
<?php wpzoom_canonical(); } ?>

<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/style.css" />
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/dropdown.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/custom.css" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php wpzoom_rss(); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/css/SyntaxHighlighter.css"></link>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shCore.js"></script>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shBrushCSharp.js"></script>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shBrushCpp.js"></script>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shBrushCss.js"></script>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shBrushDelphi.js"></script>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shBrushJava.js"></script>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shBrushJScript.js"></script>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shBrushPhp.js"></script>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shBrushPython.js"></script>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shBrushRuby.js"></script>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shBrushSql.js"></script>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shBrushVb.js"></script>
<script language="javascript" src="<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/shBrushXml.js"></script>

<?php wp_enqueue_script('jquery');  ?>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>  
<?php wp_head(); ?>
<?php wpzoom_js("dropdown"); ?>
<?php if (is_home() && $wpzoom_featured_posts_show == 'Yes') { ?>
<?php wpzoom_js("loopedslider.min"); ?>
<?php } ?>
</head>

<body <?php body_class() ?>>

<div id="container">
  
  <div class="wrapper">

  <div id="header">

      <div id="logo">
        <a href="<?php echo get_option('home'); ?>"><?php if ($wpzoom_misc_logo_path) { ?><img src="<?php echo "$wpzoom_misc_logo_path";?>" alt="<?php bloginfo('name'); ?>" /><?php } else { ?><img src="<?php bloginfo('template_directory'); ?>/images/logo.png" alt="<?php bloginfo('name'); ?>" /><?php } ?></a>
      </div><!-- end #logo -->
      <div id="menu" class="dropdown">
      <?php wp_nav_menu( array('container' => '', 'container_class' => '', 'menu_class' => 'dropdown', 'menu_id' => 'nav', 'sort_column' => 'menu_order', 'theme_location' => 'primary' ) ); ?>
      </div>
      
      <div class="cleaner">&nbsp;</div>

  </div><!-- end #header -->

  </div><!-- end .wrapper -->  