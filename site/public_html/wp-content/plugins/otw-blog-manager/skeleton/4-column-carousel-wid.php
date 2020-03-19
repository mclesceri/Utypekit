<!-- Sidebar -->
<aside class="otw-twentyfour otw-columns otw_blog_manager-sidebar" id="otw-bm-list-<?php echo $this->listOptions['id'];?>">
  <ul class="bm_clearfix">
    <li class="widget otw_blog_manager-widget-carousel bm_clearfix">
      <div class="otw_blog_manager-slider otw_blog_manager-carousel flex-viewport" data-type="widget" data-animation="slide" data-item-per-page="4" data-item-margin="10" data-nav="<?php echo $this->listOptions['slider_nav'];?>" data-auto-slide="<?php echo $this->listOptions['slider-auto-scroll'];?>">
        <ul class="slides">
          <?php
            foreach( $otw_bm_posts->posts as $widgetPost ):
              $widgetAsset = $this->getPostAsset( $widgetPost );
              $imgAsset = parse_url( $widgetAsset );

              $widgetPostLink = $this->getLink($widgetPost, 'media');
          ?>
          <li>
            <?php $this->getMediaProportions(); ?>
            <?php if( !empty( $widgetPostLink ) ) : ?>
              <a href="<?php echo $widgetPostLink;?>" class="otw-slider-image" title="<?php echo $widgetPost->post_title;?>">
                <img src="<?php echo $this->otwImageCrop->resize( $imgAsset['path'], $this->imageWidth, $this->imageHeight, $this->imageCrop, $this->imageWhiteSpaces, $this->imageBackground, $this->imageFormat );?>" alt="">
              </a>
            <?php else: ?>
              <img src="<?php echo $this->otwImageCrop->resize( $imgAsset['path'], $this->imageWidth, $this->imageHeight, $this->imageCrop, $this->imageWhiteSpaces, $this->imageBackground, $this->imageFormat );?>" alt="">
            <?php endif; ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </li>
  </ul>
</aside>
<!-- End Sidebar -->