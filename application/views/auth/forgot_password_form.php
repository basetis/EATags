<?php
	$this->load->view('header');

	// PAGE CONTENT - FORGOT PASSWORD
?>
    <div class="page-header">
      <h1><?= $this->lang->line('auth_forgot_header') ?></h1>
    </div>

	<div class="row">
		<div class="span4">
            <div class="alert alert-info">
            	<?= $this->lang->line('auth_forgot_intro') ?>
            </div>
        </div>

        <div class="well span3">
<?php
		// FORGOT PASSWORD FORM
			echo form_open($this->uri->uri_string());

			// LOGIN OR MAIL

			$login_error_text = '';
			if( form_error('login') ){
				$login_error_text = form_error('login', '<span class="label label-important">', '</span>');
			} else if ( isset($errors['login']) && $errors['login'] ){
				$login_error_text = '<span class="label label-important">' . $errors['login'] . '</span>';
			} else {
				if ($this->config->item('use_username', 'tank_auth')) {
					$login_label = $this->lang->line('auth_login_email_or_username');
				} else {
					$login_label = 'Email';
				}
			}

			$fieldset_class = ($login_error_text)? ' error' : '';
			echo '<fieldset class="control-group' . $fieldset_class .'">';

			$label_text = ($login_error_text)? $login_error_text : $login_label;
			$label_attributes = array( 'class' => "control-label" );
			echo form_label($label_text, 'login', $label_attributes);

			$input_attributes = array(
				'name'	=> 'login',
				'id'	=> 'login',
				'value'	=> set_value('login'),
				'maxlength'	=> 80,
				'class' => "input-xlarge",
			);
			echo form_input($input_attributes);

			echo '</fieldset>';

			// SUBMIT
			$submit_attributes = array('name' => 'reset', 'class' => 'btn btn-success btn-large' );
			echo form_submit($submit_attributes, $this->lang->line('auth_forgot_get_new'));


			echo form_close();

			?>

        </div><!--/well-->
	</div> <!--/row-->

<?php
	// PRINT FOOTER
	$this->load->view('footer');
	?>
