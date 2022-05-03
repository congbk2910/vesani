<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\Config\Backend;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Message\ManagerInterface as MessageManager;

class SafeMode extends \Magento\Framework\App\Config\Value
{
    public const RECIPIENT_EMAIL_FIELDSET = 'recipient_email';
    public const DEFAULT_VALUE = 0;

    /**
     * @var MessageManager
     */
    protected $messageManager;

    public function __construct(
        Context $context,
        MessageManager $messageManager,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->messageManager = $messageManager;
        parent::__construct($context, $registry, $scopeConfig, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function beforeSave()
    {
        if ($this->getValue() && !$this->getFieldsetDataValue(self::RECIPIENT_EMAIL_FIELDSET)) {
            $this->setValue(self::DEFAULT_VALUE);
            $this->messageManager->addNoticeMessage(
                __('Please fill in the test email in the extension configuration section')
            );
        }

        parent::beforeSave();
    }
}
