<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title><?= str_replace('?1', $site_name, $this->lang->line('email_reset_title')) ?></title></head>
<body>
<div style="max-width: 800px; margin: 0; padding: 30px 0;">
<table width="80%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="5%"></td>
<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;"><?= str_replace('?1', $site_name, $this->lang->line('email_reset_title')) ?></h2>
<?= nl2br($this->lang->line('email_reset_info')) ?>
<br />
<br />
<?php if (strlen($username) > 0) { ?><?= $this->lang->line('email_activate_username') ?> <?php echo $username; ?><br /><?php } ?>
<?= $this->lang->line('email_activate_email') ?> <?php echo $email; ?><br />
<?php /* Your new password: <?php echo $new_password; ?><br /> */ ?>
<br />
<br />
<?= nl2br(str_replace('?1', $site_name, $this->lang->line('email_bye'))) ?>
</td>
</tr>
</table>
</div>
</body>
</html>