<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

JHtml::_('behavior.tooltip');

JHTML::_('jquery.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$doc = JFactory::getDocument();
$doc->addStyleSheet("components/com_digicom/assets/css/digicom.css");
		
$doc->addScript(JURI::root() . 'media/digicom/assets/js/ajaxupload.js');
$doc->addScript(JURI::root() . 'administrator/components/com_digicom/assets/js/redactor.min.js');
$doc->addStyleSheet(JURI::root() . 'administrator/components/com_digicom/assets/css/redactor.css');

$upload_script = '
	window.addEvent( "domready", function(){
		new AjaxUpload("ajaxuploadcatimage", {
			action: "' . JURI::root() . 'administrator/index.php?option=com_digicom&controller=categories&task=uploadimage&tmpl=component&no_html=1",
			name: "catimage",
			multiple: false,
			data: {\'CatId\' : \''.( $this->cat->id ? $this->cat->id : 0 ).'\'},
			onComplete: function(file, response){
				document.getElementById("catthumbnail").innerHTML = response;
			}
		});

		jQuery(".useredactor").redactor();
		jQuery(".redactor_useredactor").css("height","400px");
	});
';
$doc->addScriptDeclaration( $upload_script );
?>
<script language="javascript" type="text/javascript">
		<!--

		function submitbutton(pressbutton) {

			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform(pressbutton);
				return;
			}


			if ( form.name.value == "" ) {
				alert( '<?php echo JText::_("VIEWCATEGORYCATMUSTHAVENAME");?>' );
				return false;
			} else if (form.title.value == ""){
				alert( '<?php echo JText::_("VIEWCATEGORYCATMUSTHAVETITLE");?>' )
				return false;
			} 

			submitform( pressbutton );
		}
		-->
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
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

	echo JHtml::_( 'dctabs.start', 'category_settings', $options ); 
	echo JHtml::_( 'dctabs.addTab', 'category_settings', 'general_settings', JText::_('VIEWCATEGORYGENERAL',true));
?>
	<fieldset class="adminform">
			<div>
				<div class="control-group">
					<div class="control-label"><label>
					<?php echo JText::_('VIEWCATEGORYCATNAME');?>:
					</label></div>
					<div class="controls">
						<input class="text_area" type="text" name="name" value="<?php echo stripslashes( $this->cat->name ); ?>" size="50" maxlength="255" title="<?php echo JText::_('Menu name');?>" />
						<?php
							echo JHTML::tooltip(JText::_("COM_DIGICOM_CATEGNAME_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
						?>
					</div>
				</div>
		<div style="clear: both;"></div>
				<div class="control-group">
					<div class="control-label"><label>
					<?php echo JText::_('VIEWCATEGORYORDERING'); ?>:
					</label></div>
					<div class="controls">
					<?php echo $this->lists['ordering']; ?>
					<?php
						echo JHTML::tooltip(JText::_("COM_DIGICOM_CATEGORDERING_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
					</div>
				</div>
				<div style="clear: both;"></div>
				<div class="control-group">
					<div class="control-label"><label>
					<?php echo JText::_('VIEWCATEGORYAL');?>:
					</label></div>
					<div class="controls">
					<?php echo $this->lists['access']; ?>
					<?php
						echo JHTML::tooltip(JText::_("COM_DIGICOM_CATEGACCESS_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
					</div>
				</div>
				<div style="clear: both;"></div>
				<div class="control-group">
					<div class="control-label"><label>
					<?php echo JText::_('VIEWCATEGORYPUBLISHED');?>
					</label>
					</div>
					<div class="controls">
						<table>
							<tr>
								<td style="width:1%">
									<input type="radio" value="0" id="published0" name="published">
								</td>
								<td style="width:1%">
									<?php echo JText::_("DSNO"); ?>
								</td>
								<td style="width:1%">
									<input type="radio" checked="checked" value="1" id="published1" name="published">
								</td>
								<td style="width:1%">
									<?php echo JText::_("DSYES"); ?>
								</td>
								<td style="width:50%">
									<?php
										echo JHTML::tooltip(JText::_("COM_DIGICOM_CATEGPUBLISHED_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
									?>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<div class="control-group">
					<div class="control-label"><label>
					<?php echo JText::_('VIEWCATEGORYPARENT');?>
					</label></div>
					<div class="controls">
					<?php echo $this->lists['parent']; ?>
					<?php
						echo JHTML::tooltip(JText::_("COM_DIGICOM_CATEGPARENT_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label"><label>
						<?php echo JText::_('VIEWCATEGORYDESCRIPTION');?>: <?php
						echo JHTML::tooltip(JText::_("COM_DIGICOM_CATEGDESCRIPTION_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
						?>
					</label></div>
				</div>
				<div class="control-group">
						<textarea id="description" name="description" class="useredactor" style="width:100%;height:550px;"><?php echo $this->cat->description;?></textarea>
				</div>
			</div>
	</fieldset>
<?php
	echo JHtml::_( 'dctabs.endTab');


	echo JHtml::_( 'dctabs.addTab', 'category_settings', 'image_info_new', JText::_('CATEGORY_EDIT_TAB_TITLE',true));
	// Image Tab (NEW)
?>
	<fieldset class="adminform">
		<?php $this->addMediaScript(); ?>
			<div class="control-group">
				<div class="control-label">
					<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_PRODUCT_INTRO_IMAGE_TIP'); ?>" ><?php echo JText::_('COM_DIGICOM_PRODUCT_INTRO_IMAGE');?>:</label>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php
							//print_r($this->cat);die;
							$src = $this->cat->image;
							$imgattr = array(
								'id' =>'cat_image_preview',
								'class' => 'media-preview',
								'style' => '',
							);
							$img = JHtml::image($src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $imgattr);
							
							$previewImg = '<div id="cat_image_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
							$previewImgEmpty = '<div id="cat_image_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
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
						<input type="text" name="image" id="cat_image" value="<?php echo $this->cat->image; ?>" readonly="readonly" class="input-small" aria-invalid="false">
						
						<a class="modal btn" title="Select" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=com_content&amp;author=&amp;fieldid=cat_image&amp;folder=" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
							Select
						</a>
						<a class="btn hasTooltip" title="" href="#" onclick="
						jInsertFieldValue('', 'cat_image');
						return false;
						" data-original-title="Clear">
						<i class="icon-remove"></i></a>
					</div>
				</div>
			</div>

	</fieldset>
<?php
	echo JHtml::_( 'dctabs.endTab');


	echo JHtml::_( 'dctabs.addTab', 'category_settings', 'meta_info', JText::_('VIEWCATEGORYMETATAGS',true));
	// Meta Tab
?>
			<div class="control-group">
				<div class="control-label"><label><?php echo JText::_('VIEWCATEGORYMETATITLE');?>:</label></div>
				<div class="controls">
					<input class="text_area" type="text" name="title" value="<?php echo stripslashes( $this->cat->title ); ?>" size="50" maxlength="50"/>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label"><label><?php echo JText::_('VIEWCATEGORYMK');?>:</label></div>
				<div class="controls">
					<textarea rows=10 cols=30 name="metakeywords" ><?php echo stripslashes( $this->cat->metakeywords );?></textarea>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label"><label><?php echo JText::_('VIEWCATEGORYMT');?>:</label></div>
				<div class="controls">
					<textarea rows=10 cols=30 name="metadescription" ><?php echo stripslashes( $this->cat->metadescription );?></textarea>
				</div>
			</div>

<?php
		echo JHtml::_( 'dctabs.endTab');
		echo JHtml::_( 'dctabs.end' );
?>
			<input type="hidden" name="option" value="com_digicom" />
			<input type="hidden" name="id" value="<?php echo $this->cat->id; ?>" />
			<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="Categories" />
		<input type="hidden" name="oldtitle" value="<?php echo $this->cat->name;?>" />
</form>