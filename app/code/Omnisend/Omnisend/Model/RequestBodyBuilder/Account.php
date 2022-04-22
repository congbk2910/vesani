<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Serializer\Json;

/**
 * Class Account
 * @package Omnisend\Omnisend\Model\RequestBodyBuilder
 */
class Account extends AbstractBodyBuilder implements RequestBodyBuilderInterface
{
    const WEBSITE = 'website';
    const PLATFORM = 'platform';
    const PLATFORM_NAME = 'magento';
    const STORE_ID = 'store_id';
    const VERSION = 'version';
    const VERIFICATION_URL = 'verificationUrl';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Json
     */
    protected $json;

    /**
     * Account constructor.
     * @param StoreManagerInterface $storeManager
     * @param Json $json
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Json $json
    ) {
        $this->storeManager = $storeManager;
        $this->json = $json;
    }

    /**
     * @param $data
     * @return string
     * @throws NoSuchEntityException
     */
    public function build($data)
    {
        $this->addData(self::WEBSITE, $data[self::WEBSITE]);
        $this->addData(self::PLATFORM, self::PLATFORM_NAME);
        $this->addData(self::VERSION, $data[self::VERSION]);
        $this->addData(self::VERIFICATION_URL, $data[self::VERIFICATION_URL]);

        return $this->json->serialize($this->getData());
    }
}
