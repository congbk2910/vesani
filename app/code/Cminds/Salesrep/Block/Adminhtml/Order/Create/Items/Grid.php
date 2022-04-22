<?php

namespace Cminds\Salesrep\Block\Adminhtml\Order\Create\Items;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Backend\Model\Auth\Session;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\GiftMessage\Model\Save;
use Magento\Tax\Model\Config;
use Magento\Tax\Helper\Data;
use Magento\GiftMessage\Helper\Message;

class Grid extends AbstractCreate
{
    /**
     * Flag to check can items be move to customer storage
     *
     * @var bool
     */
    protected $moveToCustomerStorage = true;

    /**
     * Tax data
     *
     * @var Data
     */
    protected $taxData;

    /**
     * Wishlist factory
     *
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * Gift message save
     *
     * @var Save
     */
    protected $giftMessageSave;

    /**
     * Tax config
     *
     * @var Config
     */
    protected $taxConfig;

    /**
     * Message helper
     *
     * @var Message
     */
    protected $messageHelper;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var StockStateInterface
     */
    protected $stockState;

    /**
     * @param Context $context,
     * @param Quote $sessionQuote,
     * @param Create $orderCreate,
     * @param Session $authSession,
     * @param WishlistFactory $wishlistFactory,
     * @param Save $giftMessageSave,
     * @param Config $taxConfig,
     * @param Data $taxData,
     * @param Message $messageHelper,
     * @param StockRegistryInterface $stockRegistry,
     * @param StockStateInterface $stockState,
     * @param PriceCurrencyInterface $priceCurrency,
     * @param array $data
     */
    public function __construct(
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        Session $authSession,
        WishlistFactory $wishlistFactory,
        Save $giftMessageSave,
        Config $taxConfig,
        Data $taxData,
        Message $messageHelper,
        StockRegistryInterface $stockRegistry,
        StockStateInterface $stockState,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->messageHelper = $messageHelper;
        $this->wishlistFactory = $wishlistFactory;
        $this->giftMessageSave = $giftMessageSave;
        $this->taxConfig = $taxConfig;
        $this->taxData = $taxData;
        $this->stockRegistry = $stockRegistry;
        $this->stockState = $stockState;
        $this->authSession = $authSession;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_search_grid');
    }

    /**
     * Get items.
     *
     * @return Item[]
     */
    public function getItems()
    {
        $items = $this->getParentBlock()->getItems();
        $oldSuperMode = $this->getQuote()->getIsSuperMode();
        $this->getQuote()->setIsSuperMode(false);
        foreach ($items as $item) {
            $item->setQty($item->getQty());

            if (!$item->getMessage()) {
                $stockItemToCheck = [];

                $childItems = $item->getChildren();
                if (count($childItems)) {
                    foreach ($childItems as $childItem) {
                        $stockItemToCheck[] = $childItem->getProduct()->getId();
                    }
                } else {
                    $stockItemToCheck[] = $item->getProduct()->getId();
                }

                foreach ($stockItemToCheck as $productId) {
                    $check = $this->stockState->checkQuoteItemQty(
                        $productId,
                        $item->getQty(),
                        $item->getQty(),
                        $item->getQty(),
                        $this->getQuote()->getStore()->getWebsiteId()
                    );
                    $item->setMessage($check->getMessage());
                    $item->setHasError($check->getHasError());
                }
            }

            if ($item->getProduct()->getStatus() == ProductStatus::STATUS_DISABLED) {
                $item->setMessage(__('This product is disabled.'));
                $item->setHasError(true);
            }
        }
        $this->getQuote()->setIsSuperMode($oldSuperMode);

        return $items;
    }

    /**
     * Get session
     *
     * @return SessionManagerInterface
     */
    public function getSession()
    {
        return $this->getParentBlock()->getSession();
    }

    /**
     * Get item editable price.
     *
     * @param Item $item
     * 
     * @return float
     */
    public function getItemEditablePrice($item)
    {
        return $item->getCalculationPrice() * 1;
    }

    /**
     * Get original editable price.
     *
     * @param Item $item
     * 
     * @return float
     */
    public function getOriginalEditablePrice($item)
    {
        if ($item->hasOriginalCustomPrice()) {
            $result = $item->getOriginalCustomPrice() * 1;
        } elseif ($item->hasCustomPrice()) {
            $result = $item->getCustomPrice() * 1;
        } else {
            if ($this->taxData->priceIncludesTax($this->getStore())) {
                $result = $item->getPriceInclTax() * 1;
            } else {
                $result = $item->getOriginalPrice() * 1;
            }
        }

        return $result;
    }

    /**
     * Get item original price.
     *
     * @param Item $item
     * 
     * @return float
     */
    public function getItemOrigPrice($item)
    {
        return $this->convertPrice($item->getPrice());
    }

    /**
     * Check gift messages availability.
     *
     * @param Item|null $item
     * 
     * @return bool|null|string
     */
    public function isGiftMessagesAvailable($item = null)
    {
        if ($item === null) {
            return $this->messageHelper->isMessagesAllowed(
                'items', 
                $this->getQuote(), 
                $this->getStore()
            );
        }

        return $this->messageHelper->isMessagesAllowed(
            'item', 
            $item, 
            $this->getStore()
        );
    }

    /**
     * Check if allowed for gift message.
     *
     * @param Item $item
     * 
     * @return bool
     */
    public function isAllowedForGiftMessage($item)
    {
        return $this->giftMessageSave->getIsAllowedQuoteItem($item);
    }

    /**
     * Check if we need display grid totals include tax.
     *
     * @return bool
     */
    public function displayTotalsIncludeTax()
    {
        $result = $this->taxConfig->displayCartSubtotalInclTax($this->getStore())
            || $this->taxConfig->displayCartSubtotalBoth($this->getStore());

        return $result;
    }

    /**
     * Get subtotal.
     *
     * @return false|float
     */
    public function getSubtotal()
    {
        $address = $this->getQuoteAddress();
        if (!$this->displayTotalsIncludeTax()) {
            
            return $address->getSubtotal();
        }
        if ($address->getSubtotalInclTax()) {
            
            return $address->getSubtotalInclTax();
        }

        return $address->getSubtotal() + $address->getTaxAmount();
    }

    /**
     * Get subtotal with discount.
     *
     * @return float
     */
    public function getSubtotalWithDiscount()
    {
        $address = $this->getQuoteAddress();
        if ($this->displayTotalsIncludeTax()) {
            
            return $address->getSubtotal()
                + $address->getTaxAmount()
                + $address->getDiscountAmount()
                + $address->getDiscountTaxCompensationAmount();
        } else {
            
            return $address->getSubtotal() + $address->getDiscountAmount();
        }
    }

    /**
     * Get discount amount.
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->getQuote()->getShippingAddress()->getDiscountAmount();
    }

    /**
     * Retrieve quote address.
     *
     * @return Address
     */
    public function getQuoteAddress()
    {
        if ($this->getQuote()->isVirtual()) {
            return $this->getQuote()->getBillingAddress();
        } else {
            return $this->getQuote()->getShippingAddress();
        }
    }

    /**
     * Define if specified item has already applied custom price.
     *
     * @param Item $item
     * 
     * @return bool
     */
    public function usedCustomPriceForItem($item)
    {
        return $item->hasCustomPrice();
    }

    /**
     * Define if custom price can be applied for specified item.
     *
     * @param Item $item
     * 
     * @return bool
     */
    public function canApplyCustomPrice($item)
    {
        return !$item->isChildrenCalculated();
    }

    /**
     * Get qty title
     *
     * @param Item $item
     * 
     * @return string
     */
    public function getQtyTitle($item)
    {
        $prices = $item->getProduct()
            ->getPriceInfo()
            ->getPrice(\Magento\Catalog\Pricing\Price\TierPrice::PRICE_CODE)
            ->getTierPriceList();
        if ($prices) {
            $info = [];
            foreach ($prices as $data) {
                $price = $this->convertPrice($data['price']);
                $info[] = __('Buy %1 for price %2', $data['price_qty'], $price);
            }
            
            return implode(', ', $info);
        } else {
            
            return __('Item ordered qty');
        }
    }

    /**
     * Get tier price html.
     *
     * @param Item $item
     * 
     * @return string
     */
    public function getTierHtml($item)
    {
        $html = '';
        $prices = $item->getProduct()->getTierPrice();
        if ($prices) {
            if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                $info = $this->_getBundleTierPriceInfo($prices);
            } else {
                $info = $this->_getTierPriceInfo($prices);
            }

            $html = implode('<br />', $info);
        }
        
        return $html;
    }

    /**
     * Get tier price info to display in grid for Bundle product.
     *
     * @param array $prices
     * 
     * @return string[]
     */
    protected function _getBundleTierPriceInfo($prices)
    {
        $info = [];
        foreach ($prices as $data) {
            $qty = $data['price_qty'] * 1;
            $info[] = __('%1 with %2 discount each', $qty, $data['price'] * 1 . '%');
        }
        
        return $info;
    }

    /**
     * Get tier price info to display in grid.
     *
     * @param array $prices
     * 
     * @return string[]
     */
    protected function _getTierPriceInfo($prices)
    {
        $info = [];
        foreach ($prices as $data) {
            $qty = $data['price_qty'] * 1;
            $price = $this->convertPrice($data['price']);
            $info[] = __('%1 for %2', $qty, $price);
        }
        
        return $info;
    }

    /**
     * Get Custom Options of item.
     *
     * @param Item $item
     * @return string
     *
     * @deprecated 100.2.0
     */
    public function getCustomOptions(Item $item)
    {
        $optionStr = '';
        $this->moveToCustomerStorage = true;
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $item->getProduct()->getOptionById($optionId);
                if ($option) {
                    $optionStr .= $option->getTitle() . ':';
                    $quoteItemOption = $item->getOptionByCode('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setQuoteItemOption($quoteItemOption);

                    $optionStr .= $group->getEditableOptionValue(
                        $quoteItemOption->getValue()
                    );
                    $optionStr .= "\n";
                }
            }
        }
        
        return $optionStr;
    }

    /**
     * Get flag for rights to move items to customer storage.
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getMoveToCustomerStorage()
    {
        return $this->moveToCustomerStorage;
    }

    /**
     * Display subtotal including tax.
     *
     * @param Item $item
     * 
     * @return string
     */
    public function displaySubtotalInclTax($item)
    {
        if ($item->getTaxBeforeDiscount()) {
            $tax = $item->getTaxBeforeDiscount();
        } else {
            $tax = $item->getTaxAmount() ? $item->getTaxAmount() : 0;
        }
        
        return $this->formatPrice($item->getRowTotal() + $tax);
    }

    /**
     * Display original price including tax.
     *
     * @param Item $item
     * 
     * @return float
     */
    public function displayOriginalPriceInclTax($item)
    {
        $tax = 0;
        if ($item->getTaxPercent()) {
            $tax = $item->getPrice() * ($item->getTaxPercent() / 100);
        }
        
        return $this->convertPrice($item->getPrice() + $tax / $item->getQty());
    }

    /**
     * Display row total with discount including tax.
     *
     * @param Item $item
     * 
     * @return string
     */
    public function displayRowTotalWithDiscountInclTax($item)
    {
        $tax = $item->getTaxAmount() ? $item->getTaxAmount() : 0;
        
        return $this->formatPrice($item->getRowTotal() - $item->getDiscountAmount() + $tax);
    }

    /**
     * Get including/excluding tax message.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getInclExclTaxMessage()
    {
        if ($this->taxData->priceIncludesTax($this->getStore())) {
            
            return __('* - Enter custom price including tax');
        } else {
            
            return __('* - Enter custom price excluding tax');
        }
    }

    /**
     * Get store.
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->getQuote()->getStore();
    }

    /**
     * Return html button which calls configure window.
     *
     * @param Item $item
     * @return string
     */
    public function getConfigureButtonHtml($item)
    {
        $product = $item->getProduct();

        $options = ['label' => __('Configure')];
        if ($product->canConfigure()) {
            $options['onclick'] = sprintf(
                'order.showQuoteItemConfiguration(%s)', 
                $item->getId()
            );
        } else {
            $options['class'] = ' disabled';
            $options['title'] = __('This product does not have any configurable options');
        }

        return $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData($options)->toHtml();
    }

    /**
     * Get order item extra info block.
     *
     * @param Item $item
     * 
     * @return AbstractBlock
     */
    public function getItemExtraInfo($item)
    {
        return $this->getLayout()->getBlock('order_item_extra_info')->setItem($item);
    }

    /**
     * Returns whether moving to wishlist is allowed for this item.
     *
     * @param Item $item
     * 
     * @return bool
     */
    public function isMoveToWishlistAllowed($item)
    {
        return $item->getProduct()->isVisibleInSiteVisibility();
    }

    /**
     * Retrieve collection of customer wishlists.
     *
     * @return \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection
     */
    public function getCustomerWishlists()
    {
        return $this->wishlistFactory->create()->getCollection()->filterByCustomerId($this->getCustomerId());
    }

    /**
     * Get the item unit price html.
     *
     * @param Item $item
     * 
     * @return string
     */
    public function getItemUnitPriceHtml(Item $item)
    {
        $block = $this->getLayout()->getBlock('item_unit_price');
        $block->setItem($item);
        
        return $block->toHtml();
    }

    /**
     * Get the item row total html.
     *
     * @param Item $item
     * 
     * @return string
     */
    public function getItemRowTotalHtml(Item $item)
    {
        $block = $this->getLayout()->getBlock('item_row_total');
        $block->setItem($item);
        
        return $block->toHtml();
    }

    /**
     * Return html for row total with discount.
     *
     * @param Item $item
     * 
     * @return string
     */
    public function getItemRowTotalWithDiscountHtml(Item $item)
    {
        $block = $this->getLayout()->getBlock('item_row_total_with_discount');
        $block->setItem($item);
        
        return $block->toHtml();
    }

    /**
     * Return the current customer data.
     *
     * @param Item $item
     * 
     * @return array
     */
    public function getCurrentUser()
    {
        return $this->authSession->getUser();
    }
}
