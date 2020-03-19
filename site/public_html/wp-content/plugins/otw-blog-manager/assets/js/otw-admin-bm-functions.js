var $ =  jQuery.noConflict(); //Wordpress by default uses jQuery instead of $

jQuery(document).ready(function(){
	var $ =  jQuery.noConflict(); 
	
	var frameUpload; // WP Media holder;
	/**
	 * Init Blog Items ( Title, Description, Media, Meta, Continue Reading )
	 * Init Meta Items ( Author, Date, Category, Tags, Comments )
	 */
	setupBlogElements();

	// Translation enabled messages
	var $messages = JSON.parse( messages );

	// Disable caching of AJAX responses - DEVELOPMENT ONLY
	jQuery.ajaxSetup ({
	    cache: false
	});

	/**
	 * Enable jQuery UI Accordion for Add List Page
	 */
	jQuery( '.accordion-container' ).accordion({
		header: "> ul > li > h3",
		collapsible: true,
		heightStyle: 'content',
		speed: 'fast'
	});

	/**
	 * Enable Color Picker
	 */
	$element = new Array();
	jQuery('.js-color-picker').each( function(index, element) {
		$element[index] = element;

		jQuery($element[index]).ColorPicker({
			color: '#000000',
				onShow: function (colpkr) {
					jQuery(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					jQuery(colpkr).fadeOut(500);
					// jQuery($colr).next( 'input').change();
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					
					jQuery( $element[index] ).parent().children('.js-color-picker-value').val( '#'+hex );
					jQuery( $element[index] ).children('.js-color-container').css( 'backgroundColor', '#'+hex );
					
				}
			});	
		
	});

	/**
	 * Autocomplete Functionality for: Categories, Tags and Users (Authors)
	 */
	
	var req_url = ajaxurl;
	 
	var otw_select2_categories_params = {};
	otw_select2_categories_params.allowClear = true;
	otw_select2_categories_params.multiple = true;
	
	otw_select2_categories_params.ajax = {
		url: req_url,
		data: function (search, page) {
			return {
				action: 'otw_bm_select2_options',
				otw_options_type: 'category', //search term
				otw_options_search: search,
				otw_options_limit: 10 // page size
			};
		},
		type: 'post',
		results: function (data, page) {
			return { results: data.results };
		}
	}
	otw_select2_categories_params.initSelection = function(element, callback){
		var id = jQuery(element).val();
		
		if(id !== "") {
			jQuery.ajax( req_url , {
				data: {
					otw_options_ids: id,
					action: 'otw_bm_select2_options',
					otw_options_type: 'category'
				},
				method: 'post',
				dataType: "json"
			}).done(function(data) {
				callback(data.results);
			});
		}
	}
	
	var otw_select2_tags_params = {};
	otw_select2_tags_params.allowClear = true;
	otw_select2_tags_params.multiple = true;
	otw_select2_tags_params.ajax = {
		url: req_url,
		data: function (search, page) {
			return {
				action: 'otw_bm_select2_options',
				otw_options_type: 'tag', //search term
				otw_options_search: search,
				otw_options_limit: 10 // page size
			};
		},
		type: 'post',
		results: function (data, page) {
			return { results: data.results };
		}
	}
	otw_select2_tags_params.initSelection = function(element, callback){
		var id = jQuery(element).val();
		
		if(id !== "") {
			jQuery.ajax( req_url , {
				data: {
					otw_options_ids: id,
					action: 'otw_bm_select2_options',
					otw_options_type: 'tag'
				},
				method: 'post',
				dataType: "json"
			}).done(function(data) {
				callback(data.results);
			});
		}
	}
	
	var otw_select2_users_params = {};
	otw_select2_users_params.allowClear = true;
	otw_select2_users_params.multiple = true;
	otw_select2_users_params.ajax = {
		url: req_url,
		data: function (search, page) {
			return {
				action: 'otw_bm_select2_options',
				otw_options_type: 'user', //search term
				otw_options_search: search,
				otw_options_limit: 10 // page size
			};
		},
		type: 'post',
		results: function (data, page) {
			return { results: data.results };
		}
	}
	otw_select2_users_params.initSelection = function(element, callback){
		var id = jQuery(element).val();
		
		if(id !== "") {
			jQuery.ajax( req_url , {
				data: {
					otw_options_ids: id,
					action: 'otw_bm_select2_options',
					otw_options_type: 'user'
				},
				method: 'post',
				dataType: "json"
			}).done(function(data) {
				callback(data.results);
			});
		}
	}
	 
	jQuery('.js-categories').select2( otw_select2_categories_params );
	jQuery('.js-exclude_categories').select2( otw_select2_categories_params );
	
	jQuery('.js-tags').select2( otw_select2_tags_params );
	jQuery('.js-exclude_tags').select2( otw_select2_tags_params );
	
	jQuery('.js-users').select2( otw_select2_users_params );
	jQuery('.js-exclude_users').select2( otw_select2_users_params );
	
	var otw_select2_pages_params = {};
	otw_select2_pages_params.allowClear = true;
	otw_select2_pages_params.multiple = false;
	otw_select2_pages_params.placeholder = 'Page Name';
	otw_select2_pages_params.ajax = {
		url: req_url,
		data: function (search, page) {
			return {
				action: 'otw_bm_select2_options',
				otw_options_type: 'page', //search term
				otw_options_search: search,
				otw_options_limit: 10 // page size
			};
		},
		type: 'post',
		results: function (data, page) {
			return { results: data.results };
		}
	}
	otw_select2_pages_params.initSelection = function(element, callback){
		var id = jQuery(element).val();
		
		if(id !== "") {
			jQuery.ajax( req_url , {
				data: {
					otw_options_ids: id,
					action: 'otw_bm_select2_options',
					otw_options_type: 'page'
				},
				method: 'post',
				dataType: "json"
			}).done(function(data) {
				callback(data.results[0]);
			});
		}
	}
	 
	jQuery('.js-pages').select2( otw_select2_pages_params );

	jQuery('.js-fonts').select2({
		allowClear 	: true,
		multiple 	: false,
		data 		: JSON.parse( fonts ),
		placeholder: 'Font Family'
	});

	jQuery( '#show-social-icons' ).change( function(){
		otw_bm_set_social_related_controlers();
	});
	
	otw_bm_set_social_related_controlers();

	/**
	 * Select All Funcitonality
	 */

	jQuery('.js-select-categories, .js-select-tags, .js-select-users').on('change', function(e) {
		var sectionName = jQuery(this).data('section');

		if( jQuery(this).is(':checked') ) {
			jQuery('.js-'+sectionName+'-select').val( -1 );
			jQuery('.js-'+sectionName+'-counter').html( Number( jQuery( this ).attr( 'data-size' ) ) );
			jQuery('.js-'+sectionName+'-count').show();
			jQuery('.js-'+sectionName+'').select2("enable", false);

		} else {
			jQuery('.js-'+sectionName+'-select').val( '' );
			jQuery('.js-'+sectionName+'-counter').html( '' );
			jQuery('.js-'+sectionName+'-count').hide();
			jQuery('.js-'+sectionName+'').select2("enable", true);
		}

	});
	
	
	if( jQuery('.js-template-style').size() && jQuery('.js-template-style').val() ){
	
		var js_templ_val = jQuery('.js-template-style').val();
		if( typeof( js_template_options[ js_templ_val ] ) == 'object' ){
			jQuery( '.default_thumb_width' ).html( js_template_options[ js_templ_val ].width );
			jQuery( '.default_thumb_height' ).html( js_template_options[ js_templ_val ].height );
		}else{
			jQuery( '.default_thumb_width' ).html( '' );
			jQuery( '.default_thumb_height' ).html( '' );
		}
	}
	/**
	 * Load Front End preview based on selection
	 */
	 
	jQuery('.js-template-style').on('change', function(e){
		
		// Get Current Page Selection
		var pageName = jQuery(this).val();

		// Evaluate page selection and load preview
		// Variable templates can be found in otw-admin-bm-variables.js
		jQuery.each( templates, function( index, obj) {
			
			if ( obj.name == pageName ) {
				// Preview is disabled.
				//jQuery('.js-preview').load( frontendURL + obj.url );		
			}
			
		});
		
		if( typeof( js_template_options[ this.value ] ) == 'object' ){
		
			jQuery( '.default_thumb_width' ).html( js_template_options[ this.value ].width );
			jQuery( '.default_thumb_height' ).html( js_template_options[ this.value ].height );
		}else{
			jQuery( '.default_thumb_width' ).html( '' );
			jQuery( '.default_thumb_height' ).html( '' );
		}
		
		jQuery('.js-mosaic-settings').hide();
		jQuery('.js-slider-settings').hide();
		jQuery('.js-news-settings').hide();
		jQuery('.js-horizontal-settings').hide();

		// Add Mosaic Specific Settings to the page
		if( pageName == '1-3-mosaic' || pageName == '1-4-mosaic' ) {
			// Show Mosaic Specific Settings
			jQuery('.js-mosaic-settings').show();

		} else if ( pageName == '2-column-news' || pageName == '3-column-news' || pageName == '4-column-news' ) {
			// Show News Specific Settings
			jQuery('.js-news-settings').show();

		} else if (
				pageName == 'slider' ||
				pageName == '3-column-carousel' || 
				pageName == '4-column-carousel' || 
				pageName == '5-column-carousel' ||
				pageName == '2-column-carousel-wid' ||
				pageName == '3-column-carousel-wid' ||
				pageName == '4-column-carousel-wid'
			) {
			// Show Slider / Carousel Specific Settings
			jQuery('.js-slider-settings').show();

		} else if ( pageName == 'horizontal-layout' ) {
			jQuery('.js-horizontal-settings').show();
		}

	});

	/*
	jQuery('.js-template-style').otwpreview();
	*/
	
	/**
	 * POST and PAGES custom Meta BOX media selection
	 */

	jQuery('.js-otw-media-type').on('change', function(e) {
		
		var mediaType = jQuery(this).val();

		jQuery('.js-meta-youtube').hide();
		jQuery('.js-meta-vimeo').hide();
		jQuery('.js-meta-soundcloud').hide();
		jQuery('.js-meta-image').hide();
		jQuery('.js-meta-slider').hide();

		switch ( mediaType ) {
			case 'youtube':
				jQuery('.js-meta-youtube').show();
			break;
			case 'vimeo':
				jQuery('.js-meta-vimeo').show();
			break;
			case 'soundcloud':
				jQuery('.js-meta-soundcloud').show();
			break;
			case 'img':
				jQuery('.js-meta-image').show();
			break;
			case 'slider':
				jQuery('.js-meta-slider').show();
			break;
		}

	});

	/**
	 * Make Slider Elements Sortable
	 */
	jQuery('.js-meta-slider-preview').sortable({
		update: function( event, ui ) {
			updateSliderAssets();
		}
	});

	/**
	 * Add functionality to delete images from slider
	 */

	jQuery(document).on('click', '.b-delete_btn', function(e) {
		e.preventDefault();
		
		// Get current selected item
		item = jQuery(this).parent();

		//Remove item from the list
		jQuery(item).remove();

		// Update assets list
		updateSliderAssets ();
	});

	/**
	 * Add Functionality for WordPress Media Upload
	 */
	jQuery(document).on('click', '.js-add-image', function(e) {
		e.preventDefault();
		/**
		 * WordPress Based Media Selection and Upload (Images)
		 * Used for Post Meta information: Images and Slider Images
		 */

		if( frameUpload ) {
			frameUpload.open();
			return;
		}

		frameUpload = wp.media({
			id: 'otw-bm-media-upload',
			// Set the title of the modal.
			title: $messages['modal_title'],
			multiple: false,
			// Tell the modal to show only images.
			library: {
				type: 'image'
			},
			// Customize the submit button.
			button: {
				// Set the text of the button.
				text: $messages['modal_btn'],
				// Change close: false, in order to prevent window to close on selection
				close: true
			}
		});

		frameUpload.on( 'select', function() {
			var attachements = frameUpload.state().get('selection').first().id;
			var attachementURL = wp.media.attachment( attachements ).attributes.url;

			if( jQuery('.js-otw-media-type').val() === 'slider' ) {

				imgTAG = '<li class="b-slider__item" data-src="'+attachementURL+'">';
				imgTAG += '<a href="#" class="b-delete_btn"></a>';
				imgTAG += '<img src="'+attachementURL+'" width="100" />';
				imgTAG += '</li>';
				
				jQuery('.js-meta-slider-preview').append( imgTAG ); //Display IMG
				updateSliderAssets();

			} else {
				// Create HTML for visual effect
				var imgTAG = '<img src="'+attachementURL+'" width="150" />';
				// Append HTML for visual preview
				jQuery('.js-img-preview').html( imgTAG ); //Display IMG

				// Add Image to Hidden input - save to DB
				jQuery('.js-img-url').val( attachementURL );
			}

		})

		frameUpload.open();
	});


	/**
	 * Capture All Links from Preview and Prevent Default
	 * Prevent Browser to follow # link
	 */
	jQuery('.js-preview').on('click', 'a', function(e) {
		e.preventDefault();
	});

	/**
	 * Interface for Meta Elements
	 * Drag & Drop support + Sortable Support
	 */
	jQuery('.js-meta-active, .js-meta-inactive').sortable({
		connectWith: ".b-meta-box",
		update: function( event, ui ) {
			updateBlogMetaElements();
		},
		stop: function( event, ui ) {
			jQuery.event.trigger({
				type: "metaEvent"
			});
		}
	});

	/**
	 * Interface for Blog List Elements
	 * Drag & Drop support + Sortable Support
	 */
	jQuery('.js-bl-active, .js-bl-inactive').sortable({
		connectWith: ".b-bl-box",
		update: function( event, ui ) {
			updateBlogListElements();
		},
		stop: function( event, ui ) {
			jQuery.event.trigger({
				type: "listEvent"
			});
		}
	});

	/**
	 * Detect Delete action and prompt message
	 */
	 jQuery('.js-delete-item').on('click', function(e) {
	 	e.preventDefault();

	 	confirmation =  window.confirm( $messages.delete_confirm + ' ' + jQuery(this).data('name') + '?' );

	 	if( confirmation ) {
	 		window.location = jQuery(this).attr('href');
	 	}

	 });
	 
	jQuery('#white_spaces').change( function(){
		
		if( this.value == 'no' ){
			jQuery( '#white_spaces_color_container' ).hide();
		}else{
			jQuery( '#white_spaces_color_container' ).show();
		}
	 } );
	 
	if( jQuery('#white_spaces').val() == 'no' ){
		jQuery( '#white_spaces_color_container' ).hide();
	}else{
		jQuery( '#white_spaces_color_container' ).show();
	}

});

/**
 * Iterate Assets from media slider and put them into a hidden field
 * Used to save possition + image path in DB
 */
function updateSliderAssets () {
	var imagesArray = new Array();
	jQuery('.b-slider-preview > .b-slider__item').each(function( item, value) {
		imagesArray.push( jQuery(value).data('src') );
	});

	// Add Array to hidden input
	jQuery('.js-img-slider-url').val( imagesArray );
}

/**
 * Iterate On Blog List Items
 * Detect Items that will be used in the list
 * Drag & Drop List Functionality
 */
function updateBlogListElements () {
	var elementsArray = new Array();

	jQuery('.js-bl-active > .js-bl--item').each( function( item, value )  {
		elementsArray.push( jQuery(value).data('value') );
	});

	jQuery('.js-blog-items').val( elementsArray );
}

/**
 * Iterate On Blog Meta Items
 * Detect Items that will be used in the meta
 * Drag & Drop List Functionality
 */
function updateBlogMetaElements () {
	var elementsArray = new Array();

	jQuery('.js-meta-active > .js-meta--item').each( function( item, value )  {
		elementsArray.push( jQuery(value).data('value') );
	});

	jQuery('.js-meta-items').val( elementsArray );
}


/**
 * Get state of Blog List Elements and Blog Meta Elements
 * Modify interface based on current input Edit / Add Error
 */
function setupBlogElements () {
	
	var $ =  jQuery.noConflict();
	
	blogElements = jQuery('.js-blog-items').val();
	metaElements = jQuery('.js-meta-items').val();
	
	if( typeof blogElements !== 'undefined' && typeof metaElements !== 'undefined' ) {
		blogItems = blogElements.split(',');
		metaItems = metaElements.split(',');

		jQuery(blogItems).each( function( item, value ) {

			jQuery('.js-bl-inactive > .js-bl--item').each( function( blItem, blValue )  {
				if( jQuery(blValue).data('value') == value ) {

					jQuery('.js-bl-active').append( jQuery(blValue) );
				} 
			});

		});

		jQuery(metaItems).each( function( item, value ) {

			jQuery('.js-meta-inactive > .js-meta--item').each( function( miItem, miValue )  {

				if( jQuery(miValue).data('value') === value ) {
					jQuery('.js-meta-active').append( miValue );
				}
			});

		});

	}
}
/** 
 * Show soccial related contollers based in the selected value
 */
function otw_bm_set_social_related_controlers(){
	var social_value = jQuery( '#show-social-icons' ).val();
	
	switch( social_value ){
		
		case '0':
				jQuery( '#otw-show-social-icons-type' ).hide();
				jQuery( '#otw-show-social-icons-custom' ).hide();
			break;
		case 'custom_icons':
				jQuery( '#otw-show-social-icons-type' ).hide();
				jQuery( '#otw-show-social-icons-custom' ).show();
			break;
		case 'like_buttons':
				var labels = jQuery( '#otw-show-social-icons-type label' );
				var inputs = jQuery( '#otw-show-social-icons-type input' );
				
				jQuery( labels[4] ).hide();
				jQuery( labels[5] ).hide();
				
				jQuery( inputs[3] ).hide();
				jQuery( inputs[4] ).hide();
				
				jQuery( '#otw-show-social-icons-type' ).show();
				jQuery( '#otw-show-social-icons-custom' ).hide();
			break;
		default:
				jQuery( '#otw-show-social-icons-type' ).show();
				jQuery( '#otw-show-social-icons-custom' ).hide();
				jQuery( '#otw-show-social-icons-type label' ).show();
				jQuery( '#otw-show-social-icons-type input' ).show();
			break;
	}
}