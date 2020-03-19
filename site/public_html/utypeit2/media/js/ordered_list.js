var OrderedList = function(attrs) {
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

OrderedList.prototype._update = function() {
	// update the id and order attribute of the first level of children
	var i = 1;
	if(this.root == 'list_parent') {
		$(this.root).select('div.orderListSection').each(function(ea){
			ea.id = ea.id='categories_'+i;
			ea.setAttribute('order',i);
			var oldname = ea.down('input#category',0).name.split('_');
			var oldnums = oldname[1].split('-');
			ea.down('input',0).name = oldname[0]+'_'+oldnums[0]+'-0-'+i;
			i++;
		});
	}
	if(this.root.split('_')[0] = 'childlist') {
		var childList = $(this.root).select('div.orderListSubSection');
		var c = 0;
		childList.each(function(ea){
			ea.setAttribute('order', (c + 1));
			var oldname = ea.down('input#subcategory',0).name.split('_');
			var oldnums = oldname[1].split('-');
			ea.down('input#subcategory',0).name = oldname[0]+'_'+oldnums[0]+'-'+oldnums[1]+'-'+(c+ 1);
			c++;
		});
	}
	var form = document.getElementsByTagName('form')[0].name;
	window.setTabIndex(form);
}
OrderedList.prototype._add = function(node) {
	tempURL = window.includes + 'OrderedList.php';
	new Ajax.Request(tempURL,{
		method:'post',
		parameters: node,
		asynchronous: false,
		onFailure: function(transport) {
			alert('Transport Failure: ' + transport.responseStatus);
		},
		onSuccess: function(transport) {
			//alert(transport.responseText);
			var ins = $(this.root).insert(transport.responseText);
			
			// update everyone's tab order
			var form = document.getElementsByTagName('form')[0].name;
			window.setTabIndex(form);
			
			// reset the drag and drop
			this._setdrag();
			
			var latest = ins.select('div')[(ins.select('div').length - 1)];
			return(latest);
				
			}.bind(this)
		});
}
OrderedList.prototype. _remove = function() {
		nodeobj.remove();
}
OrderedList.prototype._setdrag = function() {
	if(this.drag) {
		var self  = this;
		Sortable.create(this.root,{
									tag: 'div', 
									handle: 'handle',
									onUpdate: function(){
										self._update();
									}
								}
		);
	} else {
		$(this.root).childElements().each(function(ea) {
			ea.select('img.handle')[0].remove();
		});
	}
}


OrderedList.prototype._childlist = function() {
	children.each(function(ce){
		if(ce.id.split('_')[0] == 'childlist') {
			alert(ce.id);
			var nol = new OrderedList(type,ce.id);
			nol._setdrag();
		}
	});
}


OrderedList.prototype._activate = function() {
	// Unique id required for each orderedListSection for drag to work...
	var i = 0;
	var theBase = $(this.root);
	this._setdrag();
	var sections = theBase.select('div.orderListSection');
	sections.each(function(ea){
		ea.id = ea.id + '_' + (i + 1);
		var childList = ea.select('div.orderListSubSection');
		if(childList.length > 0) {
			var c = 0;
			childList.each(function(ea){ ea.id = ea.id + '_' + (c + 1); c = (c + 1); });
			var cl = new OrderedList({type: 'div',root: ea.select('div.childList')[0].id});
			cl._setdrag();
		}
		i = (i + 1);
	});
}