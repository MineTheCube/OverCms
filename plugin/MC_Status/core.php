<?php

defined('IN_ENV') or die;

/**
 *  Process
 */
$data = json_decode($data);

$address = $data->ip . ($data->port === "25565" or empty($data->port) ? '' : ':'.$data->port); 

$port = empty($data->port) ? '25565' : $data->port;
$ip = $data->ip;

$id = substr(md5($ip.$port), 0, 12);

$cookieName = 'MC_Status_'.$id;
$cookie = (int) $_COOKIE[$cookieName];

if (isset($_COOKIE[$cookieName])) {
    if ( $cookie < 0 ) {
        $state = 'danger';
        $text = '{@OFFLINE}';
    } else {
        $state = 'success';
        $players = ($cookie > 1 ? '{@PLAYERS}' : '{@PLAYER}');
        $text = $cookie . ' ' . $players;
    }
} else {
    $state = 'success';
    $text = '??' . ' {@PLAYERS}';
}

$query = HTTP_ROOT . $path . 'query.php';


/**
 *  Stylesheet
 */
Html::CSS(HTTP_ROOT.$template.'style.css');


/**
 *  Content
 */
$content = Html::file($template.'status'.EXT, array(
    'ID' => $id,
    'ADDRESS' => e($address),
    'STATE' => $state,
    'TEXT' => $text
));


/**
 *  JavaScript
 */
Html::listen();

?>
<script type="text/javascript">
(function(){
    $.post( "%QUERY%", { query: "true", ip: "%IP%", port: "%PORT%" })
      .done(function( data ) {
        if (data == parseInt(data)) {
            $("#status_%ID%").removeClass("btn-danger").addClass("btn-success");
            if (data > 1) {
                var pls = "{@PLAYERS}";
            } else {
                var pls = "{@PLAYER}";
            }
            $("#status_%ID%").html(data + " " + pls);
            var value = data;
        } else {
            $("#status_%ID%").removeClass("btn-success").addClass("btn-danger");
            $("#status_%ID%").html("{@OFFLINE}");
            var value = "-1";
        }
        var date = new Date();
        date.setTime(date.getTime() + (10 * 60 * 1000));
        document.cookie = escape("%COOKIE_NAME%") + "=" + escape(value) + "; expires=" + date.toGMTString() + "; path=/";
    });
})();
</script>
<?php

Html::output(array(
    'QUERY' => $query,
    'IP' => $ip,
    'PORT' => $port,
    'ID' => $id,
    'COOKIE_NAME' => $cookieName,
));
Html::JS();

return $content;