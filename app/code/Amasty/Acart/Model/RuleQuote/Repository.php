<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model\RuleQuote;

use Amasty\Acart\Api\Data\RuleQuoteInterface;
use Amasty\Acart\Api\Data\RuleQuoteInterfaceFactory;
use Amasty\Acart\Api\RuleQuoteRepositoryInterface;
use Amasty\Acart\Model\AbstractCachedRepository;
use Amasty\Acart\Model\ResourceModel\RuleQuote as RuleQuoteResource;
use Amasty\Acart\Model\RuleQuote as RuleQuoteModel;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;

class Repository extends AbstractCachedRepository implements RuleQuoteRepositoryInterface
{
    /**
     * @var RuleQuoteInterfaceFactory
     */
    private $ruleQuoteFactory;

    /**
     * @var RuleQuoteResource
     */
    private $ruleQuoteResource;

    public function __construct(
        RuleQuoteInterfaceFactory $ruleQuoteFactory,
        RuleQuoteResource $ruleQuoteResource
    ) {
        $this->ruleQuoteFactory = $ruleQuoteFactory;
        $this->ruleQuoteResource = $ruleQuoteResource;
    }

    private function getBy($value, $field = RuleQuoteModel::RULE_QUOTE_ID): RuleQuoteInterface
    {
        if (($result = $this->getFromCache($field, $value)) !== null) {
            return $result;
        }

        /** @var RuleQuoteInterface $ruleQuote */
        $ruleQuote = $this->ruleQuoteFactory->create();
        $this->ruleQuoteResource->load($ruleQuote, $value, $field);
        if (!$ruleQuote->getRuleQuoteId()) {
            throw new NotFoundException(
                __('Rule quote with specified %1 "%2" not found.', $field, $value)
            );
        }

        return $this->addToCache($field, $value, $ruleQuote);
    }

    public function getById(int $id): RuleQuoteInterface
    {
        return $this->getBy($id, RuleQuoteModel::RULE_QUOTE_ID);
    }

    public function save(RuleQuoteInterface $ruleQuote): RuleQuoteInterface
    {
        try {
            $this->ruleQuoteResource->save($ruleQuote);
            $this->invalidateCache($ruleQuote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Unable to save the rule quote. Error: %1', $e->getMessage())
            );
        }

        return $ruleQuote;
    }

    public function delete(RuleQuoteInterface $ruleQuote): bool
    {
        try {
            $this->ruleQuoteResource->delete($ruleQuote);
            $this->invalidateCache($ruleQuote);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Unable to delete the rule quote. Error: %1', $e->getMessage())
            );
        }

        return true;
    }

    public function deleteById(int $id): bool
    {
        $this->delete($this->getById($id));

        return true;
    }
}
