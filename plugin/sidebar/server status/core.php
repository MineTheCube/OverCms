<?php

if (!IN_ENV) die;

$data = json_decode( $data );

$ip = $data->ip;
$port = $data->port == '' ? '25565' : $data->port;
$address = $ip . ($port == '25565' ? '' : ':' . $port);

if ( isset( $_COOKIE['last_server_status'] ) ) {
    if ( $_COOKIE['last_server_status'] == 'off' ) {
        $state = 'off';
        $text = 'Hors ligne';
    } else {
        $state = 'on';
        $s = ($_COOKIE['last_server_status'] > 1 ? 's' : '');
        $text = $_COOKIE['last_server_status'] . ' joueur'.$s;
    }
} else {
    $state = 'on';
    $text = '??' . ' joueurs';
}

$query = HTTP_ROOT . $currentFile . 'query.php';

ob_start();

?>
<style>
#server_status .ip {
    background: #f5f5f5;
    text-align: center;
    font-size: 22px;
    border-radius: 4px;
    color: black;
    display: block;
    border: 1px solid #ddd;
    width: 100%;
    margin-bottom: 10px;
    font-family: Menlo,Monaco,Consolas,Courier,monospace;
}
#server_status #status {
    text-align: center;
    font-size: 24px;
    border-radius: 4px;
    color: white;
    padding: 2px 0;
}
#server_status .load {
    background: #38b44a;
}
#server_status .on {
    background: #38b44a;
}
#server_status .off {
    background: #df382c;
}
</style>
<div id="server_status">
    <input readonly class="ip" value = "<?php echo $address; ?>" onclick="select()" />
    <div id="status" class="<?=$state?>"><?=$text?></div>
</div>

<script>
$.post( "<?php echo $query; ?>", { query: "true", ip: "<?php echo $ip; ?>", port: "<?php echo $port; ?>" })
  .done(function( data ) {
    if (data == parseInt(data)) {
        $('#status').attr('class', 'on');
        if (data > 1) {
            var s = 's';
        } else {
            var s = '';
        }
        $('#status').html(data + ' joueur' + s);
        var value = data;
    } else {
        $('#status').attr('class', 'off');
        $('#status').html('Hors ligne');
        var value = 'off';
    }
    var date = new Date();
    date.setTime(date.getTime() + (10 * 60 * 1000));
    document.cookie = escape('last_server_status') + "=" + escape(value) + "; expires=" + date.toGMTString() + "; path=/";
});
</script>

<?php

$content = ob_get_clean();

