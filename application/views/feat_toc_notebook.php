<?php
  $this->load->view('header');

  // PAGE CONTENT - FEATURES - TOC NOTEBOOK
?>
    <div class="page-header">
      <h1><?= $this->lang->line('feat_toc_notebook_title') ?></h1>
    </div>

    <div class="row">
        <div class="span12">
          <?= $this->lang->line('feat_toc_notebook_text') ?>
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