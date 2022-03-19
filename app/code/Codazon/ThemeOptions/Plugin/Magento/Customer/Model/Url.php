<?php
namespace Codazon\ThemeOptions\Plugin\Magento\Customer\Model;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
class Url
{
    const REFERER_QUERY_PARAM_NAME = 'referer';
    const XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD = 'customer/startup/redirect_dashboard';

    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        EncoderInterface $urlEncoder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->urlEncoder = $urlEncoder;
        $this->urlDecoder = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Url\DecoderInterface::class);
    }

    protected function getRequestReferrer()
    {
        $referer = $this->request->getParam(self::REFERER_QUERY_PARAM_NAME);
        if ($referer && $this->hostChecker->isOwnOrigin($this->urlDecoder->decode($referer))) {
            return $referer;
        }
        return null;
    }

    public function afterGetLoginPostUrl($subject, $url)
    {
        $referers = $subject->getLoginUrlParams();
        if(isset($referers[self::REFERER_QUERY_PARAM_NAME])){
            $refererUrl = $this->urlDecoder->decode($referers[self::REFERER_QUERY_PARAM_NAME]);
            if($this->scopeConfig->isSetFlag(
                self::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD,
                ScopeInterface::SCOPE_STORE
            )){
                $referer = $this->urlBuilder->getUrl('customer/account');
            }else{
                $referer = $this->urlBuilder->getUrl($refererUrl);
            }
            $referer = $this->urlEncoder->encode($referer);
            $url = $this->urlBuilder->getUrl('customer/account/loginPost/').'referer/'.$referer;
            return $url;
        }else{
            $referer = $this->urlBuilder->getUrl('customer/account');
            $referer = $this->urlEncoder->encode($referer);
            $url = $this->urlBuilder->getUrl('customer/account/loginPost/').'referer/'.$referer;
            return $url;
        }
    }
        
}
