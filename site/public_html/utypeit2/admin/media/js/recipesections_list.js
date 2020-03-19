var RecipeSections = function(root,type) {
	this.type = type;
	this.root = root;
}

RecipeSections.prototype._settype = function(target) {
	var parent = target.up('div.recipeSection');
	var ingredients = parent.select('input#ingredients')[0];
	var method = parent.select('input#method')[0];
	var sectiontype = parent.select('input#sectiontype')[0];
	var current = null;
	if(sectiontype.value != '') {
		var current = sectiontype.value;
	}
	if(target.id != current) {
		if(current) {
			var hascontent = false;
			if(sectiontype.value == 'ingredient') {
				parent.select('input[type="text"]').each(function(ea){ if(ea.value != ''){ hascontent = true; } });
			} else {
				if(parent.select('textarea')[0].value){
					hascontent = true;
				};
			}
			if(hascontent == true) {
				var change = confirm('Changing section types will remove all\r\ninformation stored for this section. Continue?');
				if(change) {
					if(target.id == 'ingredients') {
						parent.select('span#countdown').each(function(ea){ ea.update('100')});
						parent.select('input[type="text"]').each(function(ea){ ea.value = ''});
						parent.select('div.ingredientsBox')[0].show();
						parent.select('div.methodBox')[0].hide();
					}
					if(target.id == 'method') {
						parent.select('input[type="text"]').each(function(ea){ ea.value = ''});
						parent.select('textarea')[0].value = '';
						parent.select('div.ingredientsBox')[0].hide();
						parent.select('div.methodBox')[0].show();
					}
					sectiontype.value = target.id;
				} else {
					if(target.id == 'ingredients') {
						method.checked = true;
					}
					if(target.id == 'method') {
						ingredients.checked = true;
					}
				}
			} else {
				if(target.id == 'ingredients') {
					parent.select('div.ingredientsBox')[0].show();
					parent.select('div.methodBox')[0].hide();
					ingredients.checked = true;
				}
				if(target.id == 'method') {
					parent.select('div.ingredientsBox')[0].hide();
					parent.select('div.methodBox')[0].show();
					method.checked = true;
				}
				sectiontype.value = target.id;
			}
		} else {
			if(target.id != current) {
				if(target.id == 'ingredients') {
					parent.select('div.ingredientsBox')[0].show();
				}
				if(target.id == 'method') {
					parent.select('div.methodBox')[0].show();
				}
				sectiontype.value = target.id;
			}
		}
	}
}

RecipeSections.prototype._addtitle = function(target) {
	var parent = target.up('div.recipeSection');
	if(target.id.split('-')[0] == 'ingredients') {
		var thebox = parent.select('input#ingredient-title')[0];
	}
	/*if(target.id.split('-')[0] == 'method') {
		var thebox = parent.select('input#method-title')[0];
	}*/
	if(thebox.getAttribute('style') != 'display: none') {
		thebox.value = '';
	}
	thebox.toggle();
}

RecipeSections.prototype._addnote = function(target) {
	if($('note').visible()) {
		if($('note').value != '') {
			var clear = confirm('Removing the note will clear out all text entered. This change is permanent. Continue?');
			if(clear) {
				$('note').value = '';
				$('note').toggle();
			}
		} else {
			$('note').toggle();
		}
	} else {
		$('note').toggle();
	}
}

RecipeSections.prototype._sectionsupdate = function() {
	var sections = $$('div.recipeSection');
	var count = 0;
	for(var i=0;i<sections.length;i++) {
		count = (i + 1);
		
		//<input tabindex="1" id="sectiontype" value="ingredient" type="hidden">
		var old_type = sections[i].select('input#sectiontype')[0].value;
		
		sections[i].id = 'section_'+count;
		var ingredient_type = sections[i].select('input#ingredients')[0];
		ingredient_type.name = 'section-type_'+count;
		if(old_type == 'ingredient') {
			ingredient_type.checked = true;
		}
		var method_type = sections[i].select('input#method')[0];
		method_type.name = 'section-type_'+count;
		if(old_type == 'method') {
			method_type.checked = true;
		}
		sections[i].select('div.recipeSectionControls')[0].id = 'controls_'+count;
		
		sections[i].select('div.ingredientsBox')[0].id = 'ingredients-box_'+count;
		sections[i].select('input#ingredient-title')[0].name = 'ingredient-title_'+count;
		var inglist_parent = sections[i].select('div.ingredientsList')[0];
		inglist_parent.id = 'ingredients-list_'+count;
		var ingcount = 1;
		inglist_parent.select('div.ingredientItem').each(function(ea){
			ea.id = 'ingredient_'+ingcount;
			ea.select('input#ingredient')[0].name = 'ingredient-'+count+'_'+ingcount;
			ea.select('div.ingredientItemControls')[0].id= 'controls_'+ingcount;
			ingcount++;
		});
		
		var method = sections[i].select('div.methodBox')[0]
		method.id = 'method-box_'+count;
		method.select('textarea')[0].id = 'method_'+count;
		method.select('textarea')[0].name = 'method_'+count;
	}
	var note = $$('div.recipeNote')[0];
	if(typeof note != 'undefined') {
		note.id = 'section_'+(count + 1);
		note.select('input#note_select')[0].name = 'section-type_'+(count + 1);
		note.select('textarea#note')[0].name = 'note_'+(count + 1);
	}
	
	var form = document.getElementsByTagName('form')[0].name;
  	window.setTabIndex(form);
	_recipesections._setdrag();
}

RecipeSections.prototype._ingredientsupdate = function(item,state) {
	var parent = item.id.split('_')[1];
	var all = item.select('div.ingredientItem');
	var total = all.length;
	// set up the controls
	var removeImg = Builder.node('img',{src: window.images + 'remove_button.png'});
	var removeButton = Builder.node('button',{type: 'button', className: 'ing', id: 'remove_ingredient', onClick: '_recipesections._remove(this);return false;', style: 'margin: 0 50px 0 0;'});
		removeButton.appendChild(removeImg);
	var addImg  = Builder.node('img',{src: window.images + 'add_button.png'});
	var addButton = Builder.node('button',{type: 'button', className: 'ing', id: 'add_ingredient', onClick: '_recipesections._add(this);return false;', style: 'margin: 0 50px 0 0;'});
		addButton.appendChild(addImg);
	var moveButton = Builder.node('img',{src: window.images + 'move_button.png', className: 'handle'});
	var blankButton = Builder.node('img',{src: window.images + 'blank.png', id: 'blank', style: 'cursor: default'});
	if(total == 1) {
		var element = all[0];
		var newInt = 1;
		controls = element.select('div.ingredientItemControls')[0];
		controls.id = 'controls_' + newInt;
		controls.update();
		blankButton.setAttribute('style','margin: 0 50px 0 0; cursor: default;');
		controls.appendChild(blankButton);
		controls.appendChild(addButton);
		blankButton.setAttribute('style','margin: 0 0 0 50px; cursor: default;');
		controls.appendChild(blank);
	} else {
		for(var i=0;i<total;i++) {
			var controls = null;
			var element = null;
			var newInt = null;
			element = all[i];
			newInt = i + 1;
			element.id = element.id.split('_')[0] + '_' + newInt;
			element.select('input#ingredient')[0].name = 'ingredient-'+ parent + '_' + newInt;
			controls = element.select('div.ingredientItemControls')[0];
			controls.id = 'controls_' + newInt;
			if(i == 0) {
				var count = controls.select('button#add_ingredient').length;
				if(count > 0) {
					controls.update();
					controls.insert(removeButton);
					blankButton.setAttribute('style','margin: 0 50px 0 0; cursor: default;');
					controls.insert(blankButton);
					controls.insert(moveButton);
				}
			}
			if(i < (total-1)) {
				button = controls.select('button.ing')[1];
				if(typeof button != 'undefined') {
					blankButton.setAttribute('style','margin: 0 0 0 50px; cursor: default;');
					button.replace(blankButton);
				}
			} else if(i == (total-1)) {
				button = controls.childElements()[1];
				button.replace(addButton);
			}
		}
	}
	/*
	*
	* Special exemption for admin. Need to get rid of counter and limit on the newly entered ingredient...
	*
	*/
	item.select('span.countdown').each(function(ea){ ea.update('&nbsp;&nbsp;'); });
	item.select('input#ingredient').each(function(ea){ ea.removeAttribute('maxlength') });
	var form = document.getElementsByTagName('form')[0].name;
  	window.setTabIndex(form);
  	if(state == true) {
	  	item.select('input#ingredient')[(item.select('input#ingredient').length - 1)].focus();
	}
	_recipesections._setdrag();
}

RecipeSections.prototype._remove = function(item) {
	var type = item.id.split('_')[1];
	var parent;
	if(type == 'ingredient') {
		element = item.up('div.ingredientItem',0);
		parent = item.up('div.ingredientsList',0);
	} else {
		element = item.up('div.recipeSection',0);
		type = 'section';
	}
	
	var itemidsplit = element.id.split('_');
	var idpretty = itemidsplit[0] + ' ' + itemidsplit[1];
	if(type== 'section') {
		var warn = confirm('You are about to delete ' + idpretty + '.\r\nThis change can be undone if you\r\nrefresh the page before saving.\r\nAfter saving, this change will be\r\npermanent. Continue?');
		if(warn) {
			element.remove();
			_recipesections._sectionsupdate();
		}
	} 
	if(type == 'ingredient') {
		if(element.down('input#ingredient').value) {
			var warn = confirm('You are about to delete ' + idpretty + '.\r\nThis change can be undone if you\r\nrefresh the page before saving.\r\nAfter saving, this change will be\r\npermanent. Continue?');
			if(warn) {
				element.remove();
				_recipesections._ingredientsupdate(parent,false);
			}
		} else {
			element.remove();
			_recipesections._ingredientsupdate(parent,false);
		}
	}
}

RecipeSections.prototype._add = function(target) {
	var elem = target.id.split('_')[1];
	if(elem == 'ingredient') {
		var parent = target.up('div.ingredientsList');
		var parentid = parent.id.split('_')[1];
		var count = parent.select('div.ingredientItem').length;
	} else {
		var parent = $('sections_list');
		var parentid = 0;
		var count = parent.select('div.recipeSection').length;
	}
	var url = 'http://' + window.location.host + '/utypeit2/src/includes/Recipe.php';
	var self = this;
	new Ajax.Request(url,{
		method: 'post',
		parameters: {action: elem, parent: parentid, total: count, sender: 'admin' },
		onFailure: function(transport) {
			alert(transport.statusText);
		},
		onSuccess: function(transport) {
			parent.insert(transport.responseText);
			if(elem == 'section') {
				self._sectionsupdate();
			} 
			if(elem == 'ingredient') {
				self._ingredientsupdate(parent,true);
			}
		}
	});
}

RecipeSections.prototype._setdrag = function() {
	
	Sortable.create(this.root,{
								tag: this.type, 
								handle: 'handle',
								onUpdate: function(){ 
									_recipesections._sectionsupdate();
								}  
							}
	);
	Element.select(this.root, 'div.ingredientsList').each(function(ea){
		Sortable.create(ea,{
								tag: 'div', 
								handle: 'handle',
								onUpdate: function(item){ 
									_recipesections._ingredientsupdate(item,false);
								}
					}
		);
	});
}
