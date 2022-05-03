<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Block\Email\Items;

use Magento\Catalog\Model\Product\LinkFactory as ProductLinkFactory;

class Upsell extends Link
{
    /**
     * @return ProductLinkFactory
     */
    protected function getLinkModel()
    {
        return $this->productLinkFactory->create()->useUpSellLinks();
    }
}
