jQuery(document).ready(function() {

	function assignGbx(width, height) {
		width = width || 275;
		height = height ||  275;
		jQuery('#change_cb').unbind('click').openDOMWindow({
		  "height": height,
		  "width": width,
		  positionTop: 50,
		  eventType: 'click',
		  positionLeft: 50,
		  windowSource: 'iframe',
		  windowPadding: 0
		});
		jQuery('#close_cb').closeDOMWindow({eventType: 'click'});
	}
	
	function createGbx() {
		var navArray = [jQuery('.galleria-image-nav-right'), jQuery('.galleria-image-nav-left')],
			$the_image = '', image_src = '',
			width = 275, height = 275;

		jQuery.each(navArray, function(index, elem) {
		  elem.unbind('click').click(function() {
			$the_image = jQuery('.galleria-image').filter('.active').find('img');
			image_src = $the_image.attr('src');
			width = jQuery.data($the_image[0], 'width') || 275;
			height = jQuery.data($the_image[0], 'height') || 275;
			assignGbx(width, height);
			image_src = image_src.replace('thumb', 'fly');
			jQuery('#change_cb').prop('href', image_src).click();
		  });
		});		
	}
	
	function setupGbx() {
		createGbx();
		assignGbx();
		jQuery('#dsgalleria').css('height', 'auto');
		//console.log('only now');
	}

	setTimeout( setupGbx, 3500 );
});