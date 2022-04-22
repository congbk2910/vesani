<?php

namespace Omnisend\Omnisend\Model\Attribute\IsImported;

interface AttributeUpdaterInterface
{
    /**
     * @param int $entityId
     * @param int $isImported
     * @return void
     */
    public function setIsImported($entityId, $isImported);
}
