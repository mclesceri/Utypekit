function saveOrganize() {
	$('recipe_organize').submit();
}

function reOrder() {
	var container = $('listContainer');
	var i = 1;
	container.select('div.itemRow').each(function(ea){
		ea.id = 'row_' + i;
		ea.down('input#id',0).name = 'id_' + i;
		ea.down('input#order',0).name = 'order_' + i;
		ea.down('input#order',0).value = i;
		i = parseInt(i) + 1;
	});
}

function setDrag() {
	Sortable.create('listContainer',{tag: 'div', handle: 'handle', onUpdate: function(){ reOrder(); }});
}

function drawList(category,subcategory) {
	if(!subcategory) {
		subcategory = '0';
		updateSubcats(category);
	}
	var url = window.includes + "process_list.php?type=recipe_organize&parent="+category+"&value="+subcategory;
	var b = new Ajax.Request(url,{ onSuccess: function (transport){ 
		// insert the returned HTML
		var data = transport.responseText;
		$('listContainer').update(data);
		window.setDrag();
		}
	});
}

function updateSubcats(category) {
	var url = window.includes + 'process_list.php?type=subcategories_list&parent='+category;
	var b = new Ajax.Request(url,{ onSuccess: function (transport){ 
		// insert the returned HTML
		var data = transport.responseText;
		$('subcategory').update(data);
		}
	});
}

function highlight(target) {
	
}