<?php
	$this->load->view('header');

	// PAGE CONTENT - USER LATEX CONFIG
?>
	<div class="page-header">
		<h1><?php echo $this->lang->line('account_latex_header') ?><small></small></h1>
	</div>

<?php
	// PRINT DB MESSAGE WHEN NECESSARY
	// $this->load->view('account/user_db_message');

?>

	<div class="row">
<?php
	// DISPLAY USER ACCOUNT MENU
	$this->load->view('account/user_account_menu');

	// DISPLAY FEATURE CONFIG
?>
		<div class="span7">
			<div class="span6 latex-form">
				<h4><?php echo $this->lang->line('account_latex_form_title'); ?></h4>
				<?php $form_class = array('class' => 'navbar-form');
					echo form_open(base_url('action_latex/send_data'), $form_class);
					echo form_label($this->lang->line('account_latex_form_label'),'del-latex');
                    $data = array(
                        1	=> $this->lang->line('account_latex_form_yes'),
                        0	=> 'No',
                        );
                    echo form_dropdown('del-latex',$data,$config_data['del_formulas']);

					echo form_close();
				?>
				<div id='alert-del'></div>
			</div>
			<div class="span6 latex-key-form">
				<h4><?php echo $this->lang->line('account_latex_form_key_title'); ?></h4>
				<?php echo form_open(base_url('action_latex/send_key'), $form_class);
					echo form_label($this->lang->line('account_latex_form_key_label'),'latex-key');
					$latex_key 		= $config_data['latex_key'];
					$latex_key_char = $this->lang->line('account_latex_form_key_char');
                    $data = array(
                        'name'			=> 'latex-key',
                        'id'			=> 'latex-key',
                        'class'			=> 'span2',
                        'value'			=> $latex_key,
                        'placeholder'	=> $latex_key_char,
                        'maxlength'		=> '5',
                        'pattern'		=> '.{2,5}',
                        'required'		=> 'required',
                        );
                    echo form_input($data);
                ?>
					<a id='send-key' class='btn eat-btn-success key-btn'><?php echo $this->lang->line('account_latex_form_key_send'); ?></a>
					<a id='reset-key' class='btn btn-warning key-btn' title='<?php echo $this->lang->line('account_latex_key_reset_a'); ?>' >Reset</a>
				<?php echo form_close(); ?>
				<div id='alert-key'></div>
			</div>
			<div class="span6 latex-form">
				<h4><?php echo $this->lang->line('account_latex_inline_title'); ?></h4>
				<?php $form_class = array('class' => 'navbar-form');
					echo form_open(base_url('action_latex/send_data'), $form_class);
					echo form_label($this->lang->line('account_latex_inline_label'),'inline-latex');
                    $data = array(
                        1	=> $this->lang->line('account_latex_form_yes'),
                        0	=> 'No',
                        );
                    echo form_dropdown('inline-latex',$data,$config_data['inline_latex']);

					echo form_close();
				?>
				<div id='alert-inline'></div>
			</div>
		</div>
	</div><!--/row-->
<script type="text/javascript">
	data_sent 	= "<?php echo $this->lang->line('account_latex_sent'); ?>";
    key_sent  	= "<?php echo $this->lang->line('account_latex_key_sent'); ?>";
    key_error 	= "<?php echo $this->lang->line('account_latex_key_short'); ?>";
    key_reset 	= "<?php echo $this->lang->line('account_latex_key_reset'); ?>";
</script>
<script src="<?=base_url('assets/js/latex_form.js');?>" type="text/javascript"></script>

<?php
  $this->load->view('footer');
?>
