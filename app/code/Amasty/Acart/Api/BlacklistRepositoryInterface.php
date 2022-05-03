<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api;

use Amasty\Acart\Api\Data\BlacklistInterface;
use Amasty\Acart\Api\Data\BlacklistSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Email blacklist CRUD interface
 * @api
 */
interface BlacklistRepositoryInterface
{
    /**
     * Get blacklist email by ID.
     *
     * @param int $id
     *
     * @return \Amasty\Acart\Api\Data\BlacklistInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getById(int $id): BlacklistInterface;

    /**
     * Get blacklist email by customer email.
     *
     * @param string $customerEmail
     *
     * @return \Amasty\Acart\Api\Data\BlacklistInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getByCustomerEmail(string $customerEmail): BlacklistInterface;

    /**
     * Save blacklist email.
     *
     * @param \Amasty\Acart\Api\Data\BlacklistInterface $blacklist
     *
     * @return \Amasty\Acart\Api\Data\BlacklistInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(BlacklistInterface $blacklist): BlacklistInterface;

    /**
     * Delete blacklist email.
     *
     * @param \Amasty\Acart\Api\Data\BlacklistInterface $blacklist
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(BlacklistInterface $blacklist): bool;

    /**
     * Delete blacklist email by ID.
     *
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;

    /**
     * Retrieve blacklisted emails.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Amasty\Acart\Api\Data\BlacklistSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): BlacklistSearchResultsInterface;
}
