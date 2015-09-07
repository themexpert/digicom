/**
 * @copyright	Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Digicom Plugin for script
 *
 * @package		Joomla.Extensions
 * @subpackage  Digicom
 * @since		1.0.0
 */
;(function( $, scope ) {
	"use strict";

	var Digicom = scope.Digicom = {

		/**
		 * Basic setup
		 *
		 * @return  void
		 */
		initialize: function() {
			// Set the site root path
			this.digicom_site = Digicom.getUrlParams('digicom.plugin.js', 'site');

		},
		dataSet: function(name){
			return $('[data-digicom-id="'+name+'"]');
		},

		/**
		* Update Cart Method
		*/
		updateCart: function (pid)
		{
			var url = this.digicom_site + "index.php?option=com_digicom&view=cart&task=cart.getCartItem&cid="+pid;
			if ( Digicom.dataSet('promocode').length ) {
				url += '&promocode=' + Digicom.dataSet('promocode').val();
			}

			if ( Digicom.dataSet('quantity'+pid).length ) {
				url += '&quantity'+pid+'=' + Digicom.dataSet('quantity'+pid).val();
			}
			// console.log(url);

			$.ajax({
		      url: url,
					method: 'get',
		      success: function (data, textStatus, xhr) { // data= returnval, textStatus=success, xhr = responseobject
						console.log(data);
						var responce = $.parseJSON(data);
						var cart_item_price = eval('responce.cart_item_price'+pid);
						var cart_item_total = eval('responce.cart_item_total'+pid);
						var cart_item_discount = eval('responce.cart_item_discount'+pid);

						Digicom.dataSet('price'+pid).html(cart_item_price);
						Digicom.dataSet('total'+pid).html(cart_item_total);

						Digicom.dataSet('discount'+pid).html(cart_item_discount);

						Digicom.dataSet('cart_subtotal').html(responce.cart_total);
						Digicom.dataSet('cart_discount').html(responce.cart_discount);
						Digicom.dataSet('cart_total').html(responce.cart_tax);

						// $('#cart_item_price'+pid).html(cart_item_price);
						// $('#cart_item_total'+pid).html(cart_item_total);

						// if ( $('#cart_item_discount'+pid).length ) {
						// 	 $('#cart_item_discount'+pid).html(cart_item_discount);
						// }

						// $('#cart_total').html(responce.cart_total);

						// if( $('#digicom_cart_discount').length ){
						// 	$('#digicom_cart_discount').html(responce.cart_discount);
						// }

						// if( $('#digicom_cart_tax').length ){
						// 	$('#digicom_cart_tax').html(responce.cart_tax);
						// }

						Digicom.refresCartModule();
		      }
		  });
		},
		/**
		* ajax refresh digicom cart module
		*/
		refresCartModule: function(){

			//

		},

		/**
		 * Generic function to get URL params passed in .js script include
		 *
		 * @targetScript   string  script file of plugin
		 * @varName   string  value from url
		 *
		 * @return  string
		 */

		getUrlParams: function(targetScript, varName) {
			var scripts = document.getElementsByTagName('script');
			var scriptCount = scripts.length;
			for (var a = 0; a < scriptCount; a++) {
				var scriptSrc = scripts[a].src;
				if (scriptSrc.indexOf(targetScript) >= 0) {
					varName = varName.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
					var re = new RegExp("[\\?&]" + varName + "=([^&#]*)");
					var parsedVariables = re.exec(scriptSrc);
					if (parsedVariables !== null) {
						return parsedVariables[1];
					}
				}
			}
		}

	};

	$(function() {
		// Added to populate data on iframe load
		Digicom.initialize();

	});

}( jQuery, window ));
