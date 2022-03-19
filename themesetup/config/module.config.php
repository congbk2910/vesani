<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

$config = [
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/../../setup/view/error/404.phtml',
            'error/index'             => __DIR__ . '/../../setup/view/error/index.phtml',
        ],
        'template_path_stack' => [
            'setup' => __DIR__ . '/../../setup/view',
        ],
        'strategies' => ['ViewJsonStrategy'],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../lang',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
    ],
];
$file = dirname(dirname(__DIR__))."/composer.lock";
$json = file_get_contents($file);
$data = json_decode($json, true);
$version = '';
foreach($data['packages'] as $item){
    if($item['name'] == 'magento/magento2-base'){
        $version = $item['version'];break;
    }
}
$vs = explode('.',$version);
if($vs[1] >= 4){
    $config['view_manager']['template_path_stack']['setup'] = __DIR__ . '/../view';
}
return $config;