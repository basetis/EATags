<?= str_replace('?1', (strlen($username) > 0) ? $username : '', $this->lang->line('email_hi')) ?>,

<?= str_replace('?1', $site_name, $this->lang->line('email_change_new_email')) ?>

<?php echo site_url('/auth/reset_email/'.$user_id.'/'.$new_email_key); ?>


<?= $this->lang->line('email_change_your_new_email') ?> <?php echo $new_email; ?>


<?= str_replace('?1', site_url(''), str_replace('?2', $site_name, $this->lang->line('email_change_info'))) ?><br />


<?= str_replace('?1', $site_name, $this->lang->line('email_bye')) ?>