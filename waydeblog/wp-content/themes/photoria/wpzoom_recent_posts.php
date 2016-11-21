<?php wp_reset_query(); ?>
        <?php if (have_posts()) : 
        $i = 0; ?>

        <ul class="posts">
<?php    
    while (have_posts()) : the_post();
    $i++;
    unset($img);
?>
              <li<?php if (($i % 3) == 0) {echo ' class="last"';} ?>>
                <h2><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                <div class="content">
<?php 
if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail() ) {
						$thumbURL = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), '' );
            $img = $thumbURL[0]; 
						}

            else {
                unset($img);
                if ($wpzoom_cf_use == 'Yes')
                {
                  $img = get_post_meta($post->ID, $wpzoom_cf_photo, true);
                }
                else
                {
                  if (!$img)
                  {
                    $img = catch_that_image($post->ID);
                  }
                }
              }

         if ($img){ 
         $img = wpzoom_wpmu($img);
         ?>
        <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $img ?>&amp;h=230&amp;w=300&amp;zc=1" width="300" height="230" alt="<?php the_title(); ?>" /></a>
        <?php } ?>
                 <?php the_excerpt(); ?> 
                <div class="cleaner">&nbsp;</div>
                </div>
              </li><?php if (($i % 3) == 0) { ?><div class="cleaner">&nbsp;</div><?php } ?>
<?php endwhile; //  ?>
            </ul>
            <div class="cleaner">&nbsp;</div>
          <div class="navigation">
			<span class="left_nav"><?php next_posts_link(__('&larr; Older Entries', 'wpzoom')); ?></span> <span class="right_nav"><?php previous_posts_link(__('Newer Entries &rarr;', 'wpzoom')); ?></span> <div class="cleaner">&nbsp;</div>
          </div><!-- end .navigation -->
  <?php else : ?>
  
  <p class="title"><?php _e('There are no posts in this category', 'wpzoom');?></p>
  
  <?php endif; ?>

<div class="cleaner">&nbsp;</div>