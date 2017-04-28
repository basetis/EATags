<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_gmail_draft_title'] = 'eat.gmail.draft';
$lang['feat_gmail_draft_text'] = 'Una etiqueta que envia una nota als teus esborranys de Gmail.

      <h3>Requisits</h3>
      <ul>
        <li>Has d\'haver enllaçat el teu compte d\'Evernote® amb EATags</li>
        <li>Haver activat l\' <a href="'.$lang['config_url'].'/gmail">accés a Gmail</a></li>
        <li>I seleccionar l\'idioma amb el que tens configurat el teu compte de Gmail</li>
      </ul>

      <h3>HOW-TO</h3>
      <ol>
        <li>Escriu la teva nota com ho fas normalment</li>
        <li>Etiqueta-la fent servir <strong>eat.gmail.draft</strong></li>
        <li>Espera la màgia d\'EATags</li>
      </ol>

      <h3>Exemple</h3>
      <div class="eat-sample">
        <p><strong>Pas 1:</strong> Enllaça i configura el teu compte de Gmail<br/></p>
          <a href="assets/images/photos/eat_gmail_draft1b.jpg"><img src="assets/images/photos/eat_gmail_draft1.jpg" alt="eat.gmail.draft" /></a><br/>
        <p><strong>Pas 2:</strong> Crea la teva nota esborrany a Evernote®<br/></p>
          <a href="assets/images/photos/eat_gmail_draft2b.jpg"><img src="assets/images/photos/eat_gmail_draft2.jpg" alt="eat.gmail.draft" /></a><br/>
        <p><strong>Pas 3:</strong> I EATags t\'ho envia a gmail!<br/></p>
          <a href="assets/images/photos/eat_gmail_draft3b.jpg"><img src="assets/images/photos/eat_gmail_draft3.jpg" alt="eat.gmail.draft" /></a>
      </div>
        ';