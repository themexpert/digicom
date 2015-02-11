/**
 * @version 	1.0.0
 * @package 	Com DigiCOm
 * @author 		ThemeXpert
 * @copyright 	Copyright (c) 2006 - 2014 ThemeXpert Ltd. All rights reserved.
 * @license 	GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
imgpath = "";
// Media manager
/*
jQuery('ul#submenu').toggle(function () {
	$("#user_button").addClass("active");
}, function () {
	$("#user_button").removeClass("active");
});
*/
function elFinderUpdate(fieldID, value) {
	jQuery('#' + fieldID).val(value);
	if (typeof SqueezeBox !== 'undefined' && jQuery.isFunction(SqueezeBox)) {
		SqueezeBox.close();
	} else {
		parent.document.getElementById('sbox-btn-close').click()
	}
}
function openModal(a){
  fileInput = jQuery(a).prev();
  SqueezeBox.open('index.php?option=com_digicom&controller=filemanager&tmpl=component&folder='+imgpath+'&layout=modal&fieldID='+fileInput.attr('id'),{handler:'iframe',size:{x:800,y:600}});
}
jQuery(document).ready(function() {
	jQuery("a[href*=#togglesidebar]").toggle(function () {
		jQuery("ul#submenu").addClass("submenu-collapse");
	}, function () {
		jQuery("ul#submenu").removeClass("submenu-collapse");
	});
});
