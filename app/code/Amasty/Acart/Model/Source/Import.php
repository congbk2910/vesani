<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\Source;

use Amasty\Acart\Model\ResourceModel\Blacklist;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Import extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * @var Blacklist
     */
    protected $blacklistResource;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData,
        Filesystem $filesystem,
        \Amasty\Acart\Model\ResourceModel\Blacklist $blacklistResource,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $uploaderFactory,
            $requestData,
            $filesystem,
            $resource,
            $resourceCollection,
            $data
        );
        $this->blacklistResource = $blacklistResource;
    }

    public function beforeSave()
    {
        return $this;
    }

    public function save()
    {
        $tmpName = $this->_requestData->getTmpName($this->getPath());
        $directoryRead = $this->_filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        $file = $directoryRead->openFile($directoryRead->getRelativePath($tmpName));
        $emails = [];

        while (($csvLine = $file->readCsv()) !== false) {
            foreach ($csvLine as $email) {
                if (\Zend_Validate::is($email, 'NotEmpty')
                    && \Zend_Validate::is($email, 'EmailAddress')
                ) {
                    $emails[]['customer_email'] = $email;
                }
            }
        }

        if ($emails) {
            $this->blacklistResource->saveImportData($emails);
        }

        return $this;
    }

    protected function _getAllowedExtensions()
    {
        return ['csv', 'txt'];
    }
}
