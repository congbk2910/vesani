<?php
/**
 *
 * Copyright © 2020 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\Core\Model\Config\Source;

class FontGoogle implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    protected $url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCWBE3G0k9qbhJYmml65yfuPXP9KsmLZMo';
    
    public function fetchData($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    public function getFontList()
    {
        $fontJson = $this->fetchData($this->url);
        $font     = json_decode($fontJson);
        if (isset($font->items)) {
            return $font->items;
        } else {
            return [];
        }
    }
    
    public function toOptionArray()
    {
        $fontList = $this->getFontList();
        $options = [];
        if (count($fontList)) {
            foreach ($fontList as $font) {
                $options[] = ['value' => $font->family, 'label' => $font->family];
            }
        }
        return $options;
    }
    
    public function toArray()
    {
        return $this->toOptionArray();
    }
}
