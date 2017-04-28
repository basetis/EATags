<?php
  $this->load->view('header');

  // PAGE CONTENT - ABOUT
?>
    <div class="page-header">
      <h1><?= $this->lang->line('about') ?> <small></small></h1>
    </div>

    <div class="row">
        <div class="span12">
          <?= $this->lang->line('about_text') ?>
        </div><!--/span-->

    </div> <!--/row-->

<?php

  $this->load->view('footer');

?>