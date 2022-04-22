<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Psr\Log\LoggerInterface;

/**
 * Class PushNotificationsObserver
 * @package Omnisend\Omnisend\Observer
 */
class PushNotificationsObserver implements ObserverInterface
{

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var File
     */
    protected $file;

    /**
     * @param GeneralConfig   $generalConfig
     * @param LoggerInterface $logger
     * @param DirectoryList   $directoryList
     * @param Filesystem      $filesystem
     */
    public function __construct(
        GeneralConfig $generalConfig,
        LoggerInterface $logger,
        DirectoryList $directoryList,
        Filesystem $filesystem,
        File $file
    ) {
        $this->generalConfig = $generalConfig;
        $this->logger = $logger;
        $this->directoryList = $directoryList;
        $this->filesystem = $filesystem;
        $this->file = $file;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {

        $this->buildServiceWorker('/service-worker.js');
        $this->buildServiceWorker('/pub/service-worker.js');
        return null;
    }

    /**
     * Write the service-worker.js file to the root directory.
     *
     * @return bool
     * @throws FileSystemException
     */
    public function buildServiceWorker($path)
    {
        try {

            if (!file_exists($this->directoryList->getRoot() . $path)) {

                $rootDirectory = $this->filesystem->getDirectoryWrite(
                    DirectoryList::ROOT
                );

                $streamRoot = $rootDirectory->openFile($path, 'w+');
                $streamRoot->lock();
                $fileData = 'importScripts("https://omnisnippet1.com/inshop/service-worker.js");';
                $streamRoot->write($fileData);
                $streamRoot->unlock();
                $streamRoot->close();

                $this->logger->info('Service Worker Successfully Created.');
            }
        } catch (FileSystemException $e) {
            $this->logger->error($e);
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return true;
    }
}
