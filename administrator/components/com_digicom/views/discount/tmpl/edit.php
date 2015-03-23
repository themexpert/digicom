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
$document = JFactory::getDocument();
$app = JFactory::getApplication();
$input = $app->input;
$input->set('layout', 'dgform');
$configs = $this->configs;
$nullDate = 0;
JHTML::_("behavior.calendar");
jimport('joomla.html.pane');
$f = $configs->get('time_format','DD-MM-YYYY');
$f = str_replace ("-", "-%", $f);
$f = "%".$f;
$link = 'index.php?option=com_digicom&amp;view=products&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';
JHtml::_('behavior.modal');

$ajax = <<<EOD
jQuery(document).ready(function() {
	jQuery('#jform_discount_enable_range .btn').click(function(){
		var discount_enable_range = jQuery("input:radio[name='jform[discount_enable_range]']:checked").val();
		if(discount_enable_range === '1'){
			jQuery('#discount_enable_range_product').hide('slide');
		}else{
			jQuery('#discount_enable_range_product').show('slide');
		}
	});
});
function jSelectProduct(id, title, catid, object, link, lang,price)
{
	var hreflang = '';
	if (lang !== '')
	{
		var hreflang = ' hreflang = \"' + lang + '\"';
	}

	var tag = '<tr id=\"productincludes_item_' + id + '\"><td><input type=\"hidden\" id=\"product_include_id'+id+'\" name=\"jform[products][]\" value=\"'+id+'\" /> <a' + hreflang + ' href=\"' + link + '\">' + title + '</a></td><td><a href=\#\" onclick=\"digiRemoveProduct('+ id +');\"><i class=\"icon-remove\"></i></a></td></tr>';
	//jInsertEditorText(tag, '" . 'productincludes_items' . "');
	jQuery('#productincludes_items').append(tag);
	jModalClose();
}
function digiRemoveProduct(id){
	event.preventDefault();
	jQuery('tr#productincludes_item_'+id).remove();
	changePlain();
}

/* ]]> */
EOD;
$doc = JFactory::getDocument();
$doc->addScriptDeclaration( $ajax );
?>

<script language="javascript" type="text/javascript">
function submitbutton(pressbutton) {
	submitform( pressbutton );
}
</script>

<form action="index.php?option=com_digicom&controller=promos" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
	<?php else : ?>
	<div id="j-main-container" class="">
	<?php endif;?>
		<?php
		$options = array(
			'onActive'		=> 'function(title, description){
						description.setStyle("display", "block");
						title.addClass("open").removeClass("closed");
					}',
			'onBackground'	=> 'function(title, description){
						description.setStyle("display", "none");
						title.addClass("closed").removeClass("open");
					}',
			'useCookie'		=> true, // this must not be a string. Don't use quotes.
			'active'		=> 'general-settings'
		);
		echo JHtml::_('bootstrap.startTabSet', 'promo_settings', $options);
		echo JHtml::_('bootstrap.addTab', 'promo_settings', 'general-settings', JText::_('COM_DIGICOM_DISCOUNT_TAB_TITLE_DISCOUNT_CODE_SETTINGS') );
		?>
		<div class="row-fluid">
			<div class="span8">
					<!--<h3><?php echo JText::_('COM_DIGICOM_DISCOUNT_TAB_TITLE_DISCOUNT_CODE_SETTINGS');?></h3>-->
					<div class="form-horizontal">
						<div class="control-group">
							<label for="" class="control-label"><?php echo JText::_("JGLOBAL_TITLE");?></label>
							<div class="controls">
								<?php echo $this->form->getInput('title'); ?>
								<?php
									echo JHTML::tooltip(JText::_("COM_DIGICOM_DICOUNT_TITLE_DESC"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
								?>
							</div>
						</div>

						<div class="control-group">
							<label for="" class="control-label"><?php echo JText::_("COM_DIGICOM_DISCOUNT_CODE");?></label>
							<div class="controls">
								<?php echo $this->form->getInput('code'); ?>
						<?php
							echo JHTML::tooltip(JText::_("COM_DIGICOM_DISCOUNT_CODE_DESC"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
						?>
							</div>
						</div>

						<div class="control-group">
							<label for="" class="control-label"><?php echo JText::_("COM_DIGICOM_DISCOUNT_CODE_USAGE_LIMIT");?></label>
							<div class="controls">
								<?php echo $this->form->getInput('codelimit'); ?>
						<?php
							echo JHTML::tooltip(JText::_("COM_DIGICOM_DISCOUNT_CODE_USAGE_LIMIT_DESC"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
						?>
							</div>
						</div>

						<div class="control-group">
							<label for="" class="control-label"><?php echo JText::_("COM_DIGICOM_DISCOUNT_AMOUNT");?></label>
							<div class="controls">
								<?php echo $this->form->getInput('amount'); ?>
								<?php echo $this->form->getInput('promotype'); ?>
								
						&nbsp;
						<?php
							echo JHTML::tooltip(JText::_("COM_DIGICOM_DISCOUNT_AMOUNT_DESC"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
						?>
							</div>
						</div>

						<div class="control-group">
							<label for="" class="control-label"><?php echo JText::_("COM_DIGICOM_DISCOUNT_CODE_START_PUBLISHIING");?></label>
							<div class="controls">
								<?php echo $this->form->getInput('codestart'); ?>
								<?php
									echo JHTML::tooltip(JText::_("COM_DIGICOM_DISCOUNT_CODE_START_PUBLISHIING_DESC"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
								?>
							</div>
						</div>

						<div class="control-group">
							<label for="" class="control-label"><?php echo JText::_("COM_DIGICOM_DISCOUNT_CODE_END_PUBLISHIING");?></label>
							<div class="controls">
								<?php echo $this->form->getInput('codeend'); ?>
								<?php
									echo JHTML::tooltip(JText::_("COM_DIGICOM_DISCOUNT_CODE_END_PUBLISHIING_DESC"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
								?>
							</div>
						</div>

						<div class="control-group">
							<label for="" class="control-label"><?php echo JText::_("JSTATUS");?></label>
								
							<div class="controls">
								<?php echo $this->form->getInput('published'); ?>
								<?php
									echo JHTML::tooltip(JText::_("COM_DIGICOM_DISCOUNT_CODE_STATUS_DESC"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
								?>
							</div>
						</div>

						<div class="control-group">
							<label for="" class="control-label"><?php echo JText::_("COM_DIGICOM_DISCOUNT_CODE_APPLY_ON");?></label>
							<div class="controls">
								<?php echo JText::_("COM_DIGICOM_DISCOUNT_CODE_APPLY_ON_NEW"); ?> 
								<?php echo $this->form->getInput('validfornew'); ?>

								<?php echo JText::_("COM_DIGICOM_DISCOUNT_CODE_APPLY_ON_RENEWAL"); ?> 
								<?php echo $this->form->getInput('validforrenewal'); ?>
							
							</div>
						</div>
					</div>
					
			</div>

			<div class="span4 well">
				<div class="clearfix">
					<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_DISCOUNT_CODE_ENABLE_FOR_ALL_PRODUCTS_LABEL_DESC'); ?>" ><?php echo JText::_('COM_DIGICOM_DISCOUNT_CODE_ENABLE_FOR_ALL_PRODUCTS_LABEL');?></label>
					<?php echo $this->form->getInput('discount_enable_range'); ?>
				</div>
				
				<div id="discount_enable_range_product"<?php echo (($this->item->discount_enable_range == '1' || $this->item->discount_enable_range === null) ? " class='hide'":"");?>>
				
					<h3><?php echo JText::_( 'COM_DIGICOM_DISCOUNT_CODE_PRODUCT_RESTRICTION_TITLE' ); ?></h3>

					<table id="productincludes" class="table table-striped table-hover" id="productList">
						<thead>
							<tr>
								<td>Product Name</td>
								<td width="1%">Action</td>
							</tr>
						</thead>
						<tbody id="productincludes_items">
							<?php
							if(count($this->item->products) > 0):
							foreach ($this->item->products as $product): ?>
								<tr id="productincludes_item_<?php echo $product->id; ?>">
									<td>
										<input type="hidden" id="product_include_id<?php echo $product->id; ?>" name="jform[products][]" value="<?php echo $product->id; ?>"> 
										<a href="index.php?option=com_digicom&view=product&id=<?php echo $product->id; ?>"><?php echo $product->name; ?></a>
									</td>
									<td>
										<a href="#&quot;" onclick="digiRemoveProduct(<?php echo $product->id; ?>);">
											<i class="icon-remove"></i>
										</a>
									</td>
								</tr>
							<?php
							endforeach;
							endif;
							?>
						</tbody>
					</table>
					

					<div style="margin:15px;padding:10px;">
						<a class="btn btn-small btn-primary modal" title="Products" href="<?php echo $link; ?>" 
						rel="{handler: 'iframe', size: {x: 800, y: 500}}">
							<i class="icon-file-add"></i> 
							<?php echo JText::_('COM_DIGICOM_ADD_PRODUCT'); ?>
						</a>

					</div>

				</div>

				
			</div>

		</div>


		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_DIGICOM_DISCOUNT_REPORTS');?></legend>
			<?php
			if ($this->item->codeend != $nullDate) {
				$period = $this->item->codeend - time(); //$promo->codestart;
				$days = (int ) ($period / (3600 * 24)) ;
				$left = $period % (3600 * 24);
				$hours = (int ) ($left / 3600 );
				$mins = (int )(($left - $hours*3600)/60) ;//$left % (3600 );

			} else {
				$period = 0;// $promo->codeend - time(); //$promo->codestart;
				$days = JText::_("COM_DIGICOM_UNLIMITED");//(int ) ($period / (3600 * 24)) ;
				$left = JText::_("COM_DIGICOM_UNLIMITED");//$period % (3600 * 24);
				$hours = JText::_("COM_DIGICOM_UNLIMITED");//(int ) ($left / 3600 );
				$mins = JText::_("COM_DIGICOM_UNLIMITED");//(int )(($left - $hours*3600)/60) ;//$left % (3600 );
			}
			$codelimit = ($this->item->codelimit != 0)?$this->item->codelimit:JText::_("COM_DIGICOM_INFINITE");
			$codeleft = ($this->item->codelimit != 0)?($this->item->codelimit - $this->item->used):JText::_("COM_DIGICOM_INFINITE");
			?>
			<table class="table" border="0">
				<tr>
					<td><h3><?php echo JText::_("COM_DIGICOM_DISCOUNT_REPORTS_TOTAL_USAGES")." <small>".$codelimit;?></small></h3></td>
					<td><h3><?php echo JText::_("COM_DIGICOM_DISCOUNT_REPORTS_REMAINING_USAGES")." <small>".$codeleft;?></small></h3></td>
					<td><h3><?php echo JText::_("COM_DIGICOM_DISCOUNT_REPORTS_TOTAL_USED")." <small>".$this->item->used;?></small></h3></td>
				</tr>
				<tr>
					<td><?php echo JText::_("COM_DIGICOM_DISCOUNT_REPORTS_TIME_UNTIL_EXPIRE") ." ". $days ." ". JText::_("COM_DIGICOM_DAYS"); ?></td>
					<td><?php echo $hours ." ". JText::_("COM_DIGICOM_HOURS"); ?></td>
					<td><?php echo $mins ." ". JText::_("COM_DIGICOM_MINUTES"); ?></td>
				</tr>

			</table>
		</fieldset>

		<?php
		echo JHtml::_('bootstrap.endTab');
		
		
		echo JHtml::_('bootstrap.endTabSet');
		?>
	</div>
	<input type="hidden" name="images" value="" />
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="discount" />
	<?php echo JHtml::_('form.token'); ?>
</form>
