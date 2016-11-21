<?php
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>

<?php get_header(); 
		if(get_query_var('author_name')) :
		$curauth = get_userdatabylogin(get_query_var('author_name'));
		else :
		$curauth = get_userdata(get_query_var('author'));
		endif;
?>


  <div class="wrapper">
    
    <div id="content">
    
    <h1><?php _e('Author', 'wpzoom');?>: <a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->display_name; ?></a></h1>
    
    <?php include(TEMPLATEPATH . '/wpzoom_recent_posts.php');  ?>

    </div><!-- end #content -->
    
    </div><!-- end .wrapper -->

<?php get_footer(); ?>