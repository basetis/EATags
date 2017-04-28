<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_wp_post_title'] = 'eat.wordpress.post';
$lang['feat_wp_post_text'] = 'Una etiqueta que publica un post al teu blog de Wordpress.

      <h3>Requisits</h3>
      <ul>
        <li>Has d\'haver enllaçat el teu compte d\'Evernote® amb EATags</li>
        <li>I haver activat l\'<a href="'.$lang['config_url'].'/wordpress">accés a Wordpress</a></li>
      </ul>

      <h3>HOW-TO</h3>
      <ol>
        <li>Escriu la teva nota com ho fas normalment</li>
        <li>Etiqueta-la fent servir <strong>eat.wordpress.post</strong></li>
        <li>Espera la màgia d\'EATags</li>
      </ol>

      <h3>Exemple</h3>
      <div class="eat-sample">
        <p><strong>Pas 1:</strong> Crea la teva nota a Evernote®<br/></p>
          <a href="assets/images/photos/eat_wp_post1b.jpg"><img src="assets/images/photos/eat_wp_post1.jpg" alt="eat.wordpress.post" /></a><br/>
        <p><strong>Pas 2:</strong> I EATags te la publica a Wordpress!<br/></p>
          <a href="assets/images/photos/eat_wp_post2b.jpg"><img src="assets/images/photos/eat_wp_post2.jpg" alt="eat.wordpress.post" /></a>
      </div>
        ';