<!-- Blog Newspaper Filter -->
<div class="otw-row">
  <div class="otw-twentyfour otw-columns">

    <div class="otw_blog_manager-blog-newspaper-filter">
      <ul class="option-set otw_blog_manager-blog-filter bm_clearfix">
        <li><a href="#" data-filter="*" class="selected"><?php _e('All', OTW_TRANSLATION)?></a></li>
        <?php 
		$filterCategories = array();
		if( isset( $this->listOptions['all_categories'] ) && strlen( $this->listOptions['all_categories'] ) ){
			
			$exists_categories = get_categories( array( 'number' => '', 'hide_empty' => false  ) );
			
			if( count( $exists_categories ) ){
				foreach( $exists_categories as $e_cat ){
					$filterCategories[ $e_cat->term_id ] = $e_cat->term_id;
				}
			}
		}elseif( !empty( $this->listOptions['categories'] ) ){
			$filterCategories = explode(',', $this->listOptions['categories']);
		}
          
          foreach( $filterCategories as $filterCategory ):
            $cat = get_category( $filterCategory );
        ?>
        <li><a href="#" data-filter=".<?php echo $cat->slug;?>"><?php echo $cat->name;?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

  </div>
</div>
<!-- End Blog Newspaper Filter -->