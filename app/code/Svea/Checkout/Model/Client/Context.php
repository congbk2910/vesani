<?php

namespace Svea\Checkout\Model\Client;


class Context 
{
    /**
     * @var \Svea\Checkout\Helper\Data
     */
    protected $helper;

    /**
     * @var\Svea\Checkout\Logger
     */
    protected $logger;


   /**
     * Constructor
     *
     * @param \Svea\Checkout\Helper\Data $helper
     * @param \Svea\Checkout\Logger\Logger $logger
     *
     */
    public function __construct(
        \Svea\Checkout\Helper\Data $helper,
        \Svea\Checkout\Logger\Logger $logger
    ) {
        $this->helper        = $helper;
        $this->logger = $logger;

    }

    /**
     * @return \Svea\Checkout\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @return \Svea\Checkout\Logger\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
    
}