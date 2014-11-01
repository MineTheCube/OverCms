<?php

$data = json_decode( $data );

$address = $data->address;
$button = $data->dl-button;

$content = '
<style>
#ts {
    text-align: center;
}
#ts>img {
    width: 100%;
    margin: 0 0 20px 0;
}
#ts>.btn-ts {
    color: #fff;
    width: 50%;
    padding: 10px 0 10px 0;
    display: inline-block;
    background-color: #9E9E9E;
}
#ts>#btn-ts-l {
    float: left;
    border-bottom-left-radius: 5px;
    border-top-left-radius: 5px;
}
#ts>#btn-ts-r {
    border-bottom-right-radius: 5px;
    border-top-right-radius: 5px;
}
#ts>.btn-ts:hover {
    background-color: #858585;
    text-decoration: none;
}
</style>
';

$content .= '
<div id="ts">
  <img src="http://i.imgur.com/PGKQdVW.png" alt="TeamSpeak"/>';

$content .= '
  <a href="http://www.teamspeak.com/?page=downloads" target="_blank" class="btn-ts" id="btn-ts-l">Télécharger TS3</a>
  <a href="ts3server://' . $address . '/" class="btn-ts" id="btn-ts-r">Ajouter le serveur</a>
</div>
';


?>