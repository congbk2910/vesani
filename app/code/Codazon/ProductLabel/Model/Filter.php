<?php
/**
* Copyright © 2018 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace  Codazon\ProductLabel\Model;

class Filter extends \Magento\Cms\Model\Template\Filter
{
	protected $_priceHelper;
    
	protected $_stockItemModel;
	
    protected function _getPriceHelper()
    {
        if ($this->_priceHelper === null) {
            $this->_priceHelper = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Pricing\Helper\Data');
        }
        return $this->_priceHelper;
    }
    
	public function filterLabel($object)
    {
        $this->_getPriceHelper();
        
		if (!is_string($object)) {
            $value = $object->getText();
            $product = $object->getProduct();
        } else{
            $value = $object;
		}
        
		$customVariables = $this->getCustomVariable();
		
		foreach (array(
            self::CONSTRUCTION_DEPEND_PATTERN => 'dependDirective',
            self::CONSTRUCTION_IF_PATTERN     => 'ifDirective',
            ) as $pattern => $directive) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach($constructions as $index => $construction) {
                    $replacedValue = '';
                    $callback = array($this, $directive);
                    if(!is_callable($callback)) {
                        continue;
                    }
                    try {
                        $replacedValue = call_user_func($callback, $construction);
                    } catch (Exception $e) {
                        throw $e;
                    }
                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }
		
		if (preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach($constructions as $index => $construction) {
                $replacedValue = '';
                $callback = array($this, $construction[1].'Directive');
                if(!is_callable($callback)) {
                    continue;
                }
                try {
					$replacedValue = @call_user_func($callback, $construction);
                    if(in_array($construction[0], $customVariables)) {
                        $replacedValue = $this->getCustomVariableValue($construction, $product);
                    }
                } catch (Exception $e) {
                	throw $e;
                }
                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }
        
        return $value;
	}
	public function getCustomVariable()
    {
        return array(
            '{{var save_percent}}',
            '{{var save_price}}',
            '{{var product.price}}',
            '{{var product.special_price}}',
            '{{var product.qty}}'
        );
    }
	
    public function getSpecialPrice($_product){
		if ($_product->getTypeId() == 'configurable') {
            $_children = $_product->getTypeInstance()->getUsedProducts($_product);
            $specialPrice = [];
			if (count($_children) > 0) {
                foreach($_children as $_child){
					$specialPrice[] = $_child->getFinalPrice();
				}
				
                return min($specialPrice);
			
            } else {
				return 0;
			}
		}else{
			return $_product->getFinalPrice();
		}
	}
    
	public function getPrice($_product){
		if($_product->getTypeId() == 'configurable'){
			return $_product->getPriceInfo()->getPrice('base_price')->getValue();
		}else{
			return $_product->getPrice();
		}
	}
    
    public function getSavePercent($_product)
    {
        if ($_product->getTypeId() == 'configurable') {
			$_children = $_product->getTypeInstance()->getUsedProducts($_product);
            $result = [];
			if (count($_children) > 0) {
                foreach($_children as $_child) {
					$result[] = ($_child->getPrice() > 0) ? ($_child->getPrice() - $_child->getFinalPrice()) / $_child->getPrice() : 0;
				}                
				return number_format(100*(float)(max($result)), 0);
			} else {
				return 0;
			}
		}else{
            if (!$_product->getPrice()) {
                return 0;
            } else {
                return number_format(100*(float)(($_product->getPrice() - $_product->getFinalPrice()) / $_product->getPrice()), 0);
            }
		}
    }
    
    public function getSavePrice($_product)
    {
        if ($_product->getTypeId() == 'configurable') {
			$_children = $_product->getTypeInstance()->getUsedProducts($_product);
            $result = [];
			if (count($_children) > 0) {
                foreach($_children as $_child) {
					$result[] = $_child->getPrice() - $_child->getFinalPrice();
				}
				return $this->_priceHelper->currency(max($result), true, false);
			} else {
				return $this->_priceHelper->currency(0, true, false);
			}
		}else{
            return $this->_priceHelper->currency($_product->getPrice() - $_product->getFinalPrice(), true, false);
		}
    }
    
	public function getStockQty($_product){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$stockState = $objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');
		if($_product->getTypeId() == 'configurable'){
			$qty = 0;
			$_children = $_product->getTypeInstance()->getUsedProducts($_product);
			if(count($_children) > 0){
				foreach($_children as $_child){
					$qty += $stockState->getStockQty($_child->getId(), $_product->getStore()->getWebsiteId());
				}
				return $qty;
			}else{
				return 0;
			}
		}else{
			return $stockState->getStockQty($_product->getId(), $_product->getStore()->getWebsiteId());
		}
	}
    
	public function getCustomVariableValue($construction,$_product)
    {
        $type = trim($construction[2]);
        if($type == 'save_percent') {
            return $this->getSavePercent($_product);
        } elseif($type == 'save_price'){
            return $this->getSavePrice($_product);
        } elseif($type == 'product.price') {
            return $this->_priceHelper->currency($this->getPrice($_product));
        } elseif ($type == 'product.special_price') {
            return $this->_priceHelper->currency($this->getSpecialPrice($_product), true, false);
        } else {
            return $this->getStockQty($_product);
        }
    }
}
