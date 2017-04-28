<?php
  $CI =& get_instance();
  $this->load->view('header');

  // PAGE CONTENT - USER FEATURES SUMMARY
?>
    <div class="page-header">
      <h1><?= $this->lang->line('features_account_header') ?><small></small></h1>
    </div>
<?php
  $alert = $CI->check_rate->check_rate_limit();
  if ($alert != '') {
    echo '<div class="alert alert-rate-info">' . $alert .'</div>';
  }
?>
    <?php
        $this->load->view('account/user_db_message');// PRINT DB MESSAGE WHEN NECESSARY
    ?>

    <div class="row">
    <?php
        $this->load->view('account/user_account_menu');


    ?>

        <table class="table table-bordered span8">
          <thead>
            <tr>
              <th><?= $this->lang->line('features_col_title_at') ?></th>
              <th><?= $this->lang->line('features_col_title_status') ?></th>
              <th><?= $this->lang->line('features_col_title_description') ?></th>
              <th><?= $this->lang->line('features_configuration') ?></th>
            </tr>
          </thead>
          <tbody>
    <?php
            foreach ($user_features as $feature) { ?>
            <tr>
                <td>
    <?php
                foreach ($feature['tags'] as $tag) { ?>
                    <span class="label label-info"><?php echo $tag['name']; ?></span>
    <?php       } ?>
                </td>
                <td>
    <?php       if( $feature['user_activated'] ){ ?>
                    <span class="label label-success"><?= $this->lang->line('features_status_active') ?></span>
    <?php       } else { ?>
                    <span class="label"><?= $this->lang->line('features_status_inactive') ?></span>
    <?php       } ?>
                </td>
                <td>
                    <?php echo $this->lang->line($feature['description']); ?>
                </td>
                <td>
    <?php       if( $feature['config_required'] ){
                    if( $feature['user_activated'] ){  ?>
                    <a class="btn" href="<?php echo base_url($this->lang->line('header_link_account_features') . $feature['keyname']); ?>"><?= $this->lang->line('features_btn_configure') ?></a>
    <?php           } else { ?>
                    <a class="btn btn-success" href="<?php echo base_url($this->lang->line('header_link_account_features') . $feature['keyname']); ?>"><?= $this->lang->line('features_btn_activate') ?></a>
    <?php           }
                } ?>
                </td>
            </tr>
    <?php   }        ?>
          </tbody>
        </table>
    </div><!--/row-->


<?php

  $this->load->view('footer');

?>