<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Ui\Component\Listing\Column;

use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Quote\Api\Data\CurrencyInterface as QuoteCurrencyInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Price extends Column
{
    /**
     * @var CurrencyInterface
     */
    private $localeCurrency;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CurrencyInterface $localeCurrency,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->localeCurrency = $localeCurrency;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');

            foreach ($dataSource['data']['items'] as &$item) {
                $storeCurrencyCode = $item[QuoteCurrencyInterface::KEY_QUOTE_CURRENCY_CODE] ?? null;
                $currency = $this->localeCurrency->getCurrency(
                    $storeCurrencyCode
                );

                if (isset($item[$fieldName])) {
                    $item[$fieldName] = $currency->toCurrency(sprintf("%f", $item[$fieldName]));
                }
            }
        }

        return $dataSource;
    }
}
