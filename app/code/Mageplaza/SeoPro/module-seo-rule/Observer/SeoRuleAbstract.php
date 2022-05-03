<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SeoRule
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoRule\Observer;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\SeoRule\Helper\Data as HelperData;
use Mageplaza\SeoRule\Model\Meta;
use Mageplaza\SeoRule\Model\ResourceModel\Meta\Collection as MetaCollection;
use Mageplaza\SeoRule\Model\ResourceModel\Rule\Collection as RuleCollection;
use Mageplaza\SeoRule\Model\Rule;
use Mageplaza\SeoRule\Model\Rule\Source\ApplyTemplate;
use Mageplaza\SeoRule\Model\Rule\Source\Type;

/**
 * Class SeoRuleAbstract
 * @package Mageplaza\SeoRule\Observer
 */
abstract class SeoRuleAbstract
{
    /**
     * @var Http
     */
    protected $request;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var RuleCollection
     */
    protected $ruleCollection;

    /**
     * @var MetaCollection
     */
    protected $metaCollection;

    /**
     * SeoRuleAbstract constructor.
     *
     * @param Http $request
     * @param HelperData $helperData
     * @param MetaCollection $metaCollection
     * @param RuleCollection $ruleCollection
     * @param StoreManagerInterface $storeManager
     * @param ProductFactory $productFactory
     * @param ManagerInterface $messageManager
     * @param Registry $registry
     */
    public function __construct(
        Http $request,
        HelperData $helperData,
        MetaCollection $metaCollection,
        RuleCollection $ruleCollection,
        StoreManagerInterface $storeManager,
        ProductFactory $productFactory,
        ManagerInterface $messageManager,
        Registry $registry
    ) {
        $this->request        = $request;
        $this->helperData     = $helperData;
        $this->ruleCollection = $ruleCollection;
        $this->metaCollection = $metaCollection;
        $this->storeManager   = $storeManager;
        $this->messageManager = $messageManager;
        $this->productFactory = $productFactory;
        $this->registry       = $registry;
    }

    /**
     * @param $object
     * @param $id
     * @param $type
     *
     * @throws NoSuchEntityException
     */
    public function setMetaData($object, $id, $type)
    {
        $currentStore = $this->storeManager->getStore()->getId();
        if ($type == Type::LAYERED_NAVIGATION) {
            $metaModel      = false;
            $ruleCollection = $this->ruleCollection
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('entity_type', Type::LAYERED_NAVIGATION)
                ->addFieldToFilter('stores', [
                    ['finset' => Store::DEFAULT_STORE_ID],
                    ['finset' => $currentStore]
                ])
                ->setOrder('sort_order', 'ASC');
            /** @var Rule $rule */
            foreach ($ruleCollection as $rule) {
                if ($rule->getMatchingLayerNavigation($this->request->getParams())) {
                    $metaModel = $rule;
                    break;
                }
            }

            if (!$metaModel) {
                return;
            }

            $dataAttributeFilter = [];
            $productResource     = $this->productFactory->create()->getResource();
            foreach ($this->request->getParams() as $key => $param) {
                if ($key == 'id' || $key == 'p' || is_array($param)) {
                    continue;
                }
                if ($attKey = $productResource->getAttribute($key)) {
                    $optionText = $attKey->getSource()->getOptionText($param);
                    if (is_array($optionText)) {
                        $dataAttributeFilter['{{' . $key . '}}'] = $attKey->getStoreLabel() . ' ' . implode(
                            ' ',
                            $optionText
                        );
                    } else {
                        $dataAttributeFilter['{{' . $key . '}}'] = $attKey->getStoreLabel() . ' ' . $optionText;
                    }
                }
            }
            $dataAttributeFilter['{{category_name}}'] = $object->getName();

            $metaModel->setMetaTitle($this->helperData->generateMetaTemplateForLayer(
                $metaModel->getMetaTitle(),
                $dataAttributeFilter
            ));
            $metaModel->setMetaDescription($this->helperData->generateMetaTemplateForLayer(
                $metaModel->getMetaDescription(),
                $dataAttributeFilter
            ));
            $metaModel->setMetaKeywords($this->helperData->generateMetaTemplateForLayer(
                $metaModel->getMetaKeywords(),
                $dataAttributeFilter,
                true
            ));
        } else {
            /** @var MetaCollection $metaCollection */
            $canApplyRules = $this->ruleCollection
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('entity_type', $type)
                ->addFieldToFilter('stores', [
                    ['finset' => Store::DEFAULT_STORE_ID],
                    ['finset' => $currentStore]
                ])
                ->getAllIds();
            $metaCollection = $this->metaCollection
                ->addFieldToFilter($id, $object->getId())
                ->addFieldToFilter('entity_type', $type)
                ->addFieldToFilter('stores', [
                    ['finset' => Store::DEFAULT_STORE_ID],
                    ['finset' => $currentStore]
                ])
                ->addFieldToFilter('rule_id', ['in' => $canApplyRules])
                ->setOrder('sort_order', 'ASC');

            /** @var Meta $metaModel */
            $metaModel = $metaCollection->getFirstItem();
            if (!$metaModel) {
                return;
            }

            if ($type == Type::PRODUCTS) {
                $metaModel->setMetaTitle($this->helperData->replaceCategoryName(
                    $object,
                    $metaModel->getMetaTitle(),
                    $type
                ));
                $metaModel->setMetaDescription($this->helperData->replaceCategoryName(
                    $object,
                    $metaModel->getMetaDescription(),
                    $type
                ));
                $metaModel->setMetaKeywords($this->helperData->replaceCategoryName(
                    $object,
                    $metaModel->getMetaKeywords(),
                    $type
                ));
            }
        }

        if ($metaModel->getApplyTemplate() == ApplyTemplate::FORCE_UPDATE) {
            $this->helperData->forceUpdateTemplate($object, $metaModel);
        } else {
            $this->helperData->skipUpdateTemplate($object, $metaModel);
        }
    }
}
