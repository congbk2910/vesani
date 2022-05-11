<?php
namespace Netbaseteam\TaxAdvanced\Plugin;

class Config
{
    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    public function afterDisplayCartTaxWithGrandTotal()
    {
        $isExkl = $this->request->getParam('exkl');
        if ($isExkl) return true;
    }
}