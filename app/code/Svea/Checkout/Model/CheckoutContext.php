<?php

namespace Svea\Checkout\Model;

use Magento\Customer\Api\Data\AddressInterfaceFactory;

/**
 * Class CheckoutContext
 *
 * @package Svea\Checkout\Model
 */
class CheckoutContext
{
    /**
     * @var \Svea\Checkout\Helper\Data
     */
    protected $helper;

    /**
     * @var \Svea\Checkout\Logger\Logger
     */
    protected $logger;

    /** @var \Svea\Checkout\Model\Svea\Order $sveaOrderHandler */
    protected $sveaOrderHandler;

    /** @var \Magento\Sales\Api\OrderCustomerManagementInterface */
    protected $orderCustomerManagement;

    /** @var \Magento\Newsletter\Model\Subscriber $Subscriber */
    protected $subscriber;

    /** @var \Svea\Checkout\Model\Svea\Locale $sveaLocale */
    protected $sveaLocale;

    /** @var CheckoutOrderNumberReference $sveaCheckoutReferenceHelper */
    protected $sveaCheckoutReferenceHelper;

    /** @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepository */
    protected $orderRepository;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressInterfaceFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * Constructor
     *
     * @param \Svea\Checkout\Helper\Data $helper
     * @param \Svea\Checkout\Model\Svea\Order $sveaOrderHandler
     * @param CheckoutOrderNumberReference $sveaCheckoutReferenceHelper
     * @param \Svea\Checkout\Logger\Logger $logger
     * @param \Svea\Checkout\Model\Svea\Locale $sveaLocale ,
     * @param \Magento\Sales\Api\OrderCustomerManagementInterface $orderCustomerManagement
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        \Svea\Checkout\Helper\Data $helper,
        \Svea\Checkout\Model\Svea\Order $sveaOrderHandler,
        \Svea\Checkout\Model\CheckoutOrderNumberReference $sveaCheckoutReferenceHelper,
        \Svea\Checkout\Logger\Logger $logger,
        \Svea\Checkout\Model\Svea\Locale $sveaLocale,
        \Magento\Sales\Api\OrderCustomerManagementInterface $orderCustomerManagement,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        AddressInterfaceFactory $addressInterfaceFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->helper        = $helper;
        $this->logger = $logger;
        $this->sveaOrderHandler = $sveaOrderHandler;
        $this->sveaLocale = $sveaLocale;
        $this->sveaCheckoutReferenceHelper = $sveaCheckoutReferenceHelper;
        $this->orderCustomerManagement = $orderCustomerManagement;
        $this->subscriber = $subscriber;
        $this->orderRepository = $orderRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->addressRepository = $addressRepository;
        $this->addressInterfaceFactory = $addressInterfaceFactory;
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

    /** @return \Svea\Checkout\Model\Svea\Order */
    public function getSveaOrderHandler()
    {
        return $this->sveaOrderHandler;
    }

    /**
     * @return \Magento\Sales\Api\OrderCustomerManagementInterface
     */
    public function getOrderCustomerManagement()
    {
        return $this->orderCustomerManagement;
    }

    /**
     * @return \Magento\Newsletter\Model\Subscriber
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @return Svea\Locale
     */
    public function getSveaLocale()
    {
        return $this->sveaLocale;
    }

    /**
     * @return CheckoutOrderNumberReference
     */
    public function getSveaCheckoutReferenceHelper()
    {
        return $this->sveaCheckoutReferenceHelper;
    }

    /**
     * @return \Magento\Sales\Api\OrderRepositoryInterface
     */
    public function getOrderRepository()
    {
        return $this->orderRepository;
    }

    /**
     * @return \Magento\Customer\Api\AddressRepositoryInterface
     */
    public function getAddressRepository(): \Magento\Customer\Api\AddressRepositoryInterface
    {
        return $this->addressRepository;
    }

    /**
     * @return AddressInterfaceFactory
     */
    public function getAddressInterfaceFactory(): AddressInterfaceFactory
    {
        return $this->addressInterfaceFactory;
    }
    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    public function getOrderCollectionFactory(): \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
    {
        return $this->orderCollectionFactory;
    }

}
