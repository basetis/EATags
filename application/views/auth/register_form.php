<?php
	// PRINT HEADER
	$this->load->view('header');

	// PAGE CONTENT - SIGN UP PAGE
?>

	<div class="alert eat-alert-info">
                <?= $this->lang->line('auth_register_after') ?>
	</div>

	<div class="row">
		<div class="eat-span-title span1">
			<div class="eat-span-feature eat-span-feature3">
  				<div id="logotag"></div>
			</div>
			<h3><?= $this->lang->line('auth_register_sign_up') ?></h3>
		</div><!--/hero-->

		<div class="span4">

        </div>

		<div class="span3">
<?php
			echo form_open($this->uri->uri_string());

			// NAME
			if ($use_username) {
				$username_error_text = '';
				if( form_error('username') ){
					$username_error_text = form_error('username', '<span class="label label-important">', '</span>');
				} else if ( isset($errors['username']) && $errors['username'] ){
					$username_error_text = '<span class="label label-important">' . $errors['username'] . '</span>';
				}
				$fieldset_class = ($username_error_text)? ' error' : '';
				echo '<fieldset class="control-group' . $fieldset_class .'">';

				$label_text = ($username_error_text)? $username_error_text : $this->lang->line('auth_register_username');
				$label_attributes = array( 'class' => "control-label" );
				echo form_label($label_text, 'username', $label_attributes);

				$input_attributes = array(
					'name'	=> 'username',
					'id'	=> 'username',
					'value' => set_value('username'),
					'maxlength'	=> $this->config->item('username_max_length', 'tank_auth'),
				);
				echo form_input($input_attributes);

				echo '</fieldset>';
			}

			// MAIL
			$email_error_text = '';
			if( form_error('email') ){
				$email_error_text = form_error('email', '<span class="label label-important">', '</span>');
			} else if ( isset($errors['email']) && $errors['email'] ){
				$email_error_text = '<span class="label label-important">' . $errors['email'] . '</span>';
			}
			$fieldset_class = ($email_error_text)? ' error' : '';
			echo '<fieldset class="control-group' . $fieldset_class .'">';

			$label_text = ($email_error_text)? $email_error_text : $this->lang->line('auth_register_email');
			$label_attributes = array( 'class' => "control-label" );
			echo form_label($label_text, 'email', $label_attributes);

			$input_attributes = array(
				'name'	=> 'email',
				'id'	=> 'email',
				'value'	=> set_value('email'),
				'maxlength'	=> 80,
			);
			echo form_input($input_attributes);

			echo '</fieldset>';

			// PASSWORD
			$fieldset_class = (form_error('password'))? ' error' : '';
			echo '<fieldset class="control-group' . $fieldset_class .'">';

			$label_text = $this->lang->line('auth_register_password');
			if( form_error('password') ){
				$label_text = form_error('password', '<span class="label label-important">', '</span>');
			}
			$label_attributes = array( 'class' => "control-label" );
			echo form_label($label_text, 'password', $label_attributes);

			$input_attributes = array(
				'name'	=> 'password',
				'id'	=> 'password',
				'value' => set_value('password'),
				'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
			);
			echo form_password($input_attributes);

			echo '</fieldset>';

			// CONFIRM PASSWORD
			$fieldset_class = (form_error('confirm_password'))? ' error' : '';
			echo '<fieldset class="control-group' . $fieldset_class .'">';

			$label_text = $this->lang->line('auth_register_confirm');
			if( form_error('confirm_password') ){
				$label_text = form_error('confirm_password', '<span class="label label-important">', '</span>');
			}
			$label_attributes = array( 'class' => "control-label" );
			echo form_label($label_text, 'confirm_password', $label_attributes);

			$input_attributes = array(
				'name'	=> 'confirm_password',
				'id'	=> 'confirm_password',
				'value' => set_value('confirm_password'),
				'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
			);
			echo form_password($input_attributes);

			echo '</fieldset>';

			/* TODO: ADD RECAPTCHA IF NECESSARY */
			$captcha = array(
				'name'	=> 'captcha',
				'id'	=> 'captcha',
				'maxlength'	=> 8,
			);

			// SUBMIT
			$submit_attributes = array('name' => 'register', 'class' => 'btn eat-btn-success btn-large' );
			echo form_submit($submit_attributes, $this->lang->line('auth_register'));


			echo form_close();

		?>
		</div><!--/well-->
	</div> <!--/row-->



<?php
	// PRINT FOOTER
	$this->load->view('footer');




/*
<?php
$this->load->view('header');
echo '<br/><br/><br/>';
echo form_open($this->uri->uri_string());
?>
<table>
	<?php if ($use_username) { ?>
	<tr>
		<td><?php echo form_label('Username', $username['id']); ?></td>
		<td><?php echo form_input($username); ?></td>
		<td style="color: red;"><?php echo form_error($username['name']); ?><?php echo isset($errors[$username['name']])?$errors[$username['name']]:''; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td><?php echo form_label('Email Address', $email['id']); ?></td>
		<td><?php echo form_input($email); ?></td>
		<td style="color: red;"><?php echo form_error($email['name']); ?><?php echo isset($errors[$email['name']])?$errors[$email['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Password', $password['id']); ?></td>
		<td><?php echo form_password($password); ?></td>
		<td style="color: red;"><?php echo form_error($password['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Confirm Password', $confirm_password['id']); ?></td>
		<td><?php echo form_password($confirm_password); ?></td>
		<td style="color: red;"><?php echo form_error($confirm_password['name']); ?></td>
	</tr>

	<?php if ($captcha_registration) {
		if ($use_recaptcha) { ?>
	<tr>
		<td colspan="2">
			<div id="recaptcha_image"></div>
		</td>
		<td>
			<a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a>
			<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div>
			<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="recaptcha_only_if_image">Enter the words above</div>
			<div class="recaptcha_only_if_audio">Enter the numbers you hear</div>
		</td>
		<td><input type="text" id="recaptcha_response_field" name="recaptcha_response_field" /></td>
		<td style="color: red;"><?php echo form_error('recaptcha_response_field'); ?></td>
		<?php echo $recaptcha_html; ?>
	</tr>
	<?php } else { ?>
	<tr>
		<td colspan="3">
			<p>Enter the code exactly as it appears:</p>
			<?php echo $captcha_html; ?>
		</td>
	</tr>
	<tr>
		<td><?php echo form_label('Confirmation Code', $captcha['id']); ?></td>
		<td><?php echo form_input($captcha); ?></td>
		<td style="color: red;"><?php echo form_error($captcha['name']); ?></td>
	</tr>
	<?php }
	} ?>
</table>
<?php echo form_submit('register', 'Register'); ?>
<?php echo form_close();
$this->load->view('footer');

*/
?>