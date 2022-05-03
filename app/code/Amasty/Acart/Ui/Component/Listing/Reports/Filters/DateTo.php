<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Ui\Component\Listing\Reports\Filters;

use Amasty\Acart\Model\Date;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;

class DateTo extends Field
{
    /**
     * @var Date
     */
    private $date;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Date $date,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->date = $date;
    }

    public function prepare()
    {
        $config = $this->getData('config');

        $config['default'] = $this->date->date('m/d/Y', $this->date->getDateWithOffsetByDays(0));

        $this->setData('config', $config);
        parent::prepare();
    }
}
