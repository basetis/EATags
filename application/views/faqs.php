<?php
  $this->load->view('header');

  // PAGE CONTENT - FAQS
  $faqs_text = array(
  );

  for ($index = 1; $index < 16; $index++){
    $faqs_text[]=array(
      'title' => $this->lang->line('faq_question_'.$index),
      'answer' => $this->lang->line('faq_answer_'.$index),
    );
  }
?>
    <div class="page-header">
      <h1>FAQs <small></small></h1>
    </div>

    <div class="row">
        <div class="span12">
            <div class="accordion" id="faqs">
<?php
        $i = 0;
        foreach ($faqs_text as $faq) {
          $i++;
?>
              <div class="accordion-group">
                <div class="accordion-heading">
                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#faqs" href="#collapse_<?php echo $i;?>">
                    <?php echo $faq['title']; ?>
                  </a>
                </div>
                <div id="collapse_<?php echo $i;?>" class="accordion-body collapse" style="height: 0px; ">
                  <div class="accordion-inner">
                    <?php echo $faq['answer']; ?>
                  </div>
                </div>
              </div><!--accordion-group-->
<?php   } ?>
            </div><!--/accordion-->
    </div> <!--/row-->

<?php

  $this->load->view('footer');

?>