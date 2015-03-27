<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$field = JRequest::getCmd('field');
$function  = 'jSelectDigiUser_' . $field;
$page = $this->pagination;
$prc = JRequest::getCmd("prc", "-1");
$session = JFactory::getSession();
$search_session = $session->get('digicom.product.search');
$state_filter = JRequest::getVar("state_filter", "-1");

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
$k = 0;
$n = count ($this->custs);

?>
<form id="adminForm" action="index.php" name="adminForm" method="post">

	<div class="js-stools">
		<div class="clearfix">
			<div class="btn-wrapper input-append">
				<input type="text" name="keyword" placeholder="<?php echo JText::_('DSSEARCH'); ?>" value="<?php echo (strlen(trim($this->keyword)) > 0 ?$this->keyword:"");?>" class="span6" />		
				<button type="submit" class="btn hasTooltip" title="" data-original-title="Search">
					<i class="icon-search"></i>
				</button>
			</div>

		</div>
	</div>
	<br>
	<div class="alert alert-info">
		<?php echo JText::_("HEADER_CUSTOMERS"); ?>
	</div>
	<div id="editcell" >
		
		<table class="adminlist table">
			<thead>
				<tr>
					<th width="5">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
						<th width="20">
						<?php echo JText::_('VIEWCUSTOMERID');?>
					</th>
					<th>
						<?php echo JText::_('VIEWCUSTOMERNAME');?>
					</th>
					<th>
						<?php echo JText::_('VIEWCUSTOMERUSER');?>
					</th>
				</tr>
			</thead>
			
			<tbody>
			<?php 
				//var_dump($this->custs);
				if ($n > 0): ?>		
				<?php 
				for ($i = 0; $i < $n; $i++):
					$cust = $this->custs[$i];
					$id = $cust->id;
					$checked = JHTML::_('grid.id', $i, $id);
					$link = JRoute::_("index.php?option=com_digicom&controller=customers&task=edit&cid[]=".$id.(strlen(trim($this->keyword))>0?"&keyword=".$this->keyword:""));
					$ulink = JRoute::_("index.php?option=com_users&view=user&layout=edit&id=".$id);
				?>
				<tr class="row<?php echo $k;?>"> 
						<td>
								<?php echo $checked;?>
					</td>

						<td>
								<?php echo $id;?>
					</td>
						<td>
<a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $id; ?>', '<?php echo $this->escape(addslashes($cust->firstname." ".$cust->lastname)); ?>');" >
	<?php echo $cust->firstname." ".$cust->lastname;?>
</a>


						</td>
						<td>
								<?php echo $cust->username;?>
					</td>

				</tr>


				<?php 
				$k = 1 - $k;
				endfor;
				?>

				<tr>
					<td colspan="4">
						<?php
						$total_pag = $this->pagination->get("pages.total", "0");
						$pag_start = $this->pagination->get("pages.start", "1");
						if($total_pag > ($pag_start + 9)){
							$this->pagination->set("pages.stop", ($pag_start + 9));
						}
						else{
							$this->pagination->set("pages.stop", $total_pag);
						}
						echo $this->pagination->getListFooter();
						?>
					</td>
				</tr>
			<?php else: ?>
				<tr>
					<td colspan="4">
						<p class="alert alert-warning"><?php 	echo JText::_('VIEWCUSTOMERNOCUST'); ?></p>
					</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>

	</div>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="customers" />
	<input type="hidden" name="layout" value="modal" />
	<input type="hidden" name="tmpl" value="component" />
</form>
