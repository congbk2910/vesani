<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_AbTesting
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
namespace Webkul\AbTesting\Block\Adminhtml\AbTest;

class Reports extends \Magento\Backend\Block\Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'abtest/reports.phtml';

   /**
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
    * @param \Magento\Framework\ObjectManagerInterface $objectManager
    * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Webkul\AbTesting\Helper\Data $moduleHelper,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->formKey = $context->getFormKey();
        $this->moduleHelper = $moduleHelper;
        parent::__construct($context, $data);
    }

    /**
     * return test id on goals
     *
     * @param int $testId
     * @return void
     */
    public function getAllGoalsOnTestId($testId)
    {
        return $this->moduleHelper->getAllGoalsOnTestId($testId);
    }

    /**
     * return all varinat id on test
     *
     * @param int $testId
     * @return array
     */
    public function getAllVariantIdOnTestId($testId)
    {
        return $this->moduleHelper->getAllVariantIdOnTestId($testId);
    }

    /**
     * return variants on goals
     *
     * @param array $variantArr
     * @return array
     */
    public function getGoalsDataOnVariant($variantArr)
    {
        return $this->moduleHelper->getGoalsDataOnVariant($variantArr);
    }

    /**
     * return goals on variant id
     *
     * @param int $variantId
     * @return void
     */
    public function getGoalsOnVariantId($variantId)
    {
        return $this->moduleHelper->getGoalsOnVariantId($variantId);
    }

    /**
     * return variant name from variant id
     *
     * @param int $variantId
     * @return void
     */
    public function getVariantNameFromId($variantId)
    {
        return $this->moduleHelper->getVariantNameFromId($variantId);
    }

    /**
     * return goal Name from id
     *
     * @param int $goalId
     * @return string
     */
    public function getGoalNameFromId($goalId)
    {
        return $this->moduleHelper->getGoalNameFromId($goalId);
    }

    /**
     * return main goalName
     *
     * @param int $goalId
     * @return string
     */
    public function getMainGoalNameFromId($goalId)
    {
        return $this->moduleHelper->getMainGoalNameFromId($goalId);
    }
}
