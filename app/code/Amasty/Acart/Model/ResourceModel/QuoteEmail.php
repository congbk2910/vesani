<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\ResourceModel;

use Amasty\Acart\Model\QuoteEmail as QuoteEmailModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class QuoteEmail extends AbstractDb
{
    public const TABLE_NAME = 'amasty_acart_quote_email';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, QuoteEmailModel::QUOTE_EMAIL_ID);
    }
}
