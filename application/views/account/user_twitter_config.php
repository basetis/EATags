<?php
  $this->load->view('header');

  // PAGE CONTENT - USER WORDPRESS CONFIG
?>
    <div class="page-header">
      <h1><?= $this->lang->line('account_twitter_header') ?><small></small></h1>
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
                <?= $this->lang->line('account_twitter_info') ?>
            </div>
        </div>

        <div class="span4">
<?php

            if (isset($config_data['twitter_screen_name']) && $user_activated) {
                // CURRENT TWITTER ACCOUNT
                echo '<fieldset class="control-group">';

                $label_attributes = array( 'class' => "control-label" );
                $label_text = $this->lang->line('account_twitter_username');
                echo form_label($label_text, 'twitter_screen_name', $label_attributes);

                $input_attributes = array(
                    'name' => 'twitter_screen_name',
                    'class' => "input-xlarge disabled" ,
                    'value' => $config_data['twitter_screen_name'],
                    'disabled' => 'disabled',
                );
                echo form_input($input_attributes);

                echo '</fieldset>';

                echo '<hr>';
                $label_text = $this->lang->line('account_twitter_username_change');
                echo form_label($label_text, '', $label_attributes);
            }
            // CREATE / CHANGE ACCOUNT BUTTON
                ?>
            <a href="<?php echo base_url('account/request/twitter');?>" alt="<?= $this->lang->line('account_twitter_sign_in') ?>" title="<?= $this->lang->line('account_twitter_sign_in') ?>">
                <img src="<?php echo base_url('assets/images/sign-in-with-twitter-l.png'); ?>"/>
            </a>

<?php


        // DELETE TWITTER
            if( $user_activated ) {
                echo '<hr/>';

                echo form_open($this->uri->uri_string());

                // HIDDEN ACTION DEFINITION
                echo form_hidden('action_type', 'delete');

                // SUBMIT
                $submit_attributes = array('name' => 'submit', 'class' => 'btn btn-danger' );
                echo form_submit($submit_attributes, $this->lang->line('account_twitter_deny'));

                echo '<div style="clear:both"></div>';

                echo form_close();
            }

        ?>
        </div><!--/well-->
    </div><!--/row-->


<?php
  $this->load->view('footer');
?>
