<?php
namespace Svea\Checkout\Model\Client\DTO;

use Svea\Checkout\Model\Client\DTO\Order\Address;
use Svea\Checkout\Model\Client\DTO\Order\Customer;
use Svea\Checkout\Model\Client\DTO\Order\GetDelivery;
use Svea\Checkout\Model\Client\DTO\Order\GetOrder;
use Svea\Checkout\Model\Client\DTO\Order\Gui;
use Svea\Checkout\Model\Client\DTO\Order\IdentityFlags;
use Svea\Checkout\Model\Client\DTO\Order\MerchantSettings;
use Svea\Checkout\Model\Client\DTO\Order\OrderRow;
use Svea\Checkout\Model\Client\DTO\Order\PresetValue;

class GetDeliveryResponse extends GetDelivery
{

    private $_data;

    /**
     * CreatePaymentResponse constructor.
     * @param $response string
     */
    public function __construct($response = null)
    {
        if ($response == null) {
            return;
        }

        if (is_string($response)) {
            $data = json_decode($response, true);
        } else {
            // we think its an array!
            $data = $response;
        }

        $this->_data = $data;


        $actions = $this->get('Actions');
        $actions = is_array($actions) ? $actions : [];
        $this->setActions($actions);

        $this->setId($this->get('Id'));
        $this->setInvoiceId($this->get('InvoiceId'));
        $this->setDeliveryAmount($this->get('DeliveryAmount'));

        if (isset($data['OrderRows'])) {
            $items = $data['OrderRows'];
            $orderRows = [];
            $cartActions = [];
            foreach ($items as $item) {
                $orderRow = new OrderRow();
                // fill
                $orderRow
                    ->setArticleNumber($item['ArticleNumber'])
                    ->setName($item['Name'])
                    ->setQuantity($item['Quantity'])
                    ->setUnitPrice($item['UnitPrice'])
                    ->setUnit($item['Unit'])
                    ->setDiscountPercent($item['DiscountPercent'])
                    ->setVatPercent($item['VatPercent'])
                    ->setRowNumber($item['OrderRowId']);

                // add to array
                $orderRows[] = $orderRow;

                // we save them seperatly
                $cartActions[$orderRow->getRowNumber()] = $item["Actions"];
            }

            $this->setCartActions($cartActions);
            $this->setCartItems($orderRows);
        }
    }


    // Helpers!
    private function get($key)
    {
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }

        return null;
    }

}