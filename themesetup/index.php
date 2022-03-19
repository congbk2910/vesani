<?php
$file = "../composer.lock";
$json = file_get_contents($file);
$data = json_decode($json, true);
$version = '';
foreach($data['packages'] as $item){
    if($item['name'] == 'magento/magento2-base'){
        $version = $item['version'];break;
    }
}

$vs = explode('.',$version);
ini_set('display_errors',1);
if($vs[1] < 2){
    include 'index21.php';
}else if($vs[1] < 4){
    include 'index22.php';
}else{
    include 'index24.php';
}