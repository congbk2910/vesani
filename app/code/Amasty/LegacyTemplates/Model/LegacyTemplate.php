<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_LegacyTemplates
*/


namespace Amasty\LegacyTemplates\Model;

use Magento\Email\Model\Template;
use Magento\Email\Model\Template\Config;
use Magento\Email\Model\Template\FilterFactory;
use Magento\Email\Model\TemplateFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Filesystem;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

class LegacyTemplate extends Template
{
    private const SUPPORTED_VERSIONS = [
        '2.4.4',
        '2.4.3-p2',
        '2.3.7-p3'
    ];

    /**
     * @var ProductMetadata
     */
    private $productMetadata;

    public function __construct(
        Context $context,
        DesignInterface $design,
        Registry $registry,
        Emulation $appEmulation,
        StoreManagerInterface $storeManager,
        Repository $assetRepo,
        Filesystem $filesystem,
        ScopeConfigInterface $scopeConfig,
        Config $emailConfig,
        TemplateFactory $templateFactory,
        FilterManager $filterManager,
        UrlInterface $urlModel,
        FilterFactory $filterFactory,
        ProductMetadata $productMetadata,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $design,
            $registry,
            $appEmulation,
            $storeManager,
            $assetRepo,
            $filesystem,
            $scopeConfig,
            $emailConfig,
            $templateFactory,
            $filterManager,
            $urlModel,
            $filterFactory,
            $data
        );
        $this->productMetadata = $productMetadata;
    }

    public function getProcessedTemplate(array $variables = [])
    {
        if (in_array($this->productMetadata->getVersion(), self::SUPPORTED_VERSIONS)
            && $this->isAmastyTemplate()
        ) {
            return $this->getProcessedTemplateLegacy($variables);
        }

        return parent::getProcessedTemplate($variables);
    }

    private function isAmastyTemplate(): bool
    {
        $origCode = (string)$this->getOrigTemplateCode();

        return stripos($origCode, 'amasty') !== false || substr($origCode, 0, 2) == 'am';
    }

    private function getProcessedTemplateLegacy(array $variables = [])
    {
        $processor = $this->getTemplateFilter()
            ->setPlainTemplateMode($this->isPlain())
            ->setIsChildTemplate($this->isChildTemplate())
            ->setTemplateProcessor([$this, 'getTemplateContent']);
        $variables['this'] = $this;

        $isDesignApplied = $this->applyDesignConfig();
        $processor->setDesignParams($this->getDesignParams());

        if (isset($variables['subscriber'])) {
            $storeId = $variables['subscriber']->getStoreId();
        } else {
            $storeId = $this->getDesignConfig()->getStore();
        }
        $processor->setStoreId($storeId);

        $variables = $this->addEmailVariables($variables, $storeId);
        $processor->setVariables($variables);

        $isLegacy = $this->getData('is_legacy');
        $templateId = $this->getTemplateId();
        $previousStrictMode = $processor->setStrictMode(
            !$isLegacy && (is_numeric($templateId) || empty($templateId))
        );

        try {
            $result = $processor->filter($this->getTemplateText());
        } catch (\Exception $e) {
            $this->cancelDesignConfig();
            throw new \LogicException(__($e->getMessage()), $e->getCode(), $e);
        } finally {
            $processor->setStrictMode($previousStrictMode);
        }

        if ($isDesignApplied) {
            $this->cancelDesignConfig();
        }

        return $result;
    }
}
