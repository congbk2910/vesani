<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\Core\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Styles extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $filesystem;
    
    protected $io;
    
    protected $objectManager;
    
    protected $dirHander;
    
    protected $mediaBaseDir;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $io
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->dirHander = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->mediaBaseDir = $this->dirHander->getAbsolutePath();
        $this->io = $io;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }
    
    public function getCssFromLess($lessFile, $variables = [])
    {
        $content = '';
        $parser = new \Less_Parser(
            [
                'relativeUrls' => false,
                'compress' => true
            ]
        );
        try {
            gc_disable();
            $parser->ModifyVars($variables);
            $parser->parseFile($lessFile, '');
            $content = $parser->getCss();
            gc_enable();
        } catch(\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
        return $content;
    }
    
    public function getIo()
    {
        return $this->io;
    }
    
    public function fileExists($file) {
        return $this->io->fileExists($file);
    }
    
    public function buildCssFileContentFromLessFile($lessFile, $cssFile)
    {
        $content = $this->getCssFromLess($lessFile);
        $this->io->write($cssFile, $content, 0666);
    }
    
    public function write($file, $content, $mode = 0666)
    {
        $this->io->write($file, $content, $mode);
    }
    
    public function read($file)
    {
        return $this->io->read($file);
    }
    
    public function getMediaUrl()
    {
        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);   
    }
    
    public function getMediaDir($path)
    {
        return $this->mediaBaseDir . $path;
    }
}