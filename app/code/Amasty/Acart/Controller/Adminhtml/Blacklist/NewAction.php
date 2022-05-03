<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Blacklist;

use Amasty\Acart\Controller\Adminhtml\Blacklist;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends Blacklist
{
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $result->forward('edit');
    }
}
