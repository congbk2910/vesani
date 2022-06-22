<?php
/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\GoogleAmpManager\Block;

use \Codazon\GoogleAmpManager\Model\AmpConfig;

class Styles extends AbstractAmp
{
    protected $customStyleFile = [];
    
    protected $ampJsLib = [];
    
    protected $stylesHelper;
    
    protected $ampCustomCss = [];
    
    public function getStylesHelper()
    {
        if (null === $this->stylesHelper) {
            $this->stylesHelper = $this->helper->getObjectManager()->get(\Codazon\Core\Helper\Styles::class);
        }
        return $this->stylesHelper;
    }
    
    public function addCustomStyleFile($file)
    {
        if (!in_array($file, $this->customStyleFile)) {
            $this->customStyleFile[] = $file;
        }
    }
    
    public function addAmpJs($script, $name = null)
    {
        if (!is_array($script)) {
            if (!$name) {
                return false;
            }
            $script = [
                $name => $script
            ];
        }
        foreach ($script as $element => $dataString) {
            $this->ampJsLib[$element] = $dataString;
        }
    }
    
    public function getCustomStyleFile()
    {
        return $this->customStyleFile;
    }
    
    public function getAmpJs()
    {
        return $this->ampJsLib;
    }
    
    public function getOutputAmpJs()
    {
        $script = "\n";
        foreach ($this->getAmpJs() as $dataString) {
            $script .= "<script {$dataString}></script>\n";
        }
        return $script;
    }
    
    public function addAmpCustomCss($key, $css)
    {
        $this->ampCustomCss[$key] = $css;
    }
    
    public function getOutputCustomCSS()
    {
        $stylesHelper = $this->getStylesHelper();
        $baseUrl = str_replace(['https://', 'http://'], ['//', '//'], $stylesHelper->getMediaUrl());
        $storeManager = $this->helper->getStoreManager();
        $websiteId = $storeManager->getWebsite()->getId();
        $storeId = $storeManager->getStore()->getId();
        $variables = [
            "base_url"  => "~'{$baseUrl}'",
            "website"   => $websiteId,
            "store"     => $storeId,
        ];
        
        if ($this->helper->getConfig(AmpConfig::ENABLE_DEVELOPER_MODE_PATH)) {
            $css = '';
            foreach ($this->getCustomStyleFile() as $file) {
                $css .= $stylesHelper->getCssFromLess($stylesHelper->getMediaDir(AmpConfig::SRC_LESS_DIR . '/' . $file), $variables);
            }
        } else {
            $css = $content = '';
            foreach ($this->getCustomStyleFile() as $file) {
                $destFile = $stylesHelper->getMediaDir(AmpConfig::DEST_CSS_DIR . "/{$file}-{$websiteId}-{$storeId}.css");
                if ($stylesHelper->fileExists($destFile)) {
                    $content = $stylesHelper->read($destFile);
                } else {
                    $content = $stylesHelper->getCssFromLess($stylesHelper->getMediaDir(AmpConfig::SRC_LESS_DIR . '/' . $file), $variables);
                    $stylesHelper->write($destFile, $content);
                }
                $css .= $content;
            }
        }
        $css .= $this->helper->getConfig('googleampmanager/general/custom_css');
        foreach ($this->ampCustomCss as $customCss) {
            $css .= $customCss;
        }
        return $css;
    }
    
    public function getGoogleFontsScript()
    {
        $fontScript = '';
        if ($webFonts = $this->helper->getConfig('googleampmanager/styles/typography/google_web_fonts')) {
            $webFonts = explode(',', $webFonts);
            $fontWeights = $this->helper->getConfig('googleampmanager/styles/typography/google_font_weights');
            $fontWeights = $fontWeights ? ':' . $fontWeights : '';
            $fontSubset = $this->helper->getConfig('googleampmanager/styles/typography/google_font_subset');
            $fontSubset = $fontSubset ? '&subset=' . $fontSubset : '';
            $fontScript = 'https://fonts.googleapis.com/css?family=';
            $separate = '';
            foreach($webFonts as $font) {
                $font = str_replace(' ', '+', trim($font));
                $fontScript .= $separate . $font;
                $fontScript .= $fontWeights;
                $separate = '|';
            }
            $fontScript = '<link rel="stylesheet" href="' . $fontScript . $fontSubset . '&display=swap" />';
        }
        return $fontScript;
    }
    
    public function getCustomAmpJsLibrary()
    {
        return $this->helper->getConfig('googleampmanager/general/custom_script');
    }
}
