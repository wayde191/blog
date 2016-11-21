<?php
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>

<?php get_header(); ?>


  <div class="wrapper">
    
    <div id="content">
    
<?php /* If this is a category archive */ if (is_category()) { ?>
		<h1><?php _e('Category', 'wpzoom');?>: <?php single_cat_title(); ?></h1>
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h1><?php _e('Tag', 'wpzoom');?>: <?php single_tag_title(); ?></h1>
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h1><?php _e('Archive for', 'wpzoom');?> <?php the_time('F jS, Y'); ?></h1>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h1><?php _e('Archive for', 'wpzoom');?> <?php the_time('F, Y'); ?></h1>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h1><?php _e('Author:', 'wpzoom');?> <?php the_time('Y'); ?></h1>
	  <?php /* If this is an author archive */ } ?>

    <?php include(TEMPLATEPATH . '/wpzoom_recent_posts.php'); ?>

    </div><!-- end #content -->
    
    </div><!-- end .wrapper -->

<?php get_footer(); ?>