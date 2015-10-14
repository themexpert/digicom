/**
 * @version 	1.0.0
 * @package 	Com DigiCOm
 * @author 		ThemeXpert
 * @copyright 	Copyright (c) 2006 - 2014 ThemeXpert Ltd. All rights reserved.
 * @license 	GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
imgpath = "";
/* Media manager*/

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

function changeValidity(){
  var validityType = jQuery('#jform_price_type').chosen(),
      lengthBox = jQuery('#jform_expiration_length'),
      lengthType = jQuery('#jform_expiration_type');
  if(validityType.val() != 0){
    lengthBox.removeClass('disabled').removeAttr('disabled').attr('required','false');
    lengthType.attr('disabled', false).trigger("liszt:updated");
  }else{
    lengthBox.addClass('disabled').attr('disabled','disabled').attr('required','true');
    lengthType.attr('disabled', true).trigger("liszt:updated");
  }
}

function openModal(a){
  fileInput = jQuery(a).prev();
  SqueezeBox.open('index.php?option=com_digicom&view=filemanager&tmpl=component&folder='+imgpath+'&layout=modal&fieldID='+fileInput.attr('id'),{handler:'iframe',size:{x:800,y:450}});
}

function changeOrderStatus(id,task,index,val){

  var f = document.adminForm, i, cbx,
    cb = f[id],
    status = f['orderstatus'+index];
    if (cb) {
        for (i = 0; true; i++) {
            cbx = f['cb'+i];
            if (!cbx)
                break;
            cbx.checked = false;
        } // for
        cb.checked = true;
    }

    if(status){
      orderstatus = f['orderstatus'+index];
      for (i = 0; true; i++) {
          cbx = f['orderstatus'+i];
          if (!cbx)
              break;
          cbx.value = '';
      } // for
      status.value = val;

    }

    f.boxchecked.value = 1;

    submitbutton(task);
}

function showProperTemplateEmail(type, e){
	//console.log(type);return;
  e.preventDefault();

  var templateType = jQuery('#jform_'+ type +'_email_type').chosen(),
		htmlTemplate = jQuery('.email_template_digicom.email_template_digicom_'+type+'_html'),
		rawTemplate = jQuery('.email_template_digicom.email_template_digicom_'+type+'_raw');
	//console.log(templateType.val());
	if(templateType.val() == 'html'){
		htmlTemplate.parent().parent().show();
		rawTemplate.parent().parent().hide();
	}else{
		htmlTemplate.parent().parent().hide();
    rawTemplate.parent().parent().show();
	}
}
/* files functions */
function removeFilesRow(selector)
{
  event.preventDefault();

  var row = jQuery(selector).parents('tr');

  if (confirm(Joomla.JText._('COM_DIGICOM_PRODUCTS_FILES_REMOVE_WARNING')))
  {
    beforeFileremove(row);
    jQuery(row).remove();
  }
}

function addFilesRow(){
  event.preventDefault();

  var container = jQuery('table#filesitemList tbody');
  // var row       = jQuery('table#filesRowSample tbody').clone().html();
  var row       = fileSampleRow;
  var fileindex = parseInt(jQuery('table#filesitemList tbody#itemsfilesRows tr').last().attr('data-index'))+1;
  var new_row = jQuery(row).appendTo(container);
  afterAddRow(new_row, fileindex);

  jQuery(new_row).attr('data-index',fileindex);

}

function afterAddRow(new_row, fileindex)
{

  jQuery('*', new_row).each(function()
  {
    jQuery.each(this.attributes, function(index, element){
      this.value = this.value.replace(/{{row-count-placeholder}}/, fileindex);
      this.value = this.value.replace(/_row_count_placeholder_id_/, fileindex);
    });
  });

}

function reArranageFiles()
{
  var rows = jQuery('table#filesitemList tbody#itemsfilesRows tr');
  jQuery(rows).each(function(index)
  {
    console.log(index);
    // jQuery.each(this.attributes, function(index, element){
    //   this.value = this.value.replace(/{{row-count-placeholder}}/, fileindex);
    //   this.value = this.value.replace(/_row_count_placeholder_id_/, fileindex);
    // });
    jQuery(this).find("input[id^='files_ordering_']").val(index);
  });
}

function beforeFileremove(row) {
  var fields =  jQuery(row).find("input[id^='digicom_files_id']");
  var filesId = '';
  jQuery(fields).each(function(){
    filesId = this.value;
  });
  if(!filesId) return;
  var jform_files_remove_id = jQuery("#jform_files_remove_id").val();
  if(jform_files_remove_id){
    jQuery('#jform_files_remove_id').val(jform_files_remove_id + ',' + filesId);
  }else{
    jQuery('#jform_files_remove_id').val(filesId);
  }
}

/* end files function */

jQuery(document).ready(function() {

  if( typeof jQuery.ui !== 'undefined' && typeof jQuery.ui.sortable !== 'undefined') {
    jQuery('#itemsfilesRows').sortable({
      stop         : reArranageFiles
    });
  }


  jQuery('.email_template_digicom').parent().parent().hide();

  /* Load the value from localStorage*/
  if (typeof(Storage) !== "undefined")
  {
    var toggleDigiSidebar = localStorage.getItem('digisidebar',false);
    if(toggleDigiSidebar == 'true'){
      jQuery("body").addClass("sidebar-collapse");
    }else{
      jQuery("body").removeClass("sidebar-collapse");
      localStorage.setItem('digisidebar', false);
    }
  }

  jQuery("a[href*=#togglesidebar]").click(function(e) {

    if(jQuery("body").hasClass("sidebar-collapse")) {
      /* if it's closed, then remove class and open it */
      jQuery("body").removeClass("sidebar-collapse");

      // Load the value from localStorage
      if (typeof(Storage) !== "undefined")
      {
        /* Set the last selection in localStorage */
        localStorage.setItem('digisidebar', false);
      }
    } else {
      /* as its open, close it */
      jQuery("body").addClass("sidebar-collapse");

      // Load the value from localStorage
      if (typeof(Storage) !== "undefined")
      {
        /* Set the last selection in localStorage */
        localStorage.setItem('digisidebar', true);
      }
    }

  });

	jQuery("#toggle_settings").toggle(function (e) {
		e.preventDefault();
		jQuery("body").addClass("sidebar-right-collapse");
	}, function () {
		jQuery("body").removeClass("sidebar-right-collapse");
	});

	jQuery('body').click(function(){
		if(jQuery('body').hasClass('sidebar-right-collapse')){
			jQuery('body').removeClass('sidebar-right-collapse');
		}
	});

  /* Toggle toolbar button*/
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

  /* onclick edit alias of product*/
  jQuery('a#digicom-edit-alias').click(function () {
    jQuery('#digicom-product-alias').hide();
    jQuery('#digicom-product-alias-edit').show(function(){
      jQuery('#jform_alias').focus();
    });
  });

  jQuery('input[id=jform_alias]').focusout(function() {
    jQuery('#digicom-product-alias').text(jQuery('#jform_alias').val());
    jQuery('#digicom-product-alias').show();
    jQuery('#digicom-product-alias-edit').hide();
  });

	var sortable = {
		update: function (event, ui) {
			//create the array that hold the positions...
		  var order = [];
			//loop trought each li...
		   jQuery('#digicom_item_files_items .container tr').each( function(e) {
			jQuery(this).find('input[id^=files_ordering_]').val(jQuery(this).index());

		  });
		}
	};
  // jQuery(").trigger("liszt:updated");
  //  jQuery(document).on('change', '#jform_country-1', getStatelist);
  //
  // jQuery("select.tax-country").chosen().change( function() {
  //   getStatelist();
  // });

  // jQuery("#jform_tax_rates_container select.tax-country").change(function() {
  //   getStatelist();
  // });
  // jQuery('#jform_tax_rates_container select.tax-country').prop('disabled', true).trigger("chosen:updated");

    // jQuery('#jform_tax_rates_container').find('select').val()
    // jQuery( "#jform_tax_rates_container select.tax-country").each(function() {
    //   var item = jQuery(this);
    //   autoUpdateTaxstate(item);
    // });
    jQuery('#jform_tax_rates_button').click(function(e){
      e.preventDefault();
      var ajaxurl = 'index.php?option=com_digicom&task=action&format=json';

      jQuery( "#jform_tax_rates_container select.tax-country").each(function()
      {
        var item = jQuery(this);
        var field_name = item.attr('name').replace('country', 'state');
        var stateval = jQuery('#jform_tax_rates_modal input#jform_'+field_name).val();

        if(stateval != ''){

          var data = {
            action  : 'get_store_states',
            class   : 'DigiComHelperCountry',
            country: item.val(),
            field_name: field_name
          };

          // Replace current select options. The trigger is needed for Chosen select box enhancer
          var select_field = '<select name="' + data.field_name + '"></select>';
          item.parent().next().find('input[type="text"]').replaceWith( select_field );
          // jQuery('#jform_tax_rates_modal input#jform_'+field_name).css('display','none');
          // jQuery('#jform_tax_rates_modal input#jform_'+field_name).remove();

          jQuery.getJSON(ajaxurl, data, function(response)
          {
            // console.log(data);
            // console.log(field_name);
            // console.log(stateval);

            // The response contains the options to use in help site select field
            var items = [];

            // Build options
            jQuery.each(response, function(key, val) {
              if(key == stateval){
                items.push('<option value="' + key + '" selected>' + val + '</option>');
              }else{
                items.push('<option value="' + key + '">' + val + '</option>');
              }
            });
            // Replace current select options. The trigger is needed for Chosen select box enhancer
            item.parent().next().find('select').empty().append(items).chosen();

          });

        }

       });

    });
});


function getStatelist(e){
  var ajaxurl = 'index.php?option=com_digicom&task=action&format=json';

// Update tax rate state field based on selected rate country
  // jQuery('#jform_tax_rates_modal select.tax-country').change(function() {
    // console.log(ajaxurl);
    var item = jQuery(e);
    data = {
      action  : 'get_store_states',
      class   : 'DigiComHelperCountry',
      country: item.val(),
      field_name: item.attr('name').replace('country', 'state')
    };
    // console.log(data);

    jQuery.getJSON(ajaxurl, data, function(response)
    {
      var total = Object.keys(response).length;
      if(total == '0')
      {
        var text_field = '<input type="text" name="' + data.field_name + '" value=""/>';
        item.parent().next().find('select[name="'+data.field_name+'"]').chosen('destroy');
        item.parent().next().find('select').replaceWith( text_field );
      }
      else
      {
  			var items = [];

  			// Build options
  			jQuery.each(response, function(key, val) {
  				items.push('<option value="' + key + '">' + val + '</option>');
  			});

  			// Replace current select options. The trigger is needed for Chosen select box enhancer
        var select_field = '<select name="' + data.field_name + '"></select>';
        item.parent().next().find('select').chosen('destroy');
        item.parent().next().find('select, input').replaceWith( select_field );
        item.parent().next().find('select').empty().append(items).chosen();
      }
    });

  // });
}
