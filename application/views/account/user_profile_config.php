<?php
  $this->load->view('header');

  // PAGE CONTENT - USER PROFILE CONFIGURATION
?>
    <div class="page-header">
      <h1><?= $this->lang->line('account_profile_header') ?><small></small></h1>
    </div>

    <?php
    // CHECK IF SOME MESSAGE MUST BE DISPLAYED
    if(isset($config_data['config_message']) && $config_data['config_message']){
        echo '<div class="alert alert-info">' . $config_data['config_message'] . '</div>';
    }
    ?>



<?php
    // PRINT DB MESSAGE WHEN NECESSARY
    $this->load->view('account/user_db_message');

?>

    <div class="row">
<?php
        $this->load->view('account/user_account_menu');
?>

        <div class="span4">
<?php
        // CHANGE MAIL
            echo form_open($this->uri->uri_string());

            // HIDDEN ACTION DEFINITION
            echo form_hidden('action_type', 'change_mail');

            // CURRENT USERNAME
            echo '<fieldset class="control-group">';

            $label_attributes = array( 'class' => "control-label" );
            $label_text = $this->lang->line('account_profile_username');
            echo form_label($label_text, 'old_email', $label_attributes);

            $input_attributes = array(
                'name' => 'current_username',
                'class' => "input-xlarge disabled" ,
                'value' => $this->session->userdata('username'),
                'disabled' => 'disabled',
            );
            echo form_input($input_attributes);

            echo '</fieldset>';



            // PASSWORD
            $fieldset_class = (form_error('password'))? ' error' : '';
            echo '<fieldset class="control-group' . $fieldset_class .'">';

            $label_attributes = array( 'class' => "control-label" );
            $label_text = $this->lang->line('account_profile_password');
            if( form_error('password') ){
                $label_text = form_error('password',  '<span class="label label-important">', '</span>');
            } elseif( isset($config_data) && isset($config_data['errors']) ){
                $errors = $config_data['errors'];
                if( isset($errors['password']) ){
                    if( $errors['password'] ){
                        $label_text = '<span class="label label-important">' . $errors['password'] . '</span>';
                    } else {
                        $label_text = '<span class="label label-important"> '.$this->lang->line('account_profile_error_password') .'</span>';
                    }
                }
            }

            echo form_label($label_text, 'password', $label_attributes);

            $input_attributes = array(
                'value' => '',
                'name' => 'password',
                'class' => "input-xlarge",
            );
            echo form_password($input_attributes);
            echo '</fieldset>';

            // CURRENT EMAIL ACCOUNT
            echo '<fieldset class="control-group">';

            $label_attributes = array( 'class' => "control-label" );
            $label_text = $this->lang->line('account_profile_email');
            echo form_label($label_text, 'old_email', $label_attributes);

            $input_attributes = array(
                'name' => 'old_email',
                'class' => "input-xlarge disabled" ,
                'value' => $current_email,
                'disabled' => 'disabled',
            );
            echo form_input($input_attributes);

            echo '</fieldset>';

            // MAIL
            $fieldset_class = (form_error('email'))? ' error' : '';
            echo '<fieldset class="control-group' . $fieldset_class .'">';

            $label_attributes = array( 'class' => "control-label" );
            $label_text = $this->lang->line('account_profile_new_email');
            if( form_error('email') ){
                $label_text = form_error('email',  '<span class="label label-important">', '</span>');
            } elseif( isset($config_data) && isset($config_data['errors']) ){
                $errors = $config_data['errors'];
                if( isset($errors['email']) ){
                    if( $errors['email'] ){
                        $label_text = '<span class="label label-important">' . $errors['email'] . '</span>';
                    } else {
                        $label_text = '<span class="label label-important"> '.$this->lang->line('account_profile_new_email_error').' </span>';
                    }
                }
            }

            echo form_label($label_text, 'email', $label_attributes);

            $input_attributes = array(
                'id' => 'email',
                'name' => 'email',
                'class' => "input-xlarge",
                'value' => set_value('email'),
            );
            echo form_input($input_attributes);
            echo '</fieldset>';

            // SUBMIT
            $submit_attributes = array('name' => 'submit', 'class' => 'btn eat-btn-success btn-large' );
            echo form_submit($submit_attributes, $this->lang->line('account_profile_change_email'));

            echo '<div style="clear:both"></div>';

            echo form_close();
?>
            <hr>

<?php

        // CHANGE PASSWORD
            echo form_open($this->uri->uri_string());

            // HIDDEN ACTION DEFINITION
            echo form_hidden('action_type', 'change_password');

            // OLD PASSWORD
            $fieldset_class = (form_error('old_password'))? ' error' : '';
            echo '<fieldset class="control-group' . $fieldset_class .'">';

            $label_attributes = array( 'class' => "control-label" );
            $label_text = $this->lang->line('account_profile_old_password');
            if( form_error('old_password') ){
                $label_text = form_error('old_password',  '<span class="label label-important">', '</span>');
            } else if( isset($config_data['confirm_password_error']) ){
                $label_text = '<span class="label label-important">'.$this->lang->line('account_profile_passwords_not_equal').'</span> ';
            }

            echo form_label($label_text, 'old_password', $label_attributes);

            $input_attributes = array(
                'value' => '',
                'name' => 'old_password',
                'class' => "input-xlarge",
                'autocomplete' => 'off'
            );
            echo form_password($input_attributes);
            echo '</fieldset>';

            // PASSWORD
            $fieldset_class = (form_error('new_password'))? ' error' : '';
            echo '<fieldset class="control-group' . $fieldset_class .'">';

            $label_attributes = array( 'class' => "control-label" );
            $label_text = $this->lang->line('account_profile_new_password');
            if( form_error('new_password') ){
                $label_text = form_error('new_password',  '<span class="label label-important">', '</span>');
            }
            echo form_label($label_text, 'new_password', $label_attributes);

            $input_attributes = array(
                'value' => '',
                'name' => 'new_password',
                'class' => "input-xlarge",
                'autocomplete' => 'off'
            );
            echo form_password($input_attributes);
            echo '</fieldset>';


            // SUBMIT
            $submit_attributes = array('name' => 'submit', 'class' => 'btn eat-btn-success btn-large' );
            echo form_submit($submit_attributes, $this->lang->line('account_profile_change_password'));

            echo '<div style="clear:both"></div>';

            echo form_close();


        ?>

        <hr>
<?php
        // MAILING LIST
        $attributes = array('id' => 'mailing-form');
        echo form_open($this->uri->uri_string(), $attributes);
        echo form_label($this->lang->line('account_profile_mailing_label'), 'mailing');
        // $mailing_check = FALSE;
        $mailing_data = array(
            'name'      => 'mailing',
            'id'        => 'mailing',
            'value'     => 'accept',
            'checked'   => $mailing_check,
            );
        echo form_checkbox($mailing_data);
?>
        <span id='mailing-text'><?php echo $this->lang->line('account_profile_mailing_text'); ?></span>
        <input type='hidden' name='mailing-hidden' id='mailing-hidden' value='refuse'></input>
        <input type='hidden' name='mailing-id-hidden' id='mailing-id-hidden' value='1'></input>
<?php
        echo form_close();
?>

    <div id="alert-mailing"></div>
<script src="<?=base_url('assets/js/eat_ajax_forms.js');?>" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $mailing_form = $('#mailing-form');
        mailing_url = '/account/send_mailing';
        $mailing_alert_id = $('#alert-mailing');
        mailing_alert_reg = "<?php echo $this->lang->line('account_profile_mailing_regist'); ?>";
        mailing_alert_unreg = "<?php echo $this->lang->line('account_profile_mailing_unregist'); ?>";
        $('#mailing').click(function(){
            var thisCheck = $(this);
            if (thisCheck.is (':checked')) {
                $('#mailing-hidden').prop('disabled', true);
                reg_ml_url = mailing_url + '/reg_ml';
                submit_form($mailing_form, reg_ml_url, $mailing_alert_id, mailing_alert_reg);

            } else {
                $('#mailing-hidden').prop('disabled', false);
                unreg_ml_url = mailing_url + '/unreg_ml';
                submit_form($mailing_form, unreg_ml_url, $mailing_alert_id, mailing_alert_unreg);
            }
        });
    });
</script>
<hr>
<?php

        // GO TO UNREGISTER
            echo form_open($this->uri->uri_string());

            // HIDDEN ACTION DEFINITION
            echo form_hidden('action_type', 'goto_unregister');

            // SUBMIT
            $submit_attributes = array('name' => 'submit', 'class' => 'btn btn-warning' );


            echo '<div style="float:left">'.form_submit($submit_attributes, $this->lang->line('account_profile_unregister')).'</div>';

            echo form_close();

        ?>
        <?php

        // GO TO LOGOUT
            echo form_open($this->uri->uri_string());

            // HIDDEN ACTION DEFINITION
            echo form_hidden('action_type', 'goto_logout');

            // SUBMIT
            $submit_attributes = array('name' => 'submit', 'class' => 'btn btn-warning' );


            echo '<div id="logout">'.form_submit($submit_attributes, $this->lang->line('account_profile_logout')).'</div>';

            echo form_close();


        ?>


        </div><!--/well-->
    </div><!--/row-->

<?php

  $this->load->view('footer');
?>
