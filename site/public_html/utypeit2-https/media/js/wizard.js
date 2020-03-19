var mysteps = [0,1];
var contributor_count = 0;
var account_type = 0;
var current_format = 0;

var steps = [
	{key: 1,slide: 'slide_1',title: 'Step 1 : Account Holder Signup',required: 'first_name,last_name,login,password,email', message: 'Use the "Next" and "Back" buttons above to set up your cookbook with U-Type-It&trade; <em>Online</em>. Items marked with a red asterisk (<span style="color: red">*</span>) are required. For help, click on the <a class="help">?</a> next to the item, or <a href="http://dev.cbp.ctcsdev.com/contact/" target="_blank">contact us</a>. After your choices have been made, click the "Sign Up" button at the bottom right.'},
	{key: 2,slide: 'slide_2',title: 'Step 2 : Signup Wizard Options',required: 'title,terms_of_service', message: 'We will now help you set up up your cookbook order. Options may be changed later when you log into your account. Items <span style="background: rgba(154,220,244,.6); padding: 0 5px 0 5px">highlighted with blue</span> are Designer Options. These options add more value to your book, enabling you to increase your book\'s selling price. Items <span style="background: rgba(208,255,216,.6); padding: 0 5px 0 5px">highlighted with green</span> are CentSaver&trade; options which reduce your per-book cost.'},
	{key: 3,slide: 'slide_3',title: 'Step 3 : Add Contributors',required: 'contributor_order_level,contributor_first_name,contributor_last_name,contributor_login,contributor_password,contributor_email', message: 'In addition to yourself (the Chairperson), you may add a co-chairperson, or contributors who will be assisting with the order and adding recipes. To see the list of levels and their associated permissions, click on the <a class="help">?</a> button below.'},
	{key: 4,slide: 'slide_4',title: 'Step 4 : Cookbook Options',message: 'On this screen you can enter some of the basic information for your cookbook project. Remember, if you change your mind later you can always update this information when you log in. For help click on the <a class="help">?</a> next to that item.'},
	{key: 5,slide: 'slide_5',title: 'Step 5 : Recipe Sections',message: 'Our standard recipe categories are shown below in the order they will appear in your cookbook. Custom Recipe Categories and Sub-categories can be entered here. After your account set-up is complete, categories may be modified and/or the order of their appearance in the book changed.'},
	{key: 6,slide: 'slide_6',title: 'Step 6 : Recipe Format & Designer Options',message: 'Choose the recipe format and designer options that make your book special. Each recipe format features a different typestyle and page layout. Recipe formats are available only in the typestyle shown. Click on the thumbnail to see a larger sample of each recipe format.'}
];

var Wizard = function() {
	this.current = 0;
	var self = this;
	sessionStorage.clear();
	$('organization_type').observe('change',function(event){
		var selected = event.target[event.target.selectedIndex].value;
		if(selected == 'other') {
			$('other_type').enable();
		} else {
			$('other_type').update();
			$('other_type').disable();
		}
	});
	
	$('account_type_demo').observe('click',function(){
		if($('account_type_demo').checked) {
			$('organization_type').selectedIndex = 0;
			$('organization_type').disable().addClassName('disabled');
	        $('organization_name').value = '';
	        $('organization_name').disable().addClassName('disabled');
			$('next_button').addClassName('disabled');
			$('next_bottom').enable();
		}
	});
	
	$('account_type_live').observe('click',function(){
		if($('account_type_live').checked) {
			account_type = 1;
	        if($('organization_type').hasClassName('disabled')) {
	            $('organization_type').selectedIndex = 0;
	            $('organization_type').enable().removeClassName('disabled');
	            $('organization_name').value = '';
	            $('organization_name').enable().removeClassName('disabled');
	        }
			$('next_button').removeClassName('disabled');
			$('next_bottom').disable();
		}
	});
	
	$('order_form').observe('click',function(){
		$('order_form_table').toggle();
	});
	
	
	$('use_subcategories').observe('click',function(){
		if($('use_subcategories').checked) {
			self._subcategories();
		} else {
			self._subcategories();
		}
	});
	
	$('recipes_continued_no').observe('click', function(event){
		if($('recipes_continued_no').up('td',0).next('td').hasClassName('designeroption_off')) {
			$('recipes_continued_no').up('td',0).next('td').removeClassName('designeroption_off');
			$('recipes_continued_no').up('td',0).next('td').addClassName('designeroption_on');
		}
		$('page_fillers_label').removeClassName('disabled');
		$('page_fillers_label').next('td').addClassName('designeroption_off').next('td').addClassName('designeroption_off');
		$('page_fillers_label').next('td').select('span').each(function(ea){ ea.removeClassName('disabled'); });
	
		$('use_fillers_yes').writeAttribute('class','enabled');
		$('use_fillers_no').writeAttribute('class','enabled');
		$('use_fillers_yes').enable();
		$('use_fillers_no').enable();
		this._recipe_format_select('no');
	});
	
	$('recipes_continued_yes').observe('click', function(event){
		if($('recipes_continued_no').up('td',0).next('td').hasClassName('designeroption_on')) {
			$('recipes_continued_no').up('td',0).next('td').removeClassName('designeroption_on');
			$('recipes_continued_no').up('td',0).next('td').addClassName('designeroption_off');
		}
		
		$('page_fillers_label').addClassName('disabled');
		if($('page_fillers_label').next('td').hasClassName('designeroption_off')){
			$('page_fillers_label').next('td').removeClassName('designeroption_off');
		}
		if($('page_fillers_label').next('td').hasClassName('designeroption_on')){
			$('page_fillers_label').next('td').removeClassName('designeroption_on');
		}
		if($('page_fillers_label').next('td').next('td').hasClassName('designeroption_off')){
			$('page_fillers_label').next('td').next('td').removeClassName('designeroption_off');
		}
		if($('page_fillers_label').next('td').next('td').hasClassName('designeroption_on')){
			$('page_fillers_label').next('td').next('td').removeClassName('designeroption_on');
		}
		$('page_fillers_label').next('td').select('span').each(function(ea){ ea.addClassName('disabled'); });
		$('use_fillers_no').checked = true;
		$('use_fillers_yes').disable();
		$('use_fillers_no').disable();
		
		$('filler_type_label').addClassName('disabled');
		$('filler_type').selectedIndex=0;
		$('filler_type').disable();
		
		$('filler_set_label').addClassName('disabled');
		$('filler_set').selectedIndex=0;
		$('filler_set').disable();
		this._recipe_format_select('yes');
	});
					
	$('allow_notes_yes').observe('click', function(event){
		$('allow_notes_yes').up('td',0).next('td').removeClassName('designeroption_off');
		$('allow_notes_yes').up('td',0).next('td').addClassName('designeroption_on');
	});
	
	$('allow_notes_no').observe('click', function(event){
		$('allow_notes_no').up('td',0).next('td').removeClassName('designeroption_on');
		$('allow_notes_no').up('td',0).next('td').addClassName('designeroption_off');
	});
	
	$('use_icons_yes').observe('click', function(event){
		$('use_icons_yes').up('td',0).next('td').removeClassName('designeroption_off');
		$('use_icons_yes').up('td',0).next('td').addClassName('designeroption_on');
	});
	
	$('use_icons_no').observe('click', function(event){
		$('use_icons_no').up('td',0).next('td').removeClassName('designeroption_on');
		$('use_icons_no').up('td',0).next('td').addClassName('designeroption_off');
	});
	
	$('use_fillers_yes').observe('click', function(event){
		$('use_fillers_yes').up('td',0).next('td').toggleClassName('designeroption_on');
		$('filler_type').up('td').previous('td').removeClassName('disabled');
		$('filler_type').enable();
	});
	
	$('use_fillers_no').observe('click', function(event){
		$('use_fillers_yes').up('td',0).next('td').toggleClassName('designeroption_on');
		$('filler_type').up('td').previous('td').addClassName('disabled');
		$('filler_type').selectedIndex=0;
		$('filler_type').disable();
		$('filler_set').selectedIndex=0;
		$('filler_set').up('td',0).previous('td').addClassName('disabled');
		$('filler_set').disable();
	});
	
	$('filler_type').observe('change', function(event){
	    var select = $( 'filler_type' );
	    var val = select[select.selectedIndex].value;
	    $('filler_set').up('td',0).previous('td').removeClassName('disabled');
	    $('filler_set').enable();
		$('filler_set').update('');
		self._filler_type(val);
	});
	
	var currentFormat = 'Traditional';
	$$('.recipe_format').each(function(ea) {
		ea.observe('click',function(event) {
			
			var add_exp = Array('Premiere','Fanciful','Casual','Black Tie');
			var oldFormat;
			$$('.recipe_format').each(function(ea){ if(ea.value == currentFormat) oldFormat = ea; });
			var target = event.target;
									
			if($('recipes_continued_yes').checked == true) {
				if($(target).readAttribute('flag') == 'rnc') {
					$(target).checked = false;
					oldFormat.checked = true;
					alert('This format is only available if recipes are not continued page to page');
				} else {
					if(target.value == 'CentSaver') {
						if(target.up('p').hasClassName('centsaver_off')){ target.up('p').removeClassName('centsaver_off').addClassName('centsaver_on'); }
					} else {
						$('slide_6').select('p').each(function(ea){if(ea.hasClassName('centsaver_on')){ ea.removeClassName('centsaver_on').addClassName('centsaver_off')} });
					}
					$(target).checked = true;
					currentFormat = $(target).value;
				}
				
			} else {
				if($(target).readAttribute('flag') == 'rc') {
					$(target).checked = false;
					oldFormat.checked = true;
					alert('This format is only available if recipes are continued page to page');
				} else {
					$('slide_6').select('p').each(function(ea){if(ea.hasClassName('centsaver_on')){ ea.removeClassName('centsaver_on').addClassName('centsaver_off')} });
					$('slide_6').select('p').each(function(ea){if(ea.hasClassName('designeroption_on')){ ea.removeClassName('designeroption_on').addClassName('designeroption_off')} });
					add_exp.each(function(ea){
						
						if(ea == target.value) {
							if(target.up('p').hasClassName('designeroption_off')){ target.up('p').removeClassName('designeroption_off').addClassName('designeroption_on'); }
						}
					});
					$(target).checked = true;
					currentFormat = $(target).value;
				}
			}
		});
	});
	
	$$('input.screen').each(function(ea){
		ea.observe('click',function(event) {
			self._test(event.target);
		});
	});
}

Wizard.prototype._verify = function(required) {
	
	var reqs = required.split(',');
	var err = {
		status: false,
		message: '',
		failed: []
	};
	
	for(var r=0;r<reqs.length;r++) {
		var elem = $( reqs[r] );
		
		// Set up the pretty name in case we need to use it...
		var pdiv = reqs[r].split('_');
		var pretty = '';
		if(pdiv.length > 1) {
			for(var p=0;p<pdiv.length;p++) {
				pretty = pretty + (pdiv[p].charAt(0).toUpperCase() + pdiv[p].slice(1)) + ' ';
			}
		} else {
			pretty = pdiv[0].charAt(0).toUpperCase() + pdiv[0].slice(1);
		}
		
		if(elem) {
			if(elem.type == 'text' || elem.type == 'password') {
				if(!elem.present()) {
					err.status = true;
					err.message =  err.message + ' ' + pretty + ' is required. ';
					err.failed.push(elem);
				} else {
					if(elem.id == 'email') {
						var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
						if (!filter.test(elem.value)) {
							err.status = true; 
							err.message = err.message + 'Please provide a valid email address';
							err.failed.push(elem);
						}
					}
				}
			} else if(elem.type == 'checkbox') {
				if(elem.checked != true) {
					err.status = true;
					err.message = err.message + ' ' + pretty + ' is required. ';
					err.failed.push(elem);
				}
			} else if(elem.type == 'radio') {
			
			} else if(elem.type == 'select-one') {
				if(elem.selectedIndex == 0) {
					err.status = true;
					err.message = err.message + ' ' + pretty + ' is required. ';
					err.failed.push(elem);
				}
			} else {
				if(elem.innerHTML == '') {
					err.failed.push(elem);
				}
			}
		}
	}
	return(err);
}

Wizard.prototype._get_param = function(param) {
   var search = window.location.search.substring(1);
   var compareKeyValuePair = function(pair) {
      var key_value = pair.split('=');
      var decodedKey = decodeURIComponent(key_value[0]);
      var decodedValue = decodeURIComponent(key_value[1]);
      if(decodedKey == param) return decodedValue;
      return null;
   };

   var comparisonResult = null;

   if(search.indexOf('&') > -1) {
      var params = search.split('&');
      for(var i = 0; i < params.length; i++) {
         comparisonResult = compareKeyValuePair(params[i]); 
         if(comparisonResult !== null) {
            break;
         }
      }
   } else {
      comparisonResult = compareKeyValuePair(search);
   }

   return comparisonResult;
}

Wizard.prototype._user = function() {
	var url = window.includes + 'process_form.php';
	var self = this;
	new Ajax.Request(url,{
		method: 'get',
		parameters: {
			action: 'customer_search',
			first_name: $('first_name').value,
			last_name: $('last_name').value,
			email: $('email').value,
			login: $('login').value, 
			password: $('password').value
		},
		requestHeaders: {Accept: 'application/json'},
		onFailure: function(transport) {
			alert('ERROR: ' + transport.responseStatus);
		},
		onSuccess: function(transport) {
			//alert(transport.responseText);
			var parsed = transport.responseText.evalJSON(true);
			if(parsed.status == 'false') {
				$('feedback').update(parsed.message);
				self._buttons('on');
				return false;
			} else {
				self.current = (self.current + 1);
				var step = steps[mysteps[self.current]];
				
				$('header_left').update(step.title);
				// Set the message...
				$('feedback').update(step.message);
				self._slide('forward');
				self._buttons('on');
			}
		}
	});
}

Wizard.prototype._contributor = function() {
	
	var required = steps[2].required;
	var verify = this._verify(required);
	if(verify.status) { // There is an error...
		$('feedback').update('<span style="font-weight: bold; color: #FF0000">' + verify.message + '</span>');
		verify.failed.each(function(ea){ ea.addClassName('error'); });
		this._buttons('on');
		return false;
	}
	
	contributor_count = contributor_count + 1;
	var user = Form.serialize('contributor',true);
	if(sessionStorage.getItem('contributors')) {
		var contributors = sessionStorage.getItem('contributors').evalJSON();
		contributors.push(user);
	} else {
		var contributors = [user];
	}
	sessionStorage.setItem('contributors',JSON.stringify(contributors));
	
	$('contributor_list').insert(user.contributor_first_name+' '+user.contributor_last_name+'<br />');
	$('feedback').update('<strong>User Added</strong><p>You may enter another user, or move to the next screen.</p>');
	Form.getElements('contributor').each(function(ea){ ea.value = ''; });
}

Wizard.prototype._cancel_contributor = function() {
	Form.getElements('contributor').each(function(ea){ ea.value = ''; });
}

Wizard.prototype._subcategories = function() {
	$('use_subcategories').up('td',0).toggleClassName('designeroption_on');
	if($('use_subcategories').checked) {
		var count = 1;
		$('category').select('div.orderListSection').each(function(ea) {
			var button = Builder.node('img',{src: 'media/images/show_button.png', className: 'showSubcats', onClick: 'cl._show(this)'});
			ea.down('div.orderListSectionControls').insert(button);
			
			var parentNumber = ea.readAttribute('number');
			var parentName = ea.select('input')[0].value;
			
			var sublist = Builder.node('div',{className: 'orderListSubsection', parent: parentNumber});
			var title = Builder.node('p',{className: 'orderListSubsectionTitle'},parentName);
			sublist.insert(title);
			
			var item = Builder.node('div',{className: 'orderListSection'});
			var newname = 'subcategory-title_'+count+'-'+parentNumber+'-'+count;
			var input = Builder.node('input',{name: newname, type: 'text'});
			item.insert(input);
			var control = Builder.node('div',{className: 'orderListSectionControls'});
			var button = Builder.node('img',{src: 'media/images/remove_button.png', onClick: 'cl._remove(this)'});
			control.insert(button);
			item.insert(control);
			sublist.insert(item);
			
			$('subcategory').insert(sublist);
			count++;
		});
		$('subcategory_button').enable();
		$('subcategory_button').setAttribute('onclick','cl._add(\'subcategory\',\'sublist\'); return false;');
		$('subcategory').select('div.orderListSubsection').each(function (ea){ ea.hide(); });
		$('subcategory').select('div.orderListSubsection')[0].show();
		$('slide_5').select('td').each(function(ea){ if(ea.hasClassName('disabled')){ ea.removeClassName('disabled');} });
		window.cl._update();
	} else {
		$('category').select('div.orderListSection').each(function(ea) {
			ea.down('div.orderListSectionControls').select('img')[1].remove();
		});
		$('subcategory_button').disable();
		$('subcategory_button').removeAttribute('onclick');
		$('subcategory').select('div.orderListSubsection').each(function (ea){ ea.remove(); });
		var headers = $('slide_5').select('td');
		headers[2].addClassName('disabled');
		headers[4].addClassName('disabled');
	}
}

Wizard.prototype._next_format = function() {
	if(current_format < (window.format_count - 1)) {
		new Effect.Move('formats',{x: -500,y:0,duration: 0.5});
		current_format = current_format + 1;
	}
}

Wizard.prototype._previous_format = function() {
	if(current_format > 0) {
		new Effect.Move('formats',{x: 500,y:0,duration: 0.5});
		current_format = current_format - 1;
	}
}

Wizard.prototype._recipe_format_select = function() {
	var formats = $$('div.format');
	var add_exp = Array('Premiere','Fanciful','Casual','Black Tie');
	// find out what format is currently selected,
	// and what it's flag is...
	var radio = null;
	var flag = null;
	var fallback = null;
	for(var f=0;f<formats.length;f++) {
		radio = formats[f].select('input')[0];
		if(radio.checked == true) {
			flag = radio.readAttribute('flag');
		}
		if(radio.value == "Traditional") {
			fallback = radio;
		}
	}
	if(state == 'yes') {
		if(flag == 'rnc') {
			var warn = confirm('The currently selected format is not available\nwith Recipes Continued. Click OK to continue.\nThis will select the default "Traditonal" template choice.\nOr, click cancel to keep the current settings.');
			if(warn) {
				fallback.checked = true;
				$('slide_6').select('p').each(function(ea){if(ea.hasClassName('designeroption_on')){ ea.removeClassName('designeroption_on').addClassName('designeroption_off')} });
			} else {
				$('recipes_continued_no').checked = true;
				
			}
		}
	} else {
		if(flag == 'rc') {
			var warn = confirm('The currently selected format is not available\nwith Recipes Not Continued. Click OK to continue.\nThis will select the default "Traditonal" template choice.\nOr, click cancel to keep the current settings.');
			if(warn) {
				fallback.checked = true;
				$('slide_6').select('p').each(function(ea){if(ea.hasClassName('centsaver_on')){ ea.removeClassName('centsaver_on').addClassName('centsaver_off')} });
			} else {
				$('recipes_continued_yes').checked = true;
				
			}
		}
	}
}

Wizard.prototype._filler_type = function(filler) {
	var url = window.includes + 'process_list.php';
	var b = new Ajax.Request(url,{
		method: 'get',
		parameters: {type:'filler_sets',value: filler},
		onFailure: function(transport) {
			alert(transport.responseStatus);
		},
		 onSuccess: function (transport){
		 	$('filler_set').update(transport.responseText); 
		 } 
	});
}

Wizard.prototype._test = function(element) {
	var place = element.readAttribute('rel');
	if(element.checked) {
		var i=0;
		for(var i=0;i<steps.length;i++){
			if(steps[i].key == place){ 
				mysteps.push(i); 
			}
		}
	} else {
		var i=0;
		for(var i=0;i<steps.length;i++){
			if(steps[i].key == place){ 
				mysteps.splice(i,1); 
			}
		}
	}
	mysteps.sort();
	this._buttons('on');
}

Wizard.prototype._buttons = function(state) {
	if(state == 'on') {
		if(this.current > 0) {
			$('back_button').removeClassName('disabled');
			$('next_button').removeClassName('disabled');
			// We need to find out if this is the last step...
			var last = (mysteps.length - 1);
			if(this.current == last) {
				$('next_button').removeAttribute('onclick');
				$('next_button').addClassName('disabled');
				$('back_button').setAttribute('onclick','wizard._next(\'back\')');
				$('next_bottom').enable();
			} else {
				$('next_button').setAttribute('onclick','wizard._next(\'forward\')');
				$('back_button').setAttribute('onclick','wizard._next(\'back\')');
				$('next_bottom').disable();
			}
		} else {
			$('next_button').setAttribute('onclick','wizard._next(\'forward\')');
			$('next_button').removeClassName('disabled');
			$('back_button').addClassName('disabled');
			$('back_button').removeAttribute('onclick');
			$('next_bottom').disable();
		}
	} else {
		$$('next_button','back_button','next_bottom').each(function(ea){ ea.removeAttribute('onclick'); ea.addClassName('disabled'); });
	}
}

Wizard.prototype._next = function(direction) {

	// Disable the navigation buttons while we're thinking...
	this._buttons('off');
	
	var step = steps[mysteps[this.current]];
	if(direction == 'forward') {
		if(typeof step.required != 'undefined') {
			if(step.key == 3) {
				if(sessionStorage.getItem('contributors')) {
					var go = confirm('You have not entered any information for this contributor. Are you sure you want to continue?');
					if(!go) {
						return false;
					}
				} else {
					var verify = this._verify(step.required);
					if(verify.status) { // There is an error...
						$('feedback').update('<span style="font-weight: bold; color: #FF0000">' + verify.message + '</span>');
						verify.failed.each(function(ea){ ea.addClassName('error'); });
						this._buttons('on');
						var go = confirm('You have not entered any information for this contributor. Are you sure you want to continue?');
						if(!go) {
							return false;
						}
					}
				}
			} else {
				if(this.current == 0) {
					if(this._get_param('action') != 'upgrade') {
						var verify = this._verify(step.required);
						if(verify.status) { // There is an error...
							$('feedback').update('<span style="font-weight: bold; color: #FF0000">' + verify.message + '</span>');
							verify.failed.each(function(ea){ ea.addClassName('error'); });
							this._buttons('on');
							return false;
						}
					}
				} else {
					var verify = this._verify(step.required);
					if(verify.status) { // There is an error...
						$('feedback').update('<span style="font-weight: bold; color: #FF0000">' + verify.message + '</span>');
						verify.failed.each(function(ea){ ea.addClassName('error'); });
						this._buttons('on');
						return false;
					}
				}
			}
		}
	}
	
	// Make sure to return all elements to their default CSS
	if(step.required != 'undefined') {
		if(typeof step.required != 'undefined') {
			step.required.split(',').each( function(ea){ if($(ea).hasClassName('error')){ $(ea).removeClassName('error'); } });
		}
	}
	
	if(this.current == 0) {
		if(this._get_param('action') != 'upgrade') {
			this._user();
		} else {
			this.current = (this.current + 1);
			this._slide();
		}
	} else {
		// Based on what direction we're about to go, find out what the next slide is going to be...
		if(direction == 'forward') {
			this.current = (this.current + 1);
			this._slide();
		} else {
			this.current = (this.current - 1);
			this._slide();
		}
		step = steps[mysteps[this.current]];
		
		// Set the title..
		$('header_left').update(step.title);
		// Set the message...
		$('feedback').update(step.message);
		this._buttons('on');
	}
}

Wizard.prototype._slide = function() {
	var next = steps[mysteps[this.current]].slide;
	var factor = mysteps[this.current];
	var next_x = 0;
	if(factor > 0) {
		next_x = -(1010 * mysteps[(this.current)]);
	}
	new Effect.Move('form_slider',{x:next_x,y:0,mode:'absolute',duration: 0.5});
}

Wizard.prototype._send = function() {
	var reqs = '';
	if(account_type) {
		// Check all required fields...
		mysteps.each(function(ea){
			reqs = reqs + steps[ea].required+',';
		});
	} else {
		reqs = steps[0].required;
	}
	var verify = this._verify(reqs);
	if(verify.status) { // There is an error...
		$('feedback').update('<span style="font-weight: bold; color: #FF0000">' + verify.message + '</span>');
		verify.failed.each(function(ea){ ea.addClassName('error'); });
		this._buttons('on');
		return false;
	}
	
	if(this._get_param('action') != 'upgrade') {
		var url = window.includes + 'process_form.php';
		var self = this;
		new Ajax.Request(url,{
			method: 'get',
			requestHeaders: {Accept: 'application/json'},
			parameters: {action: 'customer_search',first_name: $('first_name').value,last_name: $('last_name').value,email: $('email').value, login: $('login').value, password: $('password').value},
			onFailure: function(transport) {
				alert('ERROR: ' + transport.responseStatus);
			},
			onSuccess: function(transport) {
				//alert(transport.responseText);
				var parsed = transport.responseText.evalJSON(true);
				if(parsed.status == 'true') {
					self._do_send();
				} else {
					$('feedback').update('<span style="color: red">' + parsed.message + 'If you have lost your login information, <a href="lost_password.php">click here</a>. If you believe you have received this message in error, <a href="http://dev.cbp.ctcsdev.com/contact/">contact Cookbook Publishers Inc</a>.</span>');
				}
			}
		});
	} else {
		this._do_send();
	}
}

Wizard.prototype._do_send = function() {
	// Get the contributors if there are any and put them into the mix...
	var contributors;
	if(typeof sessionStorage.getItem('contributors') != 'undefined') {
		contributors = encodeURIComponent(sessionStorage.getItem('contributors'));
	}
	
	var params = Form.serialize('signup_wizard',true);
	if(contributors) {
		params.contributors = contributors;
	}
	
	var url = window.includes + "Wizard.php";
	new Ajax.Request(url,{
		method: 'post',
		parameters: params,
		asynchronous: true,
		requestHeaders: {Accept: 'application/json'},
		onFailure: function(transport) {
			alert("Failure:" + transport.statusText);
		},
		onSuccess: function(transport) {
			//alert(transport.responseText);
			var parsed = transport.responseText.evalJSON(true);
			if(parsed.status == 'false') {
				$('feedback').update(parsed.message);
			} else {
				var response = transport.responseText;
				new Ajax.Request(window.services + "Contacts.php",{
					method: 'post',
					parameters: {action: 'welcome',type: account_type, data: response},
					asynchronous: true,
					requestHeaders: {Accept: 'application/json'},
					onFailure: function(transport) {
						alert("Failure:" + transport.statusText);
					},
					onSuccess: function(transport) {
						//alert(transport.responseText);
						var location = window.baseurl + 'order_list.php?new=yes';
						window.location.href = location;
					}
				});
				return false;
			}
		}
	});
	
}
