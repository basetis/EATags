<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_wp_draft_title'] = 'eat.wordpress.draft';
$lang['feat_wp_draft_text'] = 'Una etiqueta que envía un borrador a tu blog de Wordpress.
      
      <h3>Requisitos</h3>
      <ul>
        <li>Tienes que haber enlazado tu cuenta de Evernote® con EATags</li>
        <li>Y haber activado el <a href="'.$lang['config_url'].'/wordpress">acceso a Wordpress</a></li>
      </ul>
      
      <h3>HOW-TO</h3>
      <ol>
        <li>Escribe tu nota como haces normalmente</li>
        <li>Etiquétala utilizando <strong>eat.wordpress.draft</strong></li>
        <li>Espera la magia de EATags</li>
      </ol>
           
      <h3>Ejemplo</h3>
      <div class="eat-sample">
        <p><strong>Paso 1:</strong> Creas tu nota borrador en Evernote®<br/></p>
          <a href="assets/images/photos/eat_wp_draft1b.jpg"><img src="assets/images/photos/eat_wp_draft1.jpg" alt="eat.wordpress.draft" /></a><br/>
        <p><strong>Paso 2:</strong> Y EATags te lo envía a Wordpress!<br/></p>
          <a href="assets/images/photos/eat_wp_draft2b.jpg"><img src="assets/images/photos/eat_wp_draft2.jpg" alt="eat.wordpress.draft" /></a>
      </div>
        ';