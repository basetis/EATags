<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_tweet_title'] = 'eat.tweet';
$lang['feat_tweet_text'] = 'Una etiqueta que publica un tweet al teu compte de Twitter.

      <h3>Requisits</h3>
      <ul>
        <li>Has d\'haver enllaçat el teu compte d\'Evernote® amb EATags</li>
        <li>I haver activat l\'<a href="'.$lang['config_url'].'/twitter">accés a Twitter</a></li>
      </ul>

      <h3>HOW-TO</h3>
      <ol>
        <li>Escriu el teu tweet al títol de la nota</li>
        <li>Etiqueta-la fent servir <strong>eat.tweet</strong></li>
        <li>Espera la màgia d\'EATags</li>
      </ol>

      <h3>Exemple</h3>
      <div class="eat-sample">
        <p><strong>Pas 1:</strong> Crea la teva nota a Evernote®<br/></p>
          <a href="assets/images/photos/eat_tweet1b.jpg"><img src="assets/images/photos/eat_tweet1.jpg" alt="eat.tweet" /></a><br/>
        <p><strong>Pas 2:</strong> I EATags la publica a Twitter!<br/></p>
          <a href="assets/images/photos/eat_tweet2b.jpg"><img src="assets/images/photos/eat_tweet2.jpg" alt="eat.tweet" /></a>
      </div>
        ';