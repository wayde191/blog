<?php
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>

<?php get_header(); ?>


  <div class="wrapper">
    
    <div id="content">
    
    <h1><?php _e('Search Results for', 'wpzoom');?>: <?php the_search_query(); ?></h1>
    <?php include(TEMPLATEPATH . '/wpzoom_recent_posts.php'); ?>

    </div><!-- end #content -->
    
    </div><!-- end .wrapper -->

<?php get_footer(); ?>