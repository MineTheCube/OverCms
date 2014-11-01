<?php

$content = "Contenu de la page chargé par le controller <b>error.php</b>.";
$content .= "<br><br>Type de l'erreur: ";

if ( $request['args'] == 404 ) {
    $content .= '{@PAGE_NOT_FOUND}';
    header("HTTP/1.0 404 Not Found");
} else if ( $request['args'] == 403 ) {
    $content .= '{@NO_PERMISSION}';
    header('HTTP/1.1 403 Forbidden');
} else {
    $content = 'Erreur inconnue...';
}