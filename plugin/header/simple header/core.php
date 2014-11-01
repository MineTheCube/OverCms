<?php

$data = json_decode( $data );

if (empty( $data->desc )) {
    $height = '115';
} else {
    $height = '150';
}

$content = '
<style>
#simple_header {
    background-image: url(\'' . HEADER . 'img/' . rand(1, 9) .'.jpg\');
    width: 100%;
    background-size: cover;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
    color: white;
}
#simple_header div.container {
    padding-top: 18px;
    padding-bottom: 22px;
}
#simple_header h1 {
    font-size: 45px;
    margin: 0;
}
#simple_header h3 {
    font-size: 24px;
    margin: 0;
    margin-top: 5px;
}
@media (max-width: 767px) {
    #simple_header h1 {
        font-size: 30px
    }
    #simple_header h3 {
        font-size: 18px;
    }
}
</style>
';

$content .= '
<div id="simple_header">
    <div class="container">
      <h1>
        {PAGE}
      </h1>';
      
if (!empty( $data->desc )) {
$content .= '
      <h3>
        ' . $data->desc . '
      </h3>';
}
      
$content .= '
    </div>
</div>
';

?>