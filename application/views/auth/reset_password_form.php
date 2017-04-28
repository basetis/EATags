<?php
	$this->load->view('header');

	// PAGE CONTENT - RESET PASSWORD
?>
    <div class="page-header">
      <h1><?= $this->lang->line('auth_reset_header') ?> <small></small></h1>
    </div>

	<div class="row">
		<div class="span4">
            <div class="alert alert-info">
				<?= $this->lang->line('auth_reset_intro') ?>
			</div>
        </div>

        <div class="well span3">
<?php
		// RESET PASSWORD FORM
			echo form_open($this->uri->uri_string());

			// PASSWORD

			$pass_error_text = '';
			if( form_error('new_password') ){
				$pass_error_text = form_error('new_password', '<span class="label label-important">', '</span>');
			} else if ( isset($errors['new_password']) && $errors['new_password'] ){
				$pass_error_text = '<span class="label label-important">' . $errors['new_password'] . '</span>';
			}

			$fieldset_class = ($pass_error_text)? ' error' : '';
			echo '<fieldset class="control-group' . $fieldset_class .'">';

			$label_text = ($pass_error_text)? $pass_error_text : $this->lang->line('auth_reset_new_password');
			$label_attributes = array( 'class' => "control-label" );
			echo form_label($label_text, 'new_password', $label_attributes);

			$input_attributes = array(
				'name'	=> 'new_password',
				'id'	=> 'new_password',
				'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
				'class' => "input-xlarge",
			);
			echo form_password($input_attributes);

			echo '</fieldset>';

			// CONFIRM PASSWORD

			$pass_error_text = '';
			if( form_error('confirm_new_password') ){
				$pass_error_text = form_error('confirm_new_password', '<span class="label label-important">', '</span>');
			} else if ( isset($errors['confirm_new_password']) && $errors['confirm_new_password'] ){
				$pass_error_text = '<span class="label label-important">' . $errors['confirm_new_password'] . '</span>';
			}

			$fieldset_class = ($pass_error_text)? ' error' : '';
			echo '<fieldset class="control-group' . $fieldset_class .'">';

			$label_text = ($pass_error_text)? $pass_error_text : $this->lang->line('auth_reset_confirm_new_password');
			$label_attributes = array( 'class' => "control-label" );
			echo form_label($label_text, 'confirm_new_password', $label_attributes);

			$input_attributes = array(
				'name'	=> 'confirm_new_password',
				'id'	=> 'confirm_new_password',
				'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
				'class' => "input-xlarge",
			);
			echo form_password($input_attributes);

			echo '</fieldset>';

			// SUBMIT
			$submit_attributes = array('name' => 'change', 'class' => 'btn btn-success btn-large' );
			echo form_submit($submit_attributes, $this->lang->line('auth_reset_change_password'));


			echo form_close();

			?>

        </div><!--/well-->
	</div> <!--/row-->

<?php
	// PRINT FOOTER
	$this->load->view('footer');
	?>