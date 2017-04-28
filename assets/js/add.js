$('#notebooks_header').change(select_note);
$('#notebooks_footer').change(select_note);
$('#notes_header').change(submit_form);
$('#notes_footer').change(submit_form);
$('#header_reset').on('click', reset_form);
$('#footer_reset').on('click', reset_form);

function select_note(){
    var $combo1 = '';
    var $combo2 = '';
    var loading = $('<option />');

    if (this.id == 'notebooks_header') {
        $combo1 = $('#notebooks_header');
        $combo2 = $('#notes_header');

    } else if (this.id == 'notebooks_footer') {
        $combo1 = $('#notebooks_footer');
        $combo2 = $('#notes_footer');
    }
    var notebook_guid = this.value;

    $combo2.empty();
    loading.text(load_text);
    $combo2.append(loading);

    $.ajax(
    {
        type:"POST",
        url:config.base+config.url.notes+notebook_guid,
        success: function(notes)
        {
            $combo2.empty();
            $combo2.append($opt2);
            var total_notes = 0;
            $.each(notes,function(note_guid,note_title)
            {
                var opt = $('<option />');
                opt.val(note_guid);
                opt.text(note_title);
                $combo2.append(opt);
                total_notes++;
            });
            var $combo2_1 = '';
            if ($combo1.selector == '#notebooks_header') {
                $combo2_1 = $('#notes_header option:nth(1)');
                $type = 'header';

            } else {
                $combo2_1 = $('#notes_footer option:nth(1)');
                $type = 'footer';
            }
            $combo2.prop('disabled', false);
            if (total_notes == 1) {
                $combo2_1.attr('selected', 'selected');
                submit_form($type);
            }
        }
    });
}

function submit_form($type){
    var form_id = this.id;
    if (form_id == 'notes_header' || $type == 'header') {
        $form = $('#header-form form');
        $reset_button = $('#header_reset');

    } else if (form_id == 'notes_footer' || $type == 'footer') {
        $form = $('#footer-form form');
        $reset_button = $('#footer_reset');
    }
    $.ajax(
    {
        url:config.base+config.url.submit,
        type:"POST",
        data: $form.serialize(),
        success: function(){
            $reset_button.attr("style", "visibility:visible");
            if (form_id == 'notes_header'){
                $div_id = $('#alert-header');
            } else if (form_id == 'notes_footer'){
                $div_id = $('#alert-footer');
            }
            alert_key($div_id, data_sent);
        }
    });
}

function reset_form(){
    var $opt1 = '';
    var form_id = this.id;
    if (form_id == 'header_reset') {
        $opt1 = $('#header_option_1');
        $combo2 = $('#notes_header');
        $form = $('#header-form form');
        $reset_button = $('#header_reset');

    } else if (form_id == 'footer_reset') {
        $opt1 = $('#footer_option_1');
        $combo2 = $('#notes_footer');
        $form = $('#footer-form form');
        $reset_button = $('#footer_reset');
    }

    $.ajax(
    {
        url:config.base+config.url.reset,
        type:"POST",
        data: $form.serialize(),
        success: function(){
            $opt1.prop('selected', true);
            $combo2.empty();
            $combo2.prop('disabled', true);
            $combo2.append($opt2);
            $reset_button.attr("style", "visibility:hidden");
            if (form_id == 'header_reset'){
                $div_id = $('#alert-header');
            } else if (form_id == 'footer_reset'){
                $div_id = $('#alert-footer');
            }
            alert_key($div_id, data_reset);
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