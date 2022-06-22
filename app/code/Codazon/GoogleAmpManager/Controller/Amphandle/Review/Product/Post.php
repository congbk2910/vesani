<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\GoogleAmpManager\Controller\Amphandle\Review\Product;

use Magento\Review\Controller\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Review\Model\Review;

class Post extends ProductController
{
    protected $_product;

    protected function getProduct()
    {
        if ($this->_product === null) {
            $this->_product = $this->initProduct();
        }
        return $this->_product;
    }

    public function execute()
    {
        $result = [
            'success' => false,
            'type'    => 'error'
        ];
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $result['message'] = __('Invalid Form Key. Please refresh the page.');
        } else {
            $data = $this->reviewSession->getFormData(true);
            if ($data) {
                $rating = [];
                if (isset($data['ratings']) && is_array($data['ratings'])) {
                    $rating = $data['ratings'];
                }
            } else {
                $data = $this->getRequest()->getPostValue();
                $rating = $this->getRequest()->getParam('ratings', []);
            }
            if (($product = $this->getProduct()) && !empty($data)) {
                $review = $this->reviewFactory->create()->setData($data);
                $review->unsetData('review_id');
                $validate = $review->validate();
                if ($validate === true) {
                    try {
                        $review->setEntityId($review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
                            ->setEntityPkValue($product->getId())
                            ->setStatusId(Review::STATUS_PENDING)
                            ->setCustomerId($this->customerSession->getCustomerId())
                            ->setStoreId($this->storeManager->getStore()->getId())
                            ->setStores([$this->storeManager->getStore()->getId()])
                            ->save();

                        foreach ($rating as $ratingId => $optionId) {
                            $this->ratingFactory->create()
                                ->setRatingId($ratingId)
                                ->setReviewId($review->getId())
                                ->setCustomerId($this->customerSession->getCustomerId())
                                ->addOptionVote($optionId, $product->getId());
                        }

                        $review->aggregate();
                        $result['success'] = true;
                        $result['type'] = 'success';
                        $result['message'] = __('You submitted your review for moderation.');
                    } catch (\Exception $e) {
                        $this->reviewSession->setFormData($data);
                        $result['message'] = __('We can\'t post your review right now.');
                    }
                } else {
                    $this->reviewSession->setFormData($data);
                    if (is_array($validate)) {
                        $result['message'] = implode('<br />', $validate);
                    } else {
                        $result['message'] = __('We can\'t post your review right now.');
                    }
                }
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );
    }
}
