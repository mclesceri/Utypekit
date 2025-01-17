<?php 
  ( $this->listOptions['show-slider-title'] )? $caption = 'has-caption' : $caption = '';
  ( $this->listOptions['slider_border'] )? $caption .= ' with-border' : $caption = $caption;
  ( $this->listOptions['slider_title_bg'] )? $cationBG = 'with-bg' : $cationBG = '';

  switch ( $this->listOptions['slider_title_alignment'] ) {
    case 'center':
      $caption .= ' caption-center';
    break;
    case 'right':
      $caption .= ' caption-right';
    break;
    default:
      $caption .= ' caption-left';
    break;
  }
?>

<section class="otw-twentyfour otw-columns" id="otw-bm-list-<?php echo $this->listOptions['id'];?>">
  
<!-- Slider without title & excpert -->
<div 
  class="otw_blog_manager-slider <?php echo $caption;?>"
  data-animation="slide"
  data-item-per-page="1"
  data-item-margin=""
  data-nav="<?php echo $this->listOptions['slider_nav'];?>"
  data-auto-slide="<?php echo $this->listOptions['slider-auto-scroll'];?>"
  > 
  <ul class="slides">
    <?php 
      $embededMediaTypes = array('soundcloud', 'vimeo', 'youtube');

      foreach( $otw_bm_posts->posts as $post ): 
        $postAsset  = $this->getPostAsset( $post );
        $asset      = parse_url( $postAsset );

        $metaBoxInfo = get_post_meta( $post->ID, 'otw_bm_meta_data', true );
        ( !empty( $metaBoxInfo ) )? $postMetaData = $metaBoxInfo : $postMetaData = array('media_type' => '');

        $widgetPostLink = $this->getLink($post, 'media');
        $widgetTitleLink = $this->getLink($post, 'title');
    ?>
    <li>
      <?php echo $this->getMedia( $post ); ?>

      <?php if( $this->listOptions['show-slider-title'] && !in_array($postMetaData['media_type'], $embededMediaTypes)) : ?>
      <div class="otw_blog_manager-flex-caption otw_blog_manager-format-gallery <?php echo $cationBG;?>">
        
        <h3 class="otw_blog_manager-caption-title" data-item="title">
          <?php if( !empty($widgetTitleLink) ) : ?>
            <a href="<?php echo $widgetTitleLink;?>" class="otw-slider-image"><?php echo $post->post_title;?></a>
          <?php else: ?>
            <?php echo $post->post_title;?>
          <?php endif; ?>
        </h3>

        <div class="otw_blog_manager-caption-excpert">
	<?php 
		$postContent = $this->getPostExcerpt( $post );
		
		$strip_tags = false;
		
		if( !isset( $this->listOptions['strip_tags'] ) || ( $this->listOptions['strip_tags'] != 'no' ) ){
			$postContent = strip_tags( $postContent );
			$strip_tags = true;
		}
		
		if( !isset( $this->listOptions['strip_shortcodes'] ) || ( $this->listOptions['strip_shortcodes'] != 'no' ) ){
			$postContent = strip_shortcodes( $postContent );
		}else{
			$postContent = do_shortcode($postContent);
		}
		
		if( !empty( $this->listOptions['excerpt_length'] ) ){
			$postContent = $this->excerptLength( $postContent, $this->listOptions['excerpt_length'], $strip_tags );
		}
		
		echo nl2br( $postContent );
	?>
        </div>

      </div> <!-- End Caption -->
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>
</div>
<!-- End Slider without title & excpert -->
</section>