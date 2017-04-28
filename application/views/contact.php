<?php
  $this->load->view('header');

  // PAGE CONTENT - CONTACT
?>
    <div class="page-header">
      <h1><?= $this->lang->line('contact_header') ?> <small></small></h1>
    </div>

<?php
  // CHECK IF A MAIL CONTACT HAS BEEN SENT
  if ( isset($mail_sending) ) {
    $alert_msg_class = ($mail_sending == 'success')? 'eat-alert-success' : 'alert-error';

?>
    <div class="alert <?php echo $alert_msg_class; ?>">
      <?php echo $mail_sending_msg; ?>
    </div><!--/alert-->
<?php
  }
?>

    <div class="row">
        <div class="span12">
            <p>
              <?= $this->lang->line('contact_twitter') ?>
            </p>
            <h3>
              <?= $this->lang->line('contact_support') ?>
            </h3>
            <p>
              <?= $this->lang->line('contact_us') ?>
              <!--send an email to: <i><strong>support
              AT eatags DOT com</strong></i>.-->
            </p>
            <h3>
              <?= $this->lang->line('contact_submit') ?>
            </h3>
            <p>
              <?= $this->lang->line('contact_basetis') ?>
            </p>
            <h3>
              <?= $this->lang->line('contact_abuse') ?>
            </h3>
            <p>
              <?= $this->lang->line('contact_violation') ?>
              <!--
              contact our Support department
              sending an email to the technical support address: &nbsp;<i><strong>support
              AT eatags DOT com</strong></i>.
              -->
            </p>
            <h3>
              <?= $this->lang->line('contact_copy') ?>
            </h3>
            <p>
              <?= $this->lang->line('contact_copy_info') ?>
            </p>
            <h3>
              <?= $this->lang->line('contact_ads') ?>
            </h3>
            <p>
              <?= $this->lang->line('contact_promotions') ?>
            </p>
            <p>
              <?= $this->lang->line('contact_develop') ?>
              <!--
                refer to
                our development email address <i><strong>development
                AT eatags DOT com</strong></i>.
              -->
            </p>
            <h3>
              <?= $this->lang->line('contact_address') ?>
            </h3>
            <p>
              BaseTIS
              <br/>C/ Ram&oacute;n y Cajal 75, 1
              <BR/>08012 Barcelona
              <BR/>Spain
            </p>
        </div><!--/span-->

    </div> <!--/row-->



    <div class="modal hide" id="contact_modal">
    <?php
      echo form_open($this->uri->uri_string());
    ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3><?= $this->lang->line('contact_form') ?></h3>
      </div>
      <div class="modal-body">
<?php
            // HIDDEN ACTION DEFINITION
            echo form_hidden('action_type', 'contact_form');

            // MAIL
            $fieldset_class = (form_error('email'))? ' error' : '';
            echo '<fieldset class="control-group' . $fieldset_class .'">';

            $label_attributes = array( 'class' => "control-label" );
            $label_text = $this->lang->line('contact_email');
            if( form_error('email') ){
                $label_text = form_error('email',  '<span class="label label-important">', '</span>');
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

            // TITLE
            $fieldset_class = (form_error('title'))? ' error' : '';
            echo '<fieldset class="control-group' . $fieldset_class .'">';

            $label_attributes = array( 'class' => "control-label" );
            $label_text = $this->lang->line('contact_title');
            if( form_error('title') ){
                $label_text = form_error('title',  '<span class="label label-important">', '</span>');
            }

            echo form_label($label_text, 'title', $label_attributes);

            $input_attributes = array(
                'id' => 'title',
                'name' => 'title',
                'class' => "input-xlarge",
                'value' => set_value('title'),
            );
            echo form_input($input_attributes);
            echo '</fieldset>';


            $fieldset_class = (form_error('message'))? ' error' : '';
            echo '<fieldset class="control-group">';

            $label_attributes = array( 'class' => "control-label" );
            $label_text = $this->lang->line('contact_message');
            if( form_error('message') ){
                $label_text = form_error('message',  '<span class="label label-important">', '</span>');
            }
            echo form_label($label_text, 'message', $label_attributes);

            echo '<textarea class="input-xlarge" id="message" name="message" rows="5">' . set_value('message') . '</textarea>';
            echo '</fieldset>';

?>
      </div>
      <div class="modal-footer">
<?php
            // SUBMIT
            $submit_attributes = array('name' => 'submit', 'class' => 'btn eat-btn-success' );
            echo form_submit($submit_attributes, $this->lang->line('contact_send'));

            echo '<div style="clear:both"></div>';
?>
      </div>
<?php
                  echo form_close();
?>
    </div>

<?php

  if(validation_errors()){
    ?>
    <script type="text/javascript">
      $(function(){
        $('#contact_modal').modal('show');
      });
    </script>
<?php

  }
?>


<?php

  $this->load->view('footer');

?>