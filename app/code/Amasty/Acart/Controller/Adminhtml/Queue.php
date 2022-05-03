<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml;

abstract class Queue extends \Magento\Backend\App\Action
{
    public const ADMIN_RESOURCE = 'Amasty_Acart::acart_queue';
}
