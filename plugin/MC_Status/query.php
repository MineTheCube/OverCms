<?php

if (!isset($_POST['query'])) die;

$ip = $_POST['ip'];
$port = $_POST['port'];

$playeronline = file_get_contents('http://api.serveurs-minecraft.com/api.php?Joueurs_En_Ligne_Ping&ip='.$ip.'&port='.$port.''); 
echo $playeronline; 

?>