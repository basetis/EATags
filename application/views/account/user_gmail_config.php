<?php
  $this->load->view('header');

  // PAGE CONTENT - USER GMAIL CONFIG
?>
	<div class="page-header">
	  <h1><?= $this->lang->line('account_gmail_header') ?><small></small></h1>
	</div>

<?php


	// PRINT DB MESSAGE WHEN NECESSARY
	$this->load->view('account/user_db_message');
?>

	<div class="row">
<?php


		// DISPLAY USER ACCOUNT MENU
		$this->load->view('account/user_account_menu');

		// DISPLAY FEATURE CONFIG
?>
		<div class="span4">
			<div class="alert alert-info">
				<?= $this->lang->line('account_gmail_info') ?>
			</div>
		</div>

		<div class="span4">
<?php

			if (isset($config_data['gmail_username']) && $user_activated) {
				// CURRENT GMAIL ACCOUNT
				echo '<fieldset class="control-group">';

				$label_attributes = array( 'class' => "control-label" );
				$label_text = $this->lang->line('account_gmail_username');
				echo form_label($label_text, 'gmail_username', $label_attributes);

				$input_attributes = array(
					'name' => 'gmail_username',
					'class' => "input-xlarge disabled" ,
					'value' => $config_data['gmail_username'],
					'disabled' => 'disabled',
				);
				echo form_input($input_attributes);

				echo '</fieldset>';

				echo '<hr>';
				// SELECT LANGUAGE
				echo form_open('', "id='gmail-lang-form'"); ?>
				<label for='gmail-lang'><?php echo $this->lang->line('account_gmail_lang'); ?></label>
				<?php
				$languages = $config_data['languages'];
				echo form_dropdown('gmail-lang',$languages,$config_data['sel_lang'],"id='gmail-lang'");
				echo form_close();
				echo "<div id='alert-gmail'></div>";

				// CHANGE ACCOUNT
				$label_text = $this->lang->line('account_gmail_username_change');
				echo form_label($label_text, '', $label_attributes);


			}
			// CREATE /  ACCOUNT BUTTON

?>
				<?php if (!$user_activated){
					echo $this->lang->line('account_gmail_add_account');
				}
				?>
				<a href="<?php echo base_url('auth/session/google');?>" alt="<?php echo $this->lang->line('account_gmail_sign_in'); ?>" title="<?= $this->lang->line('account_gmail_sign_in') ?>">
					<img src="<?php echo base_url('assets/images/gmail.png'); ?>"/>
				</a>
<?php


		// DELETE GMAIL
			if ($user_activated) {
				echo '<hr/>';

				echo form_open('', "id='gmail-del-form'");

				// HIDDEN ACTION DEFINITION
				echo form_hidden('action_type', 'delete');

				// SUBMIT

				echo "<a href='' id='delete-gmail' class='btn btn-danger'>" . $this->lang->line('account_gmail_deny') . "</a>";

				echo '<div style="clear:both"></div>';

				echo form_close();
			}
?>

		</div>
		<script src="<?=base_url('assets/js/eat_ajax_forms.js');?>" type="text/javascript"></script>
		<script type="text/javascript">
		    $(document).ready(function() {
		        $gmail_form = $('#gmail-lang-form');
		        gmail_url = '/auth/set_lang';
		        $gmail_alert_id = $('#alert-gmail');
		        gmail_alert_txt = "<?php echo $this->lang->line('account_gmail_lang_alert'); ?>";
		        $gmail_del_form = $('#gmail-del-form');
		        gmail_del_url = '/auth/del/eat_gmail';
		        $('#gmail-lang').change(function(){
		        	submit_form($gmail_form, gmail_url, $gmail_alert_id, gmail_alert_txt);
		        });
		        $('#delete-gmail').click(function(){
		        	submit_form($gmail_del_form, gmail_del_url);
		        	window.setTimeout('location.reload()', 3000);
		        })
		    });
    	</script>
	</div><!--/row-->


<?php
  $this->load->view('footer');
?>
