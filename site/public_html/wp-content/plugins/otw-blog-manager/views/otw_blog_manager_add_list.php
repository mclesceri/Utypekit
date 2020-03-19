<?php
	$writableCssError = $this->check_writing( SKIN_BM_PATH );
	
	$selectOptionData = array(
		array( 'value' => 0, 'text'	=> '------' ),
		array( 'value' => '1-column', 'text' => __('Grid - Blog 1 Column', OTW_TRANSLATION) ),
		array( 'value' => '2-column', 'text' => __('Grid - Blog 2 Columns', OTW_TRANSLATION) ),
		array( 'value' => '3-column', 'text' => __('Grid - Blog 3 Columns', OTW_TRANSLATION) ),
		array( 'value' => '4-column', 'text' => __('Grid - Blog 4 Columns', OTW_TRANSLATION) ),
		array( 'value' => '1-column-lft-img', 'text' => __('Image Left - Blog 1 Column', OTW_TRANSLATION) ),
		array( 'value' => '2-column-lft-img', 'text' => __('Image Left - Blog 2 Columns', OTW_TRANSLATION) ),
		array( 'value' => '1-column-rght-img', 'text' => __('Image Right - Blog 1 Column', OTW_TRANSLATION) ),
		array( 'value' => '2-column-rght-img', 'text' => __('Image Right - Blog 2 Columns', OTW_TRANSLATION) ),
		array( 'value' => '2-column-news', 'text' => __('Newspaper - Blog 2 Columns', OTW_TRANSLATION) ),
		array( 'value' => '3-column-news', 'text' => __('Newspaper - Blog 3 Columns', OTW_TRANSLATION) ),
		array( 'value' => '4-column-news', 'text' => __('Newspaper - Blog 4 Columns', OTW_TRANSLATION) ),
		array( 'value' => 'widget-lft', 'text' => __('Widget Style - Image Left', OTW_TRANSLATION) ),
		array( 'value' => 'widget-rght', 'text' => __('Widget Style - Image Right', OTW_TRANSLATION) ),
		array( 'value' => 'widget-top', 'text' => __('Widget Style - Image Top', OTW_TRANSLATION) ),
		array( 'value' => 'timeline', 'text' => __('Timeline', OTW_TRANSLATION) ),
		array( 'value' => 'slider', 'text' => __('Slider', OTW_TRANSLATION) ),
		array( 'value' => '3-column-carousel', 'text' => __('Carousel - 3 Columns', OTW_TRANSLATION) ),
		array( 'value' => '4-column-carousel', 'text' => __('Carousel - 4 Columns', OTW_TRANSLATION) ),
		array( 'value' => '5-column-carousel', 'text' => __('Carousel - 5 Columns', OTW_TRANSLATION) ),
		array( 'value' => '2-column-carousel-wid', 'text' => __('Widget Carousel - 2 Columns', OTW_TRANSLATION) ),
		array( 'value' => '3-column-carousel-wid', 'text' => __('Widget Carousel - 3 Columns', OTW_TRANSLATION) ),
		array( 'value' => '4-column-carousel-wid', 'text' => __('Widget Carousel - 4 Columns', OTW_TRANSLATION) ),
	);

	$selectPaginationData = array(
		array( 'value' => '0', 'text' => __('None (default)', OTW_TRANSLATION) ),
		array( 'value' => 'pagination', 'text' => __('Standard Pagination', OTW_TRANSLATION) ),
		array( 'value' => 'load-more', 'text' => __('Load More Pagination', OTW_TRANSLATION) ),
		array( 'value' => 'infinit-scroll', 'text' => __('Infinit Scroll', OTW_TRANSLATION) ),
	);	

	$selectSocialData = array(
		array( 'value' => '0', 'text' => __('None (default)', OTW_TRANSLATION) ),
		array( 'value' => 'share_icons', 'text' => __('Share Icons', OTW_TRANSLATION) ),
		array( 'value' => 'share_btn_small', 'text' => __('Share Buttons Small', OTW_TRANSLATION) ),
		array( 'value' => 'share_btn_large', 'text' => __('Share Buttons Large', OTW_TRANSLATION) ),
		array( 'value' => 'like_buttons', 'text' => __('Like Buttons', OTW_TRANSLATION) ),
		array( 'value' => 'custom_icons', 'text' => __('Custom Social Icons', OTW_TRANSLATION) )
	);	

	$selectOrderData = array(
		array( 'value' => 'date_desc', 'text' => __('Latest Created (default)', OTW_TRANSLATION) ),
		array( 'value' => 'date_asc', 'text' => __('Oldest Created', OTW_TRANSLATION) ),
		array( 'value' => 'modified_desc', 'text' => __('Latest Modified', OTW_TRANSLATION) ),
		array( 'value' => 'modified_asc', 'text' => __('Oldest Modified', OTW_TRANSLATION) ),
		array( 'value' => 'title_asc', 'text' => __('Alphabetically: A-Z', OTW_TRANSLATION) ),
		array( 'value' => 'title_desc', 'text' => __('Alphabetically: Z-A', OTW_TRANSLATION) ),
		array( 'value' => 'rand_', 'text' => __('Random', OTW_TRANSLATION) )
	);

	$selectHoverData = array(
		array( 'value' => 'hover-none', 'text' => __('None', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-1-full', 'text' => __('Full (default)', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-2-shadowin', 'text' => __('Shadowin', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-3-border', 'text' => __('Border', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-4-slidetop', 'text' => __('Slide Top', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-5-slideright', 'text' => __('Slide Right', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-6-zoom', 'text' => __('Zoom', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-7-shadowout', 'text' => __('Shadow Out', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-8-slidedown', 'text' => __('Slide Down', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-9-slideleft', 'text' => __('Slide Left', OTW_TRANSLATION) ),
		// array( 'value' => 'hover-style-10-contentslide', 'text' => __('Content Slide', OTW_TRANSLATION) ),
		// array( 'value' => 'hover-style-11-showcontent', 'text' => __('Show Content', OTW_TRANSLATION) ),
		// array( 'value' => 'hover-style-12-contentslidetop', 'text' => __('Content Slide Top', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-14-desaturate', 'text' => __('Desaturate', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-15-blur', 'text' => __('Blur', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-16-orton', 'text' => __('Orton', OTW_TRANSLATION) ),
		array( 'value' => 'hover-style-17-glow', 'text' => __('Glow', OTW_TRANSLATION) ),
	);

	$selectIconData = array(
		array( 'value' => 0, 'text' => __('None (default)', OTW_TRANSLATION) ),
		array( 'value' => 'icon-expand', 'text' => __('Icon Expand', OTW_TRANSLATION) ),
		array( 'value' => 'icon-youtube-play', 'text' => __('Icon YouTube Play', OTW_TRANSLATION) ),
		array( 'value' => 'icon-file', 'text' => __('Icon File', OTW_TRANSLATION) ),
		array( 'value' => 'icon-book', 'text' => __('Icon Book', OTW_TRANSLATION) ),
		array( 'value' => 'icon-check-sign', 'text' => __('Icon Check Sign', OTW_TRANSLATION) ),
		array( 'value' => 'icon-comments', 'text' => __('Icon Comments', OTW_TRANSLATION) ),
		array( 'value' => 'icon-ok-sign', 'text' => __('Icon OK Sign', OTW_TRANSLATION) ),
		array( 'value' => 'icon-zoom-in', 'text' => __('Icon Zoom In', OTW_TRANSLATION) ),
		array( 'value' => 'icon-thumbs-up-alt', 'text' => __('Icon Thumbs Up Alt', OTW_TRANSLATION) ),
		array( 'value' => 'icon-plus-sign', 'text' => __('Icon Plus Sign', OTW_TRANSLATION) ),
		array( 'value' => 'icon-cloud', 'text' => __('Icon Cloud', OTW_TRANSLATION) ),
		array( 'value' => 'icon-chevron-sign-right', 'text' => __('Icon Chevron Sign Right', OTW_TRANSLATION) ),
		array( 'value' => 'icon-hand-right', 'text' => __('Icon Hand Right', OTW_TRANSLATION) ),
		array( 'value' => 'icon-fullscreen', 'text' => __('Icon Fullscreen', OTW_TRANSLATION) ),
	);
	
	$selectLinkData = array(
		array( 'value' => 'single', 'text' => __('Single Post (default)', OTW_TRANSLATION) ),
		array( 'value' => 'lightbox', 'text' => __('Lightbox', OTW_TRANSLATION) ),
		array( 'value' => 'no-link', 'text' => __('No Link', OTW_TRANSLATION) ),
	);

	$selectMetaData = array(
		array( 'value' => 'horizontal', 'text' => __('Horizontal (default)', OTW_TRANSLATION) ),
		array( 'value' => 'vertical', 'text' => __('Vertical', OTW_TRANSLATION) ),
	);
	
	$selectStripTags = array(
		array( 'value' => 'yes', 'text' => __('Yes (default)', OTW_TRANSLATION) ),
		array( 'value' => 'no', 'text' => __('No', OTW_TRANSLATION) ),
	);
	
	$selectStripShortcodes = array(
		array( 'value' => 'yes', 'text' => __('Yes (default)', OTW_TRANSLATION) ),
		array( 'value' => 'no', 'text' => __('No', OTW_TRANSLATION) ),
	);
	
	$selectSliderAlignmentData = array(
		array( 'value' => 'left', 'text' => __('Left (default)', OTW_TRANSLATION) ),
		array( 'value' => 'center', 'text' => __('Center', OTW_TRANSLATION) ),
		array( 'value' => 'right', 'text' => __('Right', OTW_TRANSLATION) ),
	);

	$selectMosaicData = array(
		array( 'value' => 'full', 'text' => __('Full Content on Hover (default)', OTW_TRANSLATION) ),
		array( 'value' => 'slide', 'text' => __('Slide Content on Hover', OTW_TRANSLATION) ),
	);

	$selectFontSizeData = array(
		array( 'value' => '', 'text' => __('None (default)', OTW_TRANSLATION) ),
		array( 'value' => '8', 'text' => '8px' ),
		array( 'value' => '10', 'text' => '10px' ),
		array( 'value' => '12', 'text' => '12px' ),
		array( 'value' => '14', 'text' => '14px' ),
		array( 'value' => '16', 'text' => '16px' ),
		array( 'value' => '18', 'text' => '18px' ),
		array( 'value' => '20', 'text' => '20px' ),
		array( 'value' => '22', 'text' => '22px' ),
		array( 'value' => '24', 'text' => '24px' ),
		array( 'value' => '26', 'text' => '26px' ),
		array( 'value' => '28', 'text' => '28px' ),
		array( 'value' => '30', 'text' => '30px' ),
		array( 'value' => '32', 'text' => '32px' ),
		array( 'value' => '34', 'text' => '34px' ),
		array( 'value' => '36', 'text' => '36px' ),
		array( 'value' => '38', 'text' => '38px' ),
		array( 'value' => '40', 'text' => '40px' ),
	);

	$selectFontStyleData = array(
		array( 'value' => '', 'text' => __('None (default)', OTW_TRANSLATION) ),
		array( 'value' => 'regular', 'text' => __('Regular', OTW_TRANSLATION) ),
		array( 'value' => 'bold', 'text' => __('Bold', OTW_TRANSLATION) ),
		array( 'value' => 'italic', 'text' => __('Italic', OTW_TRANSLATION) ),
		array( 'value' => 'bold_italic', 'text' => __('Bold and Italic', OTW_TRANSLATION) ),
	);

	$selectViewTargetData = array(
		array( 'value' => '_self', 'text' => __('Same Window / Tab (default)', OTW_TRANSLATION) ),
		array( 'value' => '_blank', 'text' => __('New Window / Tab', OTW_TRANSLATION) ),
	);

	$selectCategoryTagRelation = array(
		array( 'value' => 'OR', 'text' => __('categories OR tags (default)', OTW_TRANSLATION) ),
		array( 'value' => 'AND', 'text' => __('categories AND tags', OTW_TRANSLATION) )
	);
	
	$selectBorderStyleData = array(
		array( 'value' => '', 'text' => __('None (default)', OTW_TRANSLATION) ),
		array( 'value' => 'solid', 'text' => 'Solid' ),
		array( 'value' => 'dashed', 'text' => 'Dashed' ),
		array( 'value' => 'dotted', 'text' => 'Dotted' )
	);
	
	$selectBorderSizeData = array(
		array( 'value' => '', 'text' => __('None (default)', OTW_TRANSLATION) ),
		array( 'value' => '1', 'text' => '1px' ),
		array( 'value' => '2', 'text' => '2px' ),
		array( 'value' => '3', 'text' => '3px' ),
		array( 'value' => '4', 'text' => '4px' )
	);
	
	$thumb_format_options = array(
		'' => __('Keep original file format (default)', OTW_TRANSLATION ),
		'jpg' => 'jpg',
		'png' => 'png',
		'gif' => 'gif'
	);
	
	$js_template_options = array();
	
	if( isset( $templateOptions ) && is_array( $templateOptions ) ){
		
		foreach( $templateOptions as $t_option ){
			$js_template_options[ $t_option['name'] ] = $t_option;
		}
	}
	
?>


<div class="wrap">
	<div id="icon-edit" class="icon32"></div>
	<h2>
		<?php
			if( empty($this->errors) && !empty($content['list_name']) ) {
				echo __( 'Edit Blog List', OTW_TRANSLATION ); 	
			} else {
				echo __( 'Create New Blog List', OTW_TRANSLATION );
			}
		?>
		<a class="add-new-h2" href="admin.php?page=otw-bm"><?php _e('Back', OTW_TRANSLATION);?></a>
	</h2>
	<?php
		if( $writableCssError ) {
			$message = __('The folder \''.SKIN_BM_PATH.'\' is not writable. Please make sure you add read/write permissions to this folder.', 'otw_bm');
			 echo '<div class="error"><p>'.$message.'</p></div>';
		}
	?>
	<?php
	if( !empty( $_GET['success'] ) && $_GET['success'] == 'true' ) {
			$message = __('Item was saved.', OTW_TRANSLATION);
			echo '<div class="updated"><p>'.$message.'</p></div>';
	}
	?>
	<form name="otw-bm-list" method="post" action="" class="validate">

		<input type="hidden" name="id" value="<?php echo $nextID;?>" />
		<input type="hidden" name="edit" value="<?php echo $edit;?>" />
		<input type="hidden" name="date_created" value="<?php echo $content['date_created'];?>" />
		<input type="hidden" name="user_id" value="<?php echo get_current_user_id();?>" />

		<?php
			if( !empty($this->errors) ){
				$errorMsg = __('Oops! Please check form for errors.', OTW_TRANSLATION);
				echo '<div class="error"><p>'.$errorMsg.'</p></div>';
			}
		?>
		<script type="text/javascript">
		<?php
			
			echo 'var js_template_options='.json_encode( $js_template_options ).';'
		?>
		</script>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="list_name" class="required"><?php _e('Blog List Name', OTW_TRANSLATION);?></label></th>
					<td>
						<input type="text" name="list_name" id="list_name" size="53" value="<?php echo $content['list_name'];?>" />
						<p class="description"><?php _e( 'Note: The List Name is going to be used ONLY for the admin as a reference.', OTW_TRANSLATION);?></p>
						<div class="inline-error">
							<?php 
								( !empty($this->errors['list_name']) )? $errorMessage = $this->errors['list_name'] : $errorMessage = ''; 
								echo $errorMessage;
							?>
						</div>
					</td>
				</tr>				
				<tr valign="top">
					<th scope="row"><label for="template" class="required"><?php _e('Choose Template', OTW_TRANSLATION);?></label></th>
					<td>
						<select id="template" name="template" class="js-template-style">
						<?php 
						foreach( $selectOptionData as $optionData ): 
							$selected = '';
							if( $optionData['value'] === $content['template'] ) {
								$selected = 'selected="selected"';
							}
							echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
							
						endforeach;
						?>
						</select>
						<div class="inline-error">
							<?php 
								( !empty($this->errors['template']) )? $errorMessage = $this->errors['template'] : $errorMessage = ''; 
								echo $errorMessage;
							?>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="categories"><?php _e('Categories:', OTW_TRANSLATION);?></label>
					</th>
					<td>
						<?php 
								$categoriesCount 	= wp_count_terms( 'category', array( 'number' => '', 'hide_empty' => false  ) );
								$categoriesStatus = 'otw-admin-hidden';
								$categoriesAll 		= '';
								$categoriesInput 	= '';
								
								if( !empty($content['select_categories']) ) {
									
									$categoriesStatus = '';
									$categoriesAll = 'checked="checked"';
									$categoriesInput = 'disabled="disabled"';
								}
						?>
						<input type="text" name="categories" id="categories" class="select2-input js-categories" value="<?php echo $content['categories'];?>" <?php echo $categoriesInput;?> /><br/>
						<?php _e('- OR -', OTW_TRANSLATION); ?><br/>
						<input type="hidden" name="all_categories" class="js-categories-select" value="<?php echo $content['all_categories'];?>" />
						<input type="checkbox" name="select_categories" value="1" data-size="<?php echo $categoriesCount;?>" class="js-select-categories" id="select_all_categories" data-section="categories" <?php echo $categoriesAll;?> />
						<label for="select_all_categories">
							<?php _e('Select All', OTW_TRANSLATION);?>
							<span class="js-categories-count <?php echo $categoriesStatus; ?>">
								(
								<span class="js-categories-counter"><?php echo $categoriesCount;?></span>
								<?php _e(' categories selected', OTW_TRANSLATION);?>
								)
							</span>
						</label>
						<p class="description"><?php _e( 'Choose categories to include posts from those categories in your list or use the Select all checkbox to include posts from all categories.', OTW_TRANSLATION);?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="exclude_categories"><?php _e('Exclude Categories:', OTW_TRANSLATION);?></label>
					</th>
					<td>
						<?php 
								$exclude_categoriesAll 		= '';
								$exclude_categoriesInput 	= '';
						?>
						<input type="text" name="exclude_categories" id="exclude_categories" class="select2-input js-exclude_categories" value="<?php echo $content['exclude_categories'];?>" <?php echo $exclude_categoriesInput;?> /><br/>
						<p class="description"><?php _e( 'Choose categories to exclude posts from those categories in your list.', OTW_TRANSLATION);?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tags"><?php _e('Tags:', OTW_TRANSLATION);?></label>
					</th>
					<td>
						<?php 
								$tagsCount 	= wp_count_terms( 'post_tag', array( 'number' => '', 'hide_empty' => false  ) );
								
								$tagsStatus = 'otw-admin-hidden';
								$tagsAll 		= '';
								$tagsInput 	= '';
								if( !empty($content['select_tags']) ) {
									
									$tagsStatus = '';
									$tagsAll = 'checked="checked"';
									$tagsInput = 'disabled="disabled"';
								}
						?>
						<input type="text" name="tags" id="tags" class="select2-input js-tags" value="<?php echo $content['tags'];?>" <?php echo $tagsInput;?>/><br/>
						<?php _e('- OR -', OTW_TRANSLATION); ?><br/>
						<input type="hidden" name="all_tags" class="js-tags-select" value="<?php echo $content['all_tags'];?>" />
						<input type="checkbox" name="select_tags" value="1" class="js-select-tags" data-size="<?php echo $tagsCount;?>" id="select_all_tags" data-section="tags" <?php echo $tagsAll;?>/>
						<label for="select_all_tags">
							<?php _e('Select All', OTW_TRANSLATION); ?>
							<span class="js-tags-count <?php echo $tagsStatus;?>">
								(
								<span class="js-tags-counter"><?php echo $tagsCount;?></span>
								<?php _e(' tags selected', OTW_TRANSLATION);?>
								)
							</span>
						</label>
						<p class="description"><?php _e( 'Choose tags to include posts from those tags in your list or use the Select all checkbox to include posts from all tags.', OTW_TRANSLATION);?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="exclude_tags"><?php _e('Exclude Tags:', OTW_TRANSLATION);?></label>
					</th>
					<td>
						<?php 
								$exclude_tagsAll 		= '';
								$exclude_tagsInput 	= '';
						?>
						<input type="text" name="exclude_tags" id="exclude_tags" class="select2-input js-exclude_tags" value="<?php echo $content['exclude_tags'];?>" <?php echo $exclude_tagsInput;?> /><br/>
						<p class="description"><?php _e( 'Choose tags to exclude posts from those tags in your list.', OTW_TRANSLATION);?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="authors"><?php _e('Post Author:', OTW_TRANSLATION);?></label>
					</th>
					<td>
						<?php 
								$count_users = count_users();
								$usersCount = $count_users['total_users'];
								$usersStatus = 'otw-admin-hidden';
								$usersAll 		= '';
								$usersInput 	= '';
								if( !empty($content['select_users']) ) {
									
									$usersStatus = '';
									$usersAll = 'checked="checked"';
									$usersInput = 'disabled="disabled"';
								}
						?>
						<input type="text" name="users" id="users" class="select2-input js-users" value="<?php echo $content['users'];?>" <?php echo $usersInput;?>/><br/>
						<?php _e('- OR -', OTW_TRANSLATION); ?><br/>
						<input type="hidden" name="all_users" class="js-users-select" value="<?php echo $content['all_users'];?>" />
						<input type="checkbox" name="select_users" value="1" data-size="<?php echo $usersCount; ?>" class="js-select-users" id="select_all_users" data-section="users" <?php echo $usersAll;?>/>
						<label for="select_all_users">
							<?php _e('Select All', OTW_TRANSLATION); ?>
							<span class="js-users-count <?php echo $usersStatus; ?>">
								(
								<span class="js-users-counter"><?php echo $usersCount; ?></span>
								<?php _e(' authors selected', OTW_TRANSLATION);?>
								)
							</span>
						</label>
						<p class="description"><?php _e( 'Choose authors to include posts from those authors in your list or use the Select all checkbox to include posts from all authors.', OTW_TRANSLATION);?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="exclude_users"><?php _e('Exclude Authors:', OTW_TRANSLATION);?></label>
					</th>
					<td>
						<?php 
								$exclude_usersAll 		= '';
								$exclude_usersInput 	= '';
						?>
						<input type="text" name="exclude_users" id="exclude_users" class="select2-input js-exclude_users" value="<?php echo $content['exclude_users'];?>" <?php echo $exclude_usersInput;?> /><br/>
						<p class="description"><?php _e( 'Choose authors to exclude posts from those authors in your list.', OTW_TRANSLATION);?></p>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<div class="inline-error">
							<?php 
								( !empty($this->errors['content']) )? $errorMessage = $this->errors['content'] : $errorMessage = ''; 
								echo $errorMessage;
							?>
						</div>
					</td>
				</tr>

			</tbody>
		</table>

		<div class="accordion-container">
			<ul class="outer-border">
				
				<!-- List Elements and Order -->
				<li class="control-section accordion-section  add-page top">
					<h3 class="accordion-section-title hndl" tabindex="0" title="<?php _e('List Elements and Order', OTW_TRANSLATION);?>"><?php _e('List Elements and Order', OTW_TRANSLATION);?></h3>
					<div class="accordion-section-content" style="display: none;">
						<div class="inside">
							<table class="form-table">
								<tbody>
									<tr>
										<th scope="row">
											<label for="meta_order"><?php _e('Blog List Items', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<div class="active_elements">
												<h3><?php _e('Active Elements', OTW_TRANSLATION);?></h3>
												<input type="hidden" name="blog-items" class="js-blog-items" value="<?php echo $content['blog-items'];?>"/>
												<ul id="meta-active" class="b-bl-box js-bl-active">
												</ul>
											</div>
											<div class="inactive_elements">
												<h3><?php _e('Inactive Elements', OTW_TRANSLATION);?></h3>
												<ul id="meta-inactive" class="b-bl-box js-bl-inactive">
													<li data-item="main" data-value="media" class="b-bl-items js-bl--item"><?php _e('Media', OTW_TRANSLATION);?></li>
													<li data-item="main" data-value="title" class="b-bl-items js-bl--item"><?php _e('Title', OTW_TRANSLATION);?></li>
													<li data-item="main" data-value="meta" class="b-bl-items js-bl--item"><?php _e('Meta', OTW_TRANSLATION);?></li>
													<li data-item="main" data-value="description" class="b-bl-items js-bl--item"><?php _e('Description / Excerpt', OTW_TRANSLATION);?></li>
													<li data-item="main" data-value="continue-reading" class="b-bl-items js-bl--item"><?php _e('Continue Reading', OTW_TRANSLATION);?></li>
												</ul>
											</div>
											<p class="description">
												<?php _e('Drag & drop the items that you\'d like to show in the Active Elements area on the left. Arrange them however you want to see them in your list.', OTW_TRANSLATION);?>
											</p>
											<p class="description">
												<?php _e('The setting will not affect the following templates: Slider, Carousel, Widget Style, Carousel Widget', OTW_TRANSLATION); ?>
											</p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="show-pagination"><?php _e('Show Pagination', OTW_TRANSLATION);?></label>
										</th>
										<td>
											
										<select id="show-pagination" name="show-pagination">
											<?php 
											foreach( $selectPaginationData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['show-pagination'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<p class="description">
												<?php _e('Choose pagination type for your template.', OTW_TRANSLATION); ?><br/>
												<strong><?php _e('Note:', OTW_TRANSLATION);?></strong><br/>
												<?php _e('Widget Style templates support only Load More Pagination.', OTW_TRANSLATION); ?><br/>
												<?php _e('Slider templates do not support pagination.', OTW_TRANSLATION); ?><br/>
												<?php _e('Timeline template will have the Infinite Scroll by default.', OTW_TRANSLATION); ?>
											</p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="show-post-icon"><?php _e('Show Post Type Icon', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['show-post-icon'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="show-post-icon" id="show-post-icon-no" value="0" <?php echo $no;?> /> 
											<label for="show-post-icon-no"><?php _e('No (default)', OTW_TRANSLATION);?></label>

											<input type="radio" name="show-post-icon" id="show-post-icon-yes" value="1" <?php echo $yes;?>/> 
											<label for="show-post-icon-yes"><?php _e('Yes', OTW_TRANSLATION);?></label>
											<p class="description"><?php _e('Enable the post type icon over the media. This is the icon that shows what is the type of the post.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="show-delimiter"><?php _e('Show Delimiter', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['show-delimiter'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="show-delimiter" id="show-delimiter-no" value="0" <?php echo $no;?> /> 
											<label for="show-delimiter-no"><?php _e('No (default)', OTW_TRANSLATION);?></label>

											<input type="radio" name="show-delimiter" id="show-delimiter-yes" value="1" <?php echo $yes;?> /> 
											<label for="show-delimiter-yes"><?php _e('Yes', OTW_TRANSLATION);?></label>
											<p class="description"><?php _e('Enable 1px line after post.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="show-border"><?php _e('Show Border', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['show-border'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="show-border" id="show-border-no" value="0" <?php echo $no;?> /> 
											<label for="show-border-no"><?php _e('No (default)', OTW_TRANSLATION);?></label>

											<input type="radio" name="show-border" id="show-border-yes" value="1" <?php echo $yes;?> /> 
											<label for="show-border-yes"><?php _e('Yes', OTW_TRANSLATION);?></label>
											<p class="description"><?php _e("A border (1px) is going to be applied to all of your posts within the list", OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="show-background"><?php _e('Show Background', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['show-background'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="show-background" id="show-background-no" value="0" <?php echo $no;?> /> 
											<label for="show-background-no"><?php _e('No (default)', OTW_TRANSLATION);?></label>

											<input type="radio" name="show-background" id="show-background-yes" value="1" <?php echo $yes;?>/> 
											<label for="show-background-yes"><?php _e('Yes', OTW_TRANSLATION);?></label>
											<p class="description"><?php _e("A background is going to be present on all of the posts within the list", OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="show-social-icons"><?php _e('Show Social Icons', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['show-social-icons'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<select id="show-social-icons" name="show-social-icons">
											<?php 
											foreach( $selectSocialData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['show-social-icons'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<p class="description">
												<?php 
												_e("Social Icons will make your posts easy to share in social networks. Note that to use 
													\"Share buttons small\" and \"Share buttons large\" you need to have CURL installed on your server.
													", OTW_TRANSLATION);
												?>
											</p>
										</td>
									</tr>
									<tr id="otw-show-social-icons-type">
										<th scope="row">
											<label for="show-social-icons-type"><?php _e('Select Social Icons', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<input type="checkbox" id="show-social-icons-type" name="show-social-icons-facebook" value="1" <?php echo ( $content['show-social-icons-facebook'] )?' checked="checked"':''?> /><label for="show-social-icons-type"><?php _e('Facebook', OTW_TRANSLATION);?></label>
											<input type="checkbox" id="show-social-icons-twitter" name="show-social-icons-twitter" value="1" <?php echo ( $content['show-social-icons-twitter'] )?' checked="checked"':''?>/><label for="show-social-icons-twitter"><?php _e('Twitter', OTW_TRANSLATION);?></label>
											<input type="checkbox" id="show-social-icons-googleplus" name="show-social-icons-googleplus" value="1" <?php echo ( $content['show-social-icons-googleplus'] )?' checked="checked"':''?>/><label for="show-social-icons-googleplus"><?php _e('Google+', OTW_TRANSLATION);?></label>
											<input type="checkbox" id="show-social-icons-linkedin" name="show-social-icons-linkedin" value="1" <?php echo ( $content['show-social-icons-linkedin'] )?' checked="checked"':''?>/><label for="show-social-icons-linkedin"><?php _e('LinkedIn', OTW_TRANSLATION);?></label>
											<input type="checkbox" id="show-social-icons-pinterest" name="show-social-icons-pinterest" value="1" <?php echo ( $content['show-social-icons-pinterest'] )?' checked="checked"':''?>/><label for="show-social-icons-pinterest"><?php _e('Pinterest', OTW_TRANSLATION);?></label>
											<p class="description"><?php _e( 'Select the social icons that will be displayed.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr id="otw-show-social-icons-custom">
										<th scope="row">
											<label for="show-social-icons-custom"><?php _e('Custom Social Icons', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<textarea id="show-social-icons-custom" name="show-social-icons-custom" rows="6" cols="80"><?php echo ( $content['show-social-icons-custom'] )?></textarea>
											<p class="description"><?php _e( 'Insert your Custom Social Icons. HTML and Shortcodes are allowed.', OTW_TRANSLATION);?></p>
										</td>
									</tr>

								</tbody>
							</table>
						</div><!-- .inside -->
					</div><!-- .accordion-section-content -->

				</li><!-- .accordion-section -->
				<!-- END List Elements and Order -->

				<!-- Post Order and Limits -->
				<li class="control-section accordion-section add-page top">
					<h3 class="accordion-section-title hndl" tabindex="1" title="<?php _e('Posts Order and Limits', OTW_TRANSLATION);?>"><?php _e('Posts Order and Limits', OTW_TRANSLATION);?></h3>
					<div class="accordion-section-content" style="display: none;">
						<div class="inside">
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="posts_limit"><?php _e('Number of Posts in the List:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<input type="text" name="posts_limit" id="posts_limit" value="<?php echo $content['posts_limit'];?>" />
											<p class="description"><?php _e('Please leave empty for all posts.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="posts_limit_skip"><?php _e('Number of Posts to Skip:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<input type="text" name="posts_limit_skip" id="posts_limit_skip" value="<?php echo $content['posts_limit_skip'];?>" />
											<p class="description"><?php _e('By default this field is empty which means no posts will be skipped.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="posts_limit_page"><?php _e('Number of Posts per Page:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<input type="text" name="posts_limit_page" id="posts_limit_page" value="<?php echo $content['posts_limit_page'];?>" />
											<p class="description"><?php _e('Show pagination should be ebabled in the section above in order for this option to work.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="posts_order"><?php _e('Order of Posts:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<select name="posts_order" id="posts_order">
											<?php 
											foreach( $selectOrderData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['posts_order'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<p class="description"><?php _e('Choose the order of the posts in the list. Timeline Template will ignore this selection and use Latest Created. Note that when Random is selected and pagination is enabled there might be posts displayed on more than one of the pagination pages.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div><!-- .accordion-section-content -->

				</li><!-- .accordion-section -->
				<!-- END Post Order and Limits -->

				<!-- Settings -->
				<li class="control-section accordion-section add-page top">
					<h3 class="accordion-section-title hndl" tabindex="2" title="<?php _e('Settings', OTW_TRANSLATION);?>"><?php _e('Settings', OTW_TRANSLATION);?></h3>
					<div class="accordion-section-content" style="display: none;">
						<div class="inside">
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="blog_list_title"><?php _e('Blog List Title:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<input type="text" name="blog_list_title" id="blog_list_title" value="<?php echo $content['blog_list_title'];?>" size="53" />
											<p class="description"><?php _e('This is the title on top of your list. If empty no title will be displayed.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="view_all_page"><?php _e('View All Link:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<input type="text" name="view_all_page" id="view_all_page" class="js-pages" value="<?php echo $content['view_all_page'];?>" size="53" />
											<br/><?php _e('- OR -', OTW_TRANSLATION); ?><br/>
											<input type="text" name="view_all_page_link" value="<?php echo $content['view_all_page_link'];?>" size="53" placeholder="http://www.google.com"/>
											<p class="description"><?php _e('Choose the page you want "view all" to link to. Or enter an URL.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="view_all_page_text"><?php _e('View All Link Text:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<input type="text" name="view_all_page_text" id="view_all_page_text" value="<?php echo $content['view_all_page_text'];?>" size="53"/>
											<p class="description"><?php _e('Enter View all link text. By default the text is “View all”.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="view_all_target"><?php _e('View All Link Target:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<select name="view_all_target">
											<?php 
											foreach( $selectViewTargetData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['view_all_target'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>		
											<p class="description"><?php _e('Select if you would like to open the link in a new window / tab or the same window / tab.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="excerpt_length"><?php _e('Excerpt Length:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<input type="text" name="excerpt_length" id="excerpt_length" value="<?php echo $content['excerpt_length'];?>" size="53"/>
											<p class="description"><?php _e('Excerpt is pulled from excerpt field for each post. If excerpt fields is empty excerpt is pulled from the text area (the post editor). The More tag is supported. If Excerpt length is empty or 0 this means pull the entire text.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="strip_tags"><?php _e('Strip HTML Tags:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<select name="strip_tags" id="strip_tags">
											<?php 
											foreach( $selectStripTags as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['strip_tags'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<p class="description"><?php _e('Strip HTML tags from the excerpt in your blog lists.', OTW_TRANSLATION);?>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="strip_shortcodes"><?php _e('Strip Shortcodes:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<select name="strip_shortcodes" id="strip_shortcodes">
											<?php 
											foreach( $selectStripShortcodes as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['strip_shortcodes'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<p class="description"><?php _e('Strip Shortcodes from the excerpt in your blog lists.', OTW_TRANSLATION);?>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="continue_reading"><?php _e('Continue Reading Text:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<input type="text" name="continue_reading" id="continue_reading" value="<?php echo $content['continue_reading'];?>" size="53" />
											<p class="description"><?php _e('Enter the text for your continue reading link under each post. If left empty ‘Continue reading’ is displayed.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="image_link"><?php _e('Click on Image Links to?', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<select name="image_link" id="image_link">
											<?php 
											foreach( $selectLinkData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['image_link'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>									
											<p class="description"><?php _e('Choose where a click on the image links to.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="title_link"><?php _e('Click on Title Links to?', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<select name="title_link" id="title_link">
											<?php 
											foreach( $selectLinkData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['title_link'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>									
											<p class="description"><?php _e('Choose where a click on the title links to.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="image_hover"><?php _e('Hover Effect', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<select name="image_hover" id="image_hover">
											<?php 
											foreach( $selectHoverData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['image_hover'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>									
											<p class="description"><?php _e('Choose the hover for the images in the posts list.', OTW_TRANSLATION);?></p>
											<p class="description">
												<?php _e('The setting will not affect the following templates since they have their own specific hovers: Slider, Carousel.', OTW_TRANSLATION); ?> 
											</p>
											<p class="description">
												<?php _e('Widget Templates support only Full and None hover options.', OTW_TRANSLATION); ?> 
											</p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="icon_hover"><?php _e('Icon Hover Effect', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<select name="icon_hover" id="icon_hover">
											<?php 
											foreach( $selectIconData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['icon_hover'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>									
											<p class="description"><?php _e('You can add an icon that will be displayed with the hover.', OTW_TRANSLATION);?></p>
											<p class="description"><?php _e('Icon Hover Effects will work with the following Hover Effects: Slide Top, Slide Right, Slide Down, Slide Left.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="thumb_width"><?php _e('Thumbnail Width', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php ( !isset($content['thumb_width']) )? $thumbWidth = '' : $thumbWidth = $content['thumb_width']; ?>
											<input type="text" name="thumb_width" id="thumb_width" size="3" value="<?php echo $thumbWidth;?>" />
											<p class="description"><?php _e('The width for your thumbnails in px. If left empty the default value will be used. Default value for the selected template is: ', OTW_TRANSLATION);?><span class="default_thumb_width"></span></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="thumb_height"><?php _e('Thumbnail Height', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php ( !isset($content['thumb_height']) )? $thumbHeight = '' : $thumbHeight = $content['thumb_height']; ?>
											<input type="text" name="thumb_height" id="thumb_height" size="3" value="<?php echo $thumbHeight;?>" />
											<p class="description"><?php _e('The height for your thumbnails in px. If left empty the default value will be used. Default value for the selected template is: ', OTW_TRANSLATION);?><span class="default_thumb_height"></span></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="thumb_format"><?php _e('Thumbnail Format', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php ( !isset($content['thumb_format']) )? $thumbFormat = '' : $thumbFormat = $content['thumb_format']; ?>
											<select id="thumb_format" name="thumb_format">
											<?php foreach( $thumb_format_options as $key => $name ){?>
												<?php
													$selected = '';
													if( $thumbFormat == $key ){
														$selected = ' selected="selected"';
													}
												?>
												<option value="<?php echo $key?>"<?php echo $selected?>><?php echo $name?></option>
											<?php }?>
											</select>
											<p class="description"><?php _e('The format for your thumbnails.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="thumb_crop"><?php _e('Thumnail Crop', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php ( !isset($content['thumb_crop']) )? $thumbCrop = '' : $thumbCrop = $content['thumb_crop']; ?>
											<select name="thumb_crop" id="thumb_crop">
												<option value="center_center" <?php echo ( $thumbCrop == 'center_center' )?'selected="selected"':'';?> ><?php _e( 'Crop center-center (default)', OTW_TRANSLATION);?></option>
												<option value="center_left" <?php echo ( $thumbCrop == 'center_left' )?'selected="selected"':'';?> ><?php _e( 'Crop center-left', OTW_TRANSLATION);?></option>
												<option value="center_right" <?php echo ( $thumbCrop == 'center_right' )?'selected="selected"':'';?> ><?php _e( 'Crop center-right', OTW_TRANSLATION);?></option>
												<option value="top_center" <?php echo ( $thumbCrop == 'top_center' )?'selected="selected"':'';?> ><?php _e( 'Crop top-center', OTW_TRANSLATION);?></option>
												<option value="top_left" <?php echo ( $thumbCrop == 'top_left' )?'selected="selected"':'';?> ><?php _e( 'Crop top-left', OTW_TRANSLATION);?></option>
												<option value="top_right" <?php echo ( $thumbCrop == 'top_right' )?'selected="selected"':'';?> ><?php _e( 'Crop top-right', OTW_TRANSLATION);?></option>
												<option value="bottom_center" <?php echo ( $thumbCrop == 'bottom_center' )?'selected="selected"':'';?> ><?php _e( 'Crop bottom-center', OTW_TRANSLATION);?></option>
												<option value="bottom_left" <?php echo ( $thumbCrop == 'bottom_left' )?'selected="selected"':'';?> ><?php _e( 'Crop bottom-left', OTW_TRANSLATION);?></option>
												<option value="botom_right" <?php echo ( $thumbCrop == 'bottom_right' )?'selected="selected"':'';?> ><?php _e( 'Crop bottom-right', OTW_TRANSLATION);?></option>
												<option value="no" <?php echo ( $thumbCrop == 'no' )?'selected="selected"':'';?> ><?php _e( 'No cropping, resize only', OTW_TRANSLATION);?></option>
											</select>
											<p class="description"><?php _e('Crop or just resize the thumbnail.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="white_spaces"><?php _e('Small Images', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php ( empty($content['white_spaces']) )? $whiteSpaces = 'yes' : $whiteSpaces = $content['white_spaces']; ?>
											<select name="white_spaces" id="white_spaces">
												<option value="yes" <?php echo ( $whiteSpaces != 'no' )?'selected="selected"':'';?> ><?php _e( 'Add background (default)', OTW_TRANSLATION);?></option>
												<option value="no" <?php echo ( $whiteSpaces == 'no' )?'selected="selected"':'';?> ><?php _e( 'Don\'t add background', OTW_TRANSLATION);?></option>
											</select>
											<p class="description"><?php _e('This option will affect only images which original size is smaller than the desired size.<br />\'Add background\' will add background to complete the image size to the desired image size. \'Don\'t add background\' will not add background and it will leave the images as they originally are.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top" id="white_spaces_color_container">
										<th scope="row">
											<label for="white_spaces_color"><?php _e('Image Background Color:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												if( empty( $content['white_spaces_color'] ) ){
													$content['white_spaces_color'] = '#FFFFFF';
												}
											?>
											<div class="otw-bm-color-picker">
												<div class="js-color-picker-icon js-color-picker">
													<div class="js-color-container" style="background-color: <?php echo $content['white_spaces_color'];?>;"></div>
												</div>
												<input type="text" name="white_spaces_color" class="js-color-picker-value" value="<?php echo $content['white_spaces_color'];?>"/>
											</div>
											<!-- END Excpert Font Color -->
											<p class="description"><?php _e('The extra background color to complete the image to the desired size.', OTW_TRANSLATION); ?></p>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="meta_order"><?php _e('Meta Elements and Order', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<div class="active_elements">
												<h3><?php _e('Active Elements', OTW_TRANSLATION);?></h3>
												<input type="hidden" name="meta-items" class="js-meta-items" value="<?php echo $content['meta-items'];?>"/>
												<ul class="b-meta-box js-meta-active">
												</ul>
											</div>
											<div class="inactive_elements">
												<h3><?php _e('Inactive Elements', OTW_TRANSLATION);?></h3>
												<ul class="b-meta-box js-meta-inactive">
													<li data-item="meta" data-value="author" class="b-meta-items js-meta--item"><?php _e('author', OTW_TRANSLATION);?></li>
													<li data-item="meta" data-value="date" class="b-meta-items js-meta--item"><?php _e('date', OTW_TRANSLATION);?></li>
													<li data-item="meta" data-value="category" class="b-meta-items js-meta--item"><?php _e('category', OTW_TRANSLATION);?></li>
													<li data-item="meta" data-value="tags" class="b-meta-items js-meta--item"><?php _e('tags', OTW_TRANSLATION);?></li>
													<li data-item="meta" data-value="comments" class="b-meta-items js-meta--item"><?php _e('comments', OTW_TRANSLATION);?></li>
												</ul>
											</div>
											<p class="description"><?php _e('Drag & drop the items that you\'d like to show in the Active Elements area on the left. Arrange them however you want to see them in your list.', OTW_TRANSLATION);?></p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="meta_type_align"><?php _e('Meta Type:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<select name="meta_type_align" id="meta_type_align">
											<?php 
											foreach( $selectMetaData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['meta_type_align'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<p class="description"><?php _e('Choose between horizontal and vertical meta style.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="meta_icons"><?php _e('Meta Icons:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['meta_icons'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="meta_icons" id="meta_icons-no" value="0" <?php echo $no;?>/> 
											<label for="meta_icons-no"><?php _e('No (default)', OTW_TRANSLATION);?></label>

											<input type="radio" name="meta_icons" id="meta_icons-yes" value="1" <?php echo $yes;?>/> 
											<label for="meta_icons-yes"><?php _e('Yes', OTW_TRANSLATION);?></label>
											<p class="description"><?php _e('Choose yes if you want to have icons instead of labels in your meta.', OTW_TRANSLATION);?>
										</td>
									</tr>
								</tbody>
							</table>
						</div> <!-- .inside -->
					</div><!-- .accordion-section-content -->

				</li><!-- .accordion-section -->
				<!-- END Settings -->


				<!-- Style Tab -->
				<li class="control-section accordion-section add-page top">
					<h3 class="accordion-section-title hndl" tabindex="4" title="<?php _e('Styles', OTW_TRANSLATION);?>"><?php _e('Styles', OTW_TRANSLATION);?></h3>
					<div class="accordion-section-content" style="display: none;">
						<div class="inside">
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="blog_list_title"><?php _e('Title Style:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<!-- Title Font Size -->
											<select name="title-font-size" id="title-font-size">
											<?php 
											foreach( $selectFontSizeData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['title-font-size'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<!-- END Title Font Size -->

											<!-- Title Font Style -->
											<select name="title-font-style" id="title-font-style">
											<?php 
											foreach( $selectFontStyleData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['title-font-style'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<!-- END Title Font Style -->

											<!-- Title Font Family -->
											<input type="text" name="title_font" class="js-fonts" value="<?php echo ( !empty( $content['title_font'] ) )? $content['title_font']:'';?>" />
											<!-- END Title Font Family -->

											<!-- Title Font Color -->
											<div class="otw-bm-color-picker">
												<div class="js-color-picker-icon js-color-picker">
													<div class="js-color-container" style="background-color: <?php echo $content['title-color'];?>;"></div>
												</div>
												<input type="text" name="title-color" class="js-color-picker-value" value="<?php echo $content['title-color'];?>"/>
											</div>
											<!-- END Title Font Color -->
											<p class="description"><?php _e('Adjust the style of the Title of each post in your list', OTW_TRANSLATION); ?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="blog_list_title"><?php _e('Meta Items Style:', OTW_TRANSLATION);?></label>
										</th>
										<td>

											<!-- Meta Items Font Size -->
											<select name="meta-font-size" id="meta-font-size">
											<?php 
											foreach( $selectFontSizeData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['meta-font-size'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<!-- END Meta Items Font Size -->

											<!-- Meta Font Style -->
											<select name="meta-font-style" id="meta-font-style">
											<?php 
											foreach( $selectFontStyleData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['meta-font-style'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<!-- END Meta Font Style -->

											<!-- Title Font Family -->
											<input type="text" name="meta_font" class="js-fonts" value="<?php echo ( !empty( $content['meta_font'] ) )?$content['meta_font']:'';?>" />
											<!-- END Meta Font Family -->

											<!-- Meta Font Color -->
											<div class="otw-bm-color-picker">
												<div class="js-color-picker-icon js-color-picker">
													<div class="js-color-container" style="background-color: <?php echo $content['meta-color'];?>;"></div>
												</div>
												<input type="text" name="meta-color" class="js-color-picker-value" value="<?php echo $content['meta-color'];?>"/>
											</div>
											<!-- END Meta Font Color -->

											<p class="description"><?php _e('Adjust the style of the Meta Items (e.g.: Author, Comments) of each post in your list', OTW_TRANSLATION); ?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="blog_list_title"><?php _e('Excpert Style:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<!-- Excpert Font Size -->
											<select name="excpert-font-size" id="meta-font-size">
											<?php 
											foreach( $selectFontSizeData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['excpert-font-size'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<!-- END Excpert Font Size -->

											<!-- Excpert Font Style -->
											<select name="excpert-font-style" id="excpert-font-style">
											<?php 
											foreach( $selectFontStyleData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['excpert-font-style'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<!-- END Excpert Font Style -->

											<!-- Excpert Font Family -->
											<input type="text" name="excpert_font" class="js-fonts" value="<?php echo $content['excpert_font'];?>" />
											<!-- END Excpert Font Family -->

											<!-- Excpert Font Color -->
											<div class="otw-bm-color-picker">
												<div class="js-color-picker-icon js-color-picker">
													<div class="js-color-container" style="background-color: <?php echo $content['excpert-color'];?>;"></div>
												</div>
												<input type="text" name="excpert-color" class="js-color-picker-value" value="<?php echo $content['excpert-color'];?>"/>
											</div>
											<!-- END Excpert Font Color -->
											<p class="description"><?php _e('Adjust the style of the Excpert of each post in your list', OTW_TRANSLATION); ?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="blog_list_title"><?php _e('Continue Reading Style:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<!-- Excpert Font Size -->
											<select name="read-more-font-size" id="meta-font-size">
											<?php 
											foreach( $selectFontSizeData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['read-more-font-size'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<!-- END Excpert Font Size -->

											<!-- Excpert Font Style -->
											<select name="read-more-font-style" id="read-more-font-style">
											<?php 
											foreach( $selectFontStyleData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['read-more-font-style'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<!-- END Excpert Font Style -->

											<!-- Excpert Font Family -->
											<input type="text" name="read-more_font" class="js-fonts" value="<?php echo $content['read-more_font'];?>" />
											<!-- END Excpert Font Family -->

											<!-- Excpert Font Color -->
											<div class="otw-bm-color-picker">
												<div class="js-color-picker-icon js-color-picker">
													<div class="js-color-container" style="background-color: <?php echo $content['read-more-color'];?>;"></div>
												</div>
												<input type="text" name="read-more-color" class="js-color-picker-value" value="<?php echo $content['read-more-color'];?>"/>
											</div>
											<!-- END Excpert Font Color -->
											<p class="description"><?php _e('Adjust the style of the Continue Reading of each post in your list', OTW_TRANSLATION); ?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="border-style"><?php _e('Border Style:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<!--  Font Size -->
											<select name="border-style" id="border-style">
											<?php 
											foreach( $selectBorderStyleData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['border-style'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<select name="border-size" id="border-size">
											<?php 
											foreach( $selectBorderSizeData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['border-size'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<div class="otw-bm-color-picker">
												<div class="js-color-picker-icon js-color-picker">
													<div class="js-color-container" style="background-color: <?php echo $content['border-color'];?>;"></div>
												</div>
												<input type="text" name="border-color" class="js-color-picker-value" value="<?php echo $content['border-color'];?>"/>
											</div>
											<p class="description"><?php _e('Adjust the style of the Border from the Lists elements and order tab.', OTW_TRANSLATION); ?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="background-color"><?php _e('Background Color:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<div class="otw-bm-color-picker">
												<div class="js-color-picker-icon js-color-picker">
													<div class="js-color-container" style="background-color: <?php echo $content['background-color'];?>;"></div>
												</div>
												<input type="text" name="background-color" class="js-color-picker-value" value="<?php echo $content['background-color'];?>"/>
											</div>
											<p class="description"><?php _e('Adjust the style of the Background from the Lists elements and order tab.', OTW_TRANSLATION); ?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="background-color-opacity"><?php _e('Background Opacity:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<input type="text" name="background-color-opacity" id="background-color-opacity" size="4" value="<?php echo $content['background-color-opacity'];?>" />
											<p class="description"><?php _e('The opacity of the background. Could be between 0 and 1. For example 0.61. Leave empty for not opacity', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="blog_list_title"><?php _e('Custom CSS:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<textarea name="custom_css" cols="70" rows="10"><?php echo str_replace('\\', '', $content['custom_css']);?></textarea>
										</td>
									</tr>

								</tbody>
							</table>
						</div> <!-- .inside -->
					</div><!-- .accordion-section-content -->

				</li><!-- .accordion-section -->
				<!-- Style Tab -->

				<!-- Query Selection Tab -->
				<li class="control-section accordion-section add-page top">
					<h3 class="accordion-section-title hndl" tabindex="5" title="<?php _e('Post Selection Method - Advanced Users', OTW_TRANSLATION);?>">
						<?php _e('Post Selection Method - Advanced Users', OTW_TRANSLATION);?>
					</h3>
					<div class="accordion-section-content" style="display: none;">
						<div class="inside">

							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="blog_list_title">
												<?php _e('Between Categories and Tags result-sets Selection Method:', OTW_TRANSLATION);?>
											</label>
										</th>
										<td>
											<!-- Category / Tag relation -->
											<select name="cat-tag-relation" id="cat-tag-relation">
											<?php 
											foreach( $selectCategoryTagRelation as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['cat-tag-relation'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>
											<!-- END Category / Tag relation -->
											<p class="description">
												<?php _e('categories OR tags means: WHERE post_id IN categories-result-set OR post_id IN tags-result-set. In other words your list will include all posts that are in categories-result-set or tags-result-set.', OTW_TRANSLATION); ?>
											</p>
											<p class="description">
												<?php _e('categories AND tags means: WHERE post_id IN categories-result-set AND post_id IN tags-result-set. In other words your list will include all posts that are in both result-sets categories-result-set and tags-result-set in the same time (If a post is only in categories-result-set, it will not be included in the list).', OTW_TRANSLATION); ?>
											</p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="author-relation">
												<?php _e('Before Authors result-set Selection Method:', OTW_TRANSLATION);?>
											</label>
										</th>
										<td>
											<select name="author-relation" id="author-relation">
												<option value="or" <?php echo ( isset( $content['author-relation'] ) && ( $content['author-relation'] === 'or' ) )?'selected="selected"':''?>><?php _e( 'OR authors', OTW_TRANSLATION )?></option>
												<option value="and" <?php echo ( !isset( $content['author-relation'] ) || ( $content['author-relation'] !== 'or' ) )?'selected="selected"':''?>><?php _e( 'AND authors(default)', OTW_TRANSLATION )?></option>
											</select>
											<p class="description">
												<?php _e( 'AND authors means: WHERE post_id IN categories-and-tags-result-set AND post_id IN authors -result-set. In other words your list will include all posts that are in both result-sets categories-and-tags-result-set and authors-result-set in the same time (If a post is only in categories-and-tags-result-set, it will not be included in the list).', OTW_TRANSLATION); ?>
											</p>
											<p class="description">
												<?php _e('OR authors means: WHERE post_id IN categories-and-tags-result-set OR post_id IN authors-result-set. In other words your list will include all posts that are in result-sets categories-and-tags-result-set or authors-result-set.', OTW_TRANSLATION); ?>
											</p>
										</td>
									</tr>
								</tbody>
							</table>

						</div>
					</div>
				</li>
				<!-- End Query Selection Tab -->

				<!-- Mosaic Settings Tab -->
				<?php
				$mosaicSettings = 'otw-admin-hidden';
				if( !empty($content['template']) && ( $content['template'] == '1-3-mosaic' || $content['template'] == '1-4-mosaic') ) {
					$mosaicSettings = '';
				}
				?>
				<li class="control-section accordion-section add-page top js-mosaic-settings <?php echo $mosaicSettings;?>">
					<h3 class="accordion-section-title hndl" tabindex="4" title="<?php _e('Mosaic Settings', OTW_TRANSLATION);?>"><?php _e('Mosaic Settings', OTW_TRANSLATION);?></h3>
					<div class="accordion-section-content" style="display: none;">
						<div class="inside">
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="blog_list_title"><?php _e('Space Tiles:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['space-tiles'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="space-tiles" id="space-tiles-no" value="0" <?php echo $no;?> /> 
											<label for="space-tiles-no"><?php _e('No (default)', OTW_TRANSLATION);?></label>

											<input type="radio" name="space-tiles" id="space-tiles-yes" value="1" <?php echo $yes;?> /> 
											<label for="space-tiles-yes"><?php _e('Yes', OTW_TRANSLATION);?></label>

											<p class="description"><?php _e('Add Space between the Mosaic Tiles.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="meta_type"><?php _e('Display Blog List Items as Hover:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['mosaic-content'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="mosaic-content" id="mosaic-content-no" value="0" <?php echo $no;?> /> 
											<label for="mosaic-content-no"><?php _e('No', OTW_TRANSLATION);?></label>

											<input type="radio" name="mosaic-content" id="mosaic-content-yes" value="1" <?php echo $yes;?> /> 
											<label for="mosaic-content-yes"><?php _e('Yes (default)', OTW_TRANSLATION);?></label>
											<p class="description"><?php _e('Enable the Blog List Items as Hover.', OTW_TRANSLATION);?></p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="show-mosaic-cat-filter-yes"><?php _e('Show Category Filter:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['show-mosaic-cat-filter'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="show-mosaic-cat-filter" id="show-mosaic-cat-filter-no" value="0" <?php echo $no;?> /> 
											<label for="show-mosaic-cat-filter-no"><?php _e('No', OTW_TRANSLATION);?></label>

											<input type="radio" name="show-mosaic-cat-filter" id="show-mosaic-cat-filter-yes" value="1" <?php echo $yes;?> /> 
											<label for="show-mosaic-cat-filter-yes"><?php _e('Yes (default)', OTW_TRANSLATION);?></label>

											<p class="description"><?php _e('Enable the Category filter on top of your list.', OTW_TRANSLATION);?></p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="show-mosaic-sort-filter-yes"><?php _e('Show Sort Filter:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['show-mosaic-sort-filter'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="show-mosaic-sort-filter" id="show-mosaic-sort-filter-no" value="0" <?php echo $no;?> /> 
											<label for="show-mosaic-sort-filter-no"><?php _e('No', OTW_TRANSLATION);?></label>

											<input type="radio" name="show-mosaic-sort-filter" id="show-mosaic-sort-filter-yes" value="1" <?php echo $yes;?> /> 
											<label for="show-mosaic-sort-filter-yes"><?php _e('Yes (default)', OTW_TRANSLATION);?></label>

											<p class="description"><?php _e('Enable the Sort filter on top of your list.', OTW_TRANSLATION);?></p>
										</td>
									</tr>

								</tbody>
							</table>
						</div> <!-- .inside -->
					</div><!-- .accordion-section-content -->

				</li><!-- .accordion-section -->
				<!-- Mosaic Settings Tab -->

				<!-- Horizontal Tab -->
				<?php
				$horizontalSettings = 'otw-admin-hidden';
				if( !empty($content['template']) && ( $content['template'] == 'horizontal-layout' ) ) {
					$horizontalSettings = '';
				}
				?>
				<li class="control-section accordion-section add-page top js-horizontal-settings <?php echo $horizontalSettings;?>">
					<h3 class="accordion-section-title hndl" tabindex="4" title="<?php _e('Horizontal Layout Settings', OTW_TRANSLATION);?>">
						<?php _e('Horizontal Layout Settings', OTW_TRANSLATION);?>
					</h3>
					<div class="accordion-section-content" style="display: none;">
						<div class="inside">
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="horizontal-space-tiles-no"><?php _e('Space Tiles:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['horizontal-space-tiles'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="horizontal-space-tiles" id="horizontal-space-tiles-no" value="0" <?php echo $no;?> /> 
											<label for="horizontal-space-tiles-no"><?php _e('No (default)', OTW_TRANSLATION);?></label>

											<input type="radio" name="horizontal-space-tiles" id="horizontal-space-tiles-yes" value="1" <?php echo $yes;?> /> 
											<label for="horizontal-space-tiles-yes"><?php _e('Yes', OTW_TRANSLATION);?></label>

											<p class="description"><?php _e('Add Space between the Tiles.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="horizontal-content-no"><?php _e('Display Blog List Items as Hover:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['horizontal-content'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="horizontal-content" id="horizontal-content-no" value="0" <?php echo $no;?> /> 
											<label for="horizontal-content-no"><?php _e('No', OTW_TRANSLATION);?></label>

											<input type="radio" name="horizontal-content" id="horizontal-content-yes" value="1" <?php echo $yes;?> /> 
											<label for="horizontal-content-yes"><?php _e('Yes (default)', OTW_TRANSLATION);?></label>
											<p class="description"><?php _e('Enable the Blog List Items as Hover.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
								</tbody>
							</table>
						</div> <!-- .inside -->
					</div><!-- .accordion-section-content -->

				</li><!-- .accordion-section -->
				<!-- Horizontal Slider Tab -->

				<!-- Slider Settings Tab -->
				<?php
				$sliderSettings = 'otw-admin-hidden';
	      $sliderArray = array(
	        'slider', '3-column-carousel', '4-column-carousel', '5-column-carousel',
	        '2-column-carousel-wid', '3-column-carousel-wid', '4-column-carousel-wid'
	      );
				if( !empty($content['template']) && in_array( $content['template'], $sliderArray ) ) {
					$sliderSettings = '';
				}
				?>
				<li class="control-section accordion-section  add-page top js-slider-settings <?php echo $sliderSettings; ?>">
					<h3 class="accordion-section-title hndl" tabindex="4" title="<?php _e('Slider and Carousel Settings', OTW_TRANSLATION);?>">
						<?php _e('Slider and Carousel Settings', OTW_TRANSLATION);?>
					</h3>
					<div class="accordion-section-content" style="display: none;">
						<div class="inside">
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="slider_title"><?php _e('Enable Title and Excerpt:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['show-slider-title'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="show-slider-title" id="show-slider-title-no" value="0" <?php echo $no;?> /> 
											<label for="show-slider-title-no"><?php _e('No', OTW_TRANSLATION);?></label>

											<input type="radio" name="show-slider-title" id="show-slider-title-yes" value="1" <?php echo $yes;?> /> 
											<label for="show-slider-title-yes"><?php _e('Yes (default)', OTW_TRANSLATION);?></label>

											<p class="description"><?php _e('Displays the post title and excerpt as caption for the slider. Displays only the post title for Carousel Templates. This will not affect the Widget Carousel Templates.', OTW_TRANSLATION);?></p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="slider_title_bg"><?php _e('Enable Title and Excerpt Background:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['slider_title_bg'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="slider_title_bg" id="slider_title_bg-no" value="0" <?php echo $no;?> /> 
											<label for="slider_title_bg-no"><?php _e('No', OTW_TRANSLATION);?></label>

											<input type="radio" name="slider_title_bg" id="slider_title_bg-yes" value="1" <?php echo $yes;?> /> 
											<label for="slider_title_bg-yes"><?php _e('Yes (default)', OTW_TRANSLATION);?></label>
											<p class="description"><?php _e('Enables a background for the title and excerpt. This will not affect the Widget Carousel Templates.', OTW_TRANSLATION);?></p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="carousel-auto-scroll"><?php _e('Enable Auto Scroll:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['slider-auto-scroll'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="slider-auto-scroll" id="slider-auto-scroll-no" value="0" <?php echo $no;?> /> 
											<label for="slider-auto-scroll-no"><?php _e('No', OTW_TRANSLATION);?></label>

											<input type="radio" name="slider-auto-scroll" id="slider-auto-scroll-yes" value="1" <?php echo $yes;?> /> 
											<label for="slider-auto-scroll-yes"><?php _e('Yes (default)', OTW_TRANSLATION);?></label>

											<p class="description"><?php _e('Enables auto scroll.', OTW_TRANSLATION);?></p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="slider_nav"><?php _e('Show Navigation:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['slider_nav'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="slider_nav" id="slider_nav-no" value="0" <?php echo $no;?> /> 
											<label for="slider_nav-no"><?php _e('No', OTW_TRANSLATION);?></label>

											<input type="radio" name="slider_nav" id="slider_nav-yes" value="1" <?php echo $yes;?> /> 
											<label for="slider_nav-yes"><?php _e('Yes (default)', OTW_TRANSLATION);?></label>

											<p class="description"><?php _e('Display arrows and bullet navigation for the slider and carousels. Note that when "Title and Excerpt" is enabled only the arrows navigation will be displayed.', OTW_TRANSLATION);?></p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="slider_border"><?php _e('Show Border:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['slider_border'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="slider_border" id="slider_border-no" value="0" <?php echo $no;?> /> 
											<label for="slider_border-no"><?php _e('No (default)', OTW_TRANSLATION);?></label>

											<input type="radio" name="slider_border" id="slider_border-yes" value="1" <?php echo $yes;?> /> 
											<label for="slider_border-yes"><?php _e('Yes', OTW_TRANSLATION);?></label>

											<p class="description"><?php _e('This will add 1px border to the slider and carousels container. This will not affect the Widget Carousel Templates.', OTW_TRANSLATION);?></p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="slider_title_alignment"><?php _e('Title and Excerpt Alignment:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<select id="slider_title_alignment" name="slider_title_alignment">
											<?php 
											foreach( $selectSliderAlignmentData as $optionData ): 
												$selected = '';
												if( $optionData['value'] === $content['slider_title_alignment'] ) {
													$selected = 'selected="selected"';
												}
												echo "<option value=\"".$optionData['value']."\" ".$selected.">".$optionData['text']."</option>";
												
											endforeach;
											?>
											</select>

											<p class="description"><?php _e('Choose the alignment for the title and excerpt. This will not affect the Widget Carousel Templates.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
								</tbody>
							</table>
						</div> <!-- .inside -->
					</div><!-- .accordion-section-content -->

				</li><!-- .accordion-section -->
				<!-- Slider Settings Tab -->

				<!-- News Settings Tab -->
				<?php
				$newsSettings = 'otw-admin-hidden';
				if( !empty($content['template']) && strpos($content['template'], 'news') ) {
					$newsSettings = '';
				}
				?>
				<li class="control-section accordion-section  add-page top js-news-settings <?php echo $newsSettings; ?>">
					<h3 class="accordion-section-title hndl" tabindex="4" title="<?php _e('Newspaper Settings', OTW_TRANSLATION);?>"><?php _e('Newspaper Settings', OTW_TRANSLATION);?></h3>
					<div class="accordion-section-content" style="display: none;">
						<div class="inside">
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="slider_title"><?php _e('Show Category Filter:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['show-news-cat-filter'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="show-news-cat-filter" id="show-news-cat-filter-no" value="0" <?php echo $no;?> /> 
											<label for="show-news-cat-filter-no"><?php _e('No', OTW_TRANSLATION);?></label>

											<input type="radio" name="show-news-cat-filter" id="show-news-cat-filter-yes" value="1" <?php echo $yes;?> /> 
											<label for="show-news-cat-filter-yes"><?php _e('Yes (default)', OTW_TRANSLATION);?></label>

											<p class="description"><?php _e('Enable the Category filter on top of your list.', OTW_TRANSLATION);?></p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="slider_title"><?php _e('Show Sort Filter:', OTW_TRANSLATION);?></label>
										</th>
										<td>
											<?php
												$yes = ''; $no = ''; 
												($content['show-news-sort-filter'])? $yes = 'checked="checked"' : $no = 'checked="checked"'; 
											?>
											<input type="radio" name="show-news-sort-filter" id="show-news-sort-filter-no" value="0" <?php echo $no;?> /> 
											<label for="show-news-sort-filter-no"><?php _e('No', OTW_TRANSLATION);?></label>

											<input type="radio" name="show-news-sort-filter" id="show-news-sort-filter-yes" value="1" <?php echo $yes;?> /> 
											<label for="show-news-sort-filter-yes"><?php _e('Yes (default)', OTW_TRANSLATION);?></label>

											<p class="description"><?php _e('Enable the Category filter on top of your list.', OTW_TRANSLATION);?></p>
										</td>
									</tr>
								</tbody>
							</table>
						</div> <!-- .inside -->
					</div><!-- .accordion-section-content -->

				</li><!-- .accordion-section -->
				<!-- END News Settings Tab -->

			</ul><!-- .outer-border -->
			
		</div>

		<p class="submit">
			<input type="submit" value="<?php _e( 'Save', OTW_TRANSLATION) ?>" name="submit-otw-bm" class="button button-primary button-hero"/>
		</p>

	</form>

<div class="live_preview js-preview"></div>