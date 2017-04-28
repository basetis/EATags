<script type="text/javascript">
    $(document).ready(function() {
        $('#myModal').modal('show');
        $.post("<?php echo base_url('home/evernote_callback')?>",my_evernote_callback,null,'json');
    });

    function my_evernote_callback(){
        window.location.replace("<?php echo base_url('account/features')?>");
    }
</script>

<div class="modal hide" id="myModal">
  <div class="modal-header">
    <h3><?= $this->lang->line('home_encallback_header') ?></h3>
  </div>
  <div class="modal-body">
    <p><?= $this->lang->line('home_encallback_message') ?> <img src="<?php echo base_url('assets/images/ajax-loader.gif');?>"></p>
  </div>
  <div class="modal-footer">
  </div>
</div>