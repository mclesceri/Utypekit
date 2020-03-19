var ColumnList = function(attrs) {
	this.type = 'div';
	this.root = 'root';
	this.drag = true;
	
	if(typeof attrs.type != 'undefined') {
		if(typeof attrs.type != 'undefined') {
			this.type = attrs.type;
		}
		if(typeof attrs.root != 'undefined') {
			this.root = attrs.root;
		}
		if(typeof attrs.drag != 'undefined') {
			this.drag = attrs.drag;
		}
	}
}

ColumnList.prototype._update = function() {
	
	var i = 1;
	$('category').select('div.orderListSection').each(function(ea) {
		/*
		 * <div class="orderListSection" number="1">
				<input name="category-title_1-0-1" value="Appetizers, Beverages" type="text">
				<div class="orderListSectionControls">
					<img src="media/images/remove_button.png" onclick="cl._remove(this,'list')">
					<img src="media/images/show_button.png" onclick="cl._show(this)">
				</div>
		 * </div>
		 */
		ea.setAttribute('number',i);
		ea.select('input')[0].name = 'category-title_'+i+'-'+'0-'+i;
		i++;
	});
	
	if($('use_subcategories').checked) {
		var i = 1;
		$('subcategory').select('div.orderListSubsection').each(function(ea){
			ea.setAttribute('parent',i);
			var s = 1;
			ea.select('div.orderListSection').each(function(sea){
				sea.setAttribute('number',s);
				sea.select('input')[0].name = 'subcategory-title_'+s+'-'+i+'-'+s;
				s++;
			});
			i++;
		}); 
		var inputs = $('category').select('input');
		var i = 0;
		inputs.each(function(ea) {
			var thisSub = $('subcategory').select('div.orderListSubsection')[i].select('p.orderListSubsectionTitle')[0];
			ea.on('keyup',function(event) {
				thisSub.update(ea.value);
			});
			i++;
		});
	}
},
ColumnList.prototype._add = function(element,type) {
	this.type = type;
	if(type == 'list') {
		this._list(element);
	}
	if(type == 'sublist') {
		this._sublist(element);
	}
	window.cl._update();
},
ColumnList.prototype._list = function(element) {
	var container = $(element);
	var par = container.readAttribute('parent');
	var iterant = container.childElements().length;
	var subs = $('use_subcategories').checked;
	var item = this._item(element,{type: 'category', subcategories: subs, count: iterant, parent: par});
	container.insert(item);
	if(subs == true) {
		//<div style="" class="orderListSubsection" parent="1"><p class="orderListSubsectionTitle">Appetizers, Beverages</p>
		this.type = 'sublist';
		var newSubsection = Builder.node('div',{className: 'orderListSubsection',parent: iterant},[Builder.node('p',{className: 'orderListSubsectionTitle'})]);
		$('subcategory').insert(newSubsection);
		var count = 0;
		var total = $('subcategory').select('div.orderListSubsection').length - 1;
		$('subcategory').select('div.orderListSubsection').each(function(ea){
			if(count < total) {
				ea.hide();
			} else {
				element = $('subcategory').down('button',0);
				ea.show();
			}
			count++;
		});
		$('category').lastChild.select('input')[0].focus();
		window.cl._sublist(element);
	}
},
ColumnList.prototype._sublist = function(element) {
	var container = $(element);
	var sublist;
	container.select('div.orderListSubsection').each(function(ea) {
		if(ea.visible()) {
			sublist = ea;
		}
	});
	var par = sublist.readAttribute('parent');
	var iterant = sublist.childElements().length;
	var subs = true;
	this.type = 'sublist';
	var item = this._item(element,{type: 'subcategory', subcategories: subs, count: iterant, parent: par});
	sublist.insert(item);
},
ColumnList.prototype._item = function(element,attrs) {
	if(this.type == 'list') {
		var item = Builder.node('div',{className: 'orderListSection',number: attrs.count});
	} else {
		var item = Builder.node('div',{className: 'orderListSection'});
	}
	var newname = attrs.type+'-title_'+attrs.count+'-'+attrs.parent+'-'+attrs.count;
	
	var input = Builder.node('input',{name: newname, type: 'text'});
	item.insert(input);
	var control = Builder.node('div',{className: 'orderListSectionControls'});
	var button = Builder.node('img',{src: 'media/images/remove_button.png', onClick: 'cl._remove(this,\'' + this.type + '\')'});
	control.insert(button);
	if(attrs.subcategories) {
		if(this.type != 'sublist') {
			var button = Builder.node('img',{src: 'media/images/show_button.png', className: 'showSubcats', onClick: 'cl._show(this)'});
			control.insert(button);
		}
	}
	item.insert(control);
	return(item);
}
ColumnList.prototype. _remove = function(element,type) {
		this.type = type;
		if(this.type == 'list') {
			nodeobj = element.up('div.orderListSection',0);
			var number = nodeobj.readAttribute('number');
			var container = nodeobj.up('div.orderListColumn',0);
			var count = container.childElements().length;
			if($('use_subcategories').checked) {
				$('subcategory').select('div.orderListSubsection').each(function(ea) {
					if(ea.readAttribute('parent') == number) {
						ea.remove();
					}
				});
			}
			nodeobj.remove();
		} else {
			nodeobj = element.up('div.orderListSection',0);
			var container = nodeobj.up('div.orderListSubsection',0);
			nodeobj.remove();
		}
		window.cl._update();
}
ColumnList.prototype._show = function(element) {
		var parent = element.up('div.orderListSection',0).readAttribute('number');
		$('subcategory').select('div.orderListSubsection').each(function(ea) {
			if(ea.readAttribute('parent') == parent) {
				ea.show();
			} else {
				ea.hide();
			}
		});
}
ColumnList.prototype._activate = function() {
		this._setdrag();
		$(this.root).childElements().each(function(ea){
			if(ea.select('div.childList').length > 0) {
				var cl = new OrderedList();
				cl._childlist('div',ea.select('div.childList')[0]);	
			}
	});
}