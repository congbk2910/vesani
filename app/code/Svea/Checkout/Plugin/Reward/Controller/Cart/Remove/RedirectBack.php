<?php

namespace Svea\Checkout\Plugin\Reward\Controller\Cart\Remove;

use Magento\Framework\App\Response\RedirectInterface;
use Magento\Reward\Controller\Cart\Remove;

class RedirectBack
{
    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * RedirectBack constructor.
     *
     * @param RedirectInterface $redirect
     */
    public function __construct(RedirectInterface $redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * @param Remove $removeAction
     * @param $response
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function afterExecute(Remove $removeAction, $response)
    {
        $this->redirect->redirect(
            $removeAction->getResponse(),
            $this->redirect->getRefererUrl()
        );

        return $removeAction->getResponse();
    }
}
