<?php

defined('IN_ENV') or die;

$data = json_decode($data);

$http_dir = HTTP_ROOT . $template;

Html::CSS($http_dir.'animate.css');
Html::CSS($http_dir.'style.css');

$parser = new Parser;

$parser->loadFile($template.'main'.EXT, array(
    'Indicator',
    'Slide'
));

$ini = parse_ini_file($template.'config.ini');
if (is_array($ini) and !empty($ini)) {
    foreach ($ini['effect_title'] as $key => $value) {
        $effect[] = array($ini['effect_title'][$key], $ini['effect_desc'][$key]);
    }
} else {
    $effect = array(
        array('fadeInDown', 'fadeInUp'),
        array('fadeInLeft', 'fadeInRight'),
        array('fadeInUp', 'fadeInUp')
    );
}

$effect_max = count($effect);
$effect_index = $i = 0;
$active = true;

foreach ($data->slides as $slide) {
    if ($effect_index >= $effect_max)
        $effect_index = 0;

    $parser->slide->add(array(
        'Slide title'  => $slide->title,
        'Slide desc'   => $slide->desc,
        'Effect title' => $effect[$effect_index][0],
        'Effect desc'  => $effect[$effect_index][1] . (empty($slide->desc) ? ' hide' : ''),
        'Image'        => $http_dir.'img/'.$slide->image.'.jpg',
        'Slide active' => $active ? 'active' : '',
    ));

    $parser->indicator->add(array(
        'Indicator active' => $active ? 'active' : '',
        'Indicator index'  => $i++
    ));

    $active = false;
    $effect_index++;
}

return $parser->render();
