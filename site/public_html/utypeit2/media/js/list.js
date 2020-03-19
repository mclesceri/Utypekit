function setList(list_page,list_orderby,list_name,action) {
	//alert(list_page+':'+list_start+':'+list_orderby);
	var list_limit = $( 'list_limit' )[$('list_limit').selectedIndex].text;
	var list_start = (list_limit * (parseInt(list_page)-1));
	var getstring = "start="+list_start+"&limit="+list_limit+"&orderby="+list_orderby+"&page="+list_page;
	if(action) {
		getstring = getstring+'&action='+action;
	}
	window.location = list_name+'.php?'+getstring;
}