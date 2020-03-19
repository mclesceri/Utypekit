// JavaScript Calculator 

var tds;
var ths;

jQuery(document).ready(function($) {

	window.tds = $('#price_matrix').find('td');
	window.ths = $('#price_matrix').find('th');

	$('#cover_style').on('change',function(){
		var cover_style = $('#cover_style').val();
		if(cover_style == 'hard') {
			$('#coil').hide();
		} else {
			$('#coil').show();
		}
		if(cover_style == '3ring') {
			$('#binding_options').hide();
			$('#binder').hide();
			$('#no_binding_options').show();
		} else {
			$('#binding_options').show();
			$('#binder').show();
			$('#binder').val('0');
			$('#no_binding_options').hide();
		}
	});

	$('#binder').on('change',function(){
		var binder_type = $('#binder').val();
		$('#tabbed').show();
		if(binder_type == 'coil') {
			$('#tabbed').hide();
		}
	});

	$('#cover_type').on('change', function() {
		var cover_type = $('#cover_type').val();
		if(cover_type == "standard") {
			$('#cover_design').prop('disabled',true);
			$('#cover_design').selectedIndex = 0;
		} else {
			$('#cover_design').prop('disabled',false);
		}
	});

	$('#divider_type').on('change',function(){
		var divider_type = $('divider_type').val();
		if(divider_type == 'custom') {
			$('#divider_design').enable();
			$('#divider_design').selectedIndex = 0;
		} else {
			$('#divider_design').prop('disabled',true);
			$('#divider_design').selectedIndex = 0;
		}
	});

	$('#rnc').on('click',function() {
		if($('#rnc').checked) {
			$('#use_fillers').enable();
		}
	});
	$('#rc').on('click',function() {
		if($('#rc').checked) {
			$('#use_fillers').prop('disabled',true);
		}
	});
});

function calcCost() {
	var special = false;
	// Get the selected matrix range...
	// per book price base = qty * price_ea
	var books = jQuery('#books').val(); // price each
	var recipes = jQuery('#recipes').val(); // number of recipes
	
	var books_min = 100;
	var cols = new Array();
	cols['1-150'] = new Array('4.40','2.85','2.75','2.60','2.50','2.45','2.40','2.35','2.30','2.20','2.10','2.05','2.00','1.95');
	cols['151-200'] = new Array('5.15','3.05','2.90','2.75','2.65','2.60','2.55','2.50','2.45','2.35','2.20','2.15','2.10','2.05');
	cols['201-250'] = new Array('5.65','3.35','3.20','2.95','2.80','2.75','2.70','2.65','2.60','2.50','2.35','2.30','2.20','2.15');
	cols['251-300'] = new Array('6.10','3.65','3.35','3.15','3.05','3.00','2.95','2.85','2.80','2.75','2.60','2.50','2.45','2.35');
	cols['301-350'] = new Array('6.50','3.90','3.55','3.45','3.20','3.15','3.10','3.00','2.95','2.90','2.75','2.60','2.55','2.50');
	cols['351-400'] = new Array('7.05','4.10','3.80','3.65','3.40','3.35','3.30','3.20','3.10','3.00','2.85','2.75','2.65','2.60');
	cols['401-450'] = new Array('','4.50','4.20','3.80','3.60','3.55','3.50','3.40','3.30','3.20','3.05','2.95','2.85','2.80');
	cols['451-500'] = new Array('','4.95','4.40','4.15','3.95','3.85','3.80','3.60','3.50','3.40','3.30','3.20','3.15','3.00');
	cols['501-550'] = new Array('','5.45','4.65','4.50','4.25','4.15','4.05','3.95','3.85','3.80','3.75','3.50','3.40','3.35');
	cols['551-600'] = new Array('','5.85','4.95','4.80','4.60','4.40','4.30','4.20','4.10','4.00','3.90','3.60','3.55','3.50');
	cols['601-650'] = new Array('','6.30','5.50','5.05','4.85','4.75','4.55','4.50','4.40','4.30','4.20','3.95','3.85','3.75');
	cols['651-700'] = new Array('','','5.85','5.55','5.00','4.85','4.75','4.70','4.60','4.50','4.40','4.25','4.15','4.10');
	cols['701-750'] = new Array('','','','5.75','5.40','5.25','5.10','5.00','4.95','4.85','4.75','4.50','4.40','4.30');
	cols['751-800'] = new Array('','','','6.05','5.80','5.60','5.35','5.25','5.15','5.05','4.90','4.70','4.50','4.35');
	cols['801-850'] = new Array('','','','6.55','6.10','5.90','5.70','5.45','5.35','5.30','5.25','5.15','5.10','5.05');
	cols['851-900'] = new Array('','','','7.00','6.60','6.10','5.85','5.70','5.60','5.55','5.50','5.45','5.40','5.35');
	cols['901-950'] = new Array('','','','7.30','6.85','6.25','6.10','5.90','5.85','5.80','5.75','5.70','5.65','5.60');
	cols['951-1000'] = new Array('','','','7.75','7.25','6.50','6.35','6.15','6.10','6.05','6.00','5.95','5.90','5.85');
	
	var cuts = new Array(199,299,399,499,599,699,799,899,999,1000,1500,2000,2500,3000);
	
	var rets = new Array(8.5,9.5,9.75,10,10.25,10.5,10.75,11.25,11.75,12.25,12.75,13.25,13.75,14.25,14.75,15.25,15.75,16.25);
	
	var retail = '';
	if(books < books_min) {
		alert('Please enter a quantity greater than 100 books.');
	} else if(!recipes || recipes == 0) {
		alert('Please enter the number of recipes for this cookbook.');
	} else if(recipes > 1000) {
		alert('This quote is for less than 1000 recipes.\r\nFor more books, please contact Cookbook Publishers');
	} else {
		var row = new Array();
		var i=0;
		for(var k in cols) {
			var range = k.split('-');
			var bottom = parseInt(range[0]);
			var top = parseInt(range[1]);
			if(recipes >= bottom) {
				if(recipes <= top) {
					row = cols[k];
					retail = rets[i];
				}
			}
			i++;
		}
		var ea = '';
		var root = '';
		for(var i=0;i<cuts.length;i++) {
			var max = cuts[i];
			if(max < 1000) {
				var min = cuts[i] - 99;
			} else {
					var min = cuts[i];
					var max = cuts[i] + 499;
			}
			
			if(books >= min) {
				if(books <= max) {
					root = row[i];
				}
			}
		}
		
		if(isNaN(root)) {
			special = true;
		}
		if(!root) {
			special = true;
		}
		
		// COVER VARIABLES
		var cover = 0;
		var cover_style = jQuery('#cover_style').val();
			// hard cover modifiers
			if(cover_style == 'hard') {
				if(books < 200) {
					cover = 2.20;
				}
				if(books >= 200 && books < 500) {
					cover = 1.45;
				}
				if(books >= 500 && books < 1000) {
					cover = 1.20;
				}
				if(books >= 1000) {
					cover = 1.15;
				}
			}
			
			// 3-ring modifiers
			if(cover_style == '3ring') {
				if(books <= 199) {
					cover = 3.75;
				}
				if(books >= 200 && books < 500) {
					cover = 2.70;
				}
				if(books >= 500 && books <1000) {
					cover = 2.35;
				}
				if(books >= 1000) {
					cover = 2.30;
				}
			}
		
		ea = parseFloat(root) + parseFloat(cover);
		// Types: standard and custom
		var cover_type = jQuery('#cover_type').val();
		// Designs: black ink, one color ink, multi-color ink
		var cover_design = jQuery('#cover_design').val();
		// calculate the custom cover modifiers
		if(cover_type == 'custom') {
			if(cover_design == 'one') {
				ea = parseFloat(ea)+.10; 
			}
			if(cover_design == 'multi') {
				ea = parseFloat(ea)+.20;
			}
		}
		var opt = 0;
		
		// binder variables
		var binding = jQuery('#binder').val();
			if(binding == 'impr_comb') {
				if(books < 300) {
					opt = parseFloat(opt)+.60;
				}
				if(books >= 300 && books < 500) {
					opt = parseFloat(opt)+.45;
				}
				if(books >= 500) {
					opt = parseFloat(opt)+.30;
				}
			}
			if(binding == 'coil') {
				opt = parseFloat(opt)+.00;
			}

		// divider variables
		var divider_type= jQuery('#divider_type').val();
		if(divider_type == 'tabbed') {
			opt = parseFloat(opt)+.40;
		}
		if(divider_type == 'custom') {
			opt = parseFloat(opt)+(.03*7);
		}
		
		var divider_design = jQuery('#divider_design').val();
		switch(divider_design) {
			case 'one':
				opt = parseFloat(opt) + parseFloat(parseInt(7)*.05);
				break;
			case 'multi':
				opt = parseFloat(opt) + parseFloat(parseInt(7)*.15);
				break;
		}
		
		// personal pages
		opt = parseFloat(opt) + parseFloat(jQuery('#personal_pages').val()*.04);
		opt = parseFloat(opt) + parseFloat(jQuery('#personal_photos').val()*.03);
		
		// recipe page options
		if(jQuery('#rnc').prop('checked')) {
			opt = parseFloat(opt)+.20;
		}
		if(jQuery('#use_icons').prop('checked')) {
			opt = parseFloat(opt)+.05;
		}
		if(jQuery('#allow_notes').prop('checked')) {
			opt = parseFloat(opt)+.25;
		}
		if(jQuery('#use_fillers').prop('checked')) {
			opt = parseFloat(opt)+.15;
		}
		if(jQuery('#use_subcategories').prop('checked')) {
			opt = parseFloat(opt)+.05;
		}
		
		var base = parseFloat(ea) + parseFloat(opt);
		var cost = parseFloat(base)*books;
		
		var msrp = retail;
		
		if(opt){
			msrp = parseInt(msrp) + (parseFloat(opt)*2);
		}
		if(cover){
			msrp = parseInt(msrp) + (parseFloat(cover)*1.5);
		}
		var profit = (msrp*books)-cost;
		if(special == false) {
			jQuery('#cost').val('$'+cost.toFixed(2));
			jQuery('#each').val('$'+base.toFixed(2));
			jQuery('#msrp').val('$'+msrp.toFixed(2));
			jQuery('#profit').val('$'+profit.toFixed(2));
		} else {
			jQuery('#cost').val('');
			jQuery('#each').val('');
			jQuery('#msrp').val('');
			jQuery('#profit').val('');
			alert('Please contact Cookbook Publishers, Inc. for a special quote');
		}
	}
}

jQuery(document).ready(function($) {
	if($('#price_matrix')) {
		var parent = $('#price_matrix');
		parent.find('td').each(function(){
			$(this).mouseover(function(){
				if(!$(this).hasClass('disabled')) {
					var self = this;
					var pos = $(self).index();
					$(this).closest('tr').find('td').each(function(){
						if(self != this) {
							$(this).attr('style','background: #B5E1E7');
						}
					});
					$(this).closest('tr').find('th').each(function(){
						if(self != this) {
							$(this).attr('style','background: #FFF2BE');
						}
					});
					parent.find('tr').each(function(){
						$(this).find('td').each(function() {
							if($(this).index() == pos) {
								if($(this).html() != $(self).html()) {
									$(this).attr('style','background: #B5E1E7');
								}
							}
						});
						$(this).find('th').each(function() {
							if($(this).index() == pos) {
								$(this).attr('style','background: #FFF2BE');
							}
						});
					});
				}
			});
			$(this).mouseout(function(){
				parent.find('td').each(function(){
					$(this).attr('style','');
				});
				parent.find('th').each(function(){
					$(this).attr('style','');
				});
			});
		});
	}
});