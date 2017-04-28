<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_tweet_title'] = 'eat.tweet';
$lang['feat_tweet_text'] = 'A tag that publishes a tweet on your Twitter account.
      
      <h3>Requirements</h3>
      <ul>
        <li>You must have linked your Evernote® account with EATags</li>
        <li>And have activated the <a href="'.$lang['config_url'].'/twitter">Twitter access</a></li>
      </ul>

      <h3>HOW-TO</h3>
      <ol>
        <li>Write your tweet on the title of the note</li>
        <li>Tag it using <strong>eat.tweet</strong></li>
        <li>Wait the EATags magic</li>
      </ol>

      <h3>Example</h3>
      <div class="eat-sample">
        <p><strong>Step 1:</strong> Create your note at Evernote®<br/></p>
          <a href="assets/images/photos/eat_tweet1b.jpg"><img src="assets/images/photos/eat_tweet1.jpg" alt="eat.tweet" /></a><br/>
        <p><strong>Step 2:</strong> And EATags publishes it on Twitter!<br/></p>
          <a href="assets/images/photos/eat_tweet2b.jpg"><img src="assets/images/photos/eat_tweet2.jpg" alt="eat.tweet" /></a>
      </div>
        ';