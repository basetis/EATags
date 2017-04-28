<?php


$lang['account_menu_profile'] = 'Profile';
$lang['account_menu_summary'] = 'Features';
$lang['account_menu_configuration'] = 'Features Configuration';

$lang['account_message_ok'] = '<strong>Eaten !</strong> Your data was correctly stored.';
$lang['account_message_error'] = '<strong>Oops!</strong> There was a problem while trying to store your data on the db. Try again later or contact us.';
$lang['account_message_error_wp'] = '<strong>Oops!</strong> We can\'t connect to your blog, are you sure that the credentials are correct?';
$lang['account_message_ok_delete_flickr'] = '<strong>Eaten !</strong> Your data was correctly deleted.<br/>If you want to remove our service from your Flickr account you need to revoke access from <a href="http://www.flickr.com/services/auth/list.gne?from=extend" target="_blank">this Flickr page</a>.';
$lang['account_message_ok_delete_gmail'] = '<strong>Eaten !</strong> Your data was correctly deleted.<br/>If you want to remove our service from your Gmail account you need to revoke access from <a href="https://security.google.com/settings/security/permissions?hl=en&pli=1" target="_blank">this Gmail page</a>.';
$lang['account_message_ok_delete_twitter'] = '<strong>Eaten !</strong> Your data was correctly deleted.<br/>If you want to remove our service from your Twitter account you need to revoke access from <a href="https://twitter.com/settings/applications" target="_blank">this Twitter page</a>.';
$lang['account_message_ok_delete'] = '<strong>Eaten !</strong> Your data was correctly deleted.';
$lang['account_message_error_delete'] = '<strong>Oops !</strong> There was a problem while deleting your information. Try again later or contact us.';

$lang['account_flickr_header'] = 'Your Flickr Configuration';
$lang['account_flickr_info'] = 'Sign in with your Flickr account and upload photos from Evernote® tagging your notes with eat.flickr';
$lang['account_flickr_username'] = 'You are logged with user';
$lang['account_flickr_sign_in'] = 'Sign in with Flickr';
$lang['account_flickr_add_account'] = 'Add your Flickr account: ';
$lang['account_flickr_deny'] = 'Deny Flickr Access';

$lang['account_profile_header'] = 'Your Profile Configuration';
$lang['account_profile_username'] = 'Username';
$lang['account_profile_password'] = 'Password';
$lang['account_profile_error_password'] = 'Invalid Password';
$lang['account_profile_email'] = 'Current Email Address';
$lang['account_profile_new_email'] = 'New Email Address';
$lang['account_profile_new_email_error'] = 'Invalid Email Address';
$lang['account_profile_change_email'] = 'Change Email Account';
$lang['account_profile_old_password'] = 'Old Password';
$lang['account_profile_passwords_not_equal'] = 'What a nuisance! Passwords were not equals';
$lang['account_profile_new_password'] = 'New Password';
$lang['account_profile_change_password'] = 'Change Password';
$lang['account_profile_unregister'] = 'Unregister';
$lang['account_profile_logout'] = 'Logout from Evernote®';
$lang['account_profile_mailing_label'] = 'Subscribe to newsletter';
$lang['account_profile_mailing_text'] = 'Tick to recieve our news';
$lang['account_profile_mailing_regist'] = 'You have subscribed to our newsletter';
$lang['account_profile_mailing_unregist'] = 'You have deleted your subscription';

$lang['account_require_en_header'] = 'First Step: allow us to access your Evernote® account';
$lang['account_require_en_info'] = '<p>Our service needs to receive Evernote® notifications when you type our tags into your note.</p>
                <p>So you must allow us access to your Evernote® account.</p>';
$lang['account_require_en_login'] = 'Log in to Evernote®';

$lang['account_twitter_header'] = 'Your Twitter Configuration';
$lang['account_twitter_info'] = 'Sign in with your Twitter account and tweet from Evernote® tagging your notes with eat.tweet';
$lang['account_twitter_username'] = 'You are logged with user';
$lang['account_twitter_username_change'] = 'You can change your user sign in again with another user:';
$lang['account_twitter_sign_in'] = 'Sign in with Twitter';
$lang['account_twitter_deny'] = 'Deny Twitter Access';

$lang['account_wp_header'] = 'Your Wordpress Configuration';
$lang['account_wp_info'] = 'You need to <a href="http://codex.wordpress.org/XML-RPC_Support" target="_blank">activate Wordpress XML-RPC Support</a>
                on your blog.<br/><br/>
                We strongly recommend you <a href="http://codex.wordpress.org/Users_Add_New_Screen" target="_blank">create a non-admin user</a>
                on your blog and use this user for this feature. Author Role should be fine.';
$lang['account_wp_url'] = 'Your Blog URL';
$lang['account_wp_username'] = 'Your Wordpress user name';
$lang['account_wp_password'] = 'Your Wordpress password';
$lang['account_wp_apply'] = 'Apply Configuration';
$lang['account_wp_deny'] = 'Deny Wordpress Access';

$lang['account_add_header'] = 'Your Add Configuration';
$lang['account_add_info'] = '<ul>
                                    <li>Select the notebook where the note to use as header or footer is.</li>
                                    <li>Select the note.</li>
                                    <li>On Evernote® use the tags:
                                        <ul>
                                            <li><strong>eat.add.header</strong>: to insert a header.</li>
                                            <li><strong>eat.add.footer</strong>: to insert a footer.</li>
                                            <li><strong>eat.add.surround</strong>: to insert both of them.</li>
                                        </ul>
                                    </li>
                                    <li>Press the red cross to reset choices.</li>
                                    <li>Or simply select another note to change the option.</li>
                                </ul>';
$lang['account_add_notebooks'] = 'Notebooks';
$lang['account_add_notes'] = 'Notes';
$lang['account_add_header_info'] = 'Select your Header Note';
$lang['account_add_footer_info'] = 'Select your Footer Note';
$lang['account_add_select_notebook'] = 'Select a notebook';
$lang['account_add_select_note'] = 'Select a note';
$lang['account_add_loading'] = 'Loading...';
$lang['account_add_sent'] = 'The data has been sent';
$lang['account_add_reset_header'] = 'Reset Header selections';
$lang['account_add_reset_footer'] = 'Reset Footer selections';
$lang['account_add_reset'] = 'Tha data has been reset';
$lang['account_add_no_header'] = 'Header not selected';
$lang['account_add_no_footer'] = 'Footer not selected';
$lang['account_add_no_footer_no_header'] = 'Neither the Header nor Footer are selected';
$lang['account_add_go_config'] = 'Go to configuration';

$lang['account_latex_header'] = 'Your <i>LaTeX</i> Configuration';
$lang['account_latex_form_title'] = 'Do you want to delete $$text formulas$$ once has been processed?';
$lang['account_latex_form_label'] = 'Delete $$formulas$$';
$lang['account_latex_form_yes'] = 'Yes';
$lang['account_latex_sent'] = 'Your selection has been sent';
$lang['account_latex_form_key_title'] = 'Do you want to change the default $$key$$?';
$lang['account_latex_form_key_label'] = 'Write your own combination';
$lang['account_latex_form_key_char'] = '2-5 characters';
$lang['account_latex_form_key_send'] = 'Send key';
$lang['account_latex_key_sent'] = 'Your combination has been sent';
$lang['account_latex_key_short'] = 'This combination is too short';
$lang['account_latex_key_reset_a'] = 'Reset combination';
$lang['account_latex_key_reset'] = 'Combination has been reset';
$lang['account_latex_inline_title'] = 'Do you want to insert the image inline with the text?';
$lang['account_latex_inline_label'] = 'Insert inline';

$lang['account_gmail_header'] = 'Your Gmail Configuration';
$lang['account_gmail_info'] = 'Sign in with your Gmail account, select a language and send drafts from Evernote® tagging your notes with eat.gmail.draft';
$lang['account_gmail_username'] = 'You are logged with user:';
$lang['account_gmail_username_change'] = 'You can change your gmail account, sign in again with another user:';
$lang['account_gmail_sign_in'] = 'Sign in with Gmail';
$lang['account_gmail_add_account'] = 'Add your Gmail account: ';
$lang['account_gmail_deny'] = 'Deny Gmail Access';
$lang['account_gmail_lang'] = 'Please, in which language you have your Gmail® account?';
$lang['account_gmail_lang_alert'] = 'Language selected correctly';

$lang['account_rate_limit'] = 'Your account has reached the rate limit requests to Evernote®. Your pending requests will be processed at ';
