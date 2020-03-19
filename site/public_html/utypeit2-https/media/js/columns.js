/*
*
* ColumnList
* by William Logan, 2013
*
* Creates a hierarchal list, in columns and populates the columns
* using an AJAX call to the server. This script requires prototype
* v 1.7+ and scriptaculous 1.9+.
* 
* Some assembly required.
*
* Right-click contextual menu provided courtesy of proto.menu.js
* by Juriy Zaytsev
*
*/

var ColumnList = function(container,dataurl,count,names) {
	this.container = container;
	this.count = count;
	this.names = names.split(',');
	this.dataurl = dataurl;
}

/*
*
* Receives:
* container : id of wrapper element (usually a div)
* dataurl : the url to the server script feeding us data
* columns : comma delimited string of the column names
*
* NOTE: the column name is used as the value of the 
* action variable sent to the server.
*/
ColumnList.prototype._create = function() {
	// Get the data for the first column.
	// All other columns are conditional.
	var attrs = {column: 1, name: this.names[0], parent: 0, child: 0};
	this._getAndAdd(attrs);
},
ColumnList.prototype._next = function(iterant,myparent,mychild) {
	var next = (iterant+1);
	var nextCol = 'column_'+next;
	if($(nextCol) != null) {
		$(nextCol).remove();
	}
	var attrs = {column: next, action: this.actions[iterant], type: this.columns[iterant], parent: myparent, child: mychild};
	this._getAndAdd(attrs);
}
ColumnList.prototype._detail = function(iterant,details) {
	var newcol = 3;
	var thiscol = 'column_'+newcol;
	if($(thiscol) != null) {
		$(thiscol).remove();
	}
	var element = Builder.node('div',{className: 'column', id: thiscol});
	/*
	id, title, list_order, date_added, date_modified
	*/
	for(var d in details) {
		var label = ucwords(str_replace('_',' ',d)) + ': ';
		var p = Builder.node('p',[Builder.node('span',{className: 'label'},label)],urldecode(details[d]));
		element.appendChild(p);
	}
	var button = Builder.node('button',{onClick: 'editRecipe(' + details.id + ')'},'Edit Recipe');
	element.appendChild(button);
	$(this.container).insert(element);
}
ColumnList.prototype._add = function(data,name,column) {
	// use scriptaculous Builder to create our column nodes...
	var thiscol = 'column_'+column;
	var column = Builder.node('div',{className: 'column',id: thiscol});
	var pared = eval('data.'+name);
	for(var i=0;i<pared.length;i++) {
		var element = pared[i];
		/*
		id, title, list_order, date_added, date_modified
		*/
		var inputName = name + '-title_' + element.number;
		var inputID = inputName;
		var item = Builder.node('div',{className: 'listItem',id: inputID});
		var title = urldecode(pared[i].name);
		var input = Builder.node('input',{type: 'text',name: inputName, value: title});
		var details = "{id: '"+pared[i].number+"', title: '"+title+"', list_order: '"+pared[i].order+"', date_added: '"+pared[i].date_added+"', date_modified: '"+pared[i].date_modified+"'}";
		var act = Builder.node('div',{className: 'listItemAction', onClick: 'columns._detail(' + column + ',' + details + ')'},'>');
		item.appendChild(input);
		item.appendChild(act);
		column.appendChild(item);
	}
	$(this.container).insert(column);
	this._setDrag(thiscol);
}
ColumnList.prototype._remove = function() {
	
}
ColumnList.prototype._reorder = function(column) {
	//$(column)
}
ColumnList.prototype._setDrag = function(column) {
	Sortable.create(column, {
		tag: 'div',
		onUpdate: function() {
			//this._reorder(column);
		}
	})
}
/*
*
* Receives:
* url : the url to the server script feeding us data
* action : the POST action used by the script on the
*	server to know what data to retrieve. See the
* 	sample PHP file for more info.
*
* Expects to receive a JSON string with all the column
* items in the following form:
* [
*	{"name":"column_name","number":"id_of_the_record","order":"order_of_appearance","parent":"parent_id","value":"text value"},
*	{"name":"categories","number":"1","order":"1","parent":"0","value":"First Category"}
* ]
*/
ColumnList.prototype._getAndAdd = function(attr) {
	var self = this;
	new Ajax.Request(this.dataurl,{
		method: 'post',
		parameters: {name: attr.name, parent: attr.parent, child: attr.child},
		requestHeaders: {Accept: 'application/json'},
		onSuccess: function(transport) {
			//alert('RESPONSE: '+transport.responseText);
			var json = transport.responseText.evalJSON(true);
			if(typeof json[attr.action] != 'undefined') {
				json = json[attr.action];
			}
			self._add(json,attr.name,attr.column);
		}
	});
}
ColumnList.prototype._require = function(libraryName) {
	try{
	  // inserting via DOM fails in Safari 2.0, so brute force approach
	  document.write('<script type="text/javascript" src="'+libraryName+'"><\/script>');
	} catch(e) {
	  // for xhtml+xml served content, fall back to DOM methods
	  var script = document.createElement('script');
	  script.type = 'text/javascript';
	  script.src = libraryName;
	  document.getElementsByTagName('head')[0].appendChild(script);
	}
}