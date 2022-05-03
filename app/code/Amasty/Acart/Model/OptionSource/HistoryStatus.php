<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model\OptionSource;

use Amasty\Acart\Model\History as History;
use Amasty\Acart\Model\RuleQuote;
use Magento\Framework\Data\OptionSourceInterface;

class HistoryStatus implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $result = [];

        foreach ($this->toArray() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }

    public function toArray(): array
    {
        return [
            History::STATUS_PROCESSING => __('Not sent'),
            History::STATUS_SENT => __('Sent'),
            History::STATUS_CANCEL_EVENT => __('Cancel Condition'),
            History::STATUS_BLACKLIST => __('Blacklist'),
            History::STATUS_ADMIN => __('Canceled by the admin'),
            History::STATUS_NOT_NEWSLETTER_SUBSCRIBER => __('Customer is Not Newsletter Subscriber'),
            RuleQuote::COMPLETE_QUOTE_REASON_PLACE_ORDER => __('Order Placed'),
            RuleQuote::COMPLETE_QUOTE_REASON_UPDATE_QUOTE => __('Quote Updated')
        ];
    }
}
