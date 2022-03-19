<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */


$config = [
    'router' => [
        'routes' => [
            'literal' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Magento\Setup\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'setup' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '[/:controller[/:action]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Magento\Setup\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                ],
            ],
            'navigation' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '[/navigation[/:action]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Codazon\Setup\Controller',
                        'controller'    => 'Navigation',
                        'action'        => 'index',
                    ],
                    'constraints' => [
                        'controller' => 'Navigation',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                ],
            ],
            'install' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '[/install[/:action]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Codazon\Setup\Controller',
                        'controller'    => 'Install',
                        'action'        => 'index',
                    ],
                    'constraints' => [
                        'controller' => 'Install',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                ],
            ],
            'landing' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '[/landing[/:action]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Codazon\Setup\Controller',
                        'controller'    => 'Landing',
                        'action'        => 'index',
                    ],
                    'constraints' => [
                        'controller' => 'Landing',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                ],
            ],
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
    $config['router']['routes']['navigation'] = [
        'type'    => 'Segment',
        'options' => [
            'route'    => '[/navigation[/:action]]',
            'defaults' => [
                '__NAMESPACE__' => 'Codazon\Setup\Controller',
                'controller'    => 'Navigation24',
                'action'        => 'index',
            ],
            'constraints' => [
                'controller' => 'Navigation24',
                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
            ],
        ],
    ];
}
return $config;