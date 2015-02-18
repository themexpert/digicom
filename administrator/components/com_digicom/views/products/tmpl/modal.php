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
$function = JRequest::getCmd('function');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$k = 0;
$n = count ($this->prods);
$page = $this->pagination;
$configs = $this->configs;
$prc = JRequest::getCmd("prc", "-1");
$session = JFactory::getSession();
$search_session = $session->get('digicom.product.search');
$state_filter = JRequest::getVar("state_filter", "-1");

$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
$limistart = $this->pagination->limitstart;

?>
<form id="adminForm" action="index.php" name="adminForm" method="post" class="form-horizontal">

	<div id="editcell" >

		<table>
			<tr>
				<td width="30%">
					<input type="text" name="search" placeholder="<?php echo JText::_('DSSEARCH'); ?>" value="<?php echo $search_session; ?>" class="span6" />&nbsp;&nbsp;
					<input type="submit" name="submit_search" value="Go!" class="btn" />
				</td>
				<td nowrap="nowrap" width="70%" align="right">
					<?php echo $this->csel; ?>
				
					<select name="state_filter" onchange="document.adminForm.submit();" class="span3">
						<option value="-1" <?php if($state_filter == "-1"){echo 'selected="selected"'; } ?>><?php echo JText::_("DIGI_SELECT_STATE"); ?></option>
						<option value="1" <?php if($state_filter == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("HELPERPUBLISHED"); ?></option>
						<option value="0" <?php if($state_filter == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("HELPERUNPUBLICHED"); ?></option>
					</select>
				</td>
			</tr>
		</table>

		<br/>

		<table width="100%">
			<tr>
				<td class="header_zone">
					<?php
						echo JText::_("HEADER_PRODUCTS");
					?>
				</td>
			</tr>
			<tr>
				<td colspan="3" align="right">
					<a class="modal digi_video" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_digicom&controller=about&task=vimeo&id=38448917">
						<img src="<?php echo JURI::base(); ?>components/com_digicom/assets/images/icon_video.gif" class="video_img" />
						<?php echo JText::_("COM_DIGICOM_VIDEO_PROD_ADDPROD"); ?>
					</a>
				</td>
			</tr>
		</table>

		<table class="adminlist table table-striped">

			<thead>

				<tr>
					<th width="1%">
						<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
					</th>

					<th width="1%">
						<?php echo JText::_('VIEWPRODSKU');?>
					</th>

					<th>
						<?php echo JText::_('VIEWPRODNAME');?>
					</th>

					<th>
						<?php echo JText::_('VIEWPRODTYPE');?>
					</th>

					<th width="10%">
						<?php echo JText::_('PRODUCT_IS_VISIBLE');?>
					</th>
					<th width="10%">
						<?php echo JText::_('VIEWPRODPUBLISHING');?>
					</th>
					<th>
						<?php echo JText::_('VIEWPRODCATEGORY');?>
					</th>

					<th width="1%">
							<?php echo JText::_('VIEWPRODID');?>
					</th>
				</tr>

			</thead>

			<tbody>

			<?php
			JHTML::_("behavior.tooltip");
			$ordering = true;
			$cselected = "";
			$poz = $limistart + 1;
			if ($prc > 0) $cselected .= "&prc=".$prc;
			if ($state_filter != "-1") $cselected .= "&state_filter=".$state_filter;
			else $cselected = '';
			for ($i = 0; $i < $n; $i++):
				$prod = $this->prods[$i];
				$id = $prod->id;
				$checked = JHTML::_('grid.id', $i, $id);
				$link = JRoute::_("index.php?option=com_digicom&controller=products&task=edit&cid[]=".$id.$cselected);
				$published = JHTML::_('grid.published', $prod->published, $i);
				DigiComAdminHelper::publishAndExpiryHelper($img, $alt, $times, $status, $prod->publish_up, $prod->publish_down, $prod->published, $this->configs);
				?>
				<tr class="row<?php echo (string)$k; ?>">
					<td><?php echo $checked; ?></td>
					<td align="center">
					</td>
					<td>
						<a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $id; ?>', '<?php echo $this->escape(addslashes($prod->name)); ?>', '<?php echo $this->escape($prod->cats[0]->id); ?>', '<?php echo $this->escape($prod->cats[0]->name); ?>', '', '', null);" ><?php echo $prod->name;?></a>
					</td>
					<td>
						<?php
							switch ( $prod->product_type )
								{
									case 'bundle':
										echo JText::_('VIEWPRODPRODTYPEPAK');
										break;
									case 'reguler':
									default:
										echo JText::_('VIEWPRODPRODTYPEDNR');
										break;
								}
						?>
					</td>
					<td align="center">
						<?php echo ($prod->hide_public ? '<span style="color:#ff0000;">' . JText::_("DSNO") . '</span>' : JText::_("DSYES")); ?>
					</td>
					<td align="center">
						<?php echo $published; ?>
					</td>
					<td align="center">
								<?php foreach( $prod->cats as $j => $z) {
									$clink = JRoute::_("index.php?option=com_digicom&controller=categories&task=edit&cid[]=".$z->id);
									echo '<a href="'.$clink.'" >'.$z->name.'</a><br />';
								}
								?>
					</td>
					<td align="center">
						<?php echo $id; ?>
					</td>
				</tr>
						<?php
						$k = 1 - $k;
					endfor;
					?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="8">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>

	</div>

	<input type="hidden" name="prc" value="<?php echo $this->prc; ?>" />
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="Products" />

</form>
