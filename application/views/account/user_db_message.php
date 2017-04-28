<?php
    // CHECK IF INTERACTION WITH DB IS CORRECT AND ALERT USER ABOUT IT

    $alert_class = 'alert alert-success';
    $alert_msg = '';
    if( isset($config_data['config_saved']) ){
        switch ($current_section) {
            case 'wordpress':
                if( $config_data['config_saved'] ){ // SAVE PROCESS OK
                    $alert_msg = $this->lang->line('account_message_ok');
                } else {  // SAVE PROCESS KO
                    $alert_class = 'alert alert-block';
                    $alert_msg = $this->lang->line('account_message_error_wp');
                }
                break;

            default:
                if( $config_data['config_saved'] ){ // SAVE PROCESS OK
                    $alert_msg = $this->lang->line('account_message_ok');
                } else {  // SAVE PROCESS KO
                    $alert_class = 'alert alert-block';
                    $alert_msg = $this->lang->line('account_message_error');
                }
                break;
        }

    } else if( isset($config_data['delete_done']) ){
        if( $config_data['delete_done'] ){  // DELETE PROCESS OK
            if ($current_section == 'flickr') {
                $alert_msg = $this->lang->line('account_message_ok_delete_flickr');
            } else if ($current_section == 'gmail') {
                $alert_msg = $this->lang->line('account_message_ok_delete_gmail');
            } else if ($current_section == 'twitter') {
                $alert_msg = $this->lang->line('account_message_ok_delete_twitter');
            } else {
                $alert_msg = $this->lang->line('account_message_ok_delete');
            }

        } else {  // DELETE PROCESS KO
            $alert_class = 'alert alert-block';
            $alert_msg = $this->lang->line('account_message_error_delete');
        }
    } else if ( isset($config_data['info_message']) ) {
        $alert_msg = $config_data['info_message'];
    }

    if( $alert_msg ){
?>
        <div class="row">
            <div class="<?php echo $alert_class; ?>">
              <button class="close" data-dismiss="alert">×</button>
              <?php echo $alert_msg; ?>
            </div>
        </div><!--/row-->
<?php
    }

?>