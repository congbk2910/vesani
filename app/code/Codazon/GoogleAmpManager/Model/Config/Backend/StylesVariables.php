<?php
/**
 * Copyright © 2018 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\GoogleAmpManager\Model\Config\Backend;

use Codazon\GoogleAmpManager\Model\AmpConfig;

class StylesVariables extends \Magento\Framework\App\Config\Value
{
	public function afterSave()
    {        
        $value = $this->getValue() ? : '{}';

        /* Get File */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $stylesHelper = $objectManager->get(\Codazon\Core\Helper\Styles::class);
        $ampHelper = $objectManager->get(\Codazon\GoogleAmpManager\Helper\Data::class);
        $ampConfig = $objectManager->get(AmpConfig::class);
        $storeManager = $ampHelper->getStoreManager();
        $store = $ampHelper->getRequest()->getParam('store');
        $website = $ampHelper->getRequest()->getParam('website');
        $variablesFile = $ampConfig->getVariablesFile();
        if ($store || $website) {
            $projectBaseDir = $stylesHelper->getMediaDir($ampConfig->getProjectBaseDir());
            if ($store) {
                $website = $storeManager->getWebsite()->getId();
            } elseif ($website) {
                $store = 0;
            }
            $stylesHelper->getIo()->mkdir($projectBaseDir . '/scope/' . $website . '/' . $store);
            $variablesFile = $ampConfig->getProjectBaseDir() . '/scope/' . $website . '/' . $store . '/' . $ampConfig->getVariablesFileName();
        }
        $variablesFile = $stylesHelper->getMediaDir($variablesFile);
        
        /* File Content */
        $output = '';
        $variables = json_decode($value, true);
        foreach ($variables as $name => $value) {
            $paramValue =  (stripos($name, 'font') === false) ? $value : "~'{$value}'";
            $output .= "@{$name}:{$paramValue};";
        }
        
        /* Write File */
        $stylesHelper->write($variablesFile, $output);
        $destination = $stylesHelper->getMediaDir(AmpConfig::DEST_CSS_DIR);
        $stylesHelper->getIo()->rmdirRecursive($destination);
        $stylesHelper->getIo()->mkdir($destination);
        return parent::afterSave();
    }
}