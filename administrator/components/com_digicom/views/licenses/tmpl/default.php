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
JHtml::_('formbehavior.chosen', 'select');
global $Itemid;

$k = 0;
$n = count ($this->licenses);
$configs = $this->configs;
$status = $this->status;

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>

<script type="text/javascript">
	function changeCategory(category_id){
		var url = "index.php?option=com_digicom&controller=licenses&task=changeCategory&category_id="+category_id+"&format=raw&tmpl=component";
		var req = new Request.HTML({
			method: 'get',
			url: url,
			data: { 'do' : '1' },
			onComplete: function(response){
				document.getElementById("all_products_div").empty().adopt(response);
			}
		}).send();
	}
</script>

<?php

	if ($n < 1): 
?>

<form id="adminForm" action="index.php" name="adminForm" method="post">

	<div style="text-align:right">
	   <div style="padding-bottom:0.5em">
			<?php //echo $this->psel;?>
			<?php echo $this->cathtml; ?>
		</div>
		<div style="padding-bottom:0.5em" id="all_products_div">
			<?php echo $this->all_prod_select; ?>
		</div>
		<div style="padding-bottom:0.5em">
			<?php echo JText::_("DSKEYWORD");?>:
			<input type="text" name="keyword" value="<?php echo (strlen(trim($this->keyword)) > 0 ?$this->keyword:"");?>" />
			<input type="submit" class="btn" name="go" value="<?php echo JText::_("DSSEARCH");?>" />
		</div>
		<div style="padding-bottom:0.5em">
			<?php echo JText::_("VIEWORDERSSTATUS");?>:
			<select name="status" onchange="document.adminForm.task.value=''; document.adminForm.submit();">
				<option value="-1" <?php if($status == "-1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("DIGI_SELECT"); ?></option>
				<option value="0" <?php if($status == "0"){ echo 'selected="selected"';} ?> ><?php echo JText::_("HELPERUNPUBLISHED"); ?></option>
				<option value="1" <?php if($status == "1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("PLAINPUBLISHED"); ?></option>
			</select>
		</div>
		<div style="padding-bottom:0.5em">
			<?php echo JText::_("LICENSE_CANCELLED");?>:
			<select name="cancelled" onchange="document.adminForm.task.value=''; document.adminForm.submit();">
				<option value="0" <?php if($this->cancelled == "0"){ echo 'selected="selected"';} ?> ><?php echo JText::_("DIGI_SELECT"); ?></option>
				<option value="1" <?php if($this->cancelled == "1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("LICENSE_CHARGEBACKS"); ?></option>
				<option value="2" <?php if($this->cancelled == "2"){ echo 'selected="selected"';} ?> ><?php echo JText::_("LICENSE_REFUNDS"); ?></option>
			</select>
		</div>
	</div>

	<table>
		<tr>
			<td class="header_zone" colspan="4">
				<?php
					echo JText::_("HEADER_LICENSES");
				?>
			</td>
		</tr>
		<tr>
			<td align="right">
				<a class="modal digi_video" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_digicom&controller=about&task=vimeo&id=38437538">
					<img src="<?php echo JURI::base(); ?>components/com_digicom/assets/images/icon_video.gif" class="video_img" />
					<?php echo JText::_("COM_DIGICOM_VIDEO_LICENSES_MANAGER"); ?>				  
				</a>
			</td>
		</tr>
	</table>

	<table class="adminlist table">
	<thead>

		<tr>
			<th width="5">
				<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
			</th>
				<th width="20">
				<?php echo JText::_('VIEWLICID');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICPROD');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICCUSTOMER');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICUSER');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICPLAN');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICDOMAINS');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICDOWNLOAD');?>
			</th>
			<th>
				<?php echo JText::_('VIEWORDERSDATE');?>
			</th>
			<th><?php echo JText::_("VIEWLICPUBLISH");?>
			</th>
		</tr>
	</thead>

	</table>

	  	<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="Licenses" />
	</form>

<?php

	else:

?>

<form id="adminForm" action="index.php" name="adminForm" method="post">

	<div style="text-align:right">
		<div style="padding-bottom:0.5em">
			<?php //echo $this->psel;?>
			<?php echo $this->cathtml; ?>
		</div>
		<div style="padding-bottom:0.5em" id="all_products_div">
			<?php echo $this->all_prod_select; ?>
		</div>
		<div style="padding-bottom:0.5em">
			<?php echo JText::_("DSKEYWORD");?>:
			<input type="text" name="keyword" value="<?php echo (strlen(trim($this->keyword)) > 0 ?$this->keyword:"");?>" />
			<input type="button" name="go" class="btn"  value="<?php echo JText::_("DSSEARCH");?>" onClick="document.adminForm.task.value=''; document.adminForm.submit( );"/>
		</div>
		<div style="padding-bottom:0.5em">
			<?php echo JText::_("VIEWORDERSSTATUS");?>:
			<select name="status" onchange="document.adminForm.task.value=''; document.adminForm.submit();">
				<option value="-1" <?php if($status == "-1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("DIGI_SELECT"); ?></option>
				<option value="0" <?php if($status == "0"){ echo 'selected="selected"';} ?> ><?php echo JText::_("HELPERUNPUBLISHED"); ?></option>
				<option value="1" <?php if($status == "1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("PLAINPUBLISHED"); ?></option>
			</select>
		</div>
		<div style="padding-bottom:0.5em">
			<?php echo JText::_("LICENSE_CANCELLED");?>:
			<select name="cancelled" onchange="document.adminForm.task.value=''; document.adminForm.submit();">
				<option value="0" <?php if($this->cancelled == "0"){ echo 'selected="selected"';} ?> ><?php echo JText::_("DIGI_SELECT"); ?></option>
				<option value="1" <?php if($this->cancelled == "1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("LICENSE_CHARGEBACKS"); ?></option>
				<option value="2" <?php if($this->cancelled == "2"){ echo 'selected="selected"';} ?> ><?php echo JText::_("LICENSE_REFUNDS"); ?></option>
			</select>
		</div>
	</div>

	<div class="alert alert-info">
		<?php
					echo JText::_("HEADER_LICENSES");
				?>
	</div>

	<table class="adminlist table">
	<thead>
		<tr>
			<th width="5">
				<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
			</th>
			<th width="20">
				<?php echo JText::_('VIEWLICID');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICPROD');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICCUSTOMER');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICUSER');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICPLAN');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICDOMAINS');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICDOWNLOAD');?>
			</th>
			<th>
				<?php echo JText::_('VIEWORDERSDATE');?>
			</th>
			<th>
				<?php echo JText::_('VIEWLICLICEXPIRED');?>
			</th>
			<th>
				<?php echo JText::_('LICENSE_CANCELLED');?>
			</th>
			<th>
				<?php echo JText::_("VIEWLICPUBLISH");?>
			</th>
		</tr>
	</thead>

	<tbody>
	<?php
		for ($i = 0; $i < $n; $i++):
			$license = $this->licenses[$i];
			$id = $license->id;
			$checked = JHTML::_('grid.id', $i, $id);
			$on_link = "";
			if(trim($this->keyword) != ""){
				$on_link .= "&keyword=".trim($this->keyword);
			}
			if(trim($this->status) != ""){
				$on_link .= "&status=".trim($this->status);
			}
			$link = JRoute::_("index.php?option=com_digicom&controller=licenses&task=edit&cid[]=".$id.$on_link);
			$prodlink = JRoute::_("index.php?option=com_digicom&controller=products&task=edit&cid[]=".$license->productid);
			$customerlink = JRoute::_("index.php?option=com_digicom&controller=customers&task=edit&cid[]=".$license->userid);
			$ulink = JRoute::_("index.php?option=com_users&view=user&layout=edit&id=".$license->userid);

			$published = JHTML::_('grid.published', $license, $i );
	?>
		<tr class="row<?php echo $k;?>">
				<td>
						<?php echo $checked;?>
					<input type="hidden" id="licenses<?php echo $license->id;?>" name="licenses<?php echo $license->id;?>" value="<?php echo $this->models->getLicensesInOrder($license->orderid);?>" />
			</td>

				<td align="center">
						<a href="<?php echo $link;?>" ><?php echo $license->licenseid;?></a>
			</td>

				<td>
						<a href="<?php echo $prodlink; ?>" ><?php echo $license->productname;?></a>
			</td>
			<td align="center">
						<a href="<?php echo $customerlink;?>" ><?php echo $license->firstname." ".$license->lastname;?></a>

			</td>

				<td align="center">
						<a href="<?php echo $ulink;?>" ><?php echo $license->username?></a>
			</td>


			<td nowrap="nowrap"><?php
					if ($license->duration_count != -1) {
						echo DigiComAdminHelper::getDurationType($license->duration_count, $license->duration_type);
					} else {
						echo JText::_("DS_UNLIMITED");
					}

	?></td>
				<td>

	<?php

			$regdomain_link = JRoute::_("index.php?option=com_digicom&controller=licenses&task=register&cid[]=".$id."&Itemid=".$Itemid);
			$regdevdomain_link = JRoute::_("index.php?option=com_digicom&controller=licenses&task=devregister&cid[]=".$id."&Itemid=".$Itemid);
				$download_link = JRoute::_("index.php?option=com_digicom&controller=licenses&task=download&cid[]=".$id."&Itemid=".$Itemid);
			$devdownload_link = JRoute::_("index.php?option=com_digicom&controller=licenses&task=devdownload&cid[]=".$id."&Itemid=".$Itemid);

			$regdomain_link = '<a href="'.$regdomain_link.'">'.($license->domain?$license->domain:(JText::_("DSREGDOMAIN"))).'</a>';
			$regdevdomain_link = '<a href="'.$regdevdomain_link.'">'.($license->dev_domain?$license->dev_domain:(JText::_("DSREGDEVDOMAIN"))).'</a>';
				$download_link = '<a href="'.$download_link.'">'.(JText::_("DSDOWNLOAD")).'</a>';
			$devdownload_link = '<a href="'.$devdownload_link.'">'.(JText::_("DSDEVDOWNLOAD")).'</a>';

	?>
	<?php

		if ( $license->domainrequired == 4) {

		} else if  ($license->domainrequired == 2 || $license->domainrequired == 3) {
			echo JText::_("DSPRODUCT_NOT_DOWNLOADABLE");
	   } else if ($license->domainrequired == 0) {
				echo JText::_("DSNO_DOMAIN_REQUIRED")."" ;
		} else if ($license->domainrequired == 1 && !$license->domain) {
				echo $regdomain_link."<br />";
		} else if ($license->domainrequired == 1 && $license->domain) {
				echo JText::_("DSLIVE").": ".$regdomain_link."";
		} else {
		if ( $license->domain ){
				echo JText::_("DSLIVE").": ".$regdomain_link."<br>";
			}else if (trim($license->main_zip_file) == '' && $license->domainrequired == 0) {
				echo JText::_("NO_DOMAIN_REQUIRED")."<br />" ;
			} else  {
				echo $regdomain_link."<br />";
			}
			if ( $license->dev_domain ){
				echo JText::_("DSDEV").": ".$regdevdomain_link;
		}else if ($license->domainrequired == 0) {
				echo  JText::_("DSNO_DOMAIN_REQUIRED");
			} else {
				echo $regdevdomain_link."";
			}
		}
	?>
			</td><td align="center">
	<?php

			if  ($license->domainrequired == 2 || $license->domainrequired == 3) {
			echo JText::_("DSPRODUCT_NOT_DOWNLOADABLE");
		   } else if ($license->domainrequired == 0) {
			echo $download_link;
		} else if ($license->domainrequired == 1 && $license->domain) {
			echo $download_link;
		}else if ($license->domainrequired == 1 && !$license->domain) {

		} else {
			if ( $license->domain || $license->domainrequired == 0) {
				echo $download_link;
			} else if ( ($license->domain) || ($license->domain && $license->domainrequired == 1 )){
				echo $download_link;
			}

			if ( $license->dev_domain || $license->domainrequired == 0) {
				echo "<br />".$devdownload_link;
			} else if ($license->dev_domain || ($license->dev_domain && $license->domainrequired == 1)) {
				echo "<br />".$devdownload_link;
			}
		}

	?>
			</td>

			<td align="center">
				<?php
					if($configs->get('hour24format',1) == "0"){
						$int_date = strtotime($license->purchase_date);
						$date_format = date(($configs->get('time_format','DD-MM-YYYY'))." h:i:s A", $int_date);
						echo $date_format;
					}
					else{
						$int_date = strtotime($license->purchase_date);
						$date_format = date(($configs->get('time_format','DD-MM-YYYY'))." h:i:s", $int_date);
						echo $date_format;
					}
				?>
			</td>
			<td align="center">
				<?php
					if ($license->expires == '0000-00-00 00:00:00')
					{
						echo JText::_("HELPERNEVEREXP");
					}
					else
					{
						if($configs->get('hour24format',1) == "0"){
							$int_date = strtotime($license->expires);
							$date_format = date(($configs->get('time_format','DD-MM-YYYY'))." h:i:s A", $int_date);
							echo $date_format;
						}
						else{
							$int_date = strtotime($license->expires);
							$date_format = date(($configs->get('time_format','DD-MM-YYYY'))." h:i:s", $int_date);
							echo $date_format;
						}
					}

				?>
			</td>
			<td align="center">
				<?php
				if ($license->cancelled == 1)
				{
					// Chargeback
					echo '<span style="color:#ff0000;">' . JText::_("LICENSE_CHARGEBACK") . ' - ' . DigiComAdminHelper::format_price($license->cancelled_amount, $configs->get('currency','USD'), true, $configs) . '</span>';
				}
				elseif ($license->cancelled == 2)
				{
					// Refund
					echo '<span style="color:#ff0000;">' . JText::_("LICENSE_REFUND") . ' - ' . DigiComAdminHelper::format_price($license->cancelled_amount, $configs->get('currency','USD'), true, $configs) . '</span>';
				}
				else
				{
					echo '-';
				}
				?>
			</td>
			<td align="center">
				<?php echo $published;?>
			</td>
		</tr>


	<?php
			$k = 1 - $k;
		endfor;

	?>

	</tbody>
		<tfoot>
			<tr>
				<td colspan="12">
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
		</tfoot>
	</table>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="Licenses" />
	<?php
		$startdate = JRequest::getVar("startdate", "");
		if(trim($startdate) != ""){
	?>
			<input type="hidden" name="startdate" value="<?php echo $startdate; ?>" />
	<?php
		}
	?>
	<?php
		$enddate = JRequest::getVar("enddate", "");
		if(trim($enddate) != ""){
	?>
			<input type="hidden" name="enddate" value="<?php echo $enddate; ?>" />
	<?php
		}
	?>

</form>

<?php
	endif;

?>

<script language="javascript" type="text/javascript">
Joomla.submitbutton = function (pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'remove')
	{
		if (confirm("<?php echo JText::_("CONFIRM_LICENSE_DELETE");?>"))
		{
			Joomla.submitform(pressbutton);
		}
		return;
	}

	Joomla.submitform(pressbutton);
}
</script>