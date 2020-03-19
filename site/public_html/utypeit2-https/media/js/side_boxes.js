function _setSideBoxes() {
$$('div.sideBox').each(function(ea){
if(window.first == true) {
if(ea.hasClassName('shown')) {
new Effect.Morph(ea,{style: 'right: -150px', delay: 1.0, afterFinish: function(){
ea.removeClassName('shown');
ea.addClassName('hidden');
}
});
}
} else {
ea.removeAttribute('style');
if(ea.hasClassName('shown')) {
ea.removeClassName('shown');
ea.addClassName('hidden');
}
} 
});
}

document.observe('dom:loaded',function(){
	$$('div.sideTab',0).each(function(ea) {
		ea.observe('click',function(event){
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
				container.removeAttribute('style');
				container.removeClassName('hidden');
				container.addClassName('shown');
			}
		});
	});
});