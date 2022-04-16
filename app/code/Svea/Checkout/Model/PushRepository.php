<?php
namespace Svea\Checkout\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Svea\Checkout\Api\PushRepositoryInterface;
use Svea\Checkout\Model\Resource\Push as ResourcePush;

/**
 * Class PushRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PushRepository implements PushRepositoryInterface
{
    /**
     * @var ResourcePush
     */
    protected $resource;

    /**
     * @var PushFactory
     */
    protected $pushFactory;

    /**
     * @param ResourcePush $resource
     * @param PushFactory $pushFactory
     */
    public function __construct(
        ResourcePush $resource,
        PushFactory $pushFactory
    ) {
        $this->resource = $resource;
        $this->pushFactory = $pushFactory;
    }

    /**
     * Save Push data
     *
     * @param \Svea\Checkout\Api\Data\PushInterface $push
     * @return Push
     * @throws CouldNotSaveException
     */
    public function save(\Svea\Checkout\Api\Data\PushInterface $push)
    {
        try {
            $this->resource->save($push);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the push: %1', $exception->getMessage()),
                $exception
            );
        }

        return $push;
    }

    /**
     * @param int $sveaOrderId
     * @return Push
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $push = $this->pushFactory->create();
        $this->resource->load($push, $id);
        if (!$push->getId()) {
            throw new NoSuchEntityException(__('The push doesn\'t exist.'));
        }

        return $push;
    }

    /**
     * @param int $sveaOrderId
     * @return Push
     * @throws NoSuchEntityException
     */
    public function get($sveaOrderId)
    {
        $push = $this->pushFactory->create();
        $this->resource->load($push, $sveaOrderId, 'sid');
        if (!$push->getId()) {
            throw new NoSuchEntityException(__('The push doesn\'t exist.'));
        }

        return $push;
    }
}
