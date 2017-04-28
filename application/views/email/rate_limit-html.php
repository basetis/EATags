<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title><?= str_replace('?2', $username, $this->lang->line('email_rate_hello'))?>!</title></head>
<body>
<div style="max-width: 800px; margin: 0; padding: 30px 0;">
<table width="80%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="5%"></td>
<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;"><?= str_replace('?2', $username, $this->lang->line('email_rate_hello'))?>!</h2>
<br />
<?= str_replace('?3', $rate_time,$this->lang->line('email_rate_message')) ?>
<br />
<?= $this->lang->line('email_rate_apology') ?>
<br />
<br />
<?= nl2br(str_replace('?1', $site_name, $this->lang->line('email_rate_bye')))?>
</td>
</tr>
</table>
</div>
</body>
</html>