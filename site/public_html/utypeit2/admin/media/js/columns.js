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
var ColumnList = {
	container: '',
	columns: Array(),
	actions: Array(),
	dataurl: '',
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
	_create: function(container,dataurl,columns,actions) {
		// Get the data for the first column...
		ColumnList.container = container;
		ColumnList.columns = columns.split(',');
		ColumnList.actions = actions.split(',');
		ColumnList.dataurl = dataurl;
		var url = ColumnList.dataurl;
		var attrs = {column: 1, action: ColumnList.actions[0], type: ColumnList.columns[0], parent: 0, child: 0, callBack: ColumnList._add };
		ColumnList._getData(url,attrs);
	},
	_next: function(iterant,myparent,mychild) {
		var next = (iterant+1);
		var nextCol = 'column_'+next;
		if($(nextCol) != null) {
			$(nextCol).remove();
		}
		var attrs = {column: next, action: ColumnList.actions[iterant], type: ColumnList.columns[iterant], parent: myparent, child: mychild, callBack: ColumnList._add};
		ColumnList._getData(ColumnList.dataurl,attrs);
	},
	_detail: function(iterant,details) {
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
		$(ColumnList.container).insert(element);
	},
	_add: function(data,type,column) {
		// use scriptaculous Builder to create our column nodes...
		var thiscol = 'column_'+column;
		var element = Builder.node('div',{className: 'column',id: thiscol});
		for(var i=0;i<data.length;i++) {
			/*
			id, title, list_order, date_added, date_modified
			*/
			if(typeof data[i].id != 'undefined') {
				type = 'recipe';
				var inputName = type + '-title_' + data[i].list_order;
				var inputID = type + '_' + (i + 1);
				var item = Builder.node('div',{className: 'listItem',id: inputID});
				var title = urldecode(data[i].title);
				var input = Builder.node('input',{type: 'text',name: inputName, value: title});
				var details = "{id: '"+data[i].id+"', title: '"+data[i].title+"', list_order: '"+data[i].list_order+"', date_added: '"+data[i].date_added+"', date_modified: '"+data[i].date_modified+"'}";
				var act = Builder.node('div',{className: 'listItemAction', onClick: 'ColumnList._detail(' + column + ',' + details + ')'},'>');
				item.appendChild(input);
				item.appendChild(act);
			} else {
				var inputName = type + '-title_' + data[i].number + '_' + data[i].parent + '_' + data[i].order;
				var inputID = type + '_' + (i + 1);
				var item = Builder.node('div',{className: 'listItem',id: inputID});
				var input = Builder.node('input',{type: 'text',name: inputName, value: data[i].name});
				var act = Builder.node('div',{className: 'listItemAction', onClick: 'ColumnList._next(' + column + ',' + data[i].parent + ',' + data[i].number + ')'},'>');
				item.appendChild(input);
				item.appendChild(act);
			}
			element.appendChild(item);
		}
		$(ColumnList.container).insert(element);
		ColumnList._setDrag(column);
	},
	_remove: function() {
		
	},
	_reorder: function(column) {
		//$(column)
	},
	_setDrag: function(column) {
		var column = 'column_' + column;
		Sortable.create(column, {
			tag: 'div',
			onUpdate: function() {
				ColumnList._reorder(column);
			}
		})
	},
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
	_getData: function(url,attr) {
		new Ajax.Request(url,{
			method: 'post',
			parameters: attr,
			requestHeaders: {Accept: 'application/json'},
			onSuccess: function(transport) {
				//alert('RESPONSE: '+transport.responseText);
				var json = transport.responseText.evalJSON(true);
				if(typeof json[attr.action] != 'undefined') {
					json = json[attr.action];
				}
				ColumnList._add(json,attr.type,attr.column);
			}
		});
	},
	_require: function(libraryName) {
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
	},
	REQUIRED_PROTOTYPE: '1.6.0.3',
	_load: function() {
		function convertVersionString(versionString) {
			var v = versionString.replace(/_.*|\./g, '');
			v = parseInt(v + '0'.times(4-v.length));
			return versionString.indexOf('_') > -1 ? v-1 : v;
		}
		if((typeof Prototype=='undefined') ||
			(typeof Element == 'undefined') ||
			(typeof Element.Methods=='undefined') ||
			(convertVersionString(Prototype.Version) <
			convertVersionString(ColumnList.REQUIRED_PROTOTYPE))) {
				throw("Column List requires the Prototype JavaScript framework >= " +
				ColumnList.REQUIRED_PROTOTYPE);
				var js = /columns\.js(\?.*)?$/;
				$$('script[src]').findAll(function(s) {
					return s.src.match(js);
				}).each(function(s) {
					var path = s.src.replace(js, 'src/'),
					includes = s.src.match(/\?.*load=([a-z,]*)/);
					(includes ? includes[1] : 'proto.menu').split(',').each(
					function(include) { ColumnList._require(path+include+'.js') });
				});
		}
	}
}

ColumnList._load();