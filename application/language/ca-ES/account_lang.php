<?php


$lang['account_menu_profile'] = 'Perfil';
$lang['account_menu_summary'] = 'Funcionalitats';
$lang['account_menu_configuration'] = 'Configuració de funcionalitats';

$lang['account_message_ok'] = '<strong>Eaten !</strong> La teva informació s\'ha desat correctament';
$lang['account_message_error'] = '<strong>Ups!</strong> Hi ha hagut un problema amb la base de dades. Intenta-ho de nou més endavant o contacta\'ns.';
$lang['account_message_error_wp'] = '<strong>Ups!</strong> No hem pogut connectar amb el teu blog, estàs segur que les credencials són correctes?';
$lang['account_message_ok_delete_flickr'] = '<strong>Eaten !</strong> La teva informació s\'ha eliminat correctament.<br/>Si vols eliminar el nostre servei del teu compte de Flickr necessites revocar l\'accés des d\' <a href="http://www.flickr.com/services/auth/list.gne?from=extend" target="_blank">aquesta pàgina de Flickr</a>.';
$lang['account_message_ok_delete_gmail'] = '<strong>Eaten !</strong> La teva informació s\'ha eliminat correctament.<br/>Si vols eliminar el nostre servei del teu compte de Gmail necessites revocar l\'accés des d\' <a href="https://security.google.com/settings/security/permissions?hl=ca&pli=1" target="_blank">aquesta pàgina de Gmail</a>.';
$lang['account_message_ok_delete_twitter'] = '<strong>Eaten !</strong> La teva informació s\'ha eliminat correctament.<br/>Si vols eliminar el nostre servei del teu compte de Twitter necessites revocar l\'accés des d\' <a href="https://twitter.com/settings/applications" target="_blank">aquesta pàgina de Twitter</a>.';
$lang['account_message_ok_delete'] = '<strong>Eaten !</strong> La teva informació s\'ha eliminat correctament.';
$lang['account_message_error_delete'] = '<strong>Ups!</strong> Hi ha hagut un problema en intentar esborrar la teva informació. Intenta-ho de nou més endavant o contacta\'ns.';

$lang['account_flickr_header'] = 'Connecta amb Flickr';
$lang['account_flickr_info'] = 'Connecta\'t amb el teu compte de Flickr i puja fotos des d\'Evernote® etiquetant la teva nota amb eat.flickr';
$lang['account_flickr_username'] = 'El teu usuari de Flickr';
$lang['account_flickr_sign_in'] = 'Connecta amb Flickr';
$lang['account_flickr_add_account'] = 'Afegeix el teu compte de Flickr: ';
$lang['account_flickr_deny'] = 'Denegar accés a Flickr';

$lang['account_profile_header'] = 'Configuració del Perfil';
$lang['account_profile_username'] = 'Nom d\'usuari';
$lang['account_profile_password'] = 'Contrasenya';
$lang['account_profile_error_password'] = 'Contrasenya no vàlida';
$lang['account_profile_email'] = 'Email Actual';
$lang['account_profile_new_email'] = 'Nou Email';
$lang['account_profile_new_email_error'] = 'Email no vàlid';
$lang['account_profile_change_email'] = 'Canviar Email';
$lang['account_profile_old_password'] = 'Contrasenya Anterior';
$lang['account_profile_passwords_not_equal'] = 'Però quines coses! Les contrasenyes no són iguals';
$lang['account_profile_new_password'] = 'Nova Contrasenya';
$lang['account_profile_change_password'] = 'Canviar Contrasenya';
$lang['account_profile_unregister'] = 'Donar-se de baixa';
$lang['account_profile_logout'] = 'Desconnectar d\'Evernote®';
$lang['account_profile_mailing_label'] = 'Subscriu-te a la newsletter';
$lang['account_profile_mailing_text'] = 'Marca per rebre les nostres notícies';
$lang['account_profile_mailing_regist'] = 'T\'has subscrit a la nostra newsletter';
$lang['account_profile_mailing_unregist'] = 'T\'has esborrat de la nostra newsletter';

$lang['account_require_en_header'] = 'Primer pas: Ens dones permís per accedir al teu compte d\'Evernote®?';
$lang['account_require_en_info'] = '<p>El nostre servei necessita rebre notificacions d\'Evernote® quan insereixes etiquetes a les teves notes.</p>
                <p>Així que necessitem que ens donis permís per accedir al teu compte d\'Evernote®.</p>';
$lang['account_require_en_login'] = 'Connectar amb Evernote®';

$lang['account_twitter_header'] = 'Connecta amb Twitter';
$lang['account_twitter_info'] = 'Connecta amb el teu compte de Twitter i podràs twittejar des del teu compte d\'Evernote® etiquetant les teves notes amb eat.tweet';
$lang['account_twitter_username'] = 'El teu usuari de Twitter';
$lang['account_twitter_username_change'] = 'Pots canviar el tu usuari de Twitter:';
$lang['account_twitter_sign_in'] = 'Connecta amb Twitter';
$lang['account_twitter_deny'] = 'Denegar accés a Twitter';

$lang['account_wp_header'] = 'Connecta amb Wordpress';
$lang['account_wp_info'] = 'Necessites <a href="http://codex.wordpress.org/XML-RPC_Support" target="_blank">activar suport Wordpress XML-RPC</a>
                al teu blog.<br/><br/>
                Recomanem firmement que <a href="http://codex.wordpress.org/Users_Add_New_Screen" target="_blank">creis un usuari que no sigui administrador</a>
                al teu blog i facis servir aquest usuari per a aquesta funcionalitat. Un usuari amb rol d\'Autor hauria de ser suficient.';
$lang['account_wp_url'] = 'L\'URL del teu blog';
$lang['account_wp_username'] = 'El teu usuari de Wordpress';
$lang['account_wp_password'] = 'La teva contrasenya de Wordpress';
$lang['account_wp_apply'] = 'Desar Canvis';
$lang['account_wp_deny'] = 'Denegar accés a Wordpress';

$lang['account_add_header'] = 'Configura la teva Plantilla';
$lang['account_add_info'] = '<ul>
                                    <li>Selecciona la llibreta (notebook) on està la nota que vols fer servir com a header o footer.</li>
                                    <li>Selecciona la nota.</li>
                                    <li>A Evernote® utilitza les etiquetes:
                                        <ul>
                                            <li><strong>eat.add.header</strong>: per a inserir la capçalera.</li>
                                            <li><strong>eat.add.footer</strong>: per a inserir el peu de pàgina.</li>
                                            <li><strong>eat.add.surround</strong>: per a inserir ambdós.</li>
                                        </ul>
                                    </li>
                                    <li>Dóna-li al aspa vermella per reiniciar la selecció.</li>
                                    <li>O simplement selecciona una altra nota per canviar d\'opció.</li>
                                </ul>';
$lang['account_add_notebooks'] = 'Llibretes';
$lang['account_add_notes'] = 'Notes';
$lang['account_add_header_info'] = 'Tria la teva nota com Header';
$lang['account_add_footer_info'] = 'Tria la teva nota com Footer';
$lang['account_add_select_notebook'] = 'Tria una llibreta';
$lang['account_add_select_note'] = 'Tria una nota';
$lang['account_add_loading'] = 'Carregant...';
$lang['account_add_sent'] = 'Dades enviades';
$lang['account_add_reset_header'] = 'Reinicia la selecció de Header';
$lang['account_add_reset_footer'] = 'Reinicia la selecció de Footer';
$lang['account_add_reset'] = 'Dades reiniciades';
$lang['account_add_no_header'] = 'Header no seleccionat';
$lang['account_add_no_footer'] = 'Footer no seleccionat';
$lang['account_add_no_footer_no_header'] = 'Ni el Header ni el Footer han estat seleccionats';
$lang['account_add_go_config'] = 'Ves a configuració';

$lang['account_latex_header'] = 'La teva configuració <i>LaTeX</i>';
$lang['account_latex_form_title'] = 'Vols eliminar les $$fórmules en format text$$ un cop processades?';
$lang['account_latex_form_label'] = 'Eliminar $$fórmules$$';
$lang['account_latex_form_yes'] = 'Sí';
$lang['account_latex_sent'] = 'La teva selecció ha estat enviada';
$lang['account_latex_form_key_title'] = 'Vols canviar els $$caràcters$$ que envolten les teves fórmules?';
$lang['account_latex_form_key_label'] = 'Escriu la teva pròpia combinació';
$lang['account_latex_form_key_char'] = '2-5 caràcters';
$lang['account_latex_form_key_send'] = 'Envia combinació';
$lang['account_latex_key_sent'] = 'La teva combinació ha estat enviada';
$lang['account_latex_key_short'] = 'Aquesta combinació és massa curta';
$lang['account_latex_key_reset_a'] = 'Reinicia la combinació';
$lang['account_latex_key_reset'] = 'La combinació ha estat reiniciada';
$lang['account_latex_inline_title'] = 'Vols que les imatges s\'insereixin en línia amb el text?';
$lang['account_latex_inline_label'] = 'Inserir en línia';

$lang['account_gmail_header'] = 'Connecta amb Gmail';
$lang['account_gmail_info'] = 'Connecta\'t amb el teu compte de Gmail, selecciona l\'idioma i envia esborranys des d\'Evernote® etiquetant la teva nota amb eat.gmail.draft';
$lang['account_gmail_username'] = 'El teu usuari de Gmail:';
$lang['account_gmail_username_change'] = 'Pots canviar el teu usuari de Gmail:';
$lang['account_gmail_sign_in'] = 'Connecta amb Gmail';
$lang['account_gmail_add_account'] = 'Afegeix el teu compte de Gmail: ';
$lang['account_gmail_deny'] = 'Denegar accés a Gmail';
$lang['account_gmail_lang'] = 'Si us plau, en quin idioma tens configurat el teu compte de Gmail®?';
$lang['account_gmail_lang_alert'] = 'Idioma seleccionat correctament';

$lang['account_rate_limit'] = 'El teu compte ha assolit el límit de peticions a Evernote®. Les peticions pendents seran processades en ';