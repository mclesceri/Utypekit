/*
 * 
 * Report Filter Widget
 * by William Logan - Oct 2012
 * 
 * Report filter for the U-Type-It Administration Utility's Account Report.
 * This filter is designed to allow the filtering of the data set returned 
 * by the Account Report feature of the U-Type-It Administration Utility.
 * It uses three main criteria:
 * Order report by :: orders the list by one of the given result columns
 * Filter report by :: filters the results to show only requested records
 * Limit results to :: limits the number of fields shown for each item
 * 
 * Order by values;
 * title,date_created,order_number,added_by_type,status
 * 
 * Filter by values;
 * title,date_created,order_number,added_by_type,status,date of last recipe entry
 * recipe count, contact name, city, state
 * 
 * Limit results to;
 * all fields selectable for exclusion/inclusion
 * 
 */
function _do(from) {
	if(from == 'order') {
		$('order_set').toggle();
		$('limit_set').hide();
		$('filter_set').hide();
	} else if(from == 'limit') {
		$('order_set').hide();
		$('limit_set').toggle();
		$('filter_set').hide();
	} else {
		$('order_set').hide();
		$('limit_set').hide();
		$('filter_set').toggle();
	}
}

function _filter(form) {
	var url = window.includes + "process_report.php";
	new Ajax.Request(url,{
		method: 'post',
		parameters: Form.serialize(form),
		onFailure: function(transport) {
			alert('Failure: ' + transport.responseStatus);
			$('breadcrumb').select('img')[0].remove();
		},
		onLoading: function() {
			$('breadcrumb').insert('<img src="' + window.images + 'ajax-loader.gif" style="float: left;">');
		},
		onSuccess: function(transport) {
			$('report').update(transport.responseText);
		}
	});
}