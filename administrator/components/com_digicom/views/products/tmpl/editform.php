<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 432 $
 * @lastmodified	$LastChangedDate: 2013-11-18 04:29:45 +0100 (Mon, 18 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

JHtml::_('behavior.tooltip');
JHTML::_('behavior.modal');
jimport('joomla.html.pane');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

global $isJ25;
$configs = $this->configs;
//echo "<pre>";print_r($this->prod);die();
$f = $configs->get('time_format','DD-MM-YYYY');
$f = str_replace ("-", "-%", $f);
$f = "%".$f;
$hidetab = $this->lists['hidetab'];
$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>
<script language="javascript" type="text/javascript">

/*###################################################################*/
	function getPlainDefault(eclass){
		var default_value = '';
		var dradios = $$('.'+eclass);
		dradios.each( function(el, index){
			if(el.checked){
				default_value =el.value;
			}
		});
		return default_value;
	}

	window.addEvent('domready',function(){
		$$('.plain_default').each(function(el,index){
			el.addEvent('click', function(ev){
				if(el.checked){
					$('plain_amount'+el.value).focus();
				}
			});
		});
		
		$$('.renewal_default').each(function(el,index){
			el.addEvent('click', function(ev){
				if(el.checked){
					$('renewal_amount'+el.value).focus();
				}
			});
		});
		
		$$('.plain').each(function(el, index){
			el.addEvent('click', function(ev){
				if(el.checked){
					var eid = el.id.substr(5);
					var renewal_amount = $('plain_amount'+eid).value;
					if(!renewal_amount) {
						$('plain_amount'+eid).focus();
					}
				}
			});
		});
		$$('.renewal').each(function(el, index){
			el.addEvent('click', function(ev){
				if(el.checked){
					var eid = el.id.substr(7);
					var renewal_amount = $('renewal_amount'+eid).value;
					if(!renewal_amount) {
						$('renewal_amount'+eid).focus();
					}
				}
			});
		});
		$$('.plain_amount').each(function(el,index){
			el.addEvent('focus',function(e){
				eid = this.id.substr(12);
				eplan = $('plain'+eid).checked="checked";
				var plain_default = getPlainDefault('plain_default');
				if(!plain_default){
					$('plain_default'+eid).checked="checked";
				}
			});
			el.addEvent('blur', function(e){
				if(isNaN(el.value)){
					alert("<?php echo JText::_('COM_DIGICOM_PRICE_MUST_IS_A_NUMBER'); ?>");
					el.focus();
					return;
				}
				eid = this.id.substr(12);
				if(!this.value){
					eplan = $('plain'+eid).checked="";
					$('plain_default'+eid).checked="";
				}
			});
		})

		$$('.renewal_amount').each(function(el,index){
			el.addEvent('focus',function(e){
				eid = this.id.substr(14);
				eplan = $('renewal'+eid).checked="checked";
				var plain_default = getPlainDefault('renewal_default');
				if(!plain_default){
					$('renewal_default'+eid).checked="checked";
				}
			});
			el.addEvent('blur', function(e){
				if(isNaN(el.value)){
					alert("<?php echo JText::_('COM_DIGICOM_PRICE_MUST_IS_A_NUMBER'); ?>");
					el.focus();
					return;
				}
				eid = this.id.substr(14);
				if(!this.value){
					eplan = $('renewal'+eid).checked="";
					$('renewal_default'+eid).checked="";
				}
			});
		})
	});
/*###################################################################*/
	
	function changeImageList(data) {
		var lists = new Array();
<?php
foreach ($this->lists['imagelists'] as $folder => $list) {
	echo 'lists["'.$folder.'"]="'.$list.'";';
}
?>
		document.getElementById('srcimageselector').innerHTML = unescape(lists[data.value]);
	}

	function changeShownImg(imgtype) {
		var subpath = document.getElementById('folders').options[document.getElementById('folders').selectedIndex].value;
		if (imgtype == 'src'){
			if (document.adminForm.srcimg.value !='') {
				document.adminForm.view_srcimg.src = '../images/' + subpath + '/' + document.adminForm.srcimg.value;
			} else {
				document.adminForm.view_srcimg.src = 'images/blank.png';
			}
		} else if (imgtype == 'prod') {
			if (document.adminForm.prodimg.value !='') {
				document.adminForm.view_prodimg.src = '../images/' + document.adminForm.prodimg.value;
			} else {
				document.adminForm.view_prodimg.src = 'images/blank.png';
			}
		}
	}

	function addSelectedToList() {
		var subpath = document.getElementById('folders').options[document.getElementById('folders').selectedIndex].value;
		var img = document.adminForm.srcimg.value;
		var imgpath = '../images/' + subpath + "/" + img ;
		var dst = document.getElementById("prodimg");
		var flag = 0;
		for (var i = 0; i < dst.childNodes.length; i++) {
			if (dst.childNodes[i].value == imgpath) {
				flag = 1;
				break;
			}
		}
		if (!flag) {
			insertOption(dst, imgpath, img, "option");
		}
	}

	function removeSelectedFromList() {
		var dst = document.getElementById("prodimg");
		var opt = dst.options[dst.selectedIndex];
		dst.removeChild(opt);
	}


	function moveUp () {
		var dst = document.getElementById("prodimg");
		var opt = dst.options[dst.selectedIndex];
		var prev = opt.previousSibling;
		if ( prev != null) {
			var tmp = dst.removeChild(opt);
			dst.insertBefore(tmp, prev);
		}
	}

	function moveDown () {
		var dst = document.getElementById("prodimg");
		var opt = dst.options[dst.selectedIndex];
		var next = opt.nextSibling;
		if ( next != null) {
			var tmp = dst.removeChild(next);
			dst.insertBefore(tmp, opt);
		}
	}

	function insertOption (where, value, in_html, type){
		var new_option = document.createElement(type);
		new_option.setAttribute('value', value);
		var new_text = document.createTextNode(in_html);
		new_option.appendChild(new_text);

		where.appendChild(new_option);
		return (where.options.length - 1);
	}

	function updateCheckbox (id) {
		var ids = new Array();
		ids[0] = 'linkno';
		ids[1] = 'linktoid';
		ids[2] = 'linktourl';
		for (var i = 0; i< 3; i++){
			document.getElementById(ids[i]).checked = 0;
		}
		document.getElementById(id).checked = 1;

	}


	Joomla.submitbutton = function(pressbutton){
		var form = document.adminForm;

		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		// assemble the images back into one field
		
		
		var flag=  false;
		//do field validation
		if (form.catid.value == "-1"){
			alert( "<?php echo JText::_("VIEWPRODSELPRODCAT");?>" );
			return false;
		} else if (form.catid.value == ""){
			alert( "<?php echo JText::_("VIEWPRODSELPRODCAT1"); ?>" );
			return false;
		} else if (form.title.value == "") {
			alert( "<?php echo JText::_("VIEWPRODPRODTITLEISEMPTY");?>" );
			return false;
		}  else if (form.name.value == "") {
			alert( "<?php echo JText::_("VIEWPRODPRODNAMEISEMPTY");?>" );
			return false;
		} else if (form.domainrequired.value < 2){
			flag = true;//checkFileRequired();
			if(!flag) return true;
		} else {
			flag = true;
		}

		if (!flag) return false;
		/*
		var dst = document.getElementById("prodimg");
		var flag = 0;
		var tmp = document.getElementById("images");
		tmp.value = '';
		for (var i = 0; i < dst.childNodes.length; i++) {
			if(typeof(dst.childNodes[i].value) != 'undefined'){
				tmp.value += "\n" + dst.childNodes[i].value;
			}
		}
		*/
		submitform( pressbutton );
	}


	function getElementsByClass(node,searchClass,tag) {
		var classElements = new Array();
		var els = node.getElementsByTagName(tag); // use "*" for all elements
		var elsLen = els.length;
		var pattern = new RegExp("\\b"+searchClass+"\\b");
		for (i = 0, j = 0; i < elsLen; i++) {
			if ( pattern.test(els[i].className) ) {
				classElements[j] = els[i];
				j++;
			}
		}
		return classElements;
	}


	function checkPlans(status) {

		var el = getElementsByClass(document,'plain','input');

		// var splains = document.getElementById('splains');

		for (i = 0; i < el.length; i++) {
			var eid = (el[i].id).substr(5);
			var plain_amount = document.getElementById('plain_amount'+eid).value;
			if (status) {
				if (!el[i].checked && plain_amount) el[i].checked = true;
			} else {
				if (el[i].checked && plain_amount ) el[i].checked = false;
			}
		}
	}


	function checkRenewal(status) {

		var el = getElementsByClass(document,'renewal','input');
		for (i = 0; i < el.length; i++) {
			var eid = (el[i].id).substr(7);
			var renewal_amount = document.getElementById('renewal_amount'+eid).value;
			if ( status ) {
				if (!el[i].checked && renewal_amount) el[i].checked = true;
			} else {
				if (el[i].checked && renewal_amount) el[i].checked = false;
			}
		}
	}


	function checkEmailreminder() {

		var el = getElementsByClass(document,'emailreminder','input');

		var semails = document.getElementById('semails');

		for (i = 0; i < el.length; i++) {
			if (semails.checked) {
				if (!el[i].checked) el[i].checked = true;
			} else {
				if (el[i].checked) el[i].checked = false;
			}
		}
	}
</script>
<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm" class="form-validate">
	<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="">
	<?php else : ?>
		<div id="j-main-container" class="">
	<?php endif;?>

<div class="form-inline form-inline-header">
		<div class="control-group ">
			<div class="control-label">
				<label id="jform_title-lbl" for="jform_title" class="hasTooltip required" 
					title="" data-original-title="<?php echo JText::_('COM_DIGICOM_PRODNAME_TIP'); ?>" aria-invalid="true">
					<?php echo JText::_('VIEWPRODPRODNAME');?> <span class="star">&nbsp;*</span>
				</label>
			</div>
			<div class="controls">
				<input type="text" name="name" id="jform_title" value="<?php echo $this->prod->name; ?>" class="text_area input-xxlarge input-large-text required invalid" size="40" required="required" aria-required="true" aria-invalid="true">
			</div>
		</div>
		<div class="control-group ">
			<div class="control-label">
				<label id="jform_alias-lbl" for="jform_alias" class="hasTooltip required" 
					title="" data-original-title="<?php echo JText::_('COM_DIGICOM_PRODALIAS_TIP'); ?>" aria-invalid="true">
					<?php echo JText::_('VIEWPRODPRODALIAS');?> <span class="star">&nbsp;*</span>
				</label>
			</div>
			<div class="controls">
				<input type="text" name="alias" id="jform_alias" value="<?php echo $this->prod->alias; ?>" class="text_area input-large" size="40" required="required" aria-required="true" aria-invalid="true">
			</div>
		</div>

		<?php echo $this->lists['domainrequired']; ?>
	</div>
	<br>
	<br>
	
	<?php
	$options = array(
		'onActive' => 'function(title, description){
			description.setStyle("display", "block");
			title.addClass("open").removeClass("closed");
		}',
				'onBackground' => 'function(title, description){
			description.setStyle("display", "none");
			title.addClass("closed").removeClass("open");
		}',
		'useCookie' => false, // this must not be a string. Don't use quotes.
		'startOffset'=>0,
		'active' => 'general_settings'
	);
	$options = array( 'active' => 'general_settings' );
	echo '<div class="tabbable">';
	echo JHtml::_( 'dctabs.start', 'product_settings', $options );
	
	echo JHtml::_( 'dctabs.addTab', 'product_settings', 'general_settings', '<i class=\"icon-pencil\"></i>'.(($isJ25)?'':'&nbsp;').'<strong>'.JText::_('DSDETAILS').'</strong>');
	?>
	<div class="row-fluid">
		<div class="span9">
			<fieldset class="adminform">
				<legend><?php echo JText::_('VIEWPRODPRODDESCINFO');?></legend>
				<table width="100%" class="admintable">
					 <tr>
						<td width="100%">
							<?php echo JText::_('VIEWPRODSHORTDESC');?>:
							<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODSHORTDESC_TIP'); ?>" ><img style="float: none; margin: 0px;" src="components/com_digicom/assets/images/icons/tooltip.png" border="0"/></span>
							<br />
							<textarea id="description" name="description" class="useredactor" style="width:100%;height:150px;"><?php echo $this->prod->description;?></textarea>
						</td>
					</tr>

					<tr>
						<td width="100%">
							<?php echo JText::_('VIEWPRODFULLDSC');?>:
								<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODFULLDESC_TIP'); ?>" ><img style="float: none; margin: 0px;" src="components/com_digicom/assets/images/icons/tooltip.png" border="0"/></span>
							<br />
							<textarea id="fulldescription" name="fulldescription" class="useredactor" style="width:100%;height:450px;"><?php echo $this->prod->fulldescription;?></textarea>
						</td>
					</tr>
				</table>
			</fieldset>
		</div>
		<div class="span3">
			<fieldset class="form-vertical">
				<legend><?php echo JText::_('VIEWPRODDET');?></legend>
				
				<div class="control-group">
					<div class="control-label">
						<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODPUBLISH_TIP'); ?>" ><?php echo JText::_('VIEWPRODPUBLISHED');?>:</label>
					</div>
					<div class="controls">
						<fieldset class="radio btn-group btn-group-yesno">
							<input type="radio" name="published" id="published1" value="1" <?php echo (($this->prod->published == 1 || $this->prod->published === null)?"checked='checked'":"");?> />
							<label class="btn" for="published1"><?php echo JText::_('DSYES'); ?></label>
							<input type="radio" name="published" id="published0" value="0" <?php echo (($this->prod->published == '0')?"checked='checked'":"");?> />
							<label class="btn" for="published0"><?php echo JText::_('DSNO'); ?></label>
						</fieldset>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">
						<label><?php echo JText::_('VIEWPRODFEATURED'); ?></label>
					</div>
					<div class="controls">
						<fieldset class="radio btn-group btn-group-yesno">
							<input type="radio" name="featured" id="featured1" value="1" <?php echo (($this->prod->featured == 1)?"checked='checked'":"");?> />
							<label class="btn" for="featured1"><?php echo JText::_('DSYES'); ?></label>
							<input type="radio" name="featured" id="featured0" value="0" <?php echo (($this->prod->featured == 0 || $this->prod->featured === null)?"checked='checked'":"");?> />
							<label class="btn" for="featured0"><?php echo JText::_('DSNO'); ?></label>
						</fieldset>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">
						<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODCATEGS_TIP'); ?>" ><?php echo JText::_('VIEWPRODPRODCAT');?>:</label>
					</div>
					<div class="controls">
						<?php  echo $this->lists['catid']; ?>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">
						<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODORDERING_TIP'); ?>" ><?php echo JText::_('VIEWPRODORDERING');?></label>
					</div>
					<div class="controls">
						<?php echo $this->lists['ordering']; ?>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">
						<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODACCESS_TIP'); ?>" ><?php echo JText::_('VIEWPRODUCTAL');?></label>
					</div>
					<div class="controls">
						<?php echo $this->lists['access']; ?>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">
						<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODPUSTARTBLISH_TIP'); ?>" ><?php echo JText::_('VIEWPRODUCTAL');?></label>
					</div>
					<div class="controls">
						<?php echo JHTML::_("calendar", $this->prod->publish_up > 0 ? date("Y-m-d", $this->prod->publish_up) : date("Y-m-d"), 'publish_up', 'publish_up'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">
						<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODPUENDBLISH_TIP'); ?>" ><?php echo JText::_('VIEWPRODEND');?></label>
					</div>
					<div class="controls">
						<?php echo JHTML::_("calendar", ($this->prod->publish_down>0?date("Y-m-d", $this->prod->publish_down):"Never"), 'publish_down', 'publish_down'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">
						<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODHIDDEN_TIP'); ?>" ><?php echo JText::_('VIEWPRODHIDDEN');?></label>
					</div>
					<div class="controls">
						<fieldset class="radio btn-group btn-group-yesno">
							<input type="radio" name="hide_public" id="hide_public1" value="1" <?php echo (($this->prod->hide_public == 1 || $this->prod->hide_public === null)?"checked='checked'":"");?> />
							<label class="btn" for="hide_public1"><?php echo JText::_('DSYES'); ?></label>
							<input type="radio" name="hide_public" id="hide_public0" value="0" <?php echo (($this->prod->hide_public == '0')?"checked='checked'":"");?> />
							<label class="btn" for="hide_public0"><?php echo JText::_('DSNO'); ?></label>
						</fieldset>
					</div>
				</div>
				
			</fieldset>
		</div>
	</div>
	<?php
	echo JHtml::_( 'dctabs.endTab' );
	
	echo JHtml::_( 'dctabs.addTab', 'product_settings', 'video-info', '<i class=\"icon-pictures\"></i>'.(($isJ25)?'':'&nbsp;').'<strong>'.JText::_('DIGI_MEDIA_TAB').'</strong>');
	?>
		<fieldset class="adminform">
	
			<legend><?php echo JText::_('VIEWPRODPRODIMAGE');?></legend>
	
			<div class="alert alert-info">
				<?php echo JText::_("HEADER_PRODUCTSIMAGE"); ?>
			</div>
			
			<?php $this->addMediaScript(); ?>
			<div class="control-group">
				<div class="control-label">
					<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODUCT_INTRO_IMAGE_TIP'); ?>" ><?php echo JText::_('COM_DIGICOM_PRODUCT_INTRO_IMAGE');?>:</label>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php
							//print_r($this->prod);die;
							$src = $this->prod->images;
							$imgattr = array(
								'id' =>'product_image_preview',
								'class' => 'media-preview',
								'style' => '',
							);
							$img = JHtml::image($src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $imgattr);
							
							$previewImg = '<div id="product_image_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
							$previewImgEmpty = '<div id="product_image_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
								. JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';

							$html = array();
							$html[] = '<div class="media-preview add-on">';
							$tooltip = $previewImgEmpty . $previewImg;
							$options = array(
								'title' => JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'),
								'text' => '<i class="icon-eye"></i>',
								'class' => 'hasTipPreview'
							);

							$html[] = JHtml::tooltip($tooltip, $options);
							$html[] = '</div>';
							echo implode("\n", $html);
						?>
						<input type="text" name="images" id="product_image" value="<?php echo $this->prod->images; ?>" readonly="readonly" class="input-small" aria-invalid="false">
						
						<a class="modal btn" title="Select" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=com_content&amp;author=&amp;fieldid=product_image&amp;folder=" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
							Select
						</a>
						<a class="btn hasTooltip" title="" href="#" onclick="
						jInsertFieldValue('', 'product_image');
						return false;
						" data-original-title="Clear">
						<i class="icon-remove"></i></a>
					</div>
				</div>
			</div>
	
			
		</fieldset>
	
	<?php
	echo JHtml::_( 'dctabs.endTab' );

	echo JHtml::_( 'dctabs.addTab', 'product_settings', 'pricing_details', '<i class=\"icon-cart\"></i>'.(($isJ25)?'':'&nbsp;').'<strong>'.JText::_('VIEWPRODPORODPRICING').'</strong>');
	?>		
		<fieldset class="adminform">
			<legend><?php echo JText::_('VIEWPRODPORODPRICING');?></legend>
			<?php
			$styleheader = " style='font-size:1.2em;font-weight:bold;padding:0.5em;' ";
			$stylerow = " style='padding:0.5em;' ";
			$producttype = $this->producttype;
			
			echo $this->plains;
			
			?>
		</fieldset>
	<?php
	echo JHtml::_( 'dctabs.endTab' );
	
if (!in_array('file',$hidetab)) {
	echo JHtml::_( 'dctabs.addTab', 'product_settings', 'file_details', '<i class=\"icon-download\"></i>'.(($isJ25)?'':'&nbsp;').'<strong>'.JText::_('VIEWPRODFILE').'</strong>');
	// $dispatcher	= JEventDispatcher::getInstance();
	if($isJ25){
		$dispatcher = JDispatcher::getInstance();
	} else {
		$dispatcher	= JEventDispatcher::getInstance();
	}
	JPluginHelper::importPlugin('digicom');
	$html = '';
	// Trigger the data preparation event.
	$dispatcher->trigger( 'onFileTabDisplay', array( 'com_digicom.product_edit' , $this, &$html ) );
	if( $html ) {
		echo $html;
	} elseif( !$html ) {
	?>
	<script type="text/javascript">
		function checkFileRequired(){
			var form = document.adminForm;
			file = document.getElementById("iscurfile");
			ftp_file = document.getElementById("ftpfile");
			
			if ((form.file.value == "")  && ((!file.innerHTML) && (ftp_file.value == ''))) {
				alert( "<?php echo JText::_("VIEWPRODNODOWNLOADFILE");?>" );
				return false;
			} else {
				var ttt = form.file.value;
				ttt= (ttt == '') ? ftp_file.value : ttt;
				ttt= (ttt == '') ? file.innerHTML : ttt;
				ttt = ttt.substr (ttt.length-3, ttt.length) ;
				if (ttt != 'zip' && (form.main_zip_file != undefined && form.main_zip_file.value != '')) {
					alert ("<?php echo JText::_("VIEWPRODISNOTZIP");?>");
					return false;
				} else {
					return true;
					// flag = true;
				}
			}
			return true;
		}
	</script>
		<fieldset class="adminform">
			<legend><?php echo JText::_('VIEWPRODFILE');?></legend>
			<?php
			// load core script
			$document = JFactory::getDocument();
			$document->addScript(JURI::root(true).'/media/digicom/assets/js/repeatable-fields.js?v=1.0.0');
			?>
			<script type="text/javascript">
				jQuery(function() {
					jQuery('.repeat').each(function() {
						jQuery(this).repeatable_fields();
					});
				});
			</script>
			<div id="digicom_item_files_items" class="repeat">
				<table class="table table-striped wrapper" id="itemList">
					<thead>
						<tr class="row">
							<th width="1%">
								<i class="icon-menu-2"></i>
							</th>
							<th style="width: 20%">
								File Name
							</th>
							<th>
								File URL
							</th>
							<th style="width: 2%"></th>
						</tr>
					</thead>
					<tbody class="container">
						<tr class="template row">
							<td width="1%"><span class="move"><i class="icon-move"></i></span></td>
							
							<td width="10%">
								<input type="text" name="file[{{row-count-placeholder}}][name]" placeholder="File Name"/>
							</td>
							
							<td width="70%">
								<div class="input-prepend input-append" style="display: block;">
									<input type="text" name="file[{{row-count-placeholder}}][url]" id="files_row_count_placeholder_id_url" placeholder="Upload or enter the file URL" class="span8"/>
									<a class="files_uploader_modal btn modal" title="Select" 
									href="javascript:;" onclick="openModal(this);"
									>
									Select</a>
								</div>
							</td>
							
							<td width="10%"><span class="remove"><i class="icon-remove"></i></span></td>
						</tr>
						<?php
							$files = $this->prod->file;
							
							if(count($files) >=1 && is_array($files)) :
							foreach($files as $key => $value){?>
							<tr class="row">
								<td width="1%">
									<span class="move"><i class="icon-move"></i></span>
									<input type="hidden" name="digicom_files_id" value="<?php echo $value->id; ?>" />
								</td>
								
								<td width="10%">
									<input type="text" 
									name="file[<?php echo $key; ?>][name]" placeholder="File Name" value="<?php echo $value->name; ?>"/>
								</td>
								
								<td width="70%">
									<div class="input-prepend input-append" style="display: block;">
										<input type="text" name="file[<?php echo $key; ?>][url]" id="files_<?php echo $key; ?>_url" placeholder="Upload or enter the file URL" class="span8"
										value="<?php echo $value->url; ?>"
										/>
										<a class="files_uploader_modal btn modal" title="Select" 
										href="javascript:;" onclick="openModal(this);"
										>
										Select</a>
									</div>
								</td>
								
								<td width="10%">
									<span class="remove"><i class="icon-remove"></i></span>                        
								</td>
							</tr>
							<?php
							}
							endif;
						?>
					</tbody>
					<tfoot>
						<tr class="row">
							<td width="10%" colspan="4">
								<span class="add btn btn-mini">Add</span>
								<input type="hidden" name="files_remove_id" value="" id="jform_files_remove_id"/>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			
			
		</fieldset>
		<?php
		}// end if(!$html)
		#echo $pane->endPanel();
		echo JHtml::_( 'dctabs.endTab' );
	} // hide file tab

if (!in_array('package',$hidetab)) {
	echo JHtml::_( 'dctabs.addTab', 'product_settings', 'package_info', '<i class=\"icon-cube\"></i>'.(($isJ25)?'':'&nbsp;').'<strong>'.JText::_('VIEWPRODPRODPACK').'</strong>');
	?>
		<script>
			//bundle_source_option
			jQuery(function ($) {	
				jQuery('#bundle_source_option_select .btn').click(function(){
					var bundle_source = jQuery('input[name=bundle_source]:checked').val();
					jQuery('.bundle_source_option').hide('slide');
					jQuery('#bundle_source_'+bundle_source+'_option').show('slide');
				});
			});
		</script>
		<fieldset class="adminform">
	
			<legend><?php echo JText::_('VIEWPRODPACKAGE');?></legend>
			<div class="alert alert-info">
				<?php echo JText::_("HEADER_PRODUCTINCLUDE"); ?>
			</div>
			
			<div class="control-group">
				<div class="control-label">
					<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_BUNDLE_OPTION_TIP'); ?>" ><?php echo JText::_('COM_DIGICOM_BUNDLE_OPTION');?>:</label>
				</div>
				<div class="controls">
					<fieldset id="bundle_source_option_select" class="radio btn-group">
						<input type="radio" name="bundle_source" id="bundle_source_product" value="product" <?php echo (($this->prod->bundle_source == 'product' || $this->prod->bundle_source === null)?"checked='checked'":"");?> />
						<label class="btn" for="bundle_source_product"><?php echo JText::_('VIEWPRODPRODUCT'); ?></label>
						<input type="radio" name="bundle_source" id="bundle_source_category" value="category" <?php echo (($this->prod->bundle_source == 'category')?"checked='checked'":"");?> />
						<label class="btn" for="bundle_source_category"><?php echo JText::_('VIEWPRODCATEGORY'); ?></label>
					</fieldset>
				</div>
			</div>
			
			<hr>
			
			<div class="control-group bundle_source_option <?php echo ($this->prod->bundle_source == 'category' ? '' : ' hide');?>" id="bundle_source_category_option">
				<div class="control-label">
					<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODCATEGS_TIP'); ?>" ><?php echo JText::_('VIEWPRODPRODCAT');?>:</label>
				</div>
				<div class="controls">
					<?php
					## Initialize array to store dropdown options ##
					$options = array();
					
					foreach($this->cats as $key=>$value) :
						## Create $value ##
						$options[] = JHTML::_('select.option', $value->id, $value->name);
					endforeach;
					
					## Create <select name="month" class="inputbox"></select> ##
					$bundle_cat = json_decode($this->prod->bundle_cat, true);
					$dropdown = JHTML::_('select.genericlist', $options, 'bundle_cat[]', 'multiple="multiple"', 'value', 'text', $bundle_cat);
					echo $dropdown;
					?>
				</div>
			</div>
			
			<div class="control-group bundle_source_option <?php echo ($this->prod->bundle_source == 'product' ? '' : ' hide');?>" id="bundle_source_product_option">
				<script type="text/javascript">
		
					function grayBoxiJoomla(link_element, width, height){
						SqueezeBox.open(link_element, {
							handler: 'iframe',
							size: {x: width, y: height}
						});
					}
		
					// Add new include item
		
					window.addEvent('domready', function(){
		
						$('buttonaddincludeproduct').addEvent('click', function(e) {
							e.stop()||new Event(e).stop();
		
							var url = "index.php?option=com_digicom&controller=products&task=productincludeitem&no_html=1&tmpl=component";
		
							 var req = new Request.HTML({
								method: 'get',
								url: url,
								data: { 'do' : '1' },
								//update: $('productincludes'),
								onComplete: function(transport){
									$('productincludes').adopt(transport);
		
									$$('a.modal').each(function(el) {
										el.addEvent('click', function(e) {
											new Event(e).stop();
											SqueezeBox.fromElement(el);
										});
									});
								}
							}).send();
						});
					});
		
		
					// Remove include item
		
					function remove_product_include( box_id ) {
		
						var box = document.getElementById('product_include_box_' + box_id);
						//var box = box.parentNode;
						while (box.firstChild) {
							box.removeChild( box.firstChild );
						}
		
						// remove wrapper div to include item
						var parent_box = document.getElementById('productincludes');
						parent_box.removeChild(box);
					}
		
		
					/*
					function show_plan_for_product_include( product_id, include_id ) {
		
						var plan_box = document.getElementById( 'product_include_subscr_plan_' + include_id );
		
						//plan_box.style = '';
						if(plan_box.style) {
							if(plan_box.style.display == 'none') {
								plan_box.style.display = '';
							}
						}
		
					}
					*/
		
				</script>
	
				<div id="productincludes">
		
					<?php foreach($this->include_products as $key => $include) { ?>
					
					<div id="product_include_box_<?php echo $key; ?>" style="border-bottom:1px solid #ccc;margin:15px;padding:10px;">
						<table width="100%">
							<tr>
								<td style="" width="30%"><?php echo JText::_( 'DSPROD' ); ?></td>
								<td style="">
									<div style="float:left">
										<span id="product_include_name_text_<?php echo $key; ?>" style="line-height: 17px;padding: 0.2em; border: 1px solid rgb(204, 204, 204); display: block; width: 250px;"><?php echo $include['name']; ?></span>
										<input type="hidden" value="<?php echo $include['id']; ?>" id="product_include_id<?php echo $key; ?>" name="product_include_id[<?php echo $key; ?>]"/>
									</div>
									<div class="button2-left">
										<div class="blank input-append" style="padding:0">
											<a rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="index.php?option=com_digicom&controller=products&task=selectProductInclude&id=<?php echo $key; ?>&tmpl=component" title="Select a Product Include" class="btn btn-small modal">Select</a>
										</div>
									</div>
								</td>
								<td style="">
									<a id="product_include_remove_1" class="btn btn-small btn-danger" href="javascript:void(0)" onclick="remove_product_include('<?php echo $key; ?>');">Remove</a>
								</td>
							</tr>
							
						</table>
					</div>
		
					<?php } ?>
				</div>
	
				<div style="margin:15px;padding:10px;">
					<a id="buttonaddincludeproduct" class="btn btn-small" href="#"><?php echo JText::_('VIEWPRODADDPRODUCT'); ?></a>
				</div>
				
			</div>
	
		</fieldset>
	
	<?php
		echo JHtml::_( 'dctabs.endTab' );
	} // end include tab (package)
	if($acl_groups = false){
	echo JHtml::_( 'dctabs.addTab', 'product_settings', 'acl-groups', '<i class=\"icon-users\"></i>'.(($isJ25)?'':'&nbsp;').JText::_('VIEWPRODACLGROUPS',true));
	?>
	<fieldset class="adminform">
		<legend><?php echo JText::_('VIEWPRODPRODACCESSINFO');?></legend>
		<table width="100%" class="admintable">
			<tr>
				<td>
					<?php echo JHtml::_('access.usergroups', 'groups', $this->lists['groups'], true); ?>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('Expire');?></legend>
		<table width="100%" class="admintable">
			<tr>
				<td>
					<?php echo JHtml::_('access.usergroups', 'expgroups', $this->lists['expgroups'], true); ?>
				</td>
			</tr>
		</table>
	</fieldset>

	<script>
		jQuery(function() {
			jQuery("#1group_1,#1group_2,#1group_3,#1group_4,#1group_5,#1group_6,#1group_7,#1group_8").closest('li').hide();
			jQuery("#2group_1,#2group_2,#2group_3,#2group_4,#2group_5,#2group_6,#2group_7,#2group_8").closest('li').hide();
		});
	</script>

	<?php
	echo JHtml::_( 'dctabs.endTab' );
	}
	

	echo JHtml::_( 'dctabs.addTab', 'product_settings', 'extra_info', '<i class=\"icon-bookmark\"></i>'.(($isJ25)?'':'&nbsp;').JText::_('VIEWPRODPRODEXTRAINFO',true));
	?>
	<script>
	
		function jSelectArticleID(id, title){
			document.getElementById('linktoidvalue').value=id;
			SqueezeBox.close();
		}
	</script>
	<div class="row-fluid">
		<div class="span12">
			
			<fieldset class="adminform">
				<legend><?php echo JText::_('VIEWPRODPRODMETAINFO');?></legend>
				<div class="alert alert-info">
					<?php echo JText::_("HEADER_PRODUCTSMETA"); ?>
				</div>

				<table width="100%">
					<tr>
						<td valign="top">
							<table class="admintable">

								<tr>
									<td valign="top" align="right">
										<?php echo JText::_('VIEWPRODPRODMETATITLE'); ?>:
									</td>
									<td>
										<input type="text" name="metatitle" value="<?php echo $this->prod->metatitle ; ?>" size="80"/>
										<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODMETATITLE_TIP'); ?>" ><img style="float: none; margin: 0px;" src="components/com_digicom/assets/images/icons/tooltip.png" border="0"/></span>
									</td>
								</tr>

								<tr>
									<td valign="top" align="right">
										<?php echo JText::_('VIEWPRODMETAKEY'); ?>:
									</td>
									<td>
										<textarea name="metakeywords" cols="50" rows="10"><?php echo $this->prod->metakeywords ; ?></textarea>
										<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODMETAKEYS_TIP'); ?>" ><img style="float: none; margin: 0px;" src="components/com_digicom/assets/images/icons/tooltip.png" border="0"/></span>
									</td>
								</tr>

								<tr>
									<td valign="top" align="right">
										<?php echo JText::_('VIEWPRODMETADESC'); ?>:
									</td>
									<td>
										<textarea name="metadescription" cols="50" rows="10"><?php echo $this->prod->metadescription; ?></textarea>
										<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODMETADESC_TIP'); ?>" ><img style="float: none; margin: 0px;" src="components/com_digicom/assets/images/icons/tooltip.png" border="0"/></span>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</fieldset>
		</div>
		
	</div>
		
	<?php
	echo JHtml::_( 'dctabs.endTab' );

echo JHtml::_( 'dctabs.end' );
?>
</div>
	<input type="hidden" name="featuredproducts" id="featuredproducts" value="" />
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="id" value="<?php echo $this->prod->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="prc" value="<?php echo $this->prc; ?>" />
	<input type="hidden" name="controller" value="Products" />
	<input type="hidden" name="forchange" value="" />
	<input type="hidden" name="tab" value="" />
	<input type="hidden" name="state_filter" value="<?php echo JRequest::getVar("state_filter", "-1"); ?>" />

	</div>
</form>
