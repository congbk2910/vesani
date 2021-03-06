/**
* Copyright © 2015 Magento. All rights reserved.
* See COPYING.txt for license details.
*/
define([
	'uiComponent',
	'Magento_Customer/js/customer-data',
	'jquery',
	'ko',
	'codazonSidebar',
	'Magento_Ui/js/modal/modal'
], function (Component, customerData, $, ko, paypalCheckout) {
	'use strict';
	var sidebarCart = $('[data-block="footer_minicart"]');
	var addToCartCalls = 0;
	var sidebarInitialized = false;

	function initSidebar() {
		if (sidebarCart.data('mageSidebar')) {
			sidebarCart.codazonSidebar('update');
		}
		sidebarCart.trigger('contentUpdated');
		if (sidebarInitialized) {
			return false;
		}
		sidebarInitialized = true;
		sidebarCart.codazonSidebar({
			"targetElement": "#footer-mini-cart",
			"url": {
				"checkout": window.checkout.checkoutUrl,
				"update": window.checkout.updateItemQtyUrl,
				"remove": window.checkout.removeItemUrl,
				"loginUrl": window.checkout.customerLoginUrl,
				"isRedirectRequired": window.checkout.isRedirectRequired
			},
			"button": {
				"checkout": "#footer-cart-btn-checkout",
				"remove": ".item a.action.delete",
				"close": ""
			},
			"minicart": {
				"list": "",
				"content": "",
				"qty": "",
				"subtotal": ""
			},
			"item": {
				"qty": "input.cart-item-qty",
				"button": "button.update-cart-item"
			},
			"confirmMessage": $.mage.__(
				'Are you sure you would like to remove this item from the shopping cart?'
			)
		});
		//fix paypal express checkout cart button not work on footer cart
		$( document ).ajaxComplete(function( event, xhr, settings ) {
			if( settings.url.indexOf('customer/section/load/?sections') >= 0){
				$('.footer-cart-actions [data-action="checkout-form-submit"]').off('click');
				$('.footer-cart-actions [data-action="checkout-form-submit"]').click(function(){
					$('[data-action="checkout-form-submit"]:first').click();
				});
			}
		});
		//=== end fix ===
	}

	return Component.extend({
		ajaxcart: ko.observable({}),
		crosssell: ko.observable({}),
		addedItem: ko.observable({}),
		cartSidebar: ko.observable({summary_count: false}),
		toggleFooterSidebar: function(){
			$('#footer-cart-trigger').toggleClass('active');
			$('#footer-mini-cart').slideToggle(300);	
		},
		basicScrollTop: function () {
			if (window.matchMedia('(max-width: 767px)').matches) {
				$('#footer-cart-trigger').hide();
				if ($('.cart-footer').length) {
					$(window).scroll(function() {
						var topPos = $(this).scrollTop();
						if (topPos > 100) {
							$('.footer-mini-cart').show();
						} else {
							$('.footer-mini-cart').hide();
						}
					}); 
				}
			} else {
				$('#footer-cart-trigger').show();
			}
		},
		initSidebar: initSidebar,
		initialize: function () {
			var self = this;
			this._super();
			this.addedItem({success:false});
			this.cartSidebar = customerData.get('cart');
			window.addedItem = self.addedItem;
			window.ajaxcart = self.ajaxcart;
			window.crosssell = self.crosssell;
			window.cartSidebar = self.cartSidebar;
			$('#cart-footer').show();
			initSidebar();
			this.cartSidebar.subscribe(function () {
				addToCartCalls--;
				sidebarInitialized = false;				
				initSidebar();
			}, this);
			$('[data-block="minicart"]').on('contentLoading', function(event) {
				addToCartCalls++;				
			});
			var $ajaxPopup = $('#ajax-cart-container');
			self.basicScrollTop();
			$(window).resize(function() {
				self.basicScrollTop();
			});
			// Show value Swatch option
			var objectVar = jQuery('.catalog-product-view .swatch-option');
			jQuery.each( objectVar, function(value){
				var $value = jQuery(this).attr('aria-label');
				jQuery(this).append( '<p>'+ $value +'</p>' );
			});		  
		},
	});
});
