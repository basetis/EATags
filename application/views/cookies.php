<?php
  $this->load->view('header');

  // PAGE CONTENT - PRIVACY POLICY
?>
    <div class="page-header">
      <h1><?= $this->lang->line('cookies_title') ?></h1>
    </div>

    <div class="row">
        <div class="span12">
          <?= $this->lang->line('cookies_text') ?>
        </div><!--/span-->

    </div> <!--/row-->

<?php

  $this->load->view('footer');

?>