<!-- Wrapper -->
<?php ( $this->listOptions['horizontal-space-tiles'] )? $spacer = '' : $spacer = 'without-space'; ?>
<div class="otw-twentyfour otw-columns otw-bm-list-section">
  <?php echo $this->getViewAll(); ?>

  <div class="otw-row">
    <!-- Horizontal layout - with space -->
    <div class="otw-twentyfour otw-columns otw_blog_manager-horizontal-layout-wrapper otw_blog_manager-horizontal-layout-items bm_clearfix <?php echo $spacer;?>" id="otw-bm-list-<?php echo $this->listOptions['id'];?>">
      <!-- Blog Horizontal loyout Itmes -->
      <div class="otw_blog_manager-blog-item-holder <?php echo $this->getInfiniteScrollHorizontal();?>" data-item-margin="4">

        <?php
          $itemIndex = 0; 
          $row = 0;
          $randomWidth = array(244, 345, 246, 478, 264, 600, 172, 130, 391, 738, 531);
          
          foreach( $otw_bm_posts->posts as $post ): 

            $postAsset = $this->getPostAsset( $post );
            $imgAsset = parse_url( $postAsset );

            $postLink = $this->getLink($post, 'media');

            if( $itemIndex > 10 ) { $itemIndex = 0; }
            if( $itemIndex % 5 == 0 ) { $row++; }
        ?>
        <div class="otw_blog_manager-horizontal-item" data-original-width="<?php echo $randomWidth[ $itemIndex ];?>" data-original-height="230" data-row="<?php echo $row;?>">
          <article class="otw_blog_manager-blog-full only-media hover-style-11-showcontent">
            <?php $this->getMediaProportions(); ?>
            <div class="otw_blog_manager-blog-media-wrapper otw_blog_manager-format-image">        
            <?php if( !empty( $postLink ) ) : ?>
              <a href="<?php echo $postLink;?>" title="<?php echo $post->post_title;?>">
                <img src="<?php echo $this->otwImageCrop->resize( $imgAsset['path'], $this->imageWidth, $this->imageHeight, $this->imageCrop, $this->imageWhiteSpaces, $this->imageBackground, $this->imageFormat );?>" alt="">
              </a>
            <?php else: ?>
              <img src="<?php echo $this->otwImageCrop->resize( $imgAsset['path'], $this->imageWidth, $this->imageHeight, $this->imageCrop, $this->imageWhiteSpaces, $this->imageBackground, $this->imageFormat );?>" alt="">
            <?php endif; ?>
            </div>

            <?php if( $this->listOptions['horizontal-content'] ) : ?>
            <div class="otw_blog_manager-rollover-content">
            <?php 
              $items = explode(',', $this->blogItems);

              $interfaceHTML = '';

              foreach( $items as $item ): 
                switch ( $item ) {
                  case 'title':
                    $interfaceHTML .= $this->getTitle( $post );
                  break;
                  case 'meta':
                    $interfaceHTML .= $this->buildInterfaceMetaItems( $this->metaItems, $post );
                  break;
                  case 'description':
                    $interfaceHTML .= $this->getContent( $post );
                  break;
                  case 'continue-reading':
                    $interfaceHTML .= $this->getContinueRead( $post );
                  break;
                }
              endforeach;

              echo $interfaceHTML;
            ?>
            </div>
            <?php endif; ?>

          </article>
        </div>

        <?php
          $itemIndex++;
          endforeach;
        ?>

      </div>
      <!-- End Blog Horizontal loyout Itmes -->
    </div>
    <!-- End Horizontal layout - with space-->
  </div>

  <?php if( !empty( $this->listOptions['show-pagination'] ) ) : ?>
  <!-- Pagination -->
  <div class="row">
    <div class="otw-twentyfour otw-columns">
      <?php echo $this->getPagination( $otw_bm_posts ); ?>
    </div>
  </div>
  <!-- End Pagination -->
  <?php endif; ?>
  
</div>
<!-- End Wrapper -->