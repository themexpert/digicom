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
  var fileNameIndex = value.lastIndexOf("/") + 1;
  var filename = value.substr(fileNameIndex);
  filename = filename.replace(/\.[^/.]+$/, "");
  var filenameID = fieldID.replace("url", "name");

  jQuery('#' + fieldID).val(value);
	jQuery('#' + filenameID).val(filename);
	if (typeof SqueezeBox !== 'undefined' && jQuery.isFunction(SqueezeBox)) {
		SqueezeBox.close();
	} else {
		parent.document.getElementById('sbox-btn-close').click()
	}
}

function openModal(a){
  fileInput = jQuery(a).prev();
  SqueezeBox.open('index.php?option=com_digicom&view=filemanager&tmpl=component&folder='+imgpath+'&layout=modal&fieldID='+fileInput.attr('id'),{handler:'iframe',size:{x:800,y:450}});
}

jQuery(document).ready(function() {

 jQuery("a[href*=#togglesidebar]").toggle(function () {
  jQuery("body").addClass("sidebar-collapse");
 }, function () {
  jQuery("body").removeClass("sidebar-collapse");
 });

 jQuery("#toggle_settings").toggle(function () {
  jQuery("body").addClass("sidebar-right-collapse");
 }, function () {
  jQuery("body").removeClass("sidebar-right-collapse");
 });

  // Toggle toolbar button
  var btnSelector = jQuery('#toolbar-publish,#toolbar-unpublish,#toolbar-trash,#toolbar-delete,#toolbar-edit');

  jQuery("input[type='checkbox']").change( function(){

    if(!jQuery("input[type='checkbox']").is(':checked')){
     btnSelector.removeClass('in');
    }else{
     btnSelector.addClass('in');
    }

    jQuery('input[name="checkall-toggle"]').change( function(){
      if( this.checked){
        btnSelector.removeClass('in');
        btnSelector.addClass('in');
      }else{
       btnSelector.removeClass('in');
      }
    });
  });

});