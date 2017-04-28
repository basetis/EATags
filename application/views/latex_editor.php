
<style type="text/css" media="screen">
    img {
        max-width: none;
    }
</style>

<script src="https://www.wiris.net/demo/editor/editor"></script>
<div id="server_msg_div"></div>
    <div  id="main_slogan">
      <h2><?= $this->lang->line('latex_editor'); ?></h2>
    </div><!--/row-->
<div>
    <?php echo $this->lang->line('latex_editor_description'); ?>
</div>

<br/>
<div>
    <button id="update_on_evernote" class="btn eat-btn-success"><?= $this->lang->line('latex_editor_update_btn'); ?></button>
</div>


<br/>
<div id="editorContainer" ></div>

<div id="latex_text" class="alert alert-info">
    <?php echo $this->lang->line('latex_editor_latex_text_div_title'); ?>
</div>



<script type="text/javascript" >
    <?php
        $inital_formula = ($formula_text) ? $formula_text : '<math xmlns=\'http://www.w3.org/1998/Math/MathML\'><mfrac><mn>1</mn><mn>2</mn></mfrac></math>';
    ?>
    var formula_id = "<?php echo $this->input->get('formula', TRUE); ?>";
    var initial_formula = "<?php echo $inital_formula ?>";
    var send_to_evernote_url = "<?=base_url('latex_editor/send_to_evernote');?>"
    var latex_editor_latex_text_div_title = "<?=$this->lang->line('latex_editor_latex_text_div_title');?>"
</script>
<script src="<?=base_url('assets/js/wiris_latex.js');?>" type="text/javascript"></script>
