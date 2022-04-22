<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_AbTesting
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\AbTesting\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class MoveCsvToDirectory implements
    DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

   /**
    * @param ModuleDataSetupInterface $moduleDataSetup
    * @param \Magento\Framework\Module\Dir\Reader $reader
    * @param \Magento\Framework\Filesystem $filesSystem
    * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
    */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Framework\Module\Dir\Reader $reader,
        \Magento\Framework\Filesystem $filesSystem,
        \Magento\Framework\Filesystem\Driver\File $fileDriver
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->_reader = $reader;
        $this->_filesystem = $filesSystem;
        $this->_fileDriver = $fileDriver;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $this->processSampleCsvSheets();
    }

    /**
     * move sample files to media directory
     *
     * @return void
     */
    protected function processSampleCsvSheets()
    {
        try {
            $type = \Magento\Framework\App\Filesystem\DirectoryList::MEDIA;
            $smpleFilePath = $this->_filesystem->getDirectoryRead($type)
                                        ->getAbsolutePath().'abtesting/';
            $file = 'abtesting_selectors.csv';
            if ($this->_fileDriver->isExists($smpleFilePath)) {
                $this->_fileDriver->deleteDirectory($smpleFilePath);
            }
            if (!$this->_fileDriver->isExists($smpleFilePath)) {
                $this->_fileDriver->createDirectory($smpleFilePath, 0777);
            }
            $filePath = $smpleFilePath.$file;
            if (!$this->_fileDriver->isExists($filePath)) {
                $path = '/pub/media/abtesting/'.$file;
                $mediaFile = $this->_reader->getModuleDir('', 'Webkul_AbTesting').$path;
                if ($this->_fileDriver->isExists($mediaFile)) {
                    $this->_fileDriver->copy($mediaFile, $filePath);
                }
            }
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [

        ];
    }
}
