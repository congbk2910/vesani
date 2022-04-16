<?php declare(strict_types=1);

namespace Svea\Checkout\Cron;

use Psr\Log\LogLevel;
use Svea\Checkout\Model\CampaignInfoFactory;
use Svea\Checkout\Model\Client\ClientException;

class CheckPendingPayments
{
    /**
     * @var \Svea\Checkout\Model\Client\Api\CampaignManagement
     */
    private $campaignManagement;

    /**
     * @var \Svea\Checkout\Logger\Logger
     */
    private $logger;

    /**
     * @var CampaignInfoFactory
     */
    private $campaignInfoFactory;

    /**
     * @var \Svea\Checkout\Api\CampaignInfoRepositoryInterface
     */
    private $campaignInfoRepository;

    /**
     * @var \Svea\Checkout\Model\Resource\CampaignInfo
     */
    private $campaignResource;

    /**
     * @var \Svea\Checkout\Helper\Data
     */
    private $sveaConfig;

    /**
     * CheckPendingPayments constructor.
     *
     * @param \Svea\Checkout\Api\CampaignInfoRepositoryInterface $campaignInfoRepository
     * @param \Svea\Checkout\Model\Client\Api\CampaignManagement $campaignManagement
     * @param CampaignInfoFactory $campaignInfoFactory
     * @param \Svea\Checkout\Model\Resource\CampaignInfo $campaignResource
     * @param \Svea\Checkout\Logger\Logger $logger
     */
    public function __construct(
        \Svea\Checkout\Helper\Data $sveaConfig,
        \Svea\Checkout\Api\CampaignInfoRepositoryInterface $campaignInfoRepository,
        \Svea\Checkout\Model\Client\Api\CampaignManagement $campaignManagement,
        \Svea\Checkout\Model\CampaignInfoFactory $campaignInfoFactory,
        \Svea\Checkout\Model\Resource\CampaignInfo $campaignResource,
        \Svea\Checkout\Logger\Logger $logger
    ) {
        $this->campaignManagement = $campaignManagement;
        $this->logger = $logger;
        $this->campaignInfoFactory = $campaignInfoFactory;
        $this->campaignInfoRepository = $campaignInfoRepository;
        $this->campaignResource = $campaignResource;
        $this->sveaConfig = $sveaConfig;
    }

    /**
     * Execute cron to create missed orders
     */
    public function execute() : void
    {
        if (! $this->sveaConfig->isCampaignWidgetActive()) {
            return;
        }

        try {
            $availableCampaigns = $this->campaignManagement->getAvailablePartPaymentCampaigns();
        } catch (ClientException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());
            return;
        }

        $campaignCodes = $this->campaignInfoRepository->getCodes();
        foreach ($availableCampaigns as $availableCampaign) {
            if (in_array($availableCampaign['campaign_code'], $campaignCodes)) {
                continue;
            }

            try {
                $campaign = $this->campaignInfoFactory->create();
                $campaign->setData($availableCampaign);
                $this->campaignResource->save($campaign);
            } catch (\Exception $e) {
                $this->logger->log(LogLevel::ERROR, $e->getMessage());
            }
        }
    }
}
