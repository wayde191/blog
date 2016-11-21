<?php 
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
} 
?>
  <div class="cleaner">&nbsp;</div>

<div class="wrapper">
  
  <div id="preFooter">
    <div class="column">
      <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer: Column 1') ) : ?> <?php endif; ?>
    </div>
    <div class="column">
      <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer: Column 2') ) : ?> <?php endif; ?>
    </div>
    <div class="column">
      <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer: Column 3') ) : ?> <?php endif; ?>
    </div>
    <div class="column last">
      <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer: Column 4') ) : ?> <?php endif; ?>
    </div>
    <div class="cleaner">&nbsp;</div>
  </div><!-- end #preFooter -->

  <div id="footer">
    <p class="wpzoom"><a href="http://www.wpzoom.com" target="_blank"><?php _e('Portfolio WordPress Theme', 'wpzoom'); ?></a> by <a href="http://www.wpzoom.com" target="_blank" title="Premium WordPress Themes"><img src="<?php bloginfo('template_directory'); ?>/images/wpzoom.png" alt="WPZOOM" /></a></p>
    <p class="copy"><?php _e('Copyright', 'wpzoom'); ?> &copy; <?php echo date("Y",time()); ?> <?php bloginfo('name'); ?>. <?php _e('All Rights Reserved', 'wpzoom'); ?>.</p>
  </div><!-- end #footer -->
</div>    
</div><!-- end #container -->

		<?php wp_footer(); ?>
<?php if ($wpzoom_misc_analytics != '' && $wpzoom_misc_analytics_select == 'Yes')
{
  echo stripslashes($wpzoom_misc_analytics);
} ?>
</body>

<script language="javascript">
dp.SyntaxHighlighter.ClipboardSwf = "<?php echo get_template_directory_uri(); ?>/syntaxhighlighter/js/clipboard.swf";
dp.SyntaxHighlighter.HighlightAll('code');
</script>

</html>
