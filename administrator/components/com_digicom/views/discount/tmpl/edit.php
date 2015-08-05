<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHtml::_('jquery.framework');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
JHTML::_("behavior.calendar");
JHtml::_('behavior.keepalive');
jimport('joomla.html.pane');

$document = JFactory::getDocument();
$app = JFactory::getApplication();
$input = $app->input;
$input->set('layout', 'dgform');
$configs = $this->configs;
$nullDate = 0;
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
//	function submitbutton(pressbutton) {
//		submitform( pressbutton );
//	}
Joomla.submitbutton = function(task)
{
	if (task == 'discount.cancel' || document.formvalidator.isValid(document.id('adminForm')))
	{
		Joomla.submitform(task, document.getElementById('item-form'));
	}
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

						<?php echo $this->form->getControlGroup('title'); ?>

						<?php echo $this->form->getControlGroup('code'); ?>

						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('amount'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('amount'); ?>
								<?php echo $this->form->getInput('promotype'); ?>
							</div>
						</div>

						<?php echo $this->form->getControlGroup('codelimit'); ?>

						<?php echo $this->form->getControlGroup('codestart'); ?>

						<?php echo $this->form->getControlGroup('codeend'); ?>

						<?php echo $this->form->getControlGroup('published'); ?>


					</div>
					
			</div>

			<div class="span4 well">
				<div class="clearfix">
					<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_DISCOUNT_CODE_ENABLE_FOR_ALL_PRODUCTS_LABEL_DESC'); ?>" >
						<?php echo JText::_('COM_DIGICOM_DISCOUNT_CODE_ENABLE_FOR_ALL_PRODUCTS_LABEL');?>
					</label>
					<?php echo $this->form->getInput('discount_enable_range'); ?>
				</div>
				
				<div id="discount_enable_range_product"<?php echo (($this->item->discount_enable_range == '1' || $this->item->discount_enable_range === null) ? " class='hide'":"");?>>
				
					<!--<h3><?php echo JText::_( 'COM_DIGICOM_DISCOUNT_CODE_PRODUCT_RESTRICTION_TITLE' ); ?></h3>-->
					<br/>
					<table id="productincludes" class="table table-striped table-hover" id="productList">
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
					

					<div>
						<a class="btn btn-small btn-primary modal" title="Products" href="<?php echo $link; ?>" 
						rel="{handler: 'iframe', size: {x: 800, y: 500}}">
							<i class="icon-file-add"></i> 
							<?php echo JText::_('COM_DIGICOM_ADD_PRODUCT'); ?>
						</a>

					</div>

				</div>

				
			</div>

		</div>

		<!--
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
		-->
		<?php
		echo JHtml::_('bootstrap.endTab');
		
		
		echo JHtml::_('bootstrap.endTabSet');
		?>
	</div>
	<div class="validity">
		<?php echo $this->form->getInput('validfornew'); ?>
		<?php echo $this->form->getInput('validforrenewal'); ?>
	</div>
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="view" value="discount" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />

	<?php echo JHtml::_('form.token'); ?>
</form>

<?php 
	echo JHtml::_(
		'bootstrap.renderModal',
		'videoTutorialModal',
		array(
			'url' => 'https://www.youtube-nocookie.com/embed/UtDgs00sbhw?list=PL5eH3TQ0wUTZXKs632GyKMzGVkxdfPB4f&amp;showinfo=0',//&amp;autoplay=1
			'title' => JText::_('COM_DIGICOM_ABOUT_DISCOUNT_USE_VIDEO'),
			'height' => '400px',
			'width' => '1280'
		)
	); 
?>
<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
