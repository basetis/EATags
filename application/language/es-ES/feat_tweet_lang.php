<?php
$CI = get_instance();

$lang['config_url'] = base_url($CI->lang->line('header_link_account_features'));
$lang['feat_tweet_title'] = 'eat.tweet';
$lang['feat_tweet_text'] = 'Una etiqueta que publica un tweet en tu cuenta de Twitter.
      
      <h3>Requisitos</h3>
      <ul>
        <li>Tienes que haber enlazado tu cuenta de Evernote® con EATags</li>
        <li>Y haber activado el <a href="'.$lang['config_url'].'/twitter">acceso a Twitter</a></li>
      </ul>
      
      <h3>HOW-TO</h3>
      <ol>
        <li>Escribe tu tweet en el título de la nota</li>
        <li>Etiquétala utilizando <strong>eat.tweet</strong></li>
        <li>Espera la magia de EATags</li>
      </ol>
      
      <h3>Ejemplo</h3>
      <div class="eat-sample">
        <p><strong>Paso 1:</strong> Creas tu nota en Evernote®<br/></p>
          <a href="assets/images/photos/eat_tweet1b.jpg"><img src="assets/images/photos/eat_tweet1.jpg" alt="eat.tweet" /></a><br/>
        <p><strong>Paso 2:</strong> Y EATags te la publica en Twitter!<br/></p>
          <a href="assets/images/photos/eat_tweet2b.jpg"><img src="assets/images/photos/eat_tweet2.jpg" alt="eat.tweet" /></a>
      </div>
        ';