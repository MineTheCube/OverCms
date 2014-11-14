<?php

$data = json_decode( $data );

if (empty( $data->desc )) {
    $height = '115';
} else {
    $height = '150';
}

$http_path = HTTP_ROOT . $path;

$content = '
<style>
#simpleHeader {
    background-image: url(\'' . $http_path . 'img/' . rand(1, 9) .'.jpg\');
    width: 100%;
    background-size: cover;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
    color: white;
}
#simpleHeader div.container {
    padding-top: 18px;
    padding-bottom: 22px;
}
#simpleHeader h1 {
    font-size: 45px;
    margin: 0;
}
#simpleHeader h3 {
    font-size: 24px;
    margin: 0;
    margin-top: 5px;
}
@media (max-width: 767px) {
    #simpleHeader h1 {
        font-size: 30px
    }
    #simpleHeader h3 {
        font-size: 18px;
    }
}
</style>
';

$content .= '
<div id="simpleHeader">
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

$output = array(
    'content' => $content,
    'translation' => false,
    'default_language' => false
);

?>