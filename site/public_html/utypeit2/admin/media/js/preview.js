var Preview = function() {};

Preview.prototype._scrub = function(text) {
	
	var pattern = Array(/1\/2/g,/1\/4/g,/3\/4/g,/1\/3/g,/2\/3/g,/1\/8/g,/3\/8/g,/5\/8/g,/7\/8/g,/&deg;/g,/&Acirc;/g,/&#039/g,/ć'/g,/\^/g,/&lt;/g,/&gt;/g,/\*/g);
	var replace = Array('&#189;','&#188;','&#190;','&#8531;','&#8532;','&#8539;','&#8540;','&#8541;','&#8542;','&#176;','','&#8217;','&#263;','&#176;','<','>','&#42;');
	
	for(var p=0;p<pattern.length;p++) {		
		text = text.replace(pattern[p],replace[p]);
	}
	//text = text.replace('/\b(\d+)\s+(?=\&#)/','$1',text);
	return(text);
}

Preview.prototype._draw = function() {
	
	var parent = opener.document;
	
	var recipe = parent.getElementById('edit_recipe');
	recipe = recipe.serialize(true);
	
	/*
	*
	* Draw the recipe
	*
	*/
	// HEADER...
	
	var date_added = parent.getElementById('date_added').innerHTML;	
	var date_modified = parent.getElementById('date_modified').innerHTML;

	$('recipe_id').update(recipe.id);
	
	$('recipe_name').update(recipe.title);
	$('date_added').update(date_added);
	$('last_modified').update(date_modified);
	$('added_by').update(recipe.added_by_name);
	
	var categorySelect = parent.getElementById('category');
	var options = categorySelect.select('option');
	for(var i=0;i<options.length;i++) {
		if(options[i].value == recipe.category) {
			$('category_name').update(options[i].innerHTML);
		}
	}
	
	if(parent.getElementById('subcategory')) {
		var subcategorySelect = parent.getElementById('subcategory');
		if(recipe.subcategory) {
			var options = subcategorySelect.select('option');
			for(var i=0;i<options.length;i++) {
				if(options[i].value == recipe.subcategory) {
					$('subcategory_name').update(options[i].innerHTML);
				}
			}
		}
	}
	
	var status = parent.getElementById('status')[parseInt(recipe.status) + 1].innerHTML;
	$('status').update(status);
	
	// CONTENT...
	// Title...
	
	var out = Builder.node('div',{id: 'recipe'});
	var titleblock = Builder.node('div',{className: 'title'},recipe.title);
	if(recipe.recipe_icon) {
		var img_src = parent.getElementById('recipe_icon_img').src;
		if(substr(img_src,-9) == '_none.png') {
			img_src = '';
		} else {
			var icon = Builder.node('img',{src: img_src,style: 'height: 18px'});
			titleblock.appendChild(icon);
		}
	}
	if(recipe.subtitle) {
		var subtitle = Builder.node('div',{className: 'subtitle'},recipe.subtitle);
		titleblock.appendChild(subtitle);
	}
	out.appendChild(titleblock);
	$('content').update(out);
	
	// Sections...
	/*
			<li>'.$nt->scrubText($ingredients[$i]).'</li>
	*/
	var sections = Element.select(parent,'div.recipeSection');
	for(i=0;i<sections.length;i++) {
		// Get the section type...
		if(sections[i].select('input#sectiontype')[0].value == 'ingredient') {
			var ingredients = Builder.node('div',{className: 'ingredients'});
			var ingredientsList = Builder.node('div',{className: 'ingredientsList'});
			var list = sections[i].select('input[type="text"]');
			// the first text input is always going to be the title. Does it have a value?
			if(list[0].value) {
				var title = Builder.node('div',{className: 'section_title'},list[0].value);
				$('content').insert(title);
			}
			// all the rest (if there are any) are ingredients...
			var ing_ul = Builder.node('ul',{className: 'ingredient', style: 'float: left'});
			for(var l=1;l<list.length;l++) {
				if(l == Math.ceil (list.length/2)) {
					ingredientsList.appendChild(ing_ul);
					ing_ul = Builder.node('ul',{className: 'ingredient', style: 'float: left'}); 
				}
				var li = '<li>' + this._scrub(list[l].value) + '</li>';
				ing_ul.insert(li);
			}
			ingredientsList.appendChild(ing_ul);
			ingredients.appendChild(ingredientsList);
			$('content').insert(ingredients);
		} else {
			var themethod = this._scrub(sections[i].select('textarea')[0].value);
			var method = '<div class="section">' + themethod + '</div>';
			//var method = Builder.node('div',{className: 'section'},themethod);
			$('content').insert(method);	
		}
	}
	// Note...
	if(parent.getElementById('note')) {
		if(parent.getElementById('note').value != '') {
			var note = Builder.node('div',{className: 'section note'},parent.getElementById('note').value);
			$('content').insert(note);
		}
	}
	// Credits...
	for(var i=1;i<=2;i++) {
		var first_name = '';
		var last_name = '';
		var contributor_name = '';
		if(parent.getElementById('contributor-' + i + '_first_name').value) {
			var first_name = parent.getElementById('contributor-' + i + '_first_name').value;
		}
		
		if(parent.getElementById('contributor-' + i + '_last_name').value) {
			var last_name = parent.getElementById('contributor-' + i + '_last_name').value;
		}
		
		var contributor_name = first_name + ' ' + last_name;
		if(contributor_name.length > 1) {
			var contributor = Builder.node('div',{className: 'section credits'},contributor_name);
			$('content').insert(contributor);
		}
		if(parent.getElementById('contributor-' + i + '_credits_1')) {
			var credits_1 =  parent.getElementById('contributor-' + i + '_credits_1').value;
			var credit = Builder.node('div',{className: 'section credits'}, credits_1);
			$('content').insert(credit);
		}
		if(parent.getElementById('contributor-' + i + '_credits_2')) {
			var credits_2 =  parent.getElementById('contributor-' + i + '_credits_2').value;
			var credit = Builder.node('div',{className: 'section credits'}, credits_2);
			$('content').insert(credit);
		}
	}
	
}

Preview.prototype._send = function(response) {
	if(response == 'edit_recipe') {
		this._submit('edit_recipe','Cookbook.php','recipe_title,category','recipe_edit',{mode:'redirect',action:'recipe_edit',id:$('recipe_id').innerHTML});
	}
	if(response == 'add_recipe') {
		this._submit('edit_recipe','Cookbook.php','recipe_title,category','recipe_add',{mode:'redirect',action:'recipe_add'});
	}
}

Preview.prototype._submit = function(form,responder,required,result,params) {
	
	var parent = opener.document;
	
	if(!params) {
		var params = {};
	}
	
	var err = {status: false, message: ''};
	
	if(required) {
		err = this._verify(required);
	}
	
	if(err.status == false) {
		var url = window.services + responder + '?result=' + result;
		var resp = new Ajax.Request(url,{
			method: 'post',
			parameters: Form.serialize(parent.getElementById(form)),
			asynchronous: true,
			requestHeaders: {Accept: 'application/json'},
			onFailure: function(transport) {
				alert("Failure:" + transport.statusText);
			},
			onSuccess: function(transport) {
				// response always returns a string with status , message
				// if successful, the message will always be the id of the item added/updated
				//alert(transport.responseText);
				var parsed = transport.responseText.evalJSON(true);
				if(parsed.status == 'false') {
					alert(parsed.message);
				} else {
					var id_str = '';
					if(result == 'recipe_edit') {
						id_str = '&id='+params.id;
						if(parsed.id) {
							id_str = '&id='+parsed.id;
						}
					}
					
					var h = 'recipe_edit.php?action='+result+id_str;
					window.opener.location.href = h;
					window.close();
				}
			}
		});
	} else {
		$('feedback').update(err.message);
	}

}

Preview.prototype._verify = function(required) {
	var parent = opener.document
	var reqs = required.split(',');
	var err = {status: false, message: ''};
	for(var r=0;r<reqs.length;r++) {
		var elem = parent.getElementById( reqs[r] );
		var pdiv = reqs[r].split('_');
		var pretty = '';
		if(pdiv.length > 1) {
			for(var p=0;p<pdiv.length;p++) {
				var pretty = pretty + (pdiv[p].charAt(0).toUpperCase() + pdiv[p].slice(1)) + ' ';
			}
		} else {
			var pretty = pdiv[0].charAt(0).toUpperCase() + pdiv[0].slice(1);
		}
		if(elem) {
			if(elem.type == 'text' || elem.type == 'password') {
				if(!elem.present()) {
					err.status = true;
					err.message =  err.message + ' ' + pretty + ' is required. ';
					elem.addClassName('error');
				} else {
					if(elem.id == 'email') {
						var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
						if (!filter.test(elem.value)) {
							err.status = true; 
							err.message = err.message + 'Please provide a valid email address';
						}
					}
					if(elem.hasClassName('error')) {
						elem.removeClassName('error');
					}
				}
			} else if(elem.type == 'checkbox') {
				if(elem.checked != true) {
					err.status = true;
					err.message = err.message + ' ' + pretty + ' is required. ';
					//elem.value = 'required';
					elem.addClassName('error');
				} else {
					if(elem.hasClassName('error')) {
						elem.removeClassName('error');
					}
				}
			} else if(elem.type == 'radio') {
			
			} else if(elem.type == 'select-one') {
				if(elem.selectedIndex == 0) {
					err.status = true;
					err.message = err.message + ' ' + pretty + ' is required. ';
					elem.addClassName('error');
				} else {
					if(elem.hasClassName('error')) {
						elem.removeClassName('error');
					}
				}
			} else {
				if(elem.innerHTML == '') {
					elem.addClassName('error');
				} else {
					if(elem.hasClassName('error')) {
						elem.removeClassName('error');
					}
				}
			}
		}
	}
	return(err);
}