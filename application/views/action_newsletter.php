<!-- 
e-mail list: añadir diez e-mails?

-->

<? if (isset($status_msg)) { ?>
    <div class="alert alert-success">
        <?=$status_msg;?>
    </div>
<?}?>
<? if (isset($error_msg)) { ?>
    <div class="alert alert-danger">
        <?php echo validation_errors(); ?>
    </div>
<?}?>
<div class="row-fluid">
    <div class="span1">
        <p>List up to 10 e-Mails for your newsletter</p>
    </div><!--/span-->
    <div class="span11">
        <form class="navbar-form pull-left" method="post" action="<?php echo base_url();?>index.php/action_newsletter/save">
            <? for ($index = 0; $index < 10; $index++): 
                $this_email = '';
                if (isset($email[$index]['email']))
                    $this_email = $email[$index]['email'];

            ?>
            <label>e-Mail <?= $index + 1 ?>:</label>
            <input type="text" id="email_<?= $index ?>" name="email_<?= $index ?>" value="<?= set_value('email_'.$index, $this_email) ?>" class="span11">
            <? endfor; ?>
            <input type="submit" class="btn" value="Save"/>
       </form>
       <a class="btn btn-danger" data-toggle="modal" href="#myModal" >Remove Data</a>
    </div><!--/span-->

</div><!--/row-->

<div class="modal hide" id="myModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Remove Newsletter e-Mail List</h3>
    </div>
    <div class="modal-body">
        <p>Do you want to remove your Newsletter e-Mail List?</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">Close</a>
        <a href="<?=base_url();?>index.php/action_newsletter/remove" class="btn btn-danger">Remove</a>
    </div>
</div>