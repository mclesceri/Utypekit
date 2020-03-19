<!-- Blog Mosaic 1/4 - with space -->
<?php ( $this->listOptions['space-tiles'] )? $spacer = '' : $spacer = 'without-space'; ?>
<section class="otw-twentyfour otw-columns otw-bm-list-section" id="otw-bm-list-<?php echo $this->listOptions['id'];?>">

  <?php echo $this->getViewAll(); ?>
  <?php echo $this->getMosaicFilter(); ?>
  <?php echo $this->getMosaicSort(); ?>

  <div class="otw-row">
    <!-- Blog Newspaper Itmes -->
    <div class="otw-twentyfour otw-columns otw_blog_manager-blog-item-holder otw_blog_manager-blog-newspaper <?php echo $this->getInfiniteScroll();?> otw_blog_manager-mosaic-layout <?php echo $spacer;?>">
        <?php 
          $count = 0;
          foreach( $otw_bm_posts->posts as $post ):

            $postAsset = $this->getPostAsset( $post );
            $imgAsset = parse_url( $postAsset );

            $postLink = $this->getLink($post, 'media');

            // Post Categories are used for filter
            $postCategories = get_the_category($post->ID);
            $categoriesString = '';
            foreach( $postCategories as $postCategory ):
              $categoriesString .= $postCategory->slug.' ';
            endforeach;

            if( $count > 6 ) {
              $count = 0;
            }

            if( $count == 1 ) {
              $class = 'otw_blog_manager-1-2';
            } elseif ( $count == 3 ){
              $class = 'otw_blog_manager-1-4 height2';
            } elseif ( $count == 6 ) {
              $class = 'otw_blog_manager-1-2 height1';
            } else {
              $class = 'otw_blog_manager-1-4';
            }
        ?>

          <div class="<?php echo $class;?> otw_blog_manager-iso-item otw_blog_manager-blog-newspaper-item <?php echo $categoriesString;?>">
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

              <?php if( $this->listOptions['mosaic-content'] ) : ?>
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
                <?php echo $this->getSocial( $post ); ?>
              </div>
              <?php endif; ?>
              
            </article>
          </div>

        <?php
          $count++;
          endforeach;
        ?>
    </div>
    <!-- End Blog Newspaper Itmes -->
  </div>

  <?php echo $this->getPagination( $otw_bm_posts ); ?>
</section>
<!-- End Blog Mosaic 1/4 - with space -->