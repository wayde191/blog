<?php
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>

<?php get_header(); ?>


  <div class="wrapper">
    
    <?php if ($wpzoom_featured_posts_show == 'Yes' && is_home() && $paged < 2) { include(TEMPLATEPATH . '/wpzoom_featured_posts.php'); } ?>
    
    <div id="content">
    
    <?php if ($wpzoom_recent_posts_show == 'Yes') { include(TEMPLATEPATH . '/wpzoom_recent_posts.php'); } ?>

    </div><!-- end #content -->
    
    </div><!-- end .wrapper -->

<?php get_footer(); ?>