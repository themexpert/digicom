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
});
function toGGleToolberItems(none){
  jQuery('#toolbar-publish,#toolbar-unpublish,#toolbar-trash').css('display',none);
}
jQuery(document).ready(function(){
	
	toGGleToolberItems('none');

	jQuery("input[type='checkbox']").change(function() {
	    if(this.checked) {
	       toGGleToolberItems('inline-block');
	    }else{
	    	if(!jQuery("input[type='checkbox']").is(':checked')){
	    		toGGleToolberItems('none');
	    	}else{
	    		
				form = document.getElementById('adminForm');
				// Toggle main toggle checkbox depending on checkbox selection
			    var c = true, i, e;
			    for (i = 0, n = form.elements.length; i < n; i++) {
			        e = form.elements[i];
			        if (e.type == 'checkbox') {
			            if (e.name != 'checkall-toggle' && e.checked == false) {
			                c = false;
			                break;
			            }
			        }
			    }
			    if (c) {
			        toGGleToolberItems('none');
			    }
	    	}
	    }

	});

});
