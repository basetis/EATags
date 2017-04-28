function submit_form($form, form_url, $div_id, form_text){
	$.ajax(
	{
		url: form_url,
		type:"POST",
		data: $form.serialize(),
		success: function(){
			alert_key($div_id, form_text);
		}
	});
}
function alert_key($div_id, form_text){
	slideDuration = 1000;
	$div_id.html(form_text);
	$div_id.fadeIn({ duration: slideDuration, queue: false });
	$div_id.addClass('alert alert-success');
	$div_id.css('display', 'none');
	$div_id.slideDown(slideDuration);
	setTimeout(function(){
		$div_id.fadeOut({ duration: slideDuration, queue: false });
		$div_id.slideUp(slideDuration);
		$div_id.html('');
	},7000);
}