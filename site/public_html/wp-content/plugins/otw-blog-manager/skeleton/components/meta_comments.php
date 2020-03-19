<!-- Post Comments -->
<div class="otw_blog_manager-blog-comment">
  <?php if( !$this->listOptions['meta_icons'] ) : ?>
  <span class="head"><?php _e('Comments:', OTW_TRANSLATION);?></span>
  <?php else: ?>
  <span class="head"><i class="icon-comments"></i></span>
  <?php endif; ?>
  <a href="<?php echo get_comments_link($post->ID);?>" title="<?php _e('Comment on ', OTW_TRANSLATION); echo $post->post_title;?>"><?php echo $post->comment_count;?></a>
</div>
<!-- END Post Comments -->