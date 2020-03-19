<?php
session_start();

require_once('../src/globals.php');
	
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="media/css/reset.css" />
<link rel="stylesheet" type="text/css" href="media/css/reports.css" />
<style type="text/css">
body
{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
}
table
{
	width: 480px;
	margin: 10px;
	border: 1px #333 solid;
}

td,th
{
	padding: 4px;
}
th
{
	text-align: center;
	background: #CCC;
	border-bottom: 1px #000 solid;
}
td.label
{
	font-size: .9em;
	color: #666666;
	text-align: right;
}
td.input
{
	text-align: left;
}
td.centered
{
	text-align: center;
}
</style>
<script type="text/javascript" src="<?=A_JS?>prototype.js"></script>
<script type="text/javascript" src="<?=A_JS?>scriptaculous.js"></script>
<script type="text/javascript">
var includes = '<?=UTI_URL?>src/includes/';
var images = '<?=UTI_URL?>/media/images/';
var sendRequest = function() {
	var filter = null;
	var vals = Form.serialize('outputReport',true);
	if(vals.use_filter == 'yes') {
		filter = window.opener.document.getElementById('reportFilter');
		var raw = Form.serialize(filter,true);
		var data = {};
		data.type = vals.type;
		data.response = vals.response;
		data.order_by = raw.orderby;
		if(vals.use_limit == 'yes') {
			data.limit = raw.limit;
			data.lpage = 0;
		}
		data.search_by = raw.searchby;
		data.search_mod = raw.searchmod;
		data.search_for_1 = raw.searchfor_1;
		data.search_for_2 = raw.searchfor_2;
		
		if(raw.ORDER == 'true') {
			data.ORDER = 'true';
			if(raw.date_added) {
				data.date_added = raw.date_added;
			}
			if(raw.added_by_type) {
				data.added_by_type = raw.added_by_type;
			}
			if(raw.order_number) {
				data.order_number = raw.order_number;
			}
			if(raw.title) {
				data.title = raw.title;
			}
			if(raw.status) {
				data.status = raw.status;
			}
		}
		if(raw.CONTENT == 'true') {
			data.CONTENT = 'true';
			if(raw.last_recipe) {
				data.last_recipe = raw.last_recipe;
			}
			if(raw.recipe_count) {
				data.recipe_count = raw.recipe_count;
			}
		}
		if(raw.PEOPLE == 'true') {
			data.PEOPLE = 'true';
			if(raw.first_name) {
				data.first_name = raw.first_name;
			}
			if(raw.last_name) {
				data.last_name = raw.last_name;
			}
			if(raw.phone) {
				data.phone = raw.phone;
			}
			if(raw.email) {
				data.email = raw.email;
			}
			if(raw.login) {
				data.login = raw.login;
			}
			if(raw.password) {
				data.password = raw.password;
			}
			if(raw.address1) {
				data.address1 = raw.address1;
			}
			if(raw.address2) {
				data.address2 = raw.address2;
			}
			if(raw.city) {
				data.city = raw.city;
			}
			if(raw.state) {
				data.state = raw.state;
			}
			if(raw.zip) {
				data.zip = raw.zip;
			}
			if(raw.meta) {
				data.meta = raw.meta;
			}
		}
		if(raw.ORGANIZATION == 'true') {
			data.ORGANIZATION = 'true';
			if(raw.organization_name) {
				data.organization_name = raw.organization_name;
			}
			if(raw.organization_type) {
				data.organization_type = raw.organization_type;
			}
		}
	} else {
		var data = {};
		data.type = vals.type;
		data.response = vals.response;
	}
	var url = window.includes + 'AccountReport.php';
	new Ajax.Request(url,{
		method: 'post',
		parameters: data,
		onLoading: function() {
			if(data.response == 'email') {
				
				var table = Builder.node('table');
				
				var tr = Builder.node('tr');
				var th = Builder.node('th','Your report is being prepared. You will receive an email when it\'s done.');
				tr.appendChild(th);
				table.appendChild(tr);
				
				tr = Builder.node('tr',[Builder.node('td',[Builder.node('a',{href: '#',onclick: 'window.close()'},'Close this window')])]);
				table.appendChild(tr);
				$('body').update(table);
				return;
			} else {
				var table = Builder.node('table');
				var tr = Builder.node('tr');
				var th = Builder.node('th','Please wait while your report is being generated. This could take a while so you might want to brew a cup of tea.');
				tr.appendChild(th);
				table.appendChild(tr);
				
				tr = Builder.node('tr');
				var td = Builder.node('td');
				var img = Builder.node('img',{src: window.images+'loading.gif',style: 'height: 25px; float: left'});
				td.appendChild(img);
				tr.appendChild(td);
				table.appendChild(tr);
				
				$('body').update(table);
			}
		},
		onFailure: function(transport) {
			alert(transport.responseStatus);
		},
		onSuccess: function(transport) {
			//alert(transport.responseText);
			window.resizeTo(750,900)
			$('body').update(transport.responseText);
			$('feedback').update();
		}
	});
}
</script>
</head>

<body id="body">
<form id="outputReport" name="outputReport" onsubmit="sendRequest(this); return false;">
<input type="hidden" name="action" value="send" />
<table>
	<tr>
		<th colspan="2">Choose the output options below. Depending on the filters chosen, the report can take up to five minutes to complete.</th>
	</tr>
	<tr>
		<td class="label">Output to...</td>
		<td class="input"><input type="radio" value="html" name="type">Print <input type="radio" value="csv" name="type">CSV(Excel)</td>
	</tr>
	<tr>
		<td class="label">Using the current filter...</td>
		<td class="input"><input type="radio" value="yes" name="use_filter">Yes <input type="radio" value="no" name="use_filter">No</td>
	</tr>
	<tr>
		<td class="label">Limit the results...</td>
		<td class="input"><input type="radio" value="yes" name="use_limit">Yes <input type="radio" value="no" name="use_limit">No</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" class="centered">Wait for the results <input type="radio" value="wait" name="response"> ...or... Email me when it's done <input type="radio" value="email" name="response"></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td class="label"><button type="submit">Output Report</button></td>
	</tr>
</table>
</form>
</body>