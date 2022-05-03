<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model\OptionSource;

use Magento\Email\Model\ResourceModel\Template\CollectionFactory as TemplatesCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class EmailTemplates implements OptionSourceInterface
{
    /**
     * @var TemplatesCollectionFactory
     */
    private $templatesCollectionFactory;

    public function __construct(
        TemplatesCollectionFactory $templatesCollectionFactory
    ) {
        $this->templatesCollectionFactory = $templatesCollectionFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->templatesCollectionFactory->create()
            ->addFilter('orig_template_code', 'amasty_acart_template');

        return $collection->toOptionArray();
    }
}
