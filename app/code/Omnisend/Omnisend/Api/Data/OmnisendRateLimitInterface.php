<?php

namespace Omnisend\Omnisend\Api\Data;

use DateTime;

interface OmnisendRateLimitInterface
{
    const TABLE_NAME = 'omnisend_rate_limit';

    const ID = 'id';
    const LIMIT_TOTAL = 'limit_total';
    const LIMIT_REMAINING = 'limit_remaining';
    const RESETS_IN = 'resets_in';
    const UPDATED_AT = 'updated_at';

    const LABEL_ID = 'Id';
    const LABEL_LIMIT_TOTAL = 'Limit Total';
    const LABEL_LIMIT_REMAINING = 'Limit Remaining';
    const LABEL_RESETS_IN = 'Resets In';
    const LABEL_UPDATED_AT = 'Updated At';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getLimitTotal();

    /**
     * @return int
     */
    public function getLimitRemaining();

    /**
     * @return int
     */
    public function getResetsIn();

    /**
     * @return DateTime
     */
    public function getUpdatedAt();

    /**
     * @param $id
     * @return int
     */
    public function setId($id);

    /**
     * @param $limitTotal
     * @return int
     */
    public function setLimitTotal($limitTotal);

    /**
     * @param $limitRemaining
     * @return int
     */
    public function setLimitRemaining($limitRemaining);

    /**
     * @param $resetsIn
     * @return int
     */
    public function setResetsIn($resetsIn);

    /**
     * @param $updatedAt
     * @return DateTime
     */
    public function setUpdatedAt($updatedAt);
}
