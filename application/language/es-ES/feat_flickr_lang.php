<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_flickr_title'] = 'eat.flickr';
$lang['feat_flickr_text'] = 'Una etiqueta que publica una imagen en tu cuenta de Flickr.
      
      <h3>Requisitos</h3>
      <ul>
        <li>Tienes que haber enlazado tu cuenta de Evernote® con EATags</li>
        <li>Y haber activado el <a href="'.$lang['config_url'].'/flickr">acceso a Flickr</a></li>
      </ul>
      
      <h3>HOW-TO</h3>
      <ol>
        <li>Crea tu nota como haces normalmente. El título de la nota será el título de la imagen en Flickr.</li>
        <li>Etiquétala utilizando <strong>eat.flickr</strong></li>
        <li>Espera la magia de EATags</li>
      </ol>
      
      <h3>Ejemplo</h3>
      <div class="eat-sample">
        <p><strong>Paso 1:</strong> Creas tu nota en Evernote®<br/></p>
          <a href="assets/images/photos/eat_flickr1b.jpg"><img src="assets/images/photos/eat_flickr1.jpg" alt="eat.flickr" /></a><br/>
        <p><strong>Paso 2:</strong> Y EATags te la publica en Flickr!<br/></p>
          <a href="assets/images/photos/eat_flickr2b.jpg"><img src="assets/images/photos/eat_flickr2.jpg" alt="eat.flickr" /></a>
      </div>
        ';