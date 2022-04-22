<?php

namespace Omnisend\Omnisend\Model;

interface ResponseRateManagerInterface
{
    const X_RATE_LIMIT_LIMIT = 'X-Rate-Limit-Limit';
    const X_RATE_LIMIT_REMAINING = 'X-Rate-Limit-Remaining';
    const X_RATE_LIMIT_RESET = 'X-Rate-Limit-Reset';

    const RATE_LIMIT_SAFETY_MARGIN = 0.05;

    /**
     * @param $responseHeader
     * @param $storeId
     * @return void
     */
    public function update($responseHeader, $storeId);

    /**
     * @param $storeId
     * @return boolean
     */
    public function check($storeId);
}
