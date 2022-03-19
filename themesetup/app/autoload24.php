<?php
/**
 * Register basic autoloader that uses include path
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\Framework\Autoload\AutoloaderRegistry;
use Magento\Framework\Autoload\ClassLoaderWrapper;

/**
 * Shortcut constant for the root directory
 */
define('BP', dirname(dirname(__DIR__)));

define('VENDOR_PATH', BP . '/app/etc/vendor_path.php');

if (!file_exists(VENDOR_PATH)) {
    throw new \Exception(
        'We can\'t read some files that are required to run the Magento application. '
         . 'This usually means file permissions are set incorrectly.'
    );
}

$vendorDir = require VENDOR_PATH;
$vendorAutoload = BP . "/{$vendorDir}/autoload.php";

/* 'composer install' validation */
if (file_exists($vendorAutoload)) {
    $composerAutoloader = include $vendorAutoload;
    $baseDir = dirname(dirname(__DIR__));
    $map = array(
        'Codazon\\Setup\\' => array(0 => $baseDir . '/themesetup/src/Codazon/Setup')
    );

    foreach ($map as $namespace => $path) {
        $composerAutoloader->setPsr4($namespace, $path);
    }
} else {
    throw new \Exception(
        'Vendor autoload is not found. Please run \'composer install\' under application root directory.'
    );
}

AutoloaderRegistry::registerAutoloader(new ClassLoaderWrapper($composerAutoloader));
