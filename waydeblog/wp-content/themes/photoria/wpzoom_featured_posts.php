<?php
$args = array('showposts' => $wpzoom_featured_posts_posts, 'orderby' => 'date', 'order' => 'DESC');

$featType = $wpzoom_featured_type;

if ($featType == 'Tag')
{
$args['tag'] = "$wpzoom_featured_slug";  // Breaking tag slug
}
elseif ($featType == 'Category')
{
$args['cat'] = "$wpzoom_featured_slug";  // Breaking tag slug
}
?>
  <div id="featPosts">
      <div id="featPostsBig">
        <div class="container">
        
		<?php 
		query_posts($args);
		$i = 0;
		if ( have_posts() ) : ?>

		<ul class="slides">
            <?php while (have_posts()) : the_post(); update_post_caches($posts); $i++;?>
            <li class="slide">

        <?php unset($img); if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail() ) {
			$thumbURL = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), '' );
            $img = $thumbURL[0];  }
             else {
                unset($img);
                if ($wpzoom_cf_use == 'Yes')   {  $img = get_post_meta($post->ID, $wpzoom_cf_photo, true);   }
			else  {
				if (!$img) { $img = catch_that_image($post->ID);  }
                } }
 			if ($img){ $img = wpzoom_wpmu($img); ?>
			<div class="cover"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $img ?>&amp;h=430&amp;w=950&amp;zc=1" alt="<?php the_title(); ?>" width="950" height="430" /></a></div>
        <?php } // if an image exists ?>
        </li>
        <?php endwhile; ?>
        </ul>
        <?php endif; ?>
        <div class="cleaner">&nbsp;</div>
        </div><!-- end .container -->
 
      </div><!-- end #featPostsBig -->
      
      <a href="#" class="browse previous">Prev</a>
		<a href="#" class="browse next">Next</a>
      </div><!-- end #featPosts -->
      <?php wp_reset_query(); ?>

<script type="text/javascript" charset="utf-8">
jQuery(document).ready(
function($)
{
		$('#featPosts').loopedSlider({
			autoHeight: true,
			containerClick: false,
			slidespeed: 500,
			pauseOnHover: <?php if ($wpzoom_slideshow_pause == 'Yes') { ?>true<?php } ?> <?php if ($wpzoom_slideshow_pause == 'No') { ?>false<?php } ?>,
			addPagination: false, 
  		autoStart: <?php if ($wpzoom_slideshow_auto == 'Yes') { ?><?php echo "$wpzoom_slideshow_speed"; ?><?php } ?> <?php if ($wpzoom_slideshow_auto == 'No') { ?>0<?php } ?>
		});
	});
</script>