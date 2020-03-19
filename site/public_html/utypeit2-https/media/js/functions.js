function sendForm(form,responder,required,result,params) {
	if(!params) {
		var params = {};
	}
	var err = {status: false, message: ''};
	if(required) {
		err = formVerify(required);
	}
	if(err.status == false) {
		var url = window.services + responder + '?result=' + result;
		var resp = new Ajax.Request(url,{
			method: 'post',
			parameters: Form.serialize(form),
			asynchronous: true,
			requestHeaders: {Accept: 'application/json'},
			onFailure: function(transport) {
				alert("Failure:" + transport.statusText);
			},
			onSuccess: function(transport) {
				// response always returns a string with status , message
				// if successful, the message will always be the id of the item added/updated
				// alert(transport.responseText);
				var parsed = transport.responseText.evalJSON(true);
				if(parsed.status == 'false') {
					$('feedback').update(parsed.message);
				} else {
					//$('feedback').update(parsed.message);
					function clearFeedback() {
						$('feedback').update();
					}
					clearFeedback.delay(6.0);
					if(parsed.id) {
						params.id = parsed.id;
					}
					if(parsed.result) {
						result = parsed.result;
					}
					if(parsed.action) {
						params.action = parsed.action;
					}
					if(parsed.message) {
						params.message = parsed.message;
					}
					if(parsed.mode) {
						params.mode = parsed.mode;
					}
					setContent(result,params);
				}
			}
		});
	} else {
		$('feedback').update(err.message);
	}

}

function formVerify(required) {
	var reqs = required.split(',');
	var err = {status: false, message: ''};
	for(var r=0;r<reqs.length;r++) {
		var elem = $( reqs[r] );
		var pdiv = reqs[r].split('_');
		var pretty = '';
		if(pdiv.length > 1) {
			for(var p=0;p<pdiv.length;p++) {
				var pretty = pretty + (pdiv[p].charAt(0).toUpperCase() + pdiv[p].slice(1)) + ' ';
			}
		} else {
			var pretty = pdiv[0].charAt(0).toUpperCase() + pdiv[0].slice(1);
		}
		if(elem) {
			if(elem.type == 'text' || elem.type == 'password') {
				if(!elem.present()) {
					err.status = true;
					err.message =  err.message + ' ' + pretty + ' is required. ';
					elem.addClassName('error');
				} else {
					if(elem.id == 'email') {
						var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
						if (!filter.test(elem.value)) {
							err.status = true; 
							err.message = err.message + 'Please provide a valid email address';
						}
					}
					if(elem.hasClassName('error')) {
						elem.removeClassName('error');
					}
				}
			} else if(elem.type == 'checkbox') {
				if(elem.checked != true) {
					err.status = true;
					err.message = err.message + ' ' + pretty + ' is required. ';
					//elem.value = 'required';
					elem.addClassName('error');
				} else {
					if(elem.hasClassName('error')) {
						elem.removeClassName('error');
					}
				}
			} else if(elem.type == 'radio') {
			
			} else if(elem.type == 'select-one') {
				if(elem.selectedIndex == 0) {
					err.status = true;
					err.message = err.message + ' ' + pretty + ' is required. ';
					elem.addClassName('error');
				} else {
					if(elem.hasClassName('error')) {
						elem.removeClassName('error');
					}
				}
			} else {
				if(elem.innerHTML == '') {
					elem.addClassName('error');
				} else {
					if(elem.hasClassName('error')) {
						elem.removeClassName('error');
					}
				}
			}
		}
	}
	return(err);
}

String.prototype.replaceAll = function(target, replacement) {
  return this.split(target).join(replacement);
};

function setTabIndex(form) {
	var i=1;
	Form.getElements(form).each( function(ea){ if(ea.readAttribute('type') != 'hidden'){ ea.setAttribute('tabindex',i); i = (i + 1); } });
}

function setContent(page,params) {
	// params accepts mode, id, action
	// mode: redirect, popup, start, limit, orderby
	// id : id of element as &id=1
	// action : page switch action e.g. recipe_edit / recipe_add, etc.
	page = page + '.php';
	var paramstr = '';
	if(params.id) {
		paramstr = paramstr + "&id=" + params.id;
	}
	if(params.action) {
		paramstr = paramstr + "&action=" + params.action;
	}
	if(params.start) {
		paramstr = paramstr + "&start=" + params.start;
	}
	if(params.limit) {
		paramstr = paramstr + "&limit=" + params.limit;
	}
	if(params.orderby) {
		paramstr = paramstr + "&orderby=" + params.orderby;
	}
	var width = 750;
	var height = 900;
	if(params.width) {
		width = params.width;
	}
	if(params.height) {
		height = params.height;
	}
	if(paramstr) {
		paramstr = '?' + paramstr.substr(1,paramstr.length);
	}
	if(params.mode == 'redirect') {
			window.location = page + paramstr;
	}
	if(params.mode == 'static') {
		$('feedback').update('<span style="color: red;">'+params.message+'</span>');
	}
	if(params.mode == 'popup') {
		$('feedback').update();
		window.open(page + paramstr,"_blank","scrollbars=1,status=0,toolbar=0,menubar=0,resizeable=0,location=0,height=" + height + ",width=" + width);
	}
	
}

function popWin(thewin) {
	window.open('pop_window.php?action='+thewin,"_blank","scrollbars=1,status=0,toolbar=0,menubar=0,resizeable=0,location=0,height=800,width=600");
}

function printWin(thewin,id) {
	var url = 'print_win.php?action='+thewin;
	if(id != '') {
		url = url+'&id='+id;
	}
	window.open(url,"_blank","scrollbars=1,status=0,toolbar=0,menubar=0,resizeable=1,location=0,height=900,width=810");
}

function makeOrder(orderid) {
	window.open("order_form.php?id="+orderid,"_blank","scrollbars=1,status=0,toolbar=0,menubar=0,resizeable=0,location=0,height=800,width=600");
}

function showSet(set) {
	var subs = $$('ul.sublist');
	for(var i=0; i<subs.length;i++) {
		if(i == set) {
			subs[i].show();
		} else{
			subs[i].hide();
		}
	};
}

function expandSublist(element) {
	var parent = element.up('ul',0);
	if(parent.getStyle('left') == '-140px') {
		parent.setStyle({left: '0'});
		element.setStyle({right: '15px'});
		element.innerHTML = '<';
	} else {
		parent.setStyle({left: '-140px'});
		element.setStyle({right: '-125px'});
		element.innerHTML = '>';
	}
}

function fancyNav() {
	var sideLinks = $('left_nav').select('li');
    var smallLinks = $('right_nav').select('div').each(function(ea){
        ea.observe('mouseover',function(){
            sideLinks[smallLinks.indexOf(ea)].setStyle({
                color: '#ffffff',
                textShadow: '#333333 -1px 1px 2px'
            });
        });
        ea.observe('mouseout',function(){
            sideLinks[smallLinks.indexOf(ea)].removeAttribute('style');
         })
    });
}