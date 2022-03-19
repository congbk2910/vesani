<?php
/**
* Copyright © 2019 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\GoogleAmpManager\Helper;

use Codazon\GoogleAmpManager\Model\AmpConfig;

use Codazon\GoogleAmpManager\Helper\Data as AmpHelper;

class Import extends AmpHelper
{
    protected $stylesHelper;

    protected $sampleDataContext;

    protected $fixtureManager;

    protected $csvReader;

    protected $connection;

    const PAGE_AMP_FIXTURE = 'Codazon_GoogleAmpManager::fixtures/cdz_amp_cms_page.csv';

    const BLOCK_AMP_FIXTURE = 'Codazon_GoogleAmpManager::fixtures/cdz_amp_cms_block.csv';

    protected function getSampleDataContext()
    {
        if ($this->sampleDataContext === null) {
            $this->sampleDataContext = $this->getObjectManager()->get(\Magento\Framework\Setup\SampleData\Context::class);
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

    public function getStylesHelper()
    {
        if (null === $this->stylesHelper) {
            $this->stylesHelper = $this->objectManager->get(\Codazon\Core\Helper\Styles::class);
        }
        return $this->stylesHelper;
    }

    protected function getModuleReader()
    {
        return $this->objectManager->get(\Magento\Framework\Module\Dir\Reader::class);
    }

    public function importData()
    {
        $this->importCmsPageAmp();
    }


    protected function getHomepageId()
    {
        $homepage = $this->getConfig('web/default/cms_home_page');
        $cmsModel = $this->objectManager->create(\Magento\Cms\Model\Page::class)->load($homepage, 'identifier');
        return $cmsModel->getId();
    }

    public function importCmsPageAmp()
    {
        $file = $this->getFixtureManager()->getFixture(self::PAGE_AMP_FIXTURE);
        $rows = $this->getCsvReader()->getData($file);
        $header = array_shift($rows);
        $homeData = null;
        $homeId = $this->getHomepageId();
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
                if ($data['page_id'] == $homeId) {
                    $setupHomepage = true;
                }
            }
        }
        if ((!$setupHomepage) && (!empty($homeData))) {
            $homeData['page_id'] = $homeId;
            $ampModel = $this->objectManager->create(\Codazon\GoogleAmpManager\Model\Page::class);
            $ampModel->load($homeId, 'page_id');
            if (!$ampModel->getId()) {
                $ampModel->addData($homeData);
                $ampModel->save();
            }
        }
    }
}
