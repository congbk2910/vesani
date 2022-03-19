<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Setup\Controller;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants as SetupConfigOptionsList;
use Magento\SampleData;
use Magento\Setup\Model\Installer;
use Magento\Setup\Model\Installer\ProgressFactory;
use Magento\Setup\Model\InstallerFactory;
use Magento\Setup\Model\RequestDataConverter;
use Magento\Setup\Model\WebLogger;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Magento\Setup\Model\ObjectManagerProvider;

/**
 * Install controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Install extends AbstractActionController
{
    /**
     * @var WebLogger
     */
    private $log;

    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var ProgressFactory
     */
    private $progressFactory;

    /**
     * @var \Magento\Framework\Setup\SampleData\State
     */
    protected $sampleDataState;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @var RequestDataConverter
     */
    private $requestDataConverter;

    /**
     * Default Constructor
     *
     * @param WebLogger $logger
     * @param InstallerFactory $installerFactory
     * @param ProgressFactory $progressFactory
     * @param \Magento\Framework\Setup\SampleData\State $sampleDataState
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param RequestDataConverter $requestDataConverter
     */
    public function __construct(
        WebLogger $logger,
        InstallerFactory $installerFactory,
        ProgressFactory $progressFactory,
        \Magento\Framework\Setup\SampleData\State $sampleDataState,
        DeploymentConfig $deploymentConfig,
        RequestDataConverter $requestDataConverter,
        ObjectManagerProvider $objectManagerProvider
    ) {
        $this->log = $logger;
        $this->installer = $installerFactory->create($logger);
        $this->progressFactory = $progressFactory;
        $this->sampleDataState = $sampleDataState;
        $this->deploymentConfig = $deploymentConfig;
        $this->requestDataConverter = $requestDataConverter;
        $this->objectManagerProvider = $objectManagerProvider;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $view = new ViewModel;
        $view->setTemplate('magento/setup/install');
        $view->setTerminal(true);
        return $view;
    }
    
    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public function getDefaultData($request)
    {
        session_start();

        $configFactory = $this->objectManagerProvider->get()->create(\Magento\Config\Model\Config\Factory::class);
        $configModel = $configFactory->create();
        
        $userConfig = new \Magento\Setup\Model\StoreConfigurationDataMapper();
        $appState = $this->objectManagerProvider->get()->get(\Magento\Framework\App\State::class);
        $appState->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $configData = $userConfig->getConfigData($request);

        foreach($configData as $path => $value){
            $result[$path] = $configModel->getConfigDataValue($path);
        }
        $_SESSION['currency'] = $result;
    }
    
    public function setDefaultData()
    {
        $configFactory = $this->objectManagerProvider->get()->create(\Magento\Config\Model\Config\Factory::class);
        foreach($_SESSION['currency'] as $path => $value){
            $configModel = $configFactory->create();
            $configModel->setDataByPath($path, $value);
            $configModel->save();
        }
        if(isset($_SESSION['currency'])){
            $_SESSION['currency'] = array();
        }
    }

    /**
     * Index Action
     *
     * @return JsonModel
     */
    public function startAction()
    {
        $this->log->clear();
        $json = new JsonModel;
        try {
            $this->checkForPriorInstall();
            $content = $this->getRequest()->getContent();
            $source = $content ? $source = Json::decode($content, Json::TYPE_ARRAY) : [];
            $data = $this->requestDataConverter->convert($source);
            $tmp = require(BP.'/app/etc/env.php');
            $data['db'] = [];
            $data['db']['host'] = $tmp['db']['connection']['default']['host'];
            $data['db']['name'] = $tmp['db']['connection']['default']['dbname'];
            $data['db']['user'] = $tmp['db']['connection']['default']['username'];
            $data['db']['password'] = $tmp['db']['connection']['default']['password'];
            $data['db']['prefix'] = $tmp['db']['table_prefix'];
            $data['key'] = $tmp['crypt']['key'];
            $data['backend-frontname'] = $tmp['backend']['frontName'];
            
            $data['db-host'] = $tmp['db']['connection']['default']['host'];
            $data['db-name'] = $tmp['db']['connection']['default']['dbname'];
            $data['db-user'] = $tmp['db']['connection']['default']['username'];
            $data['db-password'] = $tmp['db']['connection']['default']['password'];
            $data['db-prefix'] = $tmp['db']['table_prefix'];
            $data['admin-password'] = $this->generateRandomString();
            $data['admin-user'] = 'cdzinstaller';
            $data['currency'] = 'USD';
            $data['timezone'] = 'UTC';
            $data['cleanup-database'] = 0;
            $this->getDefaultData($data);
            /*unset($data['admin-user']);
            unset($data['admin-password']);
            unset($data['admin-email']);
            unset($data['admin-firstname']);
            unset($data['admin-lastname']);*/
            
            $this->installer->install($data);
            $json->setVariable(
                'key',
                $this->installer->getInstallInfo()[SetupConfigOptionsList::KEY_ENCRYPTION_KEY]
            );
            $this->setDefaultData();
            $json->setVariable('success', true);
            if ($this->sampleDataState->hasError()) {
                $json->setVariable('isSampleDataError', true);
            }
            $json->setVariable('messages', $this->installer->getInstallInfo()[Installer::INFO_MESSAGE]);
        } catch (\Exception $e) {
            $this->log->logError($e);
            $json->setVariable('messages', $e->getMessage());
            $json->setVariable('success', false);
        }
        return $json;
    }

    /**
     * Checks progress of installation
     *
     * @return JsonModel
     */
    public function progressAction()
    {
        $percent = 0;
        $success = false;
        $contents = [];
        $json = new JsonModel();

        // Depending upon the install environment and network latency, there is a possibility that
        // "progress" check request may arrive before the Install POST request. In that case
        // "install.log" file may not be created yet. Check the "install.log" is created before
        // trying to read from it.
        if (!$this->log->logfileExists()) {
            return $json->setVariables(['progress' => $percent, 'success' => true, 'console' => $contents]);
        }

        try {
            $progress = $this->progressFactory->createFromLog($this->log);
            $percent = sprintf('%d', $progress->getRatio() * 100);
            $success = true;
            $contents = $this->log->get();
            if ($this->sampleDataState->hasError()) {
                $json->setVariable('isSampleDataError', true);
            }
        } catch (\Exception $e) {
            $contents = [(string)$e];
        }
        return $json->setVariables(['progress' => $percent, 'success' => $success, 'console' => $contents]);
    }

    /**
     * Checks for prior install
     *
     * @return void
     * @throws \Magento\Setup\Exception
     */
    private function checkForPriorInstall()
    {
        if ($this->deploymentConfig->isAvailable()) {
            throw new \Magento\Setup\Exception('Magento application is already installed.');
        }
    }
}
