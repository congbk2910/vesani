<?php
namespace Svea\Checkout\Model\Client\DTO;


use Svea\Checkout\Model\Client\DTO\Order\OrderRow;

class UpdateOrderCart extends AbstractRequest
{

    /**
     * Required
     * @var $items OrderRow[]
     */
    protected $items;

    /** @var $merchantData string */
    protected $merchantData;

    /**
     * @return string
     */
    public function getMerchantData()
    {
        return $this->merchantData;
    }

    /**
     * @param string $merchantData
     * @return UpdateOrderCart
     */
    public function setMerchantData($merchantData)
    {
        $this->merchantData = $merchantData;
        return $this;
    }
    

    /**
     * @return OrderRow[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param OrderRow[] $items
     * @return UpdateOrderCart
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }


    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    public function toArray()
    {
        $items = [];
        if (!empty($this->getItems())) {
            foreach ($this->getItems() as $item) {
                $items[] = $item->toArray();
            }
        }

        return [
            'cart' => ['Items' => $items],
            'merchantData' => $this->getMerchantData()
        ];
    }


}