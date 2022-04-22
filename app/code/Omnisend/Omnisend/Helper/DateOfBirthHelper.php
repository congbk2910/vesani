<?php

namespace Omnisend\Omnisend\Helper;

use DateTime;
use Exception;
use Psr\Log\LoggerInterface;

class DateOfBirthHelper
{
    const DATE_FORMAT = 'Y-m-d';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $dob
     * @return null|string
     */
    public function formatCustomerDateOfBirth($dob)
    {
        if (!$dob) {
            return null;
        }

        try {
            $dateTime = new DateTime($dob);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());

            return null;
        }

        if ($dateTime instanceof DateTime) {
            return $dateTime->format(self::DATE_FORMAT);
        }

        return null;
    }
}
