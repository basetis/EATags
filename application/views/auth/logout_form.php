<?php
$this->load->view('header');
$this->lang->load('auth');?>

    <div class="page-header">
      <h1><?php echo $this->lang->line('auth_logout_header') ?><small></small></h1>
    </div>

    <div class="row">
<?php
        // DISPLAY USER ACCOUNT MENU
        $this->load->view('account/user_account_menu');
?>

        <div class="span4">
            <div class="alert alert-error">
                <?php echo $this->lang->line('auth_logout_info') ?>
            </div><!--/alert-->
        </div><!--/span-->



        <div class="well span4">

<?php
    echo form_open(base_url('auth/evernote_logout'));

        // PASSWORD
        $fieldset_class = (form_error('password'))? ' error' : '';
        echo '<fieldset class="control-group' . $fieldset_class .'">';

        $label_attributes = array( 'class' => "control-label" );
        $label_text = $this->lang->line('auth_logout_password_required');
        if( form_error('password') ){
            $label_text = form_error('password',  '<span class="label label-important">', '</span>');
        } elseif( isset($errors) ){
            $label_text = '';
            foreach ($errors as $error) {
                $label_text .= '<span class="label label-important">' . $error .'</span>';
            }
        }

        echo form_label($label_text, 'password', $label_attributes);

        $input_attributes = array(
            'value' => '',
            'name' => 'password',
            'class' => "input-xlarge",
            'placeholder' => ' Password',
            'autocomplete' => 'off'
        );
        echo form_password($input_attributes);

        echo '</fieldset>';

        // SUBMIT
        $submit_attributes = array('name' => 'submit', 'class' => 'btn btn-danger btn-large' );
        echo form_submit($submit_attributes, $this->lang->line('auth_logout_delete'));

         echo form_close();
?>
        </div><!--/well-->
    </div><!--/row-->

<?php
$this->load->view('footer');
?>




<?php /*
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'size'	=> 30,
);
?>
<?php
$this->load->view('header');
echo form_open($this->uri->uri_string()); ?>
<table>
	<tr>
		<td><?php echo form_label('Password', $password['id']); ?></td>
		<td><?php echo form_password($password); ?></td>
		<td style="color: red;"><?php echo form_error($password['name']); ?><?php echo isset($errors[$password['name']])?$errors[$password['name']]:''; ?></td>
	</tr>
</table>
<?php echo form_submit('cancel', 'Delete account'); ?>
<?php echo form_close();
$this->load->view('footer');
*/
?>