<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api;

use Amasty\Acart\Api\Data\RuleQuoteInterface;

interface RuleQuoteRepositoryInterface
{
    /**
     * @param int $id
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getById(int $id): RuleQuoteInterface;

    /**
     * @param \Amasty\Acart\Api\Data\RuleQuoteInterface $ruleQuote
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(RuleQuoteInterface $ruleQuote): RuleQuoteInterface;

    /**
     * @param \Amasty\Acart\Api\Data\RuleQuoteInterface $ruleQuote
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(RuleQuoteInterface $ruleQuote): bool;

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;
}
