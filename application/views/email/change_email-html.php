<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title><?= str_replace('?1', $site_name, $this->lang->line('email_change_new_email')); ?></title></head>
<body>
<div style="max-width: 800px; margin: 0; padding: 30px 0;">
<table width="80%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="5%"></td>
<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;"><?= str_replace('?1', $site_name, $this->lang->line('email_change_new_email')); ?></h2>
<?= nl2br(str_replace('?1', $site_name, $this->lang->line('email_change_new_email'))); ?>
<br />
<br />
<big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="<?php echo site_url('/auth/reset_email/'.$user_id.'/'.$new_email_key); ?>" style="color: #3366cc;"><?= $this->lang->line('email_change_confirm'); ?></a></b></big><br />
<br />
<?= $this->lang->line('email_link_error'); ?><br />
<nobr><a href="<?php echo site_url('/auth/reset_email/'.$user_id.'/'.$new_email_key); ?>" style="color: #3366cc;"><?php echo site_url('/auth/reset_email/'.$user_id.'/'.$new_email_key); ?></a></nobr><br />
<br />
<br />
<?= $this->lang->line('email_change_your_new_email'); ?> <?php echo $new_email; ?><br />
<br />
<br />
<?= str_replace('?1', site_url(''), str_replace('?2', $site_name, $this->lang->line('email_change_info'))); ?><br />
<br />
<br />
<?= nl2br(str_replace('?1', $site_name, $this->lang->line('email_bye'))); ?>
</td>
</tr>
</table>
</div>
</body>
</html>