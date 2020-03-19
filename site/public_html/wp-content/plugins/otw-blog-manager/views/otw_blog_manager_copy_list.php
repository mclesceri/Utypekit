<div class="wrap">
	<div id="icon-edit" class="icon32"></div>
	<h2>
		<?php	echo __( 'Duplicate Blog List', OTW_TRANSLATION ); ?>
		<a class="add-new-h2" href="admin.php?page=otw-bm"><?php _e('Back', OTW_TRANSLATION);?></a>
	</h2>
	<form name="otw-bm-copy-list" method="post" action="" class="validate">
		<?php
			if( !empty($this->errors) ){
				$errorMsg = __('Oops! Please check form for errors.', OTW_TRANSLATION);
				echo '<div class="error"><p>'.$errorMsg.'</p></div>';
			}
		?>
		<input type="hidden" name="id" value="<?php echo $listID;?>" />
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="list_name" class="required"><?php _e('Source Blog List Name', OTW_TRANSLATION);?></label></th>
					<td><?php echo $content['list_name'];?>
						<div class="inline-error">
							<?php 
								( !empty($this->errors['source_list_name']) )? $errorMessage = $this->errors['source_list_name'] : $errorMessage = ''; 
								echo $errorMessage;
							?>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="list_name" class="required"><?php _e('New Blog List Name', OTW_TRANSLATION);?></label></th>
					<td>
						<input type="text" name="list_name" id="list_name" size="53" value="<?php echo $content['new_list_name'];?>" />
						<p class="description"><?php _e( 'Note: The List Name is going to be used ONLY for the admin as a reference.', OTW_TRANSLATION);?></p>
						<div class="inline-error">
							<?php 
								( !empty($this->errors['list_name']) )? $errorMessage = $this->errors['list_name'] : $errorMessage = ''; 
								echo $errorMessage;
							?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" value="<?php _e( 'Duplicate', OTW_TRANSLATION) ?>" name="submit-otw-bm-copy" class="button button-primary button-hero"/>
		</p>
	</form>
</div>
