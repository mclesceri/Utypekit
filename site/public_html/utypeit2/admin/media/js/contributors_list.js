var Contributors = function(root,type)  {
	this.root = root;
	this.type = type;	
}
Contributors.prototype._setdrag = function() {
		Sortable.create(this.root,{
								tag: this.type, 
								handle: 'handle'
							}
		);
}
Contributors.prototype._add = function() {
		//var max = 2;
		//var total = $('contributor_list').select('div.contributor').length;
		//if(total > max) {
		//	max = total;
		//}
		//if(total < max) {
			var url = window.includes + 'Recipe.php';
			new Ajax.Request(url,{
				method: 'post',
				parameters: {action: 'contributor', sender:'admin', headers: 'First Name,Last Name,Credits 1,Credits 2,Order,Delete', total: $('contributor_list').select('div.contributor').length},
				onFailure: function(transport) {
					alert(transport.statusText);
				},
				onSuccess: function(transport) {
					$('contributor_list').insert(transport.responseText);
					var contributors = $('contributor_list').select('div.contributor');
					var newtotal = contributors.length;
					if(newtotal > 1) {
						if(contributors[0].select('img').length == 0) {
							contributors[0].insert('<img src="' + window.images + 'move_button.png" class="handle">');
							contributors[0].insert('<img src="' + window.images + 'remove_button.png" onclick="_contributors._remove(this)">');
						}
					} else {
						if(contributors[0].select('img').length == 0) {
							contributors[0].insert('<img src="' + window.images + 'remove_button.png" onclick="_contributors._remove(this)">');
						}
					}
					window._contributors._setdrag();
				}.bind(this)
			});
		//} else {
		//	alert('Only two contributors are allowed per recipe.');
		//}
}
Contributors.prototype._remove = function(item) {
		var parent = item.up('div.contributor',0);
		parent.remove();
		var contributors = $('contributor_list').select('div.contributor');
		var newtotal = contributors.length;
		if(newtotal == 1) {
			contributors[0].select('img').each(function(ea){ea.remove()});
		}
}