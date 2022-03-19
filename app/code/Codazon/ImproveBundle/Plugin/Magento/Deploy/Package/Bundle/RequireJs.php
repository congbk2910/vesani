<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codazon\ImproveBundle\Plugin\Magento\Deploy\Package\Bundle;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class RequireJs
{
    protected $allowedFiles;
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundAddFile(
        \Magento\Deploy\Package\Bundle\RequireJs $subject,
        \Closure $proceed,
        $filePath,
        $sourcePath,
        $contentType
    ) {
        //Your plugin code
        $jsOptimization = $this->scopeConfig->getValue('improvebundle/general/enabled', ScopeInterface::SCOPE_STORE);
        if ($jsOptimization) {
            $allowedFiles = $this->getAllowedFiles();
            $include = false;
            foreach ($allowedFiles as $allowedFile) {
                if (strpos($sourcePath, $allowedFile) !== false) {
                    $include = true;
                    break;
                }
            }

            if (!$include) {
                return true;
            }
        }
        
        $result = $proceed($filePath, $sourcePath, $contentType);
        return $result;
    }

    public function getAllowedFiles()
    {
        if (null === $this->allowedFiles) {
            $includeInBundling = $this->scopeConfig->getValue('improvebundle/general/included_files', ScopeInterface::SCOPE_STORE);
            $allowedFiles = str_replace("\r", "\n", $includeInBundling);
            $allowedFiles = explode("\n", $allowedFiles);

            foreach ($allowedFiles as $key => $allowedFile) {
                $allowedFiles[$key] = trim($allowedFile);
                if (empty($allowedFiles[$key])) {
                    unset($allowedFiles[$key]);
                }
            }

            //min.js
            $result = [];
            foreach($allowedFiles as $allowed){
                $result[] = $allowed;
                $result[] = str_replace('.js', '.min.js', $allowed);
            }

            /*foreach ($allowedFiles as $allowed) {
                if (false !== strpos($allowed, '.min.js')) {
                    $allowedFiles[] = $allowed;//str_replace('.min.js', '.js', $allowed);
                } else {
                    $allowedFiles[] = $allowed;//str_replace('.js', '.min.js', $allowed);
                }
            }*/

            $this->allowedFiles = $result;
        }

        return $this->allowedFiles;
    }
}

