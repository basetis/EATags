<?= str_replace('?1', (strlen($username) > 0) ? $username : '', $this->lang->line('email_hi')); ?>,

<?= $this->lang->line('email_reset_info') ?>
<?php if (strlen($username) > 0) { ?>

<?= $this->lang->line('email_activate_username') ?> <?php echo $username; ?>
<?php } ?>

<?= $this->lang->line('email_activate_email') ?> <?php echo $email; ?>

<?php /* Your new password: <?php echo $new_password; ?>

*/ ?>

<?= str_replace('?1', $site_name, $this->lang->line('email_bye')) ?>