	// On the recipe page, change icon from side bar
	function setRecipeIcon(this) {
		var src = this.attr("src");
		alert(src);
	}
	
	function getRecipeSubcategory(value,order_id, subcategory ) {
		if(value == 0) {
			$("#order_subcategory").val(0);
		} else {
			$.ajax({
				type:'post',
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				url:site_url+'/getSubcategory',
				data:{'type': value, 'order_id' : order_id},
				success:function(resp) {
					var obj = $.parseJSON(resp);
					if(obj.status == 1) {
						var optionData  = obj.data;
						var count = optionData.length;

						var html = '<option value="0">Choose One...</option>';
						for (var i=0; i < count; i++){	
							if(subcategory != '') {
								if(subcategory == i)
									html += '<option selected value="'+i+'">'+optionData[i]+'</option>';
								else 
									html += '<option value="'+i+'">'+optionData[i]+'</option>';
							} else {
								html += '<option value="'+i+'">'+optionData[i]+'</option>';
							}
							
						}
						$("#order_subcategory").html('');
						$("#order_subcategory").html(html);
						
					}
					$("#loader_div").hide();
				},
				beforeSend: function() {
					$("#loader_div").show();
				}
			});
		}
		return false;
	}
//Custom Success and Error Mesage throughout the website.
	function customSuccessMessage(msg) {
		//remove all message
			$(".alert-block").remove();
			$(".message-alert").html('');
		
		var successMessage = "<div class='alert alert-success alert-block'>";
		successMessage += "<button type='button' class='close' data-dismiss='alert'>×</button>"; 		
		successMessage += " <strong>"+msg+"</strong>";		
		successMessage += "</div>";		
		$(".message-alert").html(successMessage);
		
		setTimeout(function() { 
			$(".message-alert").html('');
		}, 10000);
	}

	function customErrorMessage(msg) {
		//remove all message
			$(".alert-block").remove();
			$(".message-alert").html('');
		
		var errorMessage = "<div class='alert alert-danger alert-block'>";
		errorMessage += "<button type='button' class='close' data-dismiss='alert'>×</button>"; 		
		errorMessage += " <strong>"+msg+"</strong>";		
		errorMessage += "</div>";	
		$(".message-alert").html(errorMessage);
		
		setTimeout(function() { 
			$(".message-alert").html('');
		}, 10000);
	}
	
//Delete for records throughout the website.
	function deleteRecord(id, model, obj) {
		if(typeof id != 'undefined' && typeof model != 'undefined'){
			$.ajax({
				type:'post',
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				url:site_url+'/deleteRecord',
				data:{'id': id, 'tableName': model},
				success:function(resp) {
					$('html, body').animate({scrollTop:0}, 'slow');
					
					var response = $.parseJSON(resp);
					if(response.status == 1) {
						$(obj).parent().parent().remove();	
						customSuccessMessage(response.message);	
					}
					else {
						customErrorMessage(response.message);	
					}	
				},
				beforeSend: function() {
					//nothing
				}
			});
		}
	}	

function createTableRows(tableClass, rowID) {
	getRow  = 0;
	$("."+tableClass).each(function () {
		getRow++;
		var tds = '<tr id="'+rowID+'_'+getRow+'">';
		jQuery.each($('tr:last td', this), function () {
			tds += '<td>' + $(this).html() + '</td>';
		});
		tds += '</tr>';
		if ($('tbody', this).length > 0) {
			$('tbody', this).append(tds);
		} else {
			$(this).append(tds);
		}
	});
}

function createRecipePartTableRows(tableClass, rowID, obj) {
	getRow  = 0;
	var recipe_part 	= 	$(obj).closest('.recipe-part').attr('id');
	$("#"+recipe_part+' .'+tableClass).each(function () {
		getRow++;
		var tds = '<tr id="'+rowID+'_'+getRow+'">';
		jQuery.each($('tr:first td', this), function () {
			tds += '<td>' + $(this).html() + '</td>';
		});
		tds += '</tr>';
		if ($('tbody', this).length > 0) {
			$('tbody', this).append(tds);
		} else {
			$(this).append(tds);
		}
	});
}

function createRecipePart(tableClass, rowID) {
	// get the last DIV which ID starts with ^= "recipe-part"
	var $div = $('div[id^="recipe-part"]:last');

	// Read the Number from that DIV's ID (i.e: 3 from "recipe-part3")
	// And increment that number by 1
	var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
	var row_num = parseInt( $div.prop("id").match(/\d+/g), 10 );

	// Clone it and assign the new ID (i.e: from num 4 to ID "klon4")
	var $klon 	= $div.clone().prop('id', 'recipe-part'+num );
	$klon.find(".section_type").attr("name","section-type-"+num);
	$klon.find(".part_cls").attr("name","part_title["+row_num+"]");
	$klon.find(".recipe_ingredients").attr("name","recipe_ingredients["+row_num+"][]");
	$klon.find(".recipe_method").attr("name","recipe_method["+row_num+"][]");

	// Finally insert $klon wherever you want
	$div.after( $klon );
}
//Show the selected Image on the page
	function readURL(input,rowID) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			
			reader.onload = function (e) {
				$('#'+rowID+' img').attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

//get Filler Start	
	
	function getFillerSet(value, selected_value){
		$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:site_url+'/getFiller',
					data:{'type': value},
					success:function(resp) {
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							var optionData  = obj.data;
							var count = optionData.length;

							var html = '<option value="0">Choose One...</option>';
							for (var i=0; i < count; i++){
								if(typeof selected_value != 'undefined' && selected_value == optionData[i].id){	
									html += '<option value="'+optionData[i].id+'" selected>'+optionData[i].name+'</option>';
								} else {
									html += '<option value="'+optionData[i].id+'">'+optionData[i].name+'</option>';
								}
							}
							$("#filler_set").html('');
							$("#filler_set").html(html);
							
						}
						$("#loader_div").hide();
					},
					beforeSend: function() {
						$("#loader_div").show();
					}
		});
	}
	
	
//get Filler End		
	
$(document).ready(function() {
	//Remove all Message
	setTimeout(function() { 
			$(".alert-block").remove();
		}, 5000);
		
	//stay tabs (order)	
		var orderTab = $.cookie('orderTab');
		if(typeof orderTab != 'undefined'){
			$("."+orderTab).addClass('active');
			var childID = $("."+orderTab).attr('href');
			$(childID).addClass('active');
		} else {
			$(".tab_1").addClass('active');
			var childID = $(".tab_1").attr('href');
			$(childID).addClass('active');
		}
		$(document).on('click', '.m-tabs__link', function(){
			var current_tab = $(this).attr('data-tab');
			$.cookie('orderTab', current_tab, {path: '/'});
		});
		
		$(document).on('click', '.removeOrderCookiee', function(){
			$.removeCookie('orderTab', {path: '/'});
		});
		
	//help info start	
		$(document).on('click', '.help-info', function(){
			var alias = $(this).attr('data-alias');
			$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:site_url+'/getHelp',
					data:{'alias': alias},
					success:function(resp) {
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							var data  = obj.data;
							$("#m_modal_help_info").modal('show');
							$("#helpModelTitle").text(obj.data.title);
							$("#helpModelDesc").html(obj.data.description);
						}
						$("#loader_div").hide();
					},
					beforeSend: function() {
						$("#loader_div").show();
					}
				});
		});	
	
	//order form [Desgin Option] start
		if($("#recipes_continued").is(':checked')){
			$("#use_fillers").attr('disabled', true);
			$("#use_fillers").prop('checked', false);
				
			$("#filler_type").attr('disabled', true);
			$("#filler_type").val(0);
				
			$("#filler_set").attr('disabled', true);
			$("#filler_set").val(0);
		}
		
		if($("#use_fillers").is(':checked')){
			$("#filler_type").attr('disabled', false);
			$("#filler_set").attr('disabled', false);
		} 
		
		var filler_type = $("#filler_type").val();
		if(filler_type != 0){
			var selected_value = $("#hidden_filler_set").val();	
			getFillerSet(filler_type, selected_value);
		}	
	
		$(document).on('change', '#recipes_continued', function(){
			if ($(this).is(':checked')) {
				$("#use_fillers").attr('disabled', true);
				$("#use_fillers").prop('checked', false);
				
				$("#filler_type").attr('disabled', true);
				$("#filler_type").val(0);
				
				$("#filler_set").attr('disabled', true);
				$("#filler_set").val(0);
				
			} else {
				$("#use_fillers").attr('disabled', false);
			}
		});
		
		$(document).on('change', '#use_fillers', function(){
			if ($(this).is(':checked')) {
				$("#filler_type").attr('disabled', false);
				$("#filler_set").attr('disabled', false);
			} else {
				$("#filler_type").attr('disabled', true);
				$("#filler_type").val(0);
				
				$("#filler_set").attr('disabled', true);
				$("#filler_set").val(0);
			}
		});	
		
		$(document).on('change', '#filler_type', function(){
			var value = $(this).val();
			if(value == 0){
				$("#filler_set").val(0);
			} else {
				getFillerSet(value, '');
			}	
		});
	//order form [Desgin Option] end
		
		
	
	$(document).on('click', '.remove_recipe_part', function () {
		var res = confirm("Are you sure, you want to delete this section?");
		if(res){
			var recipe_part 	= 	$(this).closest('.recipe-part').attr('id');
			var section_num		= 	parseInt( recipe_part.match(/\d+/g), 10 );
			if(section_num > 1) {
				$("#"+recipe_part).remove();
			} else {
				alert("You cannot delete this row.");
				return false;
			}
			
		} else {
			return false;
		}
	});
	
	$(document).on('change', '.section_type', function () {
		var recipe_part 	= 	$(this).closest('.recipe-part').attr('id');
		if ($(this).is(':checked')) {
			if($(this).attr('value') == "ingredient") {
				$("#"+recipe_part+' .m_table_recipe_ingredient_part').show();
				$("#"+recipe_part+' .m_table_recipe_method_part').hide();
			} 
			if($(this).attr('value') == "method") {
				$("#"+recipe_part+' .m_table_recipe_ingredient_part').hide();
				$("#"+recipe_part+' .m_table_recipe_method_part').show();
			}
		} else {
			
		}
	});

	$(document).on('change', '.image_upload_file_new', function () {
		var val 			= 	$(this).val();
		switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
			case 'gif': case 'jpg': case 'jpeg' : case 'png':
				var rowID 	= 	$(this).closest('tr').attr("id"); // Get the Recipe Image row reference
				readURL(this, rowID);
				break;
			default:
				$(this).val('');
				alert("Please select .gif, .jpg, .jpeg, .png file only.");
				break;
		}
	});

	$(document).on('click', '#table-draggable2 input', function () {
		$(this).focus(); 
	});
	
	$(document).on('click', '#table-draggable2 select', function () {
		$(this).focus(); 
	});
	
	$(document).on('click', '#table-draggable2 textarea', function () {
		$(this).focus(); 
	});

	
	$(document).on('click', '.preview_submit', function () {
		if($(this).hasClass('cover_section')) {
			$('#form_section').val(1);
		} else if($(this).hasClass('divider_section')) {
			$('#form_section').val(2);
		} else if($(this).hasClass('recipe_section')) {
			$('#form_section').val(3);
		}
		$('#form_preview').val(1);
		$("#order_form").attr('target', '_blank');
		$('#order_form').submit();
	});
	
	// Drag and drop on the recipes rows
/* 	var $tabs = $('.table-draggable2, .table-draggable3')
	$("tbody.connectedSortable")
	.sortable({
		connectWith: ".connectedSortable",
		items: "> tr:not(:first)",
		appendTo: $tabs,
		helper: "clone",
		zIndex: 999990
	})
	.disableSelection();

	var $tab_items = $(".nav-tabs > li", $tabs).droppable({
		accept: ".connectedSortable tr",
		hoverClass: "ui-state-hover",

		drop: function(event, ui) {
		  return false;
		}
	}); */
  
  
	//order form [Recipe Option] start
		$(document).on('click', '.addSubCategory', function(){
			var categoryId = $(this).attr('data-id-cat');
			var count = $("#"+categoryId).attr('data-count');
			var html = '';
			if(count > 0) {
				html += '<tr>';
				html += '<td class="col-lg-10 col-xl-10">ABC</td>';
				html += '<td class="col-lg-2 col-xl-2">';
				html += '<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill deleteSubCat" title="Delete" data-cat-id="'+categoryId+'">';
				html += '<i class="la la-trash"></i>';
				html += '</a>';
				html += '</td>';
				
				var newCount = parseInt(count) + 1;	
				$("#"+categoryId).attr('data-count', newCount);
				
				var table_id = 'table_'+categoryId;
				$("#"+table_id).append(html);
				
			} else {
				var table_id = 'table_'+categoryId;	
				html += '<table class="table table-striped- table-bordered table-hover table-checkable subcattable" id="'+table_id+'">';	
				html += '<tbody class="connectedSortable">';
				html += '<tr>';
				html += '<td class="col-lg-10 col-xl-10"><input class="form-control" placeholder="" name="subcategory_title[]" type="text" value=""></td>';
				html += '<td class="col-lg-2 col-xl-2">';
				html += '<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill deleteSubCat" title="Delete" data-cat-id="'+categoryId+'">';
				html += '<i class="la la-trash"></i>';
				html += '</a>';
				html += '</td>';
				html += '</tr>';
				html += '</tbody>';
				html += '</table>';
				
				var newCount = parseInt(count) + 1;	
				$("#"+categoryId).attr('data-count', newCount);
				
				$("#"+categoryId).append(html);
			}												
		});
		
		$(document).on('click', '.deleteSubCat', function(){
			var categoryId = $(this).attr('data-cat-id');
			var count = $("#"+categoryId).attr('data-count');
			var newCount = parseInt(count) - 1;
			$("#"+categoryId).attr('data-count', newCount);
			
			if(newCount == 0)
				{
					var table_id = 'table_'+categoryId;	
					$("#"+table_id).remove();	
				}
			else
				{
					$(this).parent().parent().remove();
				}	
		});	
	
		$("#insert-more-category").click(function () {
			categoryRows  = 0;
			$(".table-draggable3").each(function () {
				categoryRows++;
				var tds = '<tr id="recipe_category_'+categoryRows+'">';
				jQuery.each($('tr:last td', this), function () {
					if($(this).attr('class') == "category-title") {
						tds += '<td><input class="form-control" placeholder="" name="category_title[]" type="text" value=""></td>';
					} else {
						tds += '<td>' + $(this).html() + '</td>';
					}
					
				});
				tds += '</tr>';
				if ($('tbody', this).length > 0) {
					$('tbody', this).append(tds);
				} else {
					$(this).append(tds);
				}
			});
		});

		// Add more rows to add the recipes Images
		$("#insert-more").click(function () {
			$(".table-draggable2").each(function () {
				recipesRows++;
				var tds = '<tr id="recipe_'+recipesRows+'">';
				jQuery.each($('tr:last td', this), function () {
					tds += '<td>' + $(this).html() + '</td>';
				});
				tds += '</tr>';
				if ($('tbody', this).length > 0) {
					$('tbody', this).append(tds);
					$("#recipe_"+recipesRows).find("img").attr('src',defaultRecipeImg);
				} else {
					$(this).append(tds);
					$("#recipe_"+recipesRows).find("img").attr('src',defaultRecipeImg);
				}
			});
		});
		
		// Remove the created recipe row
		$(document).on('click', '.deleteRecipeImg', function (e) {
			e.preventDefault();
			if(($(this).closest('tr').attr("id")	== "recipe_1") || ($(this).closest('tr').attr("id")	== "recipe_category_0") || ($(this).closest('tr').attr("id")	== "contributor_0") || ($(this).closest('tr').attr("id")	== "recipe_part_0")) {
				alert("You cannot remove this row.");
				return false;
			} else {
				var deleteConfirmation 	=	 confirm("Are you sure, you want to delete this row?");
				if(deleteConfirmation) {
					$(this).closest('tr').remove();
				}
				return false;
			}	
		});
	//order form [Recipe Option] end
	
	// number vaidation
	$(document).on('keypress', '.number', function(e){
		var charCode;
		if (e.keyCode > 0) {
			charCode = e.which || e.keyCode;
		}
		else if (typeof (e.charCode) != "undefined") {
			charCode = e.which || e.keyCode;
		}
		if (charCode == 46)
			return true
		if (charCode > 31 && (charCode < 48 || charCode > 57))
			return false;
		return true;
	});
	
	$(document).on('change', '#order_category', function() {
			var value 		= 	$(this).val();
			var order_id 	= 	$(".recipe_order_id").val();
			getRecipeSubcategory(value,order_id, '')
			return false;
		});
});