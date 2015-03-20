/*!
 * com digicom
 * Licensed under a 3 clauses BSD license
 */

jQuery(document).ready(function (){
	var elem=jQuery('ul');      
	jQuery('#viewcontrols a').on('click',function(e) {
		if (jQuery(this).hasClass('gridview')) {
				elem.fadeOut(0, function () {
					elem.fadeIn(0);
					elem.removeClass('list').addClass('grid');
				});      
		}
		else if(jQuery(this).hasClass('listview')) {
			elem.fadeOut(0, function () {
				elem.fadeIn(0);
				elem.removeClass('grid').addClass('list');
			});         
		}
	});
});
