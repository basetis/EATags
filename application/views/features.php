<?php
  $this->load->view('header');

  // PAGE CONTENT - FEATURES
?>
    <div class="page-header">
      <h1><?= $this->lang->line('features') ?> <small></small></h1>
    </div>

    <div class="row">
      <?php
      foreach ($tags as $tag):
      ?>
      <div class="eat-span-feature eat-span-feature2"><a href="f<?= $tag['name'] ?>">
        <h3><?= $tag['name'] ?></h3>
        <div><?= $this->lang->line($tag['description']) ?></div></a>
      </div>
      <?php
      endforeach;
      ?>
      <div class="eat-span-feature">
        <h3>eat.***</h3>
        <div><?= $this->lang->line('features_great') ?></div>
      </div><!--/eat-span-feature-->

      <div class="eat-span-feature your-feature eat-span-feature2"><a href="http://eatags.uservoice.com/forums/166371-general">
        <h3><?= $this->lang->line('features_new') ?></h3>
        <div><?= $this->lang->line('features_new_description') ?></div></a>
      </div><!--/eat-span-feature-->

    </div>

<?php

  $this->load->view('footer');

?>