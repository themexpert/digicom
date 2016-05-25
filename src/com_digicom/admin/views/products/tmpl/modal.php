<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();

if ($app->isSite())
{
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

require_once JPATH_ROOT . '/components/com_digicom/helpers/route.php';

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework', true);
JHtml::_('formbehavior.chosen', 'select');

$function  = $app->input->getCmd('function', 'jSelectProduct');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_digicom&view=products&layout=modal&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1');?>"
      method="post" name="adminForm" id="adminForm" class="form-inline">
	<?php
	// Search tools bar
	echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
	?>
	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_DIGICOM_PRICE', 'price', $listDirn, $listOrder); ?>
				</th>
				<th width="15%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th width="15%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'a.catid', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<?php if ($item->language && JLanguageMultilang::isEnabled())
			{
				$tag = strlen($item->language);
				if ($tag == 5)
				{
					$lang = substr($item->language, 0, 2);
				}
				elseif ($tag == 6)
				{
					$lang = substr($item->language, 0, 3);
				}
				else {
					$lang = "";
				}
			}
			elseif (!JLanguageMultilang::isEnabled())
			{
				$lang = "";
			}
			$price = DigiComHelperDigiCom::format_price($item->price, $this->configs->get('currency','USD'), true, $this->configs);
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>', '<?php echo $this->escape($item->catid); ?>', null, '<?php echo $this->escape(JRoute::_('index.php?option=com_digicom&view=product&task=product.edit&id='.$item->id)); ?>', '<?php echo $this->escape($lang); ?>', '<?php echo $this->escape($price); ?>');">
						<?php echo $this->escape($item->name); ?></a>
				</td>
				<td class="small">
					<?php echo $this->escape($price); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->category_title); ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
