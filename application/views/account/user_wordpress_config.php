<?php
  $this->load->view('header');

  // PAGE CONTENT - USER WORDPRESS CONFIG
?>
    <div class="page-header">
      <h1><?= $this->lang->line('account_wp_header') ?><small></small></h1>
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
                <?= $this->lang->line('account_wp_info') ?>
            </div>
        </div>

        <div class="span4">
<?php
            echo form_open($this->uri->uri_string());


            // HIDDEN ACTION DEFINITION
            echo form_hidden('action_type', 'save');

            // BLOG URL
            $fieldset_class = (form_error('wp_blog_url'))? ' error' : '';
            echo '<fieldset class="control-group' . $fieldset_class .'">';

            $label_attributes = array( 'class' => "control-label" );
            $label_text = $this->lang->line('account_wp_url');
            if( form_error('wp_blog_url') ){
                $label_text = form_error('wp_blog_url',  '<span class="label label-important">', '</span>');
            }
            echo form_label($label_text, 'wp_blog_url', $label_attributes);


            $input_attributes = array(
                'value' => '',
                'id' => 'wp_blog_url',
                'name' => 'wp_blog_url',
                'class' => "input-xlarge",
                'value' => set_value('wp_blog_url',$config_data['wp_blog_url']),
            );
            echo form_input($input_attributes);
            echo '</fieldset>';

            // USERNAME
            $fieldset_class = (form_error('wp_username'))? ' error' : '';
            echo '<fieldset class="control-group' . $fieldset_class .'">';

            $label_text = $this->lang->line('account_wp_username');
            if( form_error('wp_username') ){
                $label_text = form_error('wp_username',  '<span class="label label-important">', '</span>');
            }
            echo form_label($label_text, 'wp_username', $label_attributes);

            $input_attributes = array(
                'value' => '',
                'id' => 'wp_username',
                'name' => 'wp_username',
                'class' => "input-xlarge",
                'value' => set_value('wp_username',$config_data['wp_username']),
            );

            echo form_input($input_attributes);

            echo '</fieldset>';


            // PASSWORD
            $fieldset_class = (form_error('wp_pass'))? ' error' : '';
            echo '<fieldset class="control-group' . $fieldset_class .'">';

            $label_text = $this->lang->line('account_wp_password');
            if( form_error('wp_pass') ){
                $label_text = form_error('wp_pass',  '<span class="label label-important">', '</span>');
            }

            echo form_label($label_text, 'wp_pass', $label_attributes);

            $input_attributes = array(
                'value'        => '',
                'id'           => 'wp_pass',
                'name'         => 'wp_pass',
                'class'        => "input-xlarge",
                'autocomplete' => 'off'
            );

            echo form_password($input_attributes);

            echo '</fieldset>';

            // SUBMIT
            $submit_attributes = array('name' => 'submit', 'class' => 'btn eat-btn-success btn-large' );
            echo form_submit($submit_attributes, $this->lang->line('account_wp_apply'));

            echo '<div style="clear:both"></div>';

            echo form_close();
?>


<?php


        // DELETE WORDPRESS
            if( $user_activated ) {
                echo '<hr/>';

                echo form_open($this->uri->uri_string());

                // HIDDEN ACTION DEFINITION
                echo form_hidden('action_type', 'delete');

                // SUBMIT
                $submit_attributes = array('name' => 'submit', 'class' => 'btn btn-danger' );
                echo form_submit($submit_attributes, $this->lang->line('account_wp_deny'));

                echo '<div style="clear:both"></div>';

                echo form_close();
            }

        ?>
        </div><!--/well-->
    </div><!--/row-->


<?php
  $this->load->view('footer');
?>
