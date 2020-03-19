function dumpReport(method,type) {
	var url = window.includes + 'process_report.php';
	new Ajax.Request(url,{
		method: 'post',
		parameters: {action: method, set: type},
		onFailure: function(transport) {
			alert('Failed: ' + transport.reponseStatus);
		},
		onSuccess: function(transport) {
  			alert('Success: ' + transport.responseText);
  			//var elemIF = document.createElement("iframe");
  			//elemIF.src = transport.responseText;
  			//elemIF.style.display = "none";
  			//document.body.appendChild(elemIF);
		}
	});
}

function _page(params) {
	var urlParams = {};
	(function () {
	    var match,
	        pl     = /\+/g,  // Regex for replacing addition symbol with a space
	        search = /([^&=]+)=?([^&]*)/g,
	        decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
	        query  = params//window.location.search.substring(1);
	
	    while (match = search.exec(query))
	       urlParams[decode(match[1])] = decode(match[2]);
	})();
	//"total=".$total."&start=".$list_start."&limit=".$limit."&orderby=".$orderby."&lpage=".$this_page."&action=account"
	
	var url = window.includes + "process_report.php";
	new Ajax.Request(url,{
		method: 'post',
		parameters: urlParams,
		onFailure: function(transport) {
			alert('Failure: ' + transport.responseStatus);
			$('breadcrumb').select('img')[0].remove();
		},
		onLoading: function() {
			$('breadcrumb').insert('<img src="' + window.images + 'ajax-loader.gif" style="float: left;">');
		},
		onSuccess: function(transport) {
			alert(transport.responseText);
		}
	});
}