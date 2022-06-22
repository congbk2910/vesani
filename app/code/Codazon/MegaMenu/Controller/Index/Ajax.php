<?php
/**
* Copyright © 2018 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\MegaMenu\Controller\Index;

class Ajax extends \Magento\Framework\App\Action\Action
{
    protected $menu;
        
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Codazon\MegaMenu\Block\Widget\Megamenu $menu
    ) {
		$this->menu = $menu;
		parent::__construct($context);
		
    }
    
    public function execute()
    {
        $request = $this->getRequest();
        if ($menu = $request->getParam('menu')) {
            $this->_view->getLayout()->createBlock(\Magento\Framework\View\Element\FormKey::class, 'formkey');
            $this->menu = $this->_view->getLayout()->createBlock(\Codazon\MegaMenu\Block\Widget\Megamenu::class);
            $this->menu->addData([
                'use_ajax_menu' => false,
                'paging_menu'   => $request->getParam('paging_menu', true)
            ]);
            $menu = $this->menu->setMenu($menu);
            if ($menu->getMenuObject()) {
                return $this->getResponse()->setBody($this->_objectManager->get(\Codazon\Core\Helper\Data::class)->minifyHtml($menu->toHtml()));
            }
        }
        return $this->getResponse()->setBody('');
    }
}