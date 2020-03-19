<?php
if ( !defined('CP_AUTH_INCLUDE') ) { echo 'Direct access not allowed.';  exit; }
// Corrects a conflict with W3 Total Cache
if( function_exists( 'w3_instance' ) )
{
	try
	{
		$w3_config = w3_instance( 'W3_Config' );
		$w3_config->set( 'minify.html.enable', false );
	}
	catch( Exception $err )
	{

	}
}

if( function_exists( 'cp_calculatedfieldsf_link_tag' ) )
{
	add_filter( 'style_loader_tag', 'cp_calculatedfieldsf_link_tag' );
}
wp_enqueue_style( 'cpcff_stylepublic', plugins_url('../css/stylepublic.css', __FILE__), array(), 'pro' );
wp_enqueue_style( 'cpcff_jquery_ui'  , plugins_url('../css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__), array(), 'pro' );

// Texts
global $cpcff_default_texts_array;
$cpcff_texts_array = cp_calculatedfieldsf_get_option( 'vs_all_texts', $cpcff_default_texts_array, $id );
$cpcff_texts_array = array_replace_recursive(
    $cpcff_default_texts_array,
    is_string( $cpcff_texts_array ) ? unserialize( $cpcff_texts_array ) : $cpcff_texts_array
);

$form_data = cp_calculatedfieldsf_get_option( 'form_structure', CP_CALCULATEDFIELDSF_DEFAULT_form_structure, $id );
$form_data = unserialize(serialize($form_data)); // clone the object to get references to different objects
if( !empty( $form_data ) )
{
	// PROCESS DATASOURCE FIELDS
	if( !empty( $form_data[ 0 ] ) )
	{
		foreach( $form_data[ 0 ] as $key => $object )
		{
			if( isset( $object->isDataSource ) && $object->isDataSource )
			{
				// Clear the data are not related with the datasource active
				$datasources = get_object_vars( $object->list );
				foreach( $datasources as $ds_key => $ds_obj )
				{
					if( $ds_key != $object->active )
					{
						unset( $object->list->$ds_key );
					}
				}

				if(
					(!empty($object->active) && $object->active != 'csv' && $object->active != 'recordset') ||
					(
						property_exists( $object->list, 'csv' ) &&
						property_exists( $object->list->csv->csvData, 'type' ) &&
						$object->list->csv->csvData->type == 'url'
					)
				)
				{
					// Save the datasource as  transient variable.
					set_transient( 'cpcff_db_'.$id.'_'.$object->name, $object->list->{$object->active}, 60*60*24 );
					$datasourceObject = new stdClass;
					$datasourceObject->form = $id;
					$datasourceObject->vars = array();

					// Extract variables if are used
					$dataStr = '';
					switch( $object->active )
					{
						case 'database':
							$queryData = $object->list->database->queryData;
							if( $queryData->active == 'query' )
							{
								$dataStr = $queryData->query;
							}
							else
							{
								$dataStr = $queryData->value.$queryData->text.$queryData->table.$queryData->where.$queryData->orderby.$queryData->limit;
							}
						break;
						case 'csv':
							$dataStr = $object->list->csv->csvData->where;
						break;
						case 'posttype':
							$posttypeData = $object->list->posttype->posttypeData;
							$dataStr = $posttypeData->id.$posttypeData->last;
						break;
						case 'taxonomy':
							$taxonomyData = $object->list->taxonomy->taxonomyData;
							$dataStr = $taxonomyData->id.$taxonomyData->slug;
						break;
						case 'user':
							$userData = $object->list->user->userData;
							if( !$userData->logged )
							{
								$dataStr = $userData->id.$userData->login;
							}
						break;
					}

					if( preg_match_all( '/<%([^%]+)%>/', $dataStr, $matches ) )
					{
						$datasourceObject->vars = $matches[ 1 ];
					}
					$object->list->{$object->active} = $datasourceObject;
				}
				$form_data[ 0 ][ $key ] = $object;
			}
		}
	}
	$form_data[ 1 ][ 'formid' ]="cp_calculatedfieldsf_pform".$CP_CFF_global_form_count;
	if( get_option( 'CP_CALCULATEDFIELDSF_FORM_CACHE', false ) )
	{
		$form_cache = cp_calculatedfieldsf_get_option( 'cache', '', $id );
		$form_data[ 1 ][ 'cache' ]  = $form_data[ 1 ][ 'setCache' ] = ( empty( $form_cache ) ) ? true : false;
	}
	else
	{
		$form_data[ 1 ][ 'cache' ]  = $form_data[ 1 ][ 'setCache' ] = false;
	}

	// PROCESS LAYOUT AND CUSTOM STYLES
	if( isset( $form_data[ 1 ] ) && isset( $form_data[ 1 ][ 0 ] ) )
	{
		if(!empty( $form_data[ 1 ][ 0 ]->formtemplate ))
		{
			$templatelist = cp_calculatedfieldsf_available_templates();
			$template = $form_data[ 1 ][ 0 ]->formtemplate;
			if( isset( $templatelist[ $template ] ) && !defined('CPCFF_LOADED_TEMPLATE'.$template))
			{
				define('CPCFF_LOADED_TEMPLATE'.$template, true);
				if(CP_CALCULATEDFIELDSF_DEFAULT_DEFER_SCRIPTS_LOADING)
				{
					 wp_enqueue_style( 'cpcff_template_css'.$template,  $templatelist[ $template ][ 'file' ], array(), 'pro' );
				}
				else
				{
					print '<link href="'.esc_attr( esc_url( $templatelist[ $template ][ 'file' ] ) ).'?ver=pro" type="text/css" rel="stylesheet" property="stylesheet" />';
				}

				if( isset( $templatelist[ $template ][ 'js' ] ) )
				{
					if(CP_CALCULATEDFIELDSF_DEFAULT_DEFER_SCRIPTS_LOADING)
					{
						 wp_enqueue_script( 'cpcff_template_js'.$template,  $templatelist[ $template ][ 'js' ], array(), 'pro' );
					}
					else
					{
						print '<script src="'.esc_attr( esc_url( $templatelist[ $template ][ 'js' ] ) ).'"></script>';
					}
				}
			}
		}

		if(!empty($form_data[ 1 ][ 0 ]->customstyles))
		{
			print '<style>'.$form_data[ 1 ][ 0 ]->customstyles.'</style>';
		}
	}
?>
	<!--noptimize--><script type="text/javascript">
	 function doValidate<?php echo $CP_CFF_global_form_count; ?>(form)
	 {
		if(typeof cpcff_validation_rules == 'undefined') cpcff_validation_rules = {};
		if(typeof cpcff_validation_rules['<?php echo $CP_CFF_global_form_count; ?>'] == 'undefined') cpcff_validation_rules['<?php echo $CP_CFF_global_form_count; ?>'] = {};
		var $dexQuery = jQuery.noConflict(),
			cpefb_error = $dexQuery("#cp_calculatedfieldsf_pform<?php echo $CP_CFF_global_form_count; ?>").find(".cpefb_error:visible").length,
			validation_rules = cpcff_validation_rules['<?php echo $CP_CFF_global_form_count; ?>'],
			processing_form = function()
			{
				for(var rule in validation_rules)
				{
					if(!validation_rules[rule]) return;
				}
				$dexQuery("#cp_calculatedfieldsf_pform<?php echo $CP_CFF_global_form_count; ?>").find("[name$='_date']:disabled").each(function(){
					var v  	 = $dexQuery(this).val(),
						name = $dexQuery(this).attr( 'name' ).replace('_date', ''),
						e 	 = $dexQuery("[name='"+name+"']"); if( e.length ){ e.val( $dexQuery.trim( e.val().replace( v, '' ) ) ); }
				});
				$dexQuery("#cp_calculatedfieldsf_pform<?php echo $CP_CFF_global_form_count; ?>").find("select").children().each(function(){
					if( typeof $dexQuery(this).attr("vt") != 'undefined' )
						$dexQuery(this).val($dexQuery(this).attr("vt"));
				});
				$dexQuery("#cp_calculatedfieldsf_pform<?php echo $CP_CFF_global_form_count; ?>").find("input:checkbox,input:radio").each(function(){
					if( typeof $dexQuery(this).attr("vt") != 'undefined' )
						$dexQuery(this).val($dexQuery(this).attr("vt"));
				});
				$dexQuery("#cp_calculatedfieldsf_pform<?php echo $CP_CFF_global_form_count; ?>").find( '.cpcff-recordset' ).remove();
				$dexQuery("#cp_calculatedfieldsf_pform<?php echo $CP_CFF_global_form_count; ?>").find( '.ignore' ).closest( '.fields' ).remove();
				$dexQuery("#form_structure<?php echo $CP_CFF_global_form_count; ?>").remove();
				$dexQuery("#cp_calculatedfieldsf_pform<?php echo $CP_CFF_global_form_count; ?>")[ 0 ].submit();
			};
		document.cp_calculatedfieldsf_pform<?php echo $CP_CFF_global_form_count; ?>.cp_ref_page.value = document.location.href;
		if (cpefb_error==0)
		{
		<?php
		// CAPTCHA SECTION
		if (cp_calculatedfieldsf_get_option('cv_enable_captcha', CP_CALCULATEDFIELDSF_DEFAULT_cv_enable_captcha,$id) != 'false')
		{
		?> 	if ($dexQuery("#hdcaptcha_cp_calculated_fields_form_post<?php echo $CP_CFF_global_form_count; ?>").val() == '')
			{
				alert('<?php echo( $cpcff_texts_array[ 'captcha_required_text' ][ 'text' ] ); ?>');
				return false;
			}
			validation_rules['captcha'] = false;
			$dexQuery.ajax({
				type: "GET",
				url:  "<?php echo cp_calculatedfieldsf_get_site_url(); ?>",
				data: {
					ps: "<?php echo $CP_CFF_global_form_count; ?>",
					hdcaptcha_cp_calculated_fields_form_post: $dexQuery("#hdcaptcha_cp_calculated_fields_form_post<?php echo $CP_CFF_global_form_count; ?>").val()
				},
				success:function(result){
					if (result == "captchafailed")
					{
						$dexQuery("#captchaimg<?php echo $CP_CFF_global_form_count; ?>").attr('src', $dexQuery("#captchaimg<?php echo $CP_CFF_global_form_count; ?>").attr('src')+'&'+Date());
						alert('<?php echo( $cpcff_texts_array[ 'incorrect_captcha_text' ][ 'text' ] ); ?>');
						return false;
					}
					else
					{
						validation_rules['captcha'] = true;
						processing_form();
					}
				}
			});
		<?php
		}
			/**
			 * Action called in the generation of javascript code to validate the forms data before submission.
			 * To the function are passed two parameters: the array with submitted data, and the number of form in the page.
			 */
			do_action( 'cpcff_script_after_validation', $CP_CFF_global_form_count, $id );
		?>
			processing_form();
		}
		return false;
	}
	</script><!--/noptimize-->
	<form name="<?php echo $form_data[ 1 ][ 'formid' ]; ?>" id="<?php echo $form_data[ 1 ][ 'formid' ]; ?>" action="<?php echo( ( ( $permalink = get_permalink() ) !== false ) ? $permalink : '?'); ?>" method="post" enctype="multipart/form-data" onsubmit="return doValidate<?php echo $CP_CFF_global_form_count; ?>(this);">
	<?php
	if( !empty( $form_cache ) )
	{
		// The form is stored in cache, the following section corrects the 	consecutive number to identify the forms on page
		$form_cache = stripcslashes( $form_cache );
		$form_cache = preg_replace( '/(fieldname|separator)(\d+)_\d+/', '$1$2'.$CP_CFF_global_form_count, $form_cache );
		$form_cache = preg_replace( '/field_\d+(\-\d+)/', 'field'.$CP_CFF_global_form_count.'$1', $form_cache );
		$form_cache = preg_replace( 	'/(form_structure|cp_calculatedfieldsf_pform|fbuilder|formheader|fieldlist|cpcaptchalayer|captchaimg|hdcaptcha_cp_calculated_fields_form_post|hdcaptcha_error|cp_subbtn)_\d+/',
			'$1'.$CP_CFF_global_form_count,
			$form_cache
		);
		$form_cache = preg_replace( '/ps=_\d+&/', 'ps='.$CP_CFF_global_form_count.'&', $form_cache );
		$form_cache = preg_replace( '/value="_\d+"/', 'value="'.$CP_CFF_global_form_count.'"', $form_cache );
		print $form_cache;
		// Prevent to call the server side to create the cache
		print '<!--noptimize--><script>form_structure'.$CP_CFF_global_form_count.'[1]["cached"]=true;form_structure'.$CP_CFF_global_form_count.'[1]["setCache"]=false;</script><!--/noptimize-->';
	}
	else
	{
		// The form is not cached, or the from's cache is disabled
	?>
		<input type="hidden" name="cp_calculatedfieldsf_pform_psequence" value="<?php echo $CP_CFF_global_form_count; ?>" /><input type="hidden" name="cp_calculatedfieldsf_pform_process" value="1" /><input type="hidden" name="cp_calculatedfieldsf_id" value="<?php echo $id; ?>" /><input type="hidden" name="cp_ref_page" value="<?php echo esc_attr(cp_calculatedfieldsf_get_site_url() ); ?>" /><pre style="display:none;"><!--noptimize--><script>form_structure<?php echo $CP_CFF_global_form_count; ?>=<?php print str_replace( array( "\n", "\r" ), " ", ((version_compare(CP_CFF_PHPVERSION,"5.3.0")>=0)?json_encode($form_data, JSON_HEX_QUOT|JSON_HEX_TAG):json_encode($form_data)) ); ?>;</script><!--/noptimize--></pre>
		<div id="fbuilder">
			<div id="fbuilder<?php echo $CP_CFF_global_form_count; ?>">
				<div id="formheader<?php echo $CP_CFF_global_form_count; ?>"></div>
				<div id="fieldlist<?php echo $CP_CFF_global_form_count; ?>"></div>
			</div>
			<div id="cpcaptchalayer<?php echo $CP_CFF_global_form_count; ?>" class="cpcaptchalayer" style="display:none;">
			<?php if(CPCFF_COUPON::active_coupons($id)) { ?>
				<div class="fields">
					<label><?php echo( $cpcff_texts_array[ 'coupon_code_text' ][ 'text' ] ); ?></label>
					<div class="dfield"><input type="text" name="couponcode" value=""></div>
					<div class="clearer"></div>
				</div>
			<?php } ?>
            <?php $paypal_enabled = cp_calculatedfieldsf_get_option('enable_paypal',CP_CALCULATEDFIELDSF_DEFAULT_ENABLE_PAYPAL); ?>
			<div class="fields" id="field-c0" <?php if ($paypal_enabled != '2') echo 'style="display:none"'; ?>>
				<label><?php echo( $cpcff_texts_array[ 'payment_options_text' ][ 'text' ] ); ?></label>
				<div class="dfield">
				 <?php if ($paypal_enabled == '1' || $paypal_enabled == '2') { ?><div><input type="radio" name="bccf_payment_option_paypal" vt="1" value="1" checked> <?php _e( cp_calculatedfieldsf_get_option('enable_paypal_option_yes',CP_CALCULATEDFIELDSF_PAYPAL_OPTION_YES,$id), 'calculated-fields-form') ; ?></div><?php } ?>
				 <!--addons-payment-options-->
    			 <?php if ($paypal_enabled == '2') { ?><div><input type="radio" name="bccf_payment_option_paypal" vt="0" value="0"> <?php _e( cp_calculatedfieldsf_get_option('enable_paypal_option_no',CP_CALCULATEDFIELDSF_PAYPAL_OPTION_NO,$id), 'calculated-fields-form') ; ?></div><?php } ?>
				</div>
				<div class="clearer"></div>
			</div>
			<!--addons-payment-fields-->
			<?php if (cp_calculatedfieldsf_get_option('cv_enable_captcha', CP_CALCULATEDFIELDSF_DEFAULT_cv_enable_captcha,$id) != 'false') { ?>
				<div class="fields">
					<label><?php echo( $cpcff_texts_array[ 'captcha_text' ][ 'text' ] ); ?></label>
					<div class="dfield">
						<img src="<?php echo cp_calculatedfieldsf_get_site_url().'/?cp_calculatedfieldsf=captcha&ps='.$CP_CFF_global_form_count.'&inAdmin=1&width='.cp_calculatedfieldsf_get_option('cv_width', CP_CALCULATEDFIELDSF_DEFAULT_cv_width,$id).'&height='.cp_calculatedfieldsf_get_option('cv_height', CP_CALCULATEDFIELDSF_DEFAULT_cv_height,$id).'&letter_count='.cp_calculatedfieldsf_get_option('cv_chars', CP_CALCULATEDFIELDSF_DEFAULT_cv_chars,$id).'&min_size='.cp_calculatedfieldsf_get_option('cv_min_font_size', CP_CALCULATEDFIELDSF_DEFAULT_cv_min_font_size,$id).'&max_size='.cp_calculatedfieldsf_get_option('cv_max_font_size', CP_CALCULATEDFIELDSF_DEFAULT_cv_max_font_size,$id).'&noise='.cp_calculatedfieldsf_get_option('cv_noise', CP_CALCULATEDFIELDSF_DEFAULT_cv_noise,$id).'&noiselength='.cp_calculatedfieldsf_get_option('cv_noise_length', CP_CALCULATEDFIELDSF_DEFAULT_cv_noise_length,$id).'&bcolor='.cp_calculatedfieldsf_get_option('cv_background', CP_CALCULATEDFIELDSF_DEFAULT_cv_background,$id).'&border='.cp_calculatedfieldsf_get_option('cv_border', CP_CALCULATEDFIELDSF_DEFAULT_cv_border,$id).'&font='.cp_calculatedfieldsf_get_option('cv_font', CP_CALCULATEDFIELDSF_DEFAULT_cv_font,$id); ?>"  id="captchaimg<?php echo $CP_CFF_global_form_count; ?>" alt="security code" border="0" title="<?php echo( $cpcff_texts_array[ 'refresh_captcha_text' ][ 'text' ] ) ; ?>" width="<?php echo cp_calculatedfieldsf_get_option('cv_width', CP_CALCULATEDFIELDSF_DEFAULT_cv_width,$id); ?>" height="<?php echo cp_calculatedfieldsf_get_option('cv_height', CP_CALCULATEDFIELDSF_DEFAULT_cv_height,$id); ?>" />
					</div>
					<div class="clearer"></div>
				</div>
				<div class="fields">
					<label><?php echo( $cpcff_texts_array[ 'security_code_text' ][ 'text' ] ); ?></label>
					<div class="dfield">
						<input type="text" size="20" name="hdcaptcha_cp_calculated_fields_form_post" id="hdcaptcha_cp_calculated_fields_form_post<?php echo $CP_CFF_global_form_count; ?>" value="" />
						<div class="error message" id="hdcaptcha_error<?php echo $CP_CFF_global_form_count; ?>" style="display:none;"></div>
					</div>
					<div class="clearer"></div>
				</div>
			<?php } ?>
			<!--add-ons-->
			</div>
			<?php if (cp_calculatedfieldsf_get_option('enable_submit','',$id) == '') { ?>
			<div id="cp_subbtn<?php echo $CP_CFF_global_form_count; ?>" class="cp_subbtn" style="display:none;"><?php _e($button_label); ?></div>
			<?php } ?>
			<div class="clearer"></div>
		</div>
	<?php
	}
	wp_nonce_field( 'cpcff_form_'.$id.$CP_CFF_global_form_count );
	// Inserts a honeypot field to protect the form against spam bots
	if( ( $honeypot = get_option( 'CP_CALCULATEDFIELDSF_HONEY_POT', '' ) ) != '' )
	{
		echo '<p style="display:none"><textarea name="'.$honeypot.'" cols="100%" rows="10"></textarea><label  for="'.$honeypot.'">'.__( 'If you are a human, do not fill in this field.', 'calculated-fields-form' ).'</label></p>';
	}
	?>
	</form>
<?php
}
?>