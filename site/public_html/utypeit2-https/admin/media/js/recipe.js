function _saveRecipe(target,recipeId) {
	if(target.nodeName == 'BUTTON') {
		target.disable();
	}
	//sendForm(form,responder,required,result,params)
	// find out if the status is set to "Approved"
	sendForm('edit_recipe','Cookbook.php','title,category','recipe_edit',{mode:'redirect',action:'recipe_edit'});
}

function _saveAndAdd(target,recipeId) {
	//sendForm(form,responder,required,result,params)
	if(target.nodeName == 'BUTTON') {
		target.disable();
	}
	var action = $('action').value;
	sendForm('edit_recipe','Cookbook.php','title,category','recipe_edit',{mode:'redirect',action:'recipe_add'});
}

function sendPreview(responder) {
	var res = '';
	window.open("preview_win.php"+res,"_blank","scrollbars=1,status=1,toolbar=1,menubar=1,resizeable=1,location=1,height=900,width=750");
}


var sendDelete = function(recipe_id) {
	var dodelete = confirm('You are about to delete this recipe. This cannot be undone. An alternate method is to set the recipe status to "Inactive". If you are sure you want to delete this recipe, click "OK." Otherwise, click "Cancel"');
	if(dodelete) {
		new Ajax.Request(window.services+'Cookbook.php',{
			method: 'post',
			parameters: {action: 'recipe_delete', id: recipe_id},
			onFailure: function(transport) {
				alert('Failed: '+transport.responseStatus);
			},
			onSuccess: function(transport) {
				//alert(transport.responseText);
				window.location = 'recipe_list.php';
			}
		});
	}
}

function _addSection() {
	window._recipesections = new RecipeSections('sections_list','div');
	window._recipesections._add($('add_section'));
	$$('span.countdown').each(function(ea){ ea.update('&nbsp;&nbsp;'); });
	$('edit_recipe').getInputs('text').each(function(ea){ if(ea.hasAttribute('maxlength')){ ea.removeAttribute('maxlength'); } });
}

function _addIngredient() {
	if(!_ins_object) {
		alert("Please put your cursor in the list where you want to add an ingredient\nby clicking on any one of the text fields in the list.");
	} else {
		var parent = _ins_object.up('div.ingredientsList',0);
		window._recipesections = new RecipeSections(parent,'div');
		var target = parent.select('button#add_ingredient')[0];
		window._recipesections._add(target);
	}
}

function _setSubCatList(category) {
	$('subcategory').writeAttribute('class','enabled');
	$('subcategory').enable();
	var url = window.includes + 'process_list.php';
	$('subcategory').update();
	var b = new Ajax.Request(url,{
		method: 'get',
		parameters: {type:'subcategories_list',parent:category},
		onFailure: function(transport) {
			alert('ERROR: '+transport.responseStatus);
		},
	    onSuccess: function (transport){ 
	        var data = transport.responseText; 
	        $('subcategory').update(data);
			Form.Element.focus('subcategory'); 
        } 
    });
}

function _setRecipeIcon(icon) {
	$('recipe_icon_img').src = icon.src;
	$('recipe_icon').value = icon.alt;
	$('iconDesk').update(icon.alt);
}

function setSecret(element) {
	window._ins_object = element;
	$( element ).focus();
}

function fillSpecial(val) {
	//var val = element.selectedIndex >=0 && element.selectedIndex ? element.options[element.selectedIndex].value : undefined;
	if(val) {
		if(Prototype.Browser.IE) {
			doinsert_ie(window._ins_object,val);
		} else {
			doinsert_ff(window._ins_object,val);
		}
		
		window._ins_object.focus();
	}
}

function doinsert_ie(lasttext,val) {
    var oldtext = lasttext.value;
    var marker = "##MARKER##";
    lasttext.focus();
    var sel = document.selection.createRange();
    sel.text = marker;
    var tmptext = lasttext.value;
    var curpos = tmptext.indexOf(marker);
    pretext = oldtext.substring(0,curpos);
    posttest = oldtext.substring(curpos,oldtext.length);
    lasttext.value = pretext + val + posttest;
}

function doinsert_ff(lasttext,val) {
    var oldtext = lasttext.value;
    var curpos = lasttext.selectionStart;
    pretext = oldtext.substring(0,curpos);
    posttest = oldtext.substring(curpos,oldtext.length);
    lasttext.value = pretext + val + posttest;
}

function limitText(element,container,limit) {
	/*if (element.value.length == limit) {
		var newnum = limit - element.value.length;
		var text = element.up(container,0).select('span#countdown')[0];
		text.addClassName('warn');
		text.update(newnum);
	} else {
		var newnum = limit - element.value.length;
		var text = element.up(container,0).select('span#countdown')[0];
		if(text.hasClassName('warn')) text.removeClassName('warn');
		text.update(newnum);
	}*/
}

function _setSideBoxes() {
	$$('div.sideBox').each(function(ea){
		if(first == true) {
			if(ea.hasClassName('shown')) {
	    		new Effect.Morph(ea,{style: 'right: -150px', afterFinish: function(){
	    	    	ea.removeClassName('shown');
	    	        ea.addClassName('hidden');
					}
				});
	    	}
	    } else {
	        ea.removeAttribute('style');
	        ea.setAttribute('class','sideBox hidden');
	    }
		ea.down('div.sideTab',0).observe('click',function(event){
        	var tab = event.target;
            var container = tab.up('div.sideBox',[0]);
            if(container.hasClassName('shown')) {
	            new Effect.Morph(container,{style: 'right: -135px', duration: 0.2, afterFinish: function(){
	            	container.removeClassName('shown');
	            	container.addClassName('hidden');
	            	container.removeAttribute('style');
	            }});
			}
            if(container.hasClassName('hidden')){
            	container.removeAttribute('class');
                container.setAttribute('class','sideBox shown');
            }
        }); 
    });
}

function _setDragDrop(){
	window._recipesections = new RecipeSections('sections_list','div');
	window._recipesections._setdrag();
	window._contributors = new Contributors('contributor_list','div',window.max_contributors);
	window._contributors._setdrag();
}
