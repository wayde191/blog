<?php
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
$dateformat = get_option('date_format');
$timeformat = get_option('time_format');
?>

<?php get_header(); 
$template = get_post_meta($post->ID, 'wpzoom_post_template', true);
?>


<div class="wrapper<?php if ($template == 'Sidebar on the left') {echo' sidebarLeft';} ?>">
    
     <?php if (isset($template) && $template != 'Full Width (no sidebar)') { ?>
    <div id="main">
    <?php } ?>
    <div id="content">
    
    <?php wp_reset_query(); if (have_posts()) : while (have_posts()) : the_post(); ?>
   
    <h1> <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a> </h1>
   
	<span class="postmetadata"><?php if ($wpzoom_singlepost_author == 'Show') { ?><?php _e('By','wpzoom'); ?> <?php the_author_posts_link(); } if ($wpzoom_singlepost_date == 'Show') { ?> <?php _e('on','wpzoom'); ?> <?php the_time("$dateformat $timeformat"); } if ($wpzoom_singlepost_cat == 'Show') { ?> <?php _e('on','wpzoom'); ?> <?php the_category(', '); } ?> <?php edit_post_link( __('EDIT', 'wpzoom'), ' / ', ''); ?></span>
 
    <div class="postcontent">
    
		<?php if (strlen($wpzoom_ad_content_imgpath) > 1 && $wpzoom_ad_content_select == 'Yes' && $wpzoom_ad_content_pos == 'Before') { echo '<div class="banner">'.stripslashes($wpzoom_ad_content_imgpath)."</div>"; }?>

		<?php the_content(); ?>
		
		<?php wp_link_pages(array('before' => '<p class="pages"><strong>'.__('Pages', 'wpzoom').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
		<?php if ($wpzoom_singlepost_tag == 'Show') { ?><?php the_tags( '<p class="tags"><strong>'.__('Tags', 'wpzoom').':</strong> ', ', ', '</p>'); ?><?php } ?>

		<div class="cleaner">&nbsp;</div>
   
		<?php if (strlen($wpzoom_ad_content_imgpath) > 1 && $wpzoom_ad_content_select == 'Yes' && $wpzoom_ad_content_pos == 'After') { echo '<div class="banner">'.stripslashes($wpzoom_ad_content_imgpath)."</div>"; }?>

    </div><!-- end .postcontent -->
    
    <?php comments_template(); ?>
    
    <?php endwhile; else: ?>
		<p><?php _e('Sorry, no posts matched your criteria', 'wpzoom');?>.</p>
    <?php endif; ?>

    </div><!-- end #content -->
    
           <?php if (isset($template) && $template != 'Full Width (no sidebar)') { ?>
          </div><!-- end #main -->
          <div id="sidebar">
          
            <?php get_sidebar(); ?>
            
          </div><!-- end #sidebar -->
          <?php } //if template is not full width  ?>
    
    </div><!-- end .wrapper -->

<?php get_footer(); ?>