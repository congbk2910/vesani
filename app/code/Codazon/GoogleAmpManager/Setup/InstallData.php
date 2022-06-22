<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\GoogleAmpManager\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface {
    
    protected $sampleDataContext;
    
    protected $fixtureManager;
    
    protected $csvReader;
    
    public function __construct(
        
    ) {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    protected function getHomepageIds()
    {
        //$homepage = $this->getConfig('web/default/cms_home_page');
        $resource = $this->objectManager->get(\Magento\Framework\App\ResourceConnection::class);
        $connection = $resource->getConnection('core_read');
        
        $pages = $this->objectManager->create(\Magento\Cms\Model\ResourceModel\Page\Collection::class);
        $pages->addFieldToSelect('page_id');
        $this->core_config_data = $resource->getTableName('core_config_data'); 
        $pages->getSelect()->join(array('config' =>$this->core_config_data), 'identifier= config.value');
        $pages->getSelect()->where("config.path='web/default/cms_home_page'");
        $result = [2]; //default home page id
        if(is_array($pages->toArray()) && isset($pages->toArray()['items'])){
            foreach($pages->toArray()['items'] as $item){
                $result[] = $item['page_id'];
            }
        }
        return $result;
    }

    protected function getSampleDataContext()
    {
        if ($this->sampleDataContext === null) {
            $this->sampleDataContext = $this->objectManager->get(\Magento\Framework\Setup\SampleData\Context::class);
        }
        return $this->sampleDataContext;
    }
    
    protected function getFixtureManager()
    {
        if ($this->fixtureManager === null) {
            $this->fixtureManager = $this->getSampleDataContext()->getFixtureManager();
        }
        return $this->fixtureManager;
    }

    protected function getCsvReader()
    {
        if ($this->csvReader === null) {
            $this->csvReader = $this->getSampleDataContext()->getCsvReader();
        }
        return $this->csvReader;
    }
    
    public function importCmsPageAmp()
    {
        $file = $this->getFixtureManager()->getFixture(\Codazon\GoogleAmpManager\Helper\Import::PAGE_AMP_FIXTURE);
        $rows = $this->getCsvReader()->getData($file);
        $header = array_shift($rows);
        $homeData = null;
        $homeIds = $this->getHomepageIds();
        $setupHomepage = false;
        foreach ($rows as $row) {
            $data = [];
            foreach ($row as $key => $value) {
                $data[$header[$key]] = $value;
            }
            if ($data['is_home_page']) {
                $homeData = $data;
            }
            $page = $this->objectManager->create(\Magento\Cms\Model\Page::class)->load($data['identifier'], 'identifier');
            if ($data['page_id'] = $page->getId()) {
                $ampModel = $this->objectManager->create(\Codazon\GoogleAmpManager\Model\Page::class);
                $ampModel->load($data['page_id'], 'page_id');
                if ($ampModel->getId()) {
                    continue;
                }
                $ampModel->addData($data);
                $ampModel->save();
            }
        }
        foreach($homeIds as $homeId){
            if (!empty($homeData)) {
                $ampModel = $this->objectManager->create(\Codazon\GoogleAmpManager\Model\Page::class);
                $ampModel->load($homeId, 'page_id');
                if ($ampModel->getId()) {
                    continue;
                }
                $homeData['page_id'] = $homeId;
                $ampModel->addData($homeData);
                $ampModel->save();
            }
        }
        
    }
    
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /* import data */
        $this->importCmsPageAmp();
        //$this->importHelper->importData();
    }
    
}
