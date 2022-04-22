<?php

namespace Omnisend\Omnisend\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;

class OrderStatusActions extends Column
{
    /**
     * Url paths
     */
    const ORDER_STATUS_URL_PATH_EDIT = 'omnisend/orderstatus/edit';
    const ORDER_STATUS_URL_PATH_DELETE = 'omnisend/orderstatus/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $name = $this->getData('name');

        foreach ($dataSource['data']['items'] as & $item) {
            if (!isset($item[OmnisendOrderStatusInterface::STATUS])) {
                continue;
            }

            $item[$name]['edit'] = [
                'href' => $this->urlBuilder->getUrl(
                    self::ORDER_STATUS_URL_PATH_EDIT,
                    [OmnisendOrderStatusInterface::STATUS => $item[OmnisendOrderStatusInterface::STATUS]]
                ),
                'label' => __('Edit')
            ];

            $item[$name]['delete'] = [
                'href' => $this->urlBuilder->getUrl(
                    self::ORDER_STATUS_URL_PATH_DELETE,
                    [OmnisendOrderStatusInterface::STATUS => $item[OmnisendOrderStatusInterface::STATUS]]
                ),
                'label' => __('Delete'),
                'confirm' => [
                    'title' => __('Delete Order Status'),
                    'message' => __('Are you sure you want to delete this?')
                ]
            ];
        }

        return $dataSource;
    }
}
