<?= str_replace('?1', $site_name, $this->lang->line('email_activate_welcome'))?>,

<?= str_replace('?1', $site_name, $this->lang->line('email_activate_thanks'))?>

<?php echo site_url('/auth/activate/'.$user_id.'/'.$new_email_key); ?>

<?= str_replace('?1', $activation_period, $this->lang->line('email_activate_please'))?>
<?php if (strlen($username) > 0) { ?>

<?= $this->lang->line('email_activate_username') ?> <?php echo $username; ?>
<?php } ?>

<?= $this->lang->line('email_activate_email') ?> <?php echo $email; ?>
<?php if (isset($password)) { /* ?>

Your password: <?php echo $password; ?>
<?php */ } ?>



<?= str_replace('?1', $site_name, $this->lang->line('email_activate_bye'))?>