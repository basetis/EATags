<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_wp_post_title'] = 'eat.wordpress.post';
$lang['feat_wp_post_text'] = 'A tag that publishes a post in your Wordpress blog.
      
      <h3>Requirements</h3>
      <ul>
        <li>You must have linked your Evernote® account with EATags</li>
        <li>And have activated the <a href="'.$lang['config_url'].'/wordpress">Wordpress access</a></li>
      </ul>

      <h3>HOW-TO</h3>
      <ol>
        <li>Write your note as usual</li>
        <li>Tag it using <strong>eat.wordpress.post</strong></li>
        <li>Wait the EATags magic</li>
      </ol>
     
      <h3>Example</h3>
      <div class="eat-sample">
        <p><strong>Step 1:</strong> Create your note at Evernote®<br/></p>          
          <a href="assets/images/photos/eat_wp_post1b.jpg"><img src="assets/images/photos/eat_wp_post1.jpg" alt="eat.wordpress.post" /></a><br/>        
        <p><strong>Step 2:</strong> And EATags publishes it on Wordpress!<br/></p>          
          <a href="assets/images/photos/eat_wp_post2b.jpg"><img src="assets/images/photos/eat_wp_post2.jpg" alt="eat.wordpress.post" /></a> 
      </div>
        ';