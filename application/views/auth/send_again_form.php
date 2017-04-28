
<?php
$this->load->view('header');?>

	<div class="row">

        <div class="span4">
            <div class="alert alert-info">
                <?= $this->lang->line('auth_send_again_info') ?>
            </div><!--/alert-->
        </div><!--/span-->

        <div class="well span4">

<?php
	echo form_open($this->uri->uri_string());

        // MAIL
        $fieldset_class = (form_error('email'))? ' error' : '';
        echo '<fieldset class="control-group' . $fieldset_class .'">';

        $label_attributes = array( 'class' => "control-label" );
        $label_text = $this->lang->line('auth_register_email');
        if( form_error('email') ){
            $label_text = form_error('email',  '<span class="label label-important">', '</span>');
        }
        echo form_label($label_text, 'email', $label_attributes);

        $input_attributes = array( 'class' => "input-xlarge" );
        echo form_input('email',set_value('email'), $input_attributes);
        echo '</fieldset>';

        // SUBMIT
        $submit_attributes = array('name' => 'submit', 'class' => 'btn btn-success btn-large' );
        echo form_submit($submit_attributes, $this->lang->line('auth_send'));

		 echo form_close();
?>
		</div><!--/well-->
	</div><!--/row-->

<?php
$this->load->view('footer');
?>