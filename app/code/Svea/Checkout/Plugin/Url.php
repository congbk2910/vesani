<?php


namespace Svea\Checkout\Plugin;


class Url
{
    /**
     * @var \Svea\Checkout\Helper\Data
     */
    protected $helper;

    public function __construct(\Svea\Checkout\Helper\Data $helper)
    {
        $this->helper = $helper;
    }


    /**
     * Replace checkout url
     *
     * @param $subject
     * @param $result
     * @return string
     */
    public function afterGetCheckoutUrl($subject, $result)
    {
        if (!$this->helper->isEnabled()) {
            return $result;
        }

        return $this->helper->replaceCheckout()
            ? $this->helper->getCheckoutUrl()
            : $result;
    }
}