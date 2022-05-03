<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api;

use Amasty\Acart\Api\Data\RuleInterface;

/**
 * Abandoned cart email rule CRUD interface
 * @api
 */
interface RuleRepositoryInterface
{
    /**
     * Get rule by ID.
     *
     * @param int $id
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get(int $id): RuleInterface;

    /**
     * Save rule.
     *
     * @param \Amasty\Acart\Api\Data\RuleInterface $rule
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(RuleInterface $rule): RuleInterface;

    /**
     * Delete rule.
     *
     * @param \Amasty\Acart\Api\Data\RuleInterface $rule
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(RuleInterface $rule): bool;

    /**
     * Delete rule by ID.
     *
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;
}
