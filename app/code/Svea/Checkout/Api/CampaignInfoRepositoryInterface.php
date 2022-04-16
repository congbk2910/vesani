<?php

namespace Svea\Checkout\Api;

interface CampaignInfoRepositoryInterface
{
    /**
     * Create or update a campaign
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(Data\CampaignInfoInterface $push);

    /**
     * Retrieve campaign.
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($campaignId) : Data\CampaignInfoInterface;

    /**
     * Retrieve campaign by code.
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCode($campaignCode) : Data\CampaignInfoInterface;

    /**
     * Get all codes
     */
    public function getCodes() : array;
}
