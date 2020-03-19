
var _show = function(message) {
	/// If the message is already shown...
	var params = {action: 'retrieve',file: message};
	if($('message_block').visible()) {
		if(window.message == message) {
			$('message_block').hide();
		} else {
			_send(params);
		}
	} else {
		_send(params);
	}
}

var _send = function(params) {
	var url = window.includes + 'ContactMessages.php';
	new Ajax.Request(url,{
		method: 'post',
		parameters: params,
		onFailure: function(transport) {
			alert(transport.responseStatus);
		},
		onSuccess: function(transport) {
			///alert(transport.responseText);
			window.message = params.file;
			tinyMCE.activeEditor.setContent(transport.responseText, {format : 'raw'});
			$('message_block').show();
		}
	});
}

var _save = function() {
	tinymce.activeEditor.save();
	var pagetext = encodeURIComponent($('message_text').value);
	var params = {action: 'store',file: window.message,content: pagetext};
	_send(params);
}

var message = '';

document.observe('dom:loaded',function(){
	tinymce.init({
		selector: "textarea#message_text",
		plugins: [
			"advlist autolink lists image charmap print preview",
			"searchreplace visualblocks code fullscreen",
			"insertdatetime table contextmenu paste jbimages save"
		],
		toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image jbimages | save",
		relative_urls: false,
		resize: true,
		save_enablewhendirty: true,
		save_onsavecallback: function() {_save();}
	});
	$('message_block').hide();
});