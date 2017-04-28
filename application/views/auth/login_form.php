<?php

$remember = array(
	'name'	=> 'remember',
	'id'	=> 'remember',
	'value'	=> 1,
	'checked'	=> set_value('remember'),
	'style' => 'margin:0;padding:0',
);
$captcha = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8,
);
?>
<?php
	// PRINT HEADER
	$this->load->view('header');

	// PAGE CONTENT - SIGN IN PAGE

?>

	<div class="row">
		<div class="eat-span-title span1 hidden-phone">
			<div class="eat-span-feature eat-span-feature3">
  				<div id="logotag"></div>
			</div>
			<h3><?= $this->lang->line('auth_login_sign_in') ?></h3>
		</div><!--/eat-span-title-->

		<div class="span3">
<?php
		// LOGIN FORM
			echo form_open($this->uri->uri_string());

			// NAME OR MAIL
			$fieldset_class = (form_error('login'))? ' error' : '';
			echo '<fieldset class="control-group' . $fieldset_class .'">';

			if ($login_by_username AND $login_by_email) {
				$label_text = $this->lang->line('auth_login_email_or_username');
			} else if ($login_by_username) {
				$label_text = $this->lang->line('auth_login');
			} else {
				$label_text = $this->lang->line('auth_login_email');
			}
			if( form_error('login') ){
                $label_text = form_error('login',  '<span class="label label-important">', '</span>');
            } elseif( isset($errors['login']) &&  $errors['login']){
            	$label_text = '<span class="label label-important">' . $errors['login'] . '</span>';
            }
			$label_attributes = array( 'class' => "control-label" );
			echo form_label($label_text, 'login', $label_attributes);

			$input_attributes = array(
				'name'	=> 'login',
				'id'	=> 'login',
				'value' => set_value('login'),
				'maxlength'	=> 80,
			);
			echo form_input($input_attributes);

			echo '</fieldset>';

			// PASSWORD
			$fieldset_class = (form_error('password'))? ' error' : '';
			echo '<fieldset class="control-group' . $fieldset_class .'">';

			$label_text = $this->lang->line('auth_login_password');
			if( form_error('password') ){
                $label_text = form_error('password',  '<span class="label label-important">', '</span>');
            } elseif( isset($errors['password']) &&  $errors['password']){
            	$label_text = '<span class="label label-important">' . $errors['password'] . '</span>';
            }
			echo form_label($label_text, 'password', $label_attributes);

			$input_attributes = array(
				'name'	=> 'password',
				'id'	=> 'password',
				'maxlength'	=> 80,
			);
			echo form_password($input_attributes);

			echo '</fieldset>';

			// REMEMBER ME
			echo form_label( form_checkbox($remember) . ' ' . $this->lang->line('auth_login_remember_me'), $remember['id'], $label_attributes);

			// SUBMIT
			$submit_attributes = array('name' => 'submit', 'class' => 'btn eat-btn-success btn-large' );
			echo form_submit($submit_attributes, $this->lang->line('auth_login_let_me_in'));

//			echo anchor('/auth/forgot_password/', 'Forgot password');

//			if ($this->config->item('allow_registration', 'tank_auth'))
//				echo anchor('/auth/register/', 'Register');



			echo form_close();

		// FORGOT PASSWORD??
		?>
			<hr>
			<a href="<?php echo base_url('auth/forgot_password'); ?>" class="btn btn-warning"><?= $this->lang->line('auth_login_forgot_password') ?></a>
		</div><!--/well-->
	</div> <!--/row-->




<?php
	// PRINT FOOTER
	$this->load->view('footer');




/*
	<br/><BR/>
<table>
	<tr>
		<td><?php echo form_label($login_label, $login['id']); ?></td>
		<td><?php echo form_input($login); ?></td>
		<td style="color: red;"><?php echo form_error($login['name']); ?><?php echo isset($errors[$login['name']])?$errors[$login['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Password', $password['id']); ?></td>
		<td><?php echo form_password($password); ?></td>
		<td style="color: red;"><?php echo form_error($password['name']); ?><?php echo isset($errors[$password['name']])?$errors[$password['name']]:''; ?></td>
	</tr>

	<?php if ($show_captcha) {
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

	<tr>
		<td colspan="3">
			<?php echo form_checkbox($remember); ?>
			<?php echo form_label('Remember me', $remember['id']); ?>
			<?php echo anchor('/auth/forgot_password/', 'Forgot password'); ?>
			<?php if ($this->config->item('allow_registration', 'tank_auth')) echo anchor('/auth/register/', 'Register'); ?>
		</td>
	</tr>
</table>
<?php echo form_submit('submit', 'Let me in'); ?>
<?php echo form_close();
$this->load->view('footer');

*/
?>