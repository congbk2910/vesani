<?php declare(strict_types=1);

namespace Svea\Checkout\Controller\Adminhtml\Campaign;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LogLevel;
use Svea\Checkout\Model\Client\ClientException;

class Collect extends Action
{
    protected $resultJsonFactory;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var \Svea\Checkout\Helper\Data
     */
    private $sveaConfig;

    /**
     * @var \Svea\Checkout\Api\CampaignInfoRepositoryInterface
     */
    private $campaignInfoRepository;

    /**
     * @var \Svea\Checkout\Model\Client\Api\CampaignManagement
     */
    private $campaignManagement;

    /**
     * @var \Svea\Checkout\Model\CampaignInfoFactory
     */
    private $campaignInfoFactory;

    /**
     * @var \Svea\Checkout\Model\Resource\CampaignInfo
     */
    private $campaignResource;

    /**
     * @var \Svea\Checkout\Logger\Logger
     */
    private $logger;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Svea\Checkout\Helper\Data $sveaConfig,
        \Svea\Checkout\Api\CampaignInfoRepositoryInterface $campaignInfoRepository,
        \Svea\Checkout\Model\Client\Api\CampaignManagement $campaignManagement,
        \Svea\Checkout\Model\CampaignInfoFactory $campaignInfoFactory,
        \Svea\Checkout\Model\Resource\CampaignInfo $campaignResource,
        \Svea\Checkout\Logger\Logger $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->context = $context;
        $this->sveaConfig = $sveaConfig;
        $this->campaignInfoRepository = $campaignInfoRepository;
        $this->campaignManagement = $campaignManagement;
        $this->campaignInfoFactory = $campaignInfoFactory;
        $this->campaignResource = $campaignResource;
        $this->logger = $logger;
        parent::__construct($context);
    }


    /**
     * Collect relations data
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Json $result */
        $result = $this->resultJsonFactory->create();

        try {
            $amountImported = $this->importCampaigns();
        } catch (\Exception $e) {
            return $result->setData(['status' => $e->getMessage()]);
        }

        return $result->setData(['status' => sprintf('Successfully imported %s campaigns.', $amountImported)]);
    }

    /**
     * @throws \Exception
     */
    private function importCampaigns() : int
    {
        if (! $this->sveaConfig->isCampaignWidgetActive()) {
            throw new \Exception('Campaigns is not active');
        }

        try {
            $availableCampaigns = $this->campaignManagement->getAvailablePartPaymentCampaigns();
        } catch (ClientException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());
            throw new \Exception('Unfable to fetch campaigns from API. Please check your connection');
        }

        $newCampaigns = 0;
        $campaignCodes = $this->campaignInfoRepository->getCodes();
        foreach ($availableCampaigns as $availableCampaign) {
            if (in_array($availableCampaign['campaign_code'], $campaignCodes)) {
                continue;
            }

            try {
                $campaign = $this->campaignInfoFactory->create();
                $campaign->setData($availableCampaign);
                $this->campaignResource->save($campaign);
                $newCampaigns ++;
            } catch (\Exception $e) {
                $this->logger->log(LogLevel::ERROR, $e->getMessage());
            }
        }

        return $newCampaigns;
    }
}
