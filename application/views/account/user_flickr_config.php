<?php
  $this->load->view('header');

  // PAGE CONTENT - USER WORDPRESS CONFIG
?>
    <div class="page-header">
      <h1><?= $this->lang->line('account_flickr_header') ?><small></small></h1>
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
                <?= $this->lang->line('account_flickr_info') ?>
            </div>
        </div>

        <div class="span4">
<?php

            if (isset($config_data['flickr_username']) && $user_activated) {
                // CURRENT TWITTER ACCOUNT
                echo '<fieldset class="control-group">';

                $label_attributes = array( 'class' => "control-label" );
                $label_text = $this->lang->line('account_flickr_username');
                echo form_label($label_text, 'flickr_username', $label_attributes);

                $input_attributes = array(
                    'name' => 'flickr_username',
                    'class' => "input-xlarge disabled" ,
                    'value' => $config_data['flickr_username'],
                    'disabled' => 'disabled',
                );
                echo form_input($input_attributes);

                echo '</fieldset>';

                //echo '<hr>';
                //$label_text = 'You can change your user sign in again with another user:';
                //echo form_label($label_text, '', $label_attributes);
            }
            // CREATE /  ACCOUNT BUTTON

?>
                <?php if (!$user_activated){
                    echo $this->lang->line('account_flickr_add_account');
                }
                ?>
                <a href="<?php echo base_url('account/request/flickr');?>" alt="<?= $this->lang->line('account_flickr_sign_in') ?>" title="<?= $this->lang->line('account_flickr_sign_in') ?>">
                    <img src="<?php echo base_url('assets/images/flickr.png'); ?>"/>
                </a>
<?php


        // DELETE FLICKR
            if( $user_activated ) {
                echo '<hr/>';

                echo form_open('', "id='flickr-del-form'");

                // HIDDEN ACTION DEFINITION
                echo form_hidden('action_type', 'delete');

                // SUBMIT

                echo "<a href='' id='delete-flickr' class='btn btn-danger'>" . $this->lang->line('account_flickr_deny') . "</a>";

                echo '<div style="clear:both"></div>';

                echo form_close();
            }
?>

        </div><!--/well-->
        <script src="<?=base_url('assets/js/eat_ajax_forms.js');?>" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $flickr_del_form = $('#flickr-del-form');
                flickr_del_url = '/auth/del/eat_flickr';
                $('#delete-flickr').click(function(){
                    submit_form($flickr_del_form, flickr_del_url);
                    window.setTimeout('location.reload()', 3000);
                })
            });
        </script>
    </div><!--/row-->


<?php
  $this->load->view('footer');
?>
