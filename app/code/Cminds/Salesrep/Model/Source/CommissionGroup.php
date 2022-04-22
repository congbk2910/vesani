<?php

namespace Cminds\Salesrep\Model\Source;

use Cminds\Salesrep\Model\CommissionGroupRepository;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class CommissionGroup extends AbstractSource
{
    private $commissionGroupRepository;

    public function __construct(
        CommissionGroupRepository $commissionGroupRepository
    ) {
        $this->commissionGroupRepository = $commissionGroupRepository;
    }

    public function getAllOptions()
    {
        $label = __('-- Please Select --');
        return $this->commissionGroupRepository->getListForSource($label);
    }
}
