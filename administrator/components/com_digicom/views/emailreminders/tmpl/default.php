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

$k = 0;
$n = count ($this->emails);
$page = $this->pagination;
$configs = $this->configs;

$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
?>
<form action="<?php echo JRoute::_('index.php?option=com_digicom&controller=emailreminders'); ?>" id="adminForm" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container" class="span12">
<?php endif;?>
		<div class="alert alert-info">
			<?php echo JText::_( "HEADER_EMAILS" ); ?>
		</div>
		
		<table class="adminlist table">

			<thead>
				<tr>
					<th width="5">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="20">
							<?php echo JText::_('VIEWPLAINID');?>
					</th>
					<th>
							<?php echo JText::_('VIEWPLAINNAME');?>
					</th>

					<th>
							<?php echo JText::_('VIEWPLAINTERMS');?>
					</th>

					<th>
							<?php echo JText::_("VIEWPLAINORDERING");?>
							<?php echo JHTML::_('grid.order',  $this->emails ); ?>
					</th>

					<th>
							<?php echo JText::_("VIEWPLAINPUBLISH");?>
					</th>

				</tr>
			</thead>

			<tbody>

			<?php
			if($n > 0):
				JHTML::_("behavior.tooltip");

				$ordering = true;

				for ($i = 0; $i < $n; $i++):
					$plain = $this->emails[$i];
					$id = $plain->id;
					$checked = JHTML::_('grid.id', $i, $id);
					$link = JRoute::_("index.php?option=com_digicom&controller=emailreminders&task=edit&cid[]=".$id);
					$published = JHTML::_('grid.published', $plain, $i );
					?>
				<tr class="row<?php echo $k;?>">

					<td>
								<?php echo $checked;?>
					</td>

					<td align="right"><?php echo $plain->id; //echo $i+1;?></td>

					<td>
						<a href="<?php echo $link;?>"><?php echo $plain->name;?></a>
					</td>

					<td>
						<a href="<?php echo $link;?>"><?php
							echo DigiComAdminHelper::getEmailReminderType($plain->type, $plain->calc, $plain->date_calc, $plain->period);
						?></a>
					</td>
					<td class="order">
						<span><?php echo $page->orderUpIcon( $i, true, 'orderup', 'Move Up', $ordering); ?></span>
						<span><?php echo $page->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', $ordering ); ?></span>
								<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $plain->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />
					</td>

					<td align="center">
						<a href="javascript: void(0);" onClick="return listItemTask('cb<?php  echo $i;?>','<?php  echo $plain->published ? "unpublish" : "publish";?>')">
						   <?php echo $published; ?>
						</a>
					</td>

				</tr>


					<?php
					$k = 1 - $k;
					endfor;
			else: ?>
				<tr>
					<td colspan="6">
						<?php echo JText::_('DIGICOM_NO_EMAIL_REMINDER_FOUND'); ?>
					</td>
				</tr>
				<?php
			endif;
					?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="9">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>

		</table>
		<div>
			<input type="hidden" name="option" value="com_digicom" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="controller" value="emailreminders" />
		</div>
	</div>
</form>