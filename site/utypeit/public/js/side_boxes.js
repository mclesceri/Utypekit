// function _setSideBoxes() {
// $$('div.sideBox').each(function(ea){
// if(window.first == true) {
// if(ea.hasClassName('shown')) {
// new Effect.Morph(ea,{style: 'right: -150px', delay: 1.0, afterFinish: function(){
// ea.removeClassName('shown');
// ea.addClassName('hidden');
// }
// });
// }
// } else {
// ea.removeAttribute('style');
// if(ea.hasClassName('shown')) {
// ea.removeClassName('shown');
// ea.addClassName('hidden');
// }
// } 
// });
// }

$(document).ready(function() {
	$(document).on('click', '#recipe_icons', function () {
		var container  = $(this);
		if(container.hasClass('shown')) {
			container.removeClass('shown');
			container.addClass('hidden');
			container.removeAttr('style');
			return false;
		}
		if(container.hasClass('hidden')){
			container.removeAttr('style');
			container.removeClass('hidden');
			container.addClass('shown');
			return false;
		}
	});
});