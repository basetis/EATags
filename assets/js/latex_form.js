$del_latex      = $('.latex-form:first select');
$del_form       = $('.latex-form:first form');
del_form_url    = '/action_latex/send_data/del_form';
$del_alert_id   = $('#alert-del');
$key_button     = $('#send-key');
$key_input      = $('#latex-key');
$key_form       = $('.latex-key-form form');
key_form_url    = '/action_latex/send_data/key_form';
$key_alert_id   = $('#alert-key');
$inline_latex   = $('.latex-form:last select');
$inline_form    = $('.latex-form:last form');
inline_form_url = '/action_latex/send_data/inline_form';
$inline_alert_id   = $('#alert-inline');

$del_latex.change(function(){
	submit_form($del_form, del_form_url, $del_alert_id, data_sent);
});

$key_button.click(function(){
	latex_key_val = $('#latex-key').val();
	if (latex_key_val.length >= 2 && latex_key_val.length <= 5){
		submit_form($key_form, key_form_url, $key_alert_id, key_sent);
	} else {
		alert_key($key_alert_id, key_error);
	}
});

$key_input.keypress(function(e){
	if (e.which == 13) {
		if (this.value.length >= 2 && this.value.length <= 5){
			submit_form($key_form, key_form_url, $key_alert_id, key_sent);
		} else {
			alert_key($key_alert_id, key_error);
		}
		return false;
	}
});

$('#reset-key').click(function(){
	$('#latex-key').val('$$');
	submit_form($key_form, key_form_url, $key_alert_id, key_reset);
});

$inline_latex.change(function(){
	submit_form($inline_form, inline_form_url, $inline_alert_id, data_sent);
});

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
