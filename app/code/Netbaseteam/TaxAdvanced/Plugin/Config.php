<?php
namespace Netbaseteam\TaxAdvanced\Plugin;

class Config
{
    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    public function beforeDisplayCartTaxWithGrandTotal()
    {
        $isExkl = $this->request->getParam('exkl');
        if ($isExkl) return false;
    }
}