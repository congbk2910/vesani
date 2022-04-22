<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_AbTesting
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\AbTesting\Controller\Adminhtml\ViewSelectors;

class Grid extends \Magento\Backend\App\Action
{
    /**
     * @var RawFactory
     */
    protected $layoutFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->_backendSession = $context->getSession();
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
    }

    /**
     * execute controller
     *
     * @return void
     */
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        $class= \Webkul\AbTesting\Block\Adminhtml\ViewSelectors\SelectorsList::class;
        $block = 'abtesting.viewselectors.selectorsgrid';
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                $class,
                $block
            )->toHtml()
        );
    }
}
