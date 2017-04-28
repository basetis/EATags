<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_flickr_title'] = 'eat.flickr';
$lang['feat_flickr_text'] = 'Una etiqueta que publica una imatge al teu compte de Flickr.

      <h3>Requisits</h3>
      <ul>
        <li>Has d\'haver enllaçat el teu compte d\'Evernote® amb EATags</li>
        <li>I haver activat l\' <a href="'.$lang['config_url'].'/flickr">accés a Flickr</a></li>
      </ul>

      <h3>HOW-TO</h3>
      <ol>
        <li>Crea la teva nota com ho fas normalment. El títol de la nota serà el títol de la imatge a Flickr.</li>
        <li>Etiqueta-la fent servir <strong>eat.flickr</strong></li>
        <li>Espera la màgia d\'EATags</li>
      </ol>

      <h3>Exemple</h3>
      <div class="eat-sample">
        <p><strong>Pas 1:</strong> Crea la teva nota a Evernote®<br/></p>
          <a href="assets/images/photos/eat_flickr1b.jpg"><img src="assets/images/photos/eat_flickr1.jpg" alt="eat.flickr" /></a><br/>
        <p><strong>Pas 2:</strong> I EATags te la publica a Flickr!<br/></p>
          <a href="assets/images/photos/eat_flickr2b.jpg"><img src="assets/images/photos/eat_flickr2.jpg" alt="eat.flickr" /></a>
      </div>
        ';