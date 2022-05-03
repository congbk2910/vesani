<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model\QuoteEmail;

use Amasty\Acart\Api\Data\QuoteEmailInterface;
use Amasty\Acart\Api\Data\QuoteEmailInterfaceFactory;
use Amasty\Acart\Api\QuoteEmailRepositoryInterface;
use Amasty\Acart\Model\AbstractCachedRepository;
use Amasty\Acart\Model\QuoteEmail as QuoteEmailModel;
use Amasty\Acart\Model\ResourceModel\QuoteEmail as QuoteEmailResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;

class Repository extends AbstractCachedRepository implements QuoteEmailRepositoryInterface
{
    /**
     * @var QuoteEmailInterfaceFactory
     */
    private $quoteEmailFactory;

    /**
     * @var QuoteEmailResource
     */
    private $quoteEmailResource;

    public function __construct(
        QuoteEmailInterfaceFactory $quoteEmailFactory,
        QuoteEmailResource $quoteEmailResource
    ) {
        $this->quoteEmailFactory = $quoteEmailFactory;
        $this->quoteEmailResource = $quoteEmailResource;
    }

    private function getBy($value, $field = QuoteEmailModel::QUOTE_EMAIL_ID): QuoteEmailInterface
    {
        if (($result = $this->getFromCache($field, $value)) !== null) {
            return $result;
        }

        /** @var QuoteEmailInterface $quoteEmail */
        $quoteEmail = $this->quoteEmailFactory->create();
        $this->quoteEmailResource->load($quoteEmail, $value, $field);
        if (!$quoteEmail->getQuoteEmailId()) {
            throw new NotFoundException(
                __('Quote email with specified %1 "%2" not found.', $field, $value)
            );
        }

        return $this->addToCache($field, $value, $quoteEmail);
    }

    public function getById(int $id): QuoteEmailInterface
    {
        return $this->getBy($id, QuoteEmailModel::QUOTE_EMAIL_ID);
    }

    public function getByQuoteId(int $quoteId): QuoteEmailInterface
    {
        return $this->getBy($quoteId, QuoteEmailModel::QUOTE_ID);
    }

    public function save(QuoteEmailInterface $quoteEmail): QuoteEmailInterface
    {
        try {
            $this->quoteEmailResource->save($quoteEmail);
            $this->invalidateCache($quoteEmail);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Unable to save the quote email. Error: %1', $e->getMessage())
            );
        }

        return $quoteEmail;
    }

    public function delete(QuoteEmailInterface $quoteEmail): bool
    {
        try {
            $this->quoteEmailResource->delete($quoteEmail);
            $this->invalidateCache($quoteEmail);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Unable to delete the quote email. Error: %1', $e->getMessage())
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
