<?php

namespace Omnisend\Omnisend\Helper;

use DateTime;

class GmtDateHelper
{
    const ISO_FORMAT_STRING = 'Y-m-d\TH:i:s\Z';

    /**
     * @param null $dateString
     * @return string
     */
    public function getGmtDate($dateString = null)
    {
        $date = new DateTime($dateString);

        return $date->format(self::ISO_FORMAT_STRING);
    }
}
