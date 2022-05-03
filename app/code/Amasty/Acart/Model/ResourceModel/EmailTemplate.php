<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model\ResourceModel;

use Amasty\Acart\Model\EmailTemplate as EmailTemplateModel;
use Magento\Email\Model\ResourceModel\Template as ResourceTemplate;

class EmailTemplate extends ResourceTemplate
{
    public const TABLE_NAME = 'amasty_acart_email_template';

    /**
     * Initialize email template resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, EmailTemplateModel::TEMPLATE_ID);
    }
}
