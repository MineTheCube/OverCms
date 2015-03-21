<?php

defined('IN_ENV') or die;

$data = json_decode($data);

Html::listen();
?>
<div class="TeamspeakConnect">
  <img src="http://i.imgur.com/PGKQdVW.png" alt="TeamSpeak" class="img-responsive"/>
    <div class="btn-group hidden-xs hidden-sm hidden-md" style="display:block;margin-top: 15px;">
        <a href="http://www.teamspeak.com/?page=downloads" target="_blank" class="btn btn-default" style="width: 50%">{@DOWNLOAD_TS3}</a>
        <a href="ts3server://%IP%/" class="btn btn-default" style="width: 50%">{@ADD_SERVER}</a>
    </div>
    <div class="hidden-lg" style="display:block;margin-top: 15px;">
        <a href="http://www.teamspeak.com/?page=downloads" target="_blank" class="btn btn-default btn-block">{@DOWNLOAD_TS3}</a>
        <a href="ts3server://%IP%/" class="btn btn-default btn-block">{@ADD_SERVER}</a>
    </div>
</div>
<?php

$content = Html::output(array(
    'IP' => $data->ip
));

return $content;