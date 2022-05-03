<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\Mail\MessageBuilder;

use Magento\Framework\ObjectManagerInterface;

class MessageBuilderFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface|null
     */
    protected $objectManager = null;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create message Builder
     *
     * @param array $data
     *
     * @return mixed|null
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create(MessageBuilder::class, $data);
    }
}
