<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_wp_draft_title'] = 'eat.wordpress.draft';
$lang['feat_wp_draft_text'] = 'A tag that sends a draft to your Wordpress blog.
      
      <h3>Requirements</h3>
      <ul>
        <li>You must have linked your Evernote® account with EATags</li>
        <li>And have activated the <a href="'.$lang['config_url'].'/wordpress">Wordpress access</a></li>
      </ul>

      <h3>HOW-TO</h3>
      <ol>
        <li>Write your note as usual</li>
        <li>Tag it using <strong>eat.wordpress.draft</strong></li>
        <li>Wait the EATags magic</li>
      </ol>      
     
      <h3>Example</h3>
      <div class="eat-sample">
        <p><strong>Step 1:</strong> Create your draft note on Evernote®<br/></p>
          <a href="assets/images/photos/eat_wp_draft1b.jpg"><img src="assets/images/photos/eat_wp_draft1.jpg" alt="eat.wordpress.draft" /></a><br/>
        <p><strong>Step 2:</strong> And EATags sends it to Wordpress!<br/></p>
          <a href="assets/images/photos/eat_wp_draft2b.jpg"><img src="assets/images/photos/eat_wp_draft2.jpg" alt="eat.wordpress.draft" /></a>
      </div>
        ';