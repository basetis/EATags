<?php

$lang['db_invalid_connection_str'] = 'No s\'ha pogut determinar la configuració de la base de dades basant-se amb la cadena proporcionada.';
$lang['db_unable_to_connect'] = 'No s\'ha pogut connectar al servidor de base de dades fent servir la configuració subministrada.';
$lang['db_unable_to_select'] = 'No s\'ha pogut seleccionar la base de dades especificada: %s';
$lang['db_unable_to_create'] = 'No s\'ha pogut crear la base de dades especificada: %s';
$lang['db_invalid_query'] = 'La consulta enviada no és vàlida.';
$lang['db_must_set_table'] = 'Ha d\'especificar la taula que serà utilitzada a la seva consulta.';
$lang['db_must_use_set'] = 'Ha d\'usar el mètode "SET" per actualitzar una entrada.';
$lang['db_must_use_index'] = 'Ha d\'especificar un índex que coincideixi per les actualitzacions per lots.';
$lang['db_batch_missing_index'] = 'Una o més columnes sotmeses al procés d\'actualització per lot no es troba a l\'índex especificat.';
$lang['db_must_use_where'] = 'Les actualitzacions no estan permeses a menys que continguin una clàusula "WHERE".';
$lang['db_del_must_use_where'] = 'Les eliminacions no estan permeses a menys que continguin una clàusula "WHERE" o "LIKE".';
$lang['db_field_param_missing'] = 'Per a retornar camps cal el nom de la taula com a paràmetre.';
$lang['db_unsupported_function'] = 'Aquesta característica no està disponible per la base de dades que està fent servir.';
$lang['db_transaction_failure'] = 'Fallada a la transacció: Rollback executat';
$lang['db_unable_to_drop'] = 'No s\'ha pogut eliminar la base de dades especificada.';
$lang['db_unsuported_feature'] = 'Característica no suportada per la plataforma de base de dades que està fent servir.';
$lang['db_unsuported_compression'] = 'El format de compressió de fitxers que ha seleccionat no està suportat pel seu servidor.';
$lang['db_filepath_error'] = 'No es poden escriure les dades a la ruta de fitxer que ha proporcionat.';
$lang['db_invalid_cache_path'] = 'La ruta de la cache que ha proporcionat no és vàlida o no es pot escriure a la mateixa.';
$lang['db_table_name_required'] = 'Cal el nom d\'una taula per aquesta operació.';
$lang['db_column_name_required'] = 'Cal el nom d\'una columna per aquesta operació.';
$lang['db_column_definition_required'] = 'Cal una definició de columna per aquesta operació.';
$lang['db_unable_to_set_charset'] = 'Impossible establir el joc de caracters de connexió del client: %s';
$lang['db_error_heading'] = 'S\'a produït un error amb la base de dades';
?>