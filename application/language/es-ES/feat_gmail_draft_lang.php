<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_gmail_draft_title'] = 'eat.gmail.draft';
$lang['feat_gmail_draft_text'] = 'Una etiqueta que envía una nota a tus borradores de Gmail.

      <h3>Requisitos</h3>
      <ul>
        <li>Tienes que haber enlazado tu cuenta de Evernote® con EATags</li>
        <li>Haber activado el <a href="'.$lang['config_url'].'/gmail">acceso a Gmail</a></li>
        <li>Y seleccionar el idioma en el que tienes configurada tu cuenta de Gmail</li>
      </ul>

      <h3>HOW-TO</h3>
      <ol>
        <li>Escribe tu nota como haces normalmente</li>
        <li>Etiquétala utilizando <strong>eat.gmail.draft</strong></li>
        <li>Espera la magia de EATags</li>
      </ol>

      <h3>Ejemplo</h3>
      <div class="eat-sample">
        <p><strong>Paso 1:</strong> Enlaza y configura tu cuenta de Gmail<br/></p>
          <a href="assets/images/photos/eat_gmail_draft1b.jpg"><img src="assets/images/photos/eat_gmail_draft1.jpg" alt="eat.gmail.draft" /></a><br/>
        <p><strong>Paso 2:</strong> Creas tu nota borrador en Evernote®<br/></p>
          <a href="assets/images/photos/eat_gmail_draft2b.jpg"><img src="assets/images/photos/eat_gmail_draft2.jpg" alt="eat.gmail.draft" /></a><br/>
        <p><strong>Paso 3:</strong> Y EATags te lo envía a gmail!<br/></p>
          <a href="assets/images/photos/eat_gmail_draft3b.jpg"><img src="assets/images/photos/eat_gmail_draft3.jpg" alt="eat.gmail.draft" /></a>
      </div>
        ';