<!-- Blog 3 Columns -->
<section class="otw-twentyfour otw-columns otw-bm-list-section" id="otw-bm-list-<?php echo $this->listOptions['id'];?>">

  <?php echo $this->getViewAll(); ?>
  <?php echo $this->getNewsFilter(); ?>
  <?php echo $this->getNewsSort(); ?>

  <!-- Blog Newspaper Itmes -->
  <div class="otw-row otw_blog_manager-blog-item-holder <?php echo $this->getInfiniteScroll();?>  otw_blog_manager-blog-newspaper">

  <?php 
    foreach( $otw_bm_posts->posts as $post ):

      // Post Categories are used for filter
      $postCategories = get_the_category($post->ID);
      $categoriesString = '';
      foreach( $postCategories as $postCategory ):
        $categoriesString .= $postCategory->slug.' ';
      endforeach;
  ?>

    <div class="otw-eight otw-columns otw_blog_manager-blog-newspaper-item <?php echo $categoriesString;?>">
      <article class="otw_blog_manager-blog-full icon__small <?php echo $this->containerBG; ?> <?php echo $this->containerBorder; ?>">        
        <?php echo $this->buildInterfaceBlogItems( $post ); ?>
        <?php echo $this->getSocial( $post ); ?>
        <?php echo $this->getDelimiter( $post ); ?>
      </article>
    </div>
  <?php
    endforeach;
  ?>
  </div>
  <!-- End Blog Newspaper Itmes -->

  <?php echo $this->getPagination( $otw_bm_posts ); ?>
</section>
<!-- End Blog 3 Columns -->