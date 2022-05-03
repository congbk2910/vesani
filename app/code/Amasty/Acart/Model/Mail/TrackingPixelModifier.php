<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model\Mail;

use Magento\Framework\Url;

class TrackingPixelModifier
{
    /**
     * @var Url
     */
    private $urlBuilder;

    public function __construct(Url $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    public function execute(string $hash, string $emailBody): string
    {
        $trackingUrl = $this->urlBuilder->getUrl('amasty_acart/email/open', ['uid' => $hash]);
        $emailBody = str_replace('</body>', '<img src="' . $trackingUrl . '"></body>', $emailBody);

        return $emailBody;
    }
}
