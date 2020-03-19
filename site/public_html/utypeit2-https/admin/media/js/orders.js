var required = 'title';

var current_format = 1;

function nextFormat() {
	if((current_format * 500) < (window.format_count * 500)) {
		new Effect.Move('formats',{x: -500,y:0,duration: 0.5});
		current_format++;
	}
}

function previousFormat() {
	if(current_format > 1) {
		new Effect.Move('formats',{x: 500,y:0,duration: 0.5});
		current_format--;
	}
}

function selectFormat(format) {
	var formats = window.formats;
	for(var i=0;i<formats.length;i++) {
		if(window.formats[i].name == format) {
			var newx = -500 * i;
			new Effect.Move('formats',{x: newx, y: 0, duration: 0.5});
			var allradios = $('formats').select('input[name="recipe_format"]');
			for(var a=0;a<allradios.length;a++) {
				if(allradios[a].value == format) {
					allradios[a].checked = true;
				}
			}
			current_format = i + 1;
		}
	}
}

function setRecipeFormat(event) {
	var target = event.target;
	if(document.getElementById('recipes_continued_yes').checked) {
		if($(target).readAttribute('flag') == 'rnc') {
			Event.stop(event);
			$(target).checked = false;
			alert('This format is only available if recipes are not continued page to page');
		} else {
			$(target).checked = true;
		}
	} else {
		if($(target).readAttribute('flag') == 'rc') {
			Event.stop(event);
			$(target).checked = false;
			alert('This format is only available if recipes are continued page to page');
		} else {
				$(target).checked = true;
		}
	}
}

function setRecipeFormatSelect(state) {
	var formats = $$('div.format');
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
			} else {
				$('recipes_continued_no').checked = true;
			}
		}
	} else {
		if(flag == 'rc') {
			var warn = confirm('The currently selected format is not available\nwith Recipes Not Continued. Click OK to continue.\nThis will select the default "Traditonal" template choice.\nOr, click cancel to keep the current settings.');
			if(warn) {
				fallback.checked = true;
			} else {
				$('recipes_continued_yes').checked = true;
			}
		}
	}
}


function setSubcategories(state) {
	
	/*if(state == 'no') {
	    $('subtoc_yes').disable();
	    $('subtoc_no').disable();
	} else {
    	$('subtoc_yes').enable();
    	$('subtoc_no').enable();
    	$('subtoc_no').checked = true;
	}*/
	switch(state) {
		case 'yes':
			// look through all categories and add a subcategory to each
			var cats = $$('.orderListSection').each(function(ea){
				var parent = ea.getAttribute('number');
				var newname = 'childlist_'+parent;
				ea.insert('<button class="orderListButton" onclick="addSubcategory(this.next(\'div\',0),'+parent+'); return false;">Add Subcategory</button>','last');
				ea.insert('<div id="' + newname + '" class="childList"></div>');
				addSubcategory(newname,parent);
			});
			break;
		case 'no':
			// look through all categories and remove subcategories from each
			var remove = confirm('Removing subcategories will not remove recipes,\nbut it will break the association between recipes and\nsubcategories. Your recipes may not appear as\nexpected in the finished cookbook. This action cannot be undone.\n\nDo you want to continue?');
			if(remove) {
			    var url = window.includes + 'process_list.php';
			    new Ajax.Request(url,{
			        method: 'get',
			        parameters: {type: 'remove_subcategories' },
			        onSuccess: function(transport) {
			           if(transport.responseText == 'true') {
			                //$('list_parent').select('button').each(function(ea){ ea.remove(); });
				            //$$('.childList').each(function(ea){ ea.remove(); });
	                        sendForm('order_edit','Orders.php','title','order_edit',{mode:'redirect',action:'order_edit'});
	                        $('use_subcategories_no').setAttribute('onclick',"setSubcategories('no')");
			            } else {
			                alert('Error removing subcategories. Please try again. If this problem persists, contact customer support.');
			            }
			        }
			    });
			} else {
				$('use_subcategories_yes').checked = true;
			}
			break;
	}
}

function setFillerType(filler) {
	$('filler_set').removeClassName('inputDisabled').addClassName('inputEnabled').enable();
	var url = window.includes + 'process_list.php';
	$('filler_set').update('');
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

function addCategory(container) {
	var numbers = Array();
	var newcount = 1;
	$(container).select('div.orderListSection').each(function(ea){
		var number = ea.getAttribute('number');
		numbers.push(number);
	});
	if(numbers.length > 0) {
		numbers.sort(function(a,b){return b-a});
		newcount = (parseInt(numbers[0]) + 1);
	}
	var subs = 'no';
	if($(container).childElements()[0].select('div.childList').length > 0) {
		subs = 'yes';
	}
	window.ol._add({action:'section',title:'categories',template:'category.tpl',subcategories: subs, number: newcount,name: '',parent: '0',order: newcount, tabindex: 1});
}

function addSubcategory(container,theparent) {
	// newcount MUST be the highest number assigned plus 1...
	//var newcount = (parseInt($(container).childElements().length)+1);
	var numbers = Array();
	var newcount = 1;
	$(container).select('div.orderListSubSection').each(function(ea){
		var number = ea.getAttribute('number');
		numbers.push(number);
	});
	if(numbers.length > 0) {
		numbers.sort(function(a,b){return b-a});
		newcount = (parseInt(numbers[0]) + 1);
	}
	var cl = new OrderedList({type: 'div',root: container, drag: true});
	cl._add({action:'subsection',title:'subcategories',template:'subcategory.tpl',number: newcount,name: '',parent: theparent,order: newcount, tabindex: 1});
}

function removeElement(element) {
    var parent = element.up('div',1);
	var input = parent.down('input',0);
	if(input.value != '') {
		var del = confirm('Removing this element will remove its association' + '\n' + 'with recipes, but will not remove recipes.' + '\n' + 'You will have to reassign the effected recipes' + '\n' + 'to a new element. Continue?');
		if(del) {
		    element.up('div',1).remove();
	    } else {
	        return false;
	    }
	} else {
	    element.up('div',1).remove();
    }
}

function sendOrder(action) {
	//sendForm(form,responder,required,result,params)
	if(action == 'delete') {
		var doit = confirm("You are about to delete this order and all associated data,\nincluding recipes. This action cannot be undone.\nA better option might be to make the order inactive instead.\nDo you want to continue?");
		if(doit) {
			$('action').value = 'order_delete';
			sendForm('order_edit','Orders.php','','order_list');
		} else {
			return false;
		}
	} else {
		var act = action;
		var req = window.required + window.of_required;
		var mod = 'static';
		if(action == 'order_add') {
			mod = 'redirect';
		}
		sendForm('order_edit','Orders.php',req,'order_edit',{mode: mod, action: act});
	}
}

var Options = function(element,width) {
	this.element = element;
	this.width = width;
};

var current = 0;

Options.prototype._sideScroll = function(slide) {
	if(slide != current) {
		var newx = -(this.width * slide);
		new Effect.Move(this.element,{x: newx, y: 0, mode: 'absolute'});
		current = slide;
	}
}

Options.prototype._setTab = function(slide) {
	var all = $(this.element).childElements();
	var nav = $('order_options_navigation').select('li');
	
	if(slide != current) {
		$(all[current]).hide();
		$(all[slide]).show();
		if($(nav[current]).hasClassName('active')) {
			 $(nav[current].removeClassName('active'));
			 $(nav[current].addClassName('inactive'));
		}
		$(nav[slide]).removeClassName('inactive');
		$(nav[slide]).addClassName('active');
		current = slide;
	} else {
		if($(nav[current]).hasClassName('active')) {
			 $(nav[current].removeClassName('active'));
			 $(nav[current].addClassName('inactive'));
		}
		$(nav[slide]).removeClassName('inactive');
		$(nav[slide]).addClassName('active');
	}
}

var options;
document.observe('dom:loaded', function() {
	options = new Options('order_options_container',805);// element to move, width of each movement
	options._setTab(0);
});