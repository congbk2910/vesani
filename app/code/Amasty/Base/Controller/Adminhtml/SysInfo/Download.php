<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/


declare(strict_types=1);

namespace Amasty\Base\Controller\Adminhtml\SysInfo;

use Amasty\Base\Model\SysInfo\Command\SysInfoService\Download as DownloadCommand;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteFactory;

class Download extends Action
{
    public const FILE_NAME = 'system_information';
    public const CONTENT_TYPE = 'application/octet-stream';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var WriteFactory
     */
    private $writeFactory;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var DownloadCommand
     */
    private $downloadCommand;

    public function __construct(
        Action\Context $context,
        Filesystem $filesystem,
        WriteFactory $writeFactory,
        FileFactory $fileFactory,
        DownloadCommand $downloadCommand
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->writeFactory = $writeFactory;
        $this->fileFactory = $fileFactory;
        $this->downloadCommand = $downloadCommand;
    }

    public function execute()
    {
        try {
            $xml = $this->downloadCommand->execute();

            $tmpDir = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
            $filePath = self::FILE_NAME . uniqid() . '.' . $xml->getExtension();
            $tmpDir->writeFile($filePath, $xml->getContent());

            return $this->fileFactory->create(
                sprintf('%s.%s', self::FILE_NAME, $xml->getExtension()),
                [
                    'type' => 'filename',
                    'value' => $filePath,
                    'rm' => true
                ],
                DirectoryList::TMP,
                self::CONTENT_TYPE
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setRefererUrl();

        return $resultRedirect;
    }
}
