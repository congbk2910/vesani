<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Omnisend\Omnisend\Helper\GmtDateHelper;
use Omnisend\Omnisend\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Class GuestContact
 * @package Omnisend\Omnisend\Model\RequestBodyBuilder
 */
class GuestContact extends AbstractBodyBuilder implements RequestBodyBuilderInterface
{
    const EMAIL = 'email';
    const CREATED_AT = 'createdAt';
    const FIRST_NAME = 'firstName';
    const LAST_NAME = 'lastName';
    const STATUS = 'status';
    const STATUS_DATE = 'statusDate';
    const PHONE = 'phone';
    const CHANNELS = 'channels';
    const ID = 'id';
    const TYPE = 'type';
    const IDENTIFIERS = 'identifiers';

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var DateTime
     */
    protected $date;
    /**
     * @var GmtDateHelper
     */
    protected $gmtDateHelper;

    /**
     * GuestContact constructor.
     * @param DateTime $date
     * @param GmtDateHelper $gmtDateHelper
     * @param Json $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        DateTime $date,
        GmtDateHelper $gmtDateHelper,
        Json $serializer,
        LoggerInterface $logger
    ) {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->date = $date;
        $this->gmtDateHelper = $gmtDateHelper;
    }

    /**
     * @inheritDoc
     */
    public function build($guestData)
    {
        $identifiers = [
            self::TYPE => self::EMAIL,
            self::ID => $guestData["email"],
            self::CHANNELS => [
                self::EMAIL => [
                    self::STATUS => "nonSubscribed",
                    self::STATUS_DATE => $this->gmtDateHelper->getGmtDate($this->date->gmtDate()),
                ],
            ],
        ];
        $this->addData(self::IDENTIFIERS, [$identifiers]);
        $this->addData(self::FIRST_NAME, $guestData[self::FIRST_NAME]);
        $this->addData(self::LAST_NAME, $guestData[self::LAST_NAME]);
        return $this->serializer->serialize($this->getData());
    }
}
