<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Block\Adminhtml;

use Amasty\Base\Helper\Module;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Info extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory
     */
    private $cronFactory;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Module
     */
    private $moduleHelper;

    /**
     * @var \Amasty\Base\Model\MagentoVersion
     */
    private $magentoVersion;

    /**
     * @var \Magento\Framework\App\DeploymentConfig\Reader
     */
    private $reader;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $cronFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\DeploymentConfig\Reader $reader,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Amasty\Base\Model\MagentoVersion $magentoVersion,
        Module $moduleHelper,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);

        $this->layoutFactory = $layoutFactory;
        $this->cronFactory = $cronFactory;
        $this->directoryList = $directoryList;
        $this->resourceConnection = $resourceConnection;
        $this->magentoVersion = $magentoVersion;
        $this->reader = $reader;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html = $this->_getHeaderHtml($element);

        $html .= $this->getSystemTime($element);
        $html .= $this->getCronInfo($element);

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    private function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $layout = $this->layoutFactory->create();

            $this->_fieldRenderer = $layout->createBlock(
                \Magento\Config\Block\System\Config\Form\Field::class
            );
        }

        return $this->_fieldRenderer;
    }

    /**
     * @param AbstractElement $fieldset
     * @return string
     */
    private function getSystemTime($fieldset)
    {
        if (version_compare($this->magentoVersion->get(), '2.2', '>=')) {
            $time = $this->resourceConnection->getConnection()->fetchOne("select now()");
        } else {
            $time = $this->_localeDate->date()->format('H:i:s');
        }

        return $this->getFieldHtml($fieldset, 'mysql_current_date_time', __("Current Time"), $time);
    }

    /**
     * @param AbstractElement $fieldset
     * @return string
     */
    private function getCronInfo($fieldset)
    {
        $crontabCollection = $this->cronFactory->create();
        $crontabCollection->addFieldToFilter('job_code', ['like' => 'amasty_acart_%']);
        $crontabCollection->setOrder('schedule_id')->setPageSize(5);

        $knowledgeBaseUrl = 'https://amasty.com/knowledge-base/magento-cron.html' .
            "?utm_source=extension&utm_medium=link&utm_campaign=abandoned-cart-m2-e-cron-faq";
        if ($this->moduleHelper->isOriginMarketplace()) {
            $knowledgeBaseUrl = "https://amasty.com/docs/doku.php?id=magento_2:cron-scheduler" .
                "&utm_source=extension&utm_medium=link&utm_campaign=cronscheduler_m2_guide";
        }
        if ($crontabCollection->count() === 0) {
            $value = '<div class="red">';
            $value .= __('No cron jobs found') . "</div>";
            $value .=
                "<a target='_blank'
                  href='".$knowledgeBaseUrl."'>" .
                __("Learn more") .
                "</a>";
        } else {
            $value = '<table>';
            foreach ($crontabCollection as $crontabRow) {
                $value .=
                    '<tr>' .
                    '<td>' . $crontabRow['job_code'] . '</td>' .
                    '<td>' . $crontabRow['status'] . '</td>' .
                    '<td>' . $crontabRow['created_at'] . '</td>' .
                    '</tr>';
            }
            $value .= '</table>';
        }

        $label = __('Cron (Last 5)');

        return $this->getFieldHtml($fieldset, 'cron_configuration', $label, $value);
    }

    /**
     * @param AbstractElement $fieldset
     * @param string $fieldName
     * @param string $label
     * @param string $value
     * @return string
     */
    private function getFieldHtml($fieldset, $fieldName, $label = '', $value = '')
    {
        $field = $fieldset->addField($fieldName, 'label', [
            'name'  => 'dummy',
            'label' => $label,
            'after_element_html' => $value,
        ])->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }
}
