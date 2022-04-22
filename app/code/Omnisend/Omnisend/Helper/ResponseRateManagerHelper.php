<?php

namespace Omnisend\Omnisend\Helper;

class ResponseRateManagerHelper
{
    /**
     * @param $responseHeader
     * @return array
     */
    public function convertResponseHeaderStringToArray($responseHeader)
    {
        $headerArray = [];
        $headerValues = explode("\r\n", $responseHeader);

        foreach ($headerValues as $headerValue) {
            $keyValuePair = explode(": ", $headerValue);

            if (count($keyValuePair) != 2) {
                continue;
            }

            $headerArray[$keyValuePair[0]] = $keyValuePair[1];
        }

        return $headerArray;
    }
}
