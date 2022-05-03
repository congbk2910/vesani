<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Rule;

use Amasty\Acart\Controller\Adminhtml\Rule;

class Index extends Rule
{
    public function execute()
    {
        $resultPage = $this->initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Campaigns'));

        return $resultPage;
    }
}
