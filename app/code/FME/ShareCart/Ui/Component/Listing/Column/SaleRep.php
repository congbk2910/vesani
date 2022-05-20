<?php
namespace FME\ShareCart\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class SaleRep extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
    const NAME = 'column.sale_rep';

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $saleRepId = '';
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $saleRepId = $item['sale_rep_id'];
                if (!$saleRepId) break; 
                $saleRep = $this->customerFactory->create()->load($saleRepId);

                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'customer/index/edit',
                        ['id' => $saleRepId]
                    ),
                    'label' => __($saleRep->getName()),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
