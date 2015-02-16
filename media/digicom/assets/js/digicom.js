/**
 * @version 	1.0.0
 * @package 	Com DigiCOm
 * @author 		ThemeXpert
 * @copyright 	Copyright (c) 2006 - 2014 ThemeXpert Ltd. All rights reserved.
 * @license 	GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
imgpath = "";
// Media manager
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
  jQuery("body").addClass("sidebar-collapse");
 }, function () {
  jQuery("body").removeClass("sidebar-collapse");
 });
});