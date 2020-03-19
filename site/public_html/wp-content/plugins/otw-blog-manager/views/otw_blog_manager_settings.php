<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2><?php _e('Blog List Settings', 'otw_bm'); ?></h2>
  <?php
    if( $writableCssError ) {
      $message = __('The file \''.SKIN_BM_PATH.'custom.css\' is not writable. Please make sure you add read/write permissions to this file.', 'otw_bm');
      echo '<div class="error"><p>'.$message.'</p></div>';
    }

  ?>
	<?php
		if( !empty( $_GET['success_css'] ) && $_GET['success_css'] == 'true' ) {
			$message = __('Custom CSS file has been updated.', OTW_TRANSLATION);
			echo '<div class="updated"><p>'.$message.'</p></div>';
		}
	?>
	<p class="description"><?php _e('Adjust your own CSS for all of your Blog Lists. Please use with caution.', 'otw_bm'); ?></p>
	
	<form name="otw-bm-list-style" method="post" action="" class="validate">
		<textarea name="otw_css" cols="100" rows="35"><?php echo $customCss;?></textarea>
		<p class="submit">
			<input type="submit" value="<?php _e( 'Save', 'otw_bm') ?>" name="submit" class="button button-primary button-hero"/>
		</p>
	</form>

</div>