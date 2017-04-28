<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_wp_draft_title'] = 'eat.wordpress.draft';
$lang['feat_wp_draft_text'] = 'Una etiqueta que envia un esborrany a teu blog de Wordpress.

      <h3>Requisits</h3>
      <ul>
        <li>Has d\'haver enllaçat el teu compte d\'Evernote® amb EATags</li>
        <li>I haver activat l\'<a href="'.$lang['config_url'].'/wordpress">accés a Wordpress</a></li>
      </ul>

      <h3>HOW-TO</h3>
      <ol>
        <li>Escriu la teva nota com ho fas normalment</li>
        <li>Etiqueta-la fent servir <strong>eat.wordpress.draft</strong></li>
        <li>Espera la màgia d\'EATags</li>
      </ol>

      <h3>Exemple</h3>
      <div class="eat-sample">
        <p><strong>Pas 1:</strong> Crea la teva nota esborrany a Evernote®<br/></p>
          <a href="assets/images/photos/eat_wp_draft1b.jpg"><img src="assets/images/photos/eat_wp_draft1.jpg" alt="eat.wordpress.draft" /></a><br/>
        <p><strong>Pas 2:</strong> I EATags te l\'envia a Wordpress!<br/></p>
          <a href="assets/images/photos/eat_wp_draft2b.jpg"><img src="assets/images/photos/eat_wp_draft2.jpg" alt="eat.wordpress.draft" /></a>
      </div>
        ';