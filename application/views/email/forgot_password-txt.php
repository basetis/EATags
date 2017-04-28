<?= str_replace('?1', (strlen($username) > 0) ? $username : '', $this->lang->line('email_hi')); ?>,

<?= $this->lang->line('email_forgot_instructions') ?>

<?php echo site_url('/auth/reset_password/'.$user_id.'/'.$new_pass_key); ?>


<?= str_replace('?1', $site_name, $this->lang->line('email_forgot_info')) ?>


<?= str_replace('?1', $site_name, $this->lang->line('email_bye')) ?>