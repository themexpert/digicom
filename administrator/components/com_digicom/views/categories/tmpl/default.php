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

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

$k = 0;
$n = count ($this->cats);
?>
<form action="<?php echo JRoute::_('index.php?option=com_digicom&controller=categories'); ?>" id="adminForm" method="post" name="adminForm" class="form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
<?php else : ?>
	<div id="j-main-container" class="">
<?php endif;?>

		<div class="alert alert-info"> <?php echo JText::_("HEADER_CATEGORIES"); ?> </div>

		<div id="editcell">
			<table class="adminlist table table-striped table-hover">
				<thead>
					<tr>
						<th width="5">
							<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
						</th>
							<th width="20">
							<?php echo JText::_('VIEWCATEGORYID');?>
						</th>
						<th class="title">
							<?php echo JText::_('VIEWCATEGORYNAME');?>
						</th>
						<th width="10%">
							<?php echo JHTML::_('grid.order',  $this->cats ); ?>
						</th>
						<th width="5%">
							<?php echo JText::_('VIEWCATEGORYPUBLISHING');?>
						</th>
					</tr>
				</thead>

				<tbody>
			<?php 
			if ($n):
				$z = 0;
				$ordering = true;
				foreach ($this->cats as $i => $v):

					$cat = $this->cats[$i];
					$id = $cat->id;
					$checked = JHTML::_('grid.id', $z, $id);
					$link = JRoute::_("index.php?option=com_digicom&controller=categories&task=edit&cid[]=".$id);
					$published = JHTML::_('grid.published', $cat, $z );
			?>
				<tr class="row<?php echo $k;?>"> 
						<td>
								<?php echo $checked;?>
					</td>

						<td>
								<?php echo $id;?>
					</td>
						<td>
								<a href="<?php echo $link;?>" >
									<?php
										//echo str_repeat('<span class="gi">|&mdash;</span>', $cat->level).$cat->name;
										echo $cat->treename;
									?>
								</a>
					</td>
					<td class="order">
						<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $cat->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />
					</td>
					<td align="center">
								<?php echo $published;?>
					</td>

				</tr>


			<?php 
					$z++;
					$k = 1 - $k;
				endforeach;
			endif;
			?>
				</tbody>

				<tfoot>
					<tr>
						<td colspan="5">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>

			</table>

		</div>
	</div>
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="Categories" />
</form>