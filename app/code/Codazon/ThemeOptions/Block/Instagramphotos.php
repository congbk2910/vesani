<?php

/**
 * Copyright © 2017 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\ThemeOptions\Block;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\Storage\ReaderInterface;

class Instagramphotos extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	protected $_template = null;
	const API_URL = 'https://api.instagram.com/v1/users/self/media/recent';
	const ACCESS_TOKEN = '3893338542.38fb276.e8dbfaac57214bf69c0439027ee39d85';
    const GRAPHQL_URL = 'https://www.instagram.com/graphql/query/?query_hash=e769aa130647d2354c40ea6a439bfc08';
	protected $_sliderData = null;
	
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        WriterInterface $configWriter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);     
        $this->httpContext = $httpContext;
        $this->configWriter = $configWriter;
        $this->_scopeConfig = $scopeConfig;
        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags' => ['CDZ_INSTAGRAM',
        ], ]);
    }
	public function getCacheKeyInfo()
    {
        $instagram = serialize($this->getData());
        return [
            'CDZ_INSTAGRAM',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),                    
            $instagram
        ];
    }
	
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
	
	public function getInstagramRecentPhotos()
    {
        if ($this->getData('use_user_id')) {
            $data = [];
            try {
                if ($userId = $this->getData('user_id')) {
                    $count = $this->getData('max_items') ? : 12;
                    $url = self::GRAPHQL_URL . '&variables={"id":"'. $userId .'","first":'. $count .'}';
                    $result = json_decode($this->fetchData($url));
                    $path = "codazon/instagram/request/$userId";
                    if(isset($result->data)){
                        $this->configWriter->save($path, json_encode($result), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
                    }else{
                        $result = $this->_scopeConfig->getValue($path);
                        $result = json_decode($result);
                    }
                    $data = [];
                    if(isset($result->data)){
                        foreach ($result->data->user->edge_owner_to_timeline_media->edges as $post) {
                            $data[] = (object)[
                                'link'      => "https://www.instagram.com/p/{$post->node->shortcode}/",
                                'images'    => (object) [
                                    'thumbnail' => (object) [
                                        'url'   => $post->node->thumbnail_resources[0]->src
                                    ],
                                    'low_resolution' => (object) [
                                        'url'   => $post->node->thumbnail_resources[1]->src
                                    ],
                                    'standard_resolution' => (object) [
                                        'url'   => $post->node->display_resources[0]->src
                                    ]
                                ]
                            ];
                        }
                    }
                }
                return $data;
            } catch (\Exceptions $e) {
                return [];
            }
        } else {
            $accessToken = $this->getData('access_token');
            if ($accessToken) {
                $url = self::API_URL . "?access_token={$accessToken}";
                $result = json_decode($this->fetchData($url));
                if ($result && !empty($result->data)) {
                    return @$result->data;
                }
            }
            return [];
        }
	}
	
	public function getTemplate()
    {   
        if($this->_template == null){
			if($this->getData('custom_template')) {
				$this->_template = $this->getData('custom_template');
			} else {
				$this->_template = 'Codazon_ThemeOptions::widget/instagramphotos/grid.phtml';
			}
		}
		return $this->_template;
    }
	
	public function getSliderData()
    {
        if (!$this->_sliderData) {
            $this->_sliderData = [
                'nav'           => (bool)$this->getData('slider_nav'),
                'dots'          => (bool)$this->getData('slider_dots'),
                'loop'          => (bool)$this->getData('slider_loop'),
                'stagePadding'  => (float)$this->getData('stage_padding'),
                'lazyLoad'      => true
            ];
            $adapts = array('1900', '1600', '1420', '1280','980','768','480','320','0');
            foreach ($adapts as $adapt) {
                 $this->_sliderData['responsive'][$adapt] = ['items' => (float)$this->getData('items_' . $adapt)];
            }
            $this->_sliderData['margin'] = (float)$this->getData('slider_margin');
        }
        return $this->_sliderData;
    }
}