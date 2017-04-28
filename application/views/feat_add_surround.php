<?php
  $this->load->view('header');

  // PAGE CONTENT - FEATURES - ADD SURROUND
?>
    <div class="page-header">
      <h1><?= $this->lang->line('feat_add_surround_title') ?></h1>
    </div>

    <div class="row">
        <div class="span12">
          <?= $this->lang->line('feat_add_surround_text') ?>
        </div><!--/span-->
        <div class="eat-span-feature-back"><a href="<?php echo base_url($this->lang->line('header_link_features')); ?>">
            <h4><?php
            $this->lang->load('features');
            echo $this->lang->line('features_back');
            ?></h4></a>
        </div><!--/feature back button-->

    </div> <!--/row-->

<?php

  $this->load->view('footer');

?>