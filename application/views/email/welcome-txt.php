<?= str_replace('?1', $site_name, $this->lang->line('email_activate_welcome'))?>,

<?= str_replace('?1', $site_name, $this->lang->line('email_welcome_thanks')) ?>

<?php echo site_url('/auth/login/'); ?>

<?php if (strlen($username) > 0) { ?>

<?= $this->lang->line('email_activate_username') ?> <?php echo $username; ?>
<?php } ?>

<?= $this->lang->line('email_activate_email') ?> <?php echo $email; ?>

<?php /* Your password: <?php echo $password; ?>

*/ ?>

<?= str_replace('?1', $site_name, $this->lang->line('email_activate_bye'))?>