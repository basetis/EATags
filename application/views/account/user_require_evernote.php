<?php
  $this->load->view('header');

  // PAGE CONTENT - USER REQUEST EVERNOTE ACCESS AS REQUIRED
?>
    <div class="page-header">
      <h1><?= $this->lang->line('account_require_en_header') ?> <small></small></h1>
    </div>



    <div class="row">
    <?php
      $this->load->view('account/user_account_menu');
    ?>
        <div class="span4">
            <div class="hero-unit">
                <img src="<?php echo base_url('/assets/images/eat.banner_3.png');?>"/>
                <?= $this->lang->line('account_require_en_info') ?>
            </div><!--/alert-->
        </div><!--/span-->

        <div class="well span2">
            <a class="btn btn-success btn-large" href="<?php echo base_url('home/evernote_oauth_authorize');?>"><?= $this->lang->line('account_require_en_login') ?></a>
        </div><!--/well-->




    </div><!--/row-->

<?php

  $this->load->view('footer');

?>