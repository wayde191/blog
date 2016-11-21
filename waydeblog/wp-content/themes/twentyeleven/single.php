<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>

		<div id="primary">
			<div id="content" role="main">
				<?php while ( have_posts() ) : the_post(); ?>

					<nav id="nav-single">

						<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentyeleven' ); ?></h3>
						<span class="nav-previous"><?php previous_post_link( '%link', __( '<span class="meta-nav">&larr;</span> Previous', 'twentyeleven' ) ); ?> 
<a href="http://www.kaixin001.com/repaste/share.php?rtitle=<?php echo urlencode($post->post_title) ?>&rurl=<?php echo urlencode(get_permalink($post->ID)); ?>&rcontent=<?php echo urlencode(strip_tags(substr($post->post_content,0,50))); ?>\" title=\"转贴到开心网\" target=\"_blank\" rel=\"nofollow\">转贴到开心网</a>
</span>
						<span class="nav-next"><?php next_post_link( '%link', __( 'Next <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) ); ?></span>
					</nav><!-- #nav-single -->

					<?php get_template_part( 'content', 'single' ); ?>

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>