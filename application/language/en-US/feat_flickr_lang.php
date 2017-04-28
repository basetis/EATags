<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_flickr_title'] = 'eat.flickr';
$lang['feat_flickr_text'] = 'A tag that publishes a photo on your Flickr account.
      
      <h3>Requirements</h3>
      <ul>
        <li>You must have linked your Evernote® account with EATags</li>
        <li>And have activated the <a href="'.$lang['config_url'].'/flickr">Flickr access</a></li>
      </ul>

      <h3>HOW-TO</h3>
      <ol>
        <li>Create your note as usual. The title of the note will be the title of the photo on Flickr</li>
        <li>Tag it using <strong>eat.flickr</strong></li>
        <li>Wait the EATags magic</li>
      </ol>      
     
      <h3>Example</h3>
      <div class="eat-sample">
        <p><strong>Step 1:</strong> Create your note at Evernote®<br/></p>
          <a href="assets/images/photos/eat_flickr1b.jpg"><img src="assets/images/photos/eat_flickr1.jpg" alt="eat.flickr" /></a><br/>
        <p><strong>Step 2:</strong> And EATags publishes it on Flickr!<br/></p>
          <a href="assets/images/photos/eat_flickr2b.jpg"><img src="assets/images/photos/eat_flickr2.jpg" alt="eat.flickr" /></a>
      </div>
        ';