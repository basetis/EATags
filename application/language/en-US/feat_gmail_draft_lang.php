<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_gmail_draft_title'] = 'eat.gmail.draft';
$lang['feat_gmail_draft_text'] = 'A tag that sends a note to your Gmail drafts.

      <h3>Requirements</h3>
      <ul>
        <li>You must have linked your Evernote® account with EATags</li>
        <li>Have activated the <a href="'.$lang['config_url'].'/gmail">Gmail access</a></li>
        <li>And select the language in which your Gmail account is configured</li>
      </ul>

      <h3>HOW-TO</h3>
      <ol>
        <li>Write your note as usual</li>
        <li>Tag it using <strong>eat.gmail.draft</strong></li>
        <li>Wait the EATags magic</li>
      </ol>

      <h3>Example</h3>
      <div class="eat-sample">
        <p><strong>Step 1:</strong> Link and config your Gmail account<br/></p>
          <a href="assets/images/photos/eat_gmail_draft1b.jpg"><img src="assets/images/photos/eat_gmail_draft1.jpg" alt="eat.gmail.draft" /></a><br/>
        <p><strong>Step 2:</strong> Create your draft note on Evernote®<br/></p>
          <a href="assets/images/photos/eat_gmail_draft2b.jpg"><img src="assets/images/photos/eat_gmail_draft2.jpg" alt="eat.gmail.draft" /></a><br/>
        <p><strong>Step 3:</strong> And EATags sends it to Gmail!<br/></p>
          <a href="assets/images/photos/eat_gmail_draft3b.jpg"><img src="assets/images/photos/eat_gmail_draft3.jpg" alt="eat.gmail.draft" /></a>
      </div>
        ';