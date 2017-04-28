<?php
  $this->load->view('header');

  // PAGE CONTENT - USER ADD CONFIG
?>
    <div class="page-header">
      <h1><?php echo $this->lang->line('account_add_header') ?><small></small></h1>
    </div>

<?php
    // PRINT DB MESSAGE WHEN NECESSARY
    $this->load->view('account/user_db_message');

    // GET AND DEFINE NOTEBOOKS FROM MODEL
    $notebooks = $config_data['notebooks'];

    // GET AND DEFINE SELECTED NOTES FROM MODEL
    $selected_header = $config_data['selected_header'];
    $selected_footer = $config_data['selected_footer'];
    $header_notes = $config_data['header_notes'];
    $footer_notes = $config_data['footer_notes'];
?>

    <div class="row">
<?php
    // DISPLAY USER ACCOUNT MENU
    $this->load->view('account/user_account_menu');

    // DISPLAY FEATURE CONFIG
?>
        <div class="span7">
            <?php echo $this->lang->line('account_add_info'); ?>
            <div class="span6">
                <h3 class="select_add">
                    <?php echo $this->lang->line('account_add_header_info'); ?>
                </h3>
                <div class="well span5" id="header-form">
                <?php
					$reset_header_lang		= $this->lang->line('account_add_reset_header');
                    $reset_footer_lang		= $this->lang->line('account_add_reset_footer');
                    $notebooks_lang         = $this->lang->line('account_add_notebooks');
                    $notes_lang             = $this->lang->line('account_add_notes');
                    $select_notebook_lang	= $this->lang->line('account_add_select_notebook');
                    $select_note_lang		= $this->lang->line('account_add_select_note');
?>
                <?php
                    $visibility_h   = ($header_notes == '') ? "style='visibility:hidden'" : "";
                    $selected_h		= ($header_notes == '') ? "selected" : "";
                    $disabled_h		= ($header_notes == '') ? "disabled" : "";
                ?>
                <a class='close reset-form' id='header_reset' title='<?php echo $reset_header_lang . "'" . $visibility_h; ?> >&times;</a>

                <?php echo form_open(); ?>
                <ul class="add_form">
                    <li>
                    <label for='notebooks_header'><?php echo $notebooks_lang; ?></label>
                    <select class='select_add' name='id_notebooks' id='notebooks_header'>
                        <option id='header_option_1' value='' disabled <?php echo $selected_h . '>' . $select_notebook_lang; ?></option>

                        <?php foreach ($notebooks as $key => $value) {
                            if ($header_notes != '' && $key == $selected_header->notebook_guid){
                                echo '<option selected value="'.$key.'">'.$value.'</option>';
                            } else {
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                    } ?>
                    </select>
                    </li>
                    <li>
                    <label for='notes_header'><?php echo $notes_lang; ?></label>
                    <select class='select_add' name='id_notes' id='notes_header'  <?php echo $disabled_h; ?> >
                    	<option value='' disabled <?php echo $selected_h . '>' . $select_note_lang; ?></option>
                        <?php if ($header_notes != '') {
                            foreach ($header_notes as $key => $value) {
                                if ($key == $selected_header->note_guid) {
                                    echo '<option selected value="'.$key.'">'.$value.'</option>';
                                } else {
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                                }
                            }
                        } ?>
                    </select>
                    </li>
                </ul>
                <input type="hidden" name="type" value="header" />

                <?php echo form_close(); ?>
                <div id='alert-header'></div>

                </div><!-- well header -->
            </div>
            <div class="span6">
                <h3 class="select_add">
                    <?php echo $this->lang->line('account_add_footer_info'); ?>
                </h3>
                <div class="well span5" id="footer-form">
                <?php
                    $visibility_f   = ($footer_notes == '') ? "style='visibility:hidden'" : "";
                    $selected_f		= ($footer_notes == '') ? "selected" : "";
                    $disabled_f		= ($footer_notes == '') ? "disabled" : "";
                ?>
                <a class='close reset-form' id='footer_reset' title='<?php echo $reset_footer_lang . "'" . $visibility_f; ?> >&times;</a>

                <?php echo form_open(); ?>
                <ul class="add_form">
                    <li>
                    <label for='notebooks_footer'><?php echo $notebooks_lang; ?></label>
                    <select class='select_add' name='id_notebooks' id='notebooks_footer'>
                        <option id='footer_option_1' value='' disabled <?php echo $selected_f . '>' . $select_notebook_lang; ?></option>

                        <?php foreach ($notebooks as $key => $value) {
                            if ($footer_notes != '' && $key == $selected_footer->notebook_guid){
                                echo '<option selected value="'.$key.'">'.$value.'</option>';
                            } else {
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                    } ?>
                    </select>
                    </li>
                    <li>
                    <label for='notes_footer'><?php echo $notes_lang; ?></label>
                    <select class='select_add' name='id_notes' id='notes_footer'  <?php echo $disabled_f; ?> >
                    	<option value='' disabled <?php echo $selected_f . '>' . $select_note_lang; ?></option>
                        <?php if ($footer_notes != '') {
                            foreach ($footer_notes as $key => $value) {
                                if ($key == $selected_footer->note_guid) {
                                    echo '<option selected value="'.$key.'">'.$value.'</option>';
                                } else {
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                                }
                            }
                        } ?>
                    </select>
                    </li>
                </ul>
                <input type="hidden" name="type" value="footer" />

                <?php echo form_close(); ?>
                <div id='alert-footer'></div>

                </div><!-- well footer -->
            </div>
        </div>
    </div><!--/row-->
    <script type='text/javascript'>
        var $opt2 = "<option value='' disabled selected><?php echo $this->lang->line('account_add_select_note'); ?></option>";

        var load_text = "<?php echo $this->lang->line('account_add_loading'); ?>";
        var data_sent = "<?php echo $this->lang->line('account_add_sent'); ?>";
        var data_reset = "<?php echo $this->lang->line('account_add_reset'); ?>";
        var config = {base: "<?php echo base_url('action_add'); ?>", url:{notes: "/get_notes_by_notebook/", submit: "/send_data", reset: "/delete_data" }};
    </script>
    <script src="<?=base_url('assets/js/add.js');?>" type="text/javascript"></script>


<?php
  $this->load->view('footer');
?>
