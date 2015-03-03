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

JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$invisible = 'style="display:none;"';

$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

$k = 0;
$n = count( $this->orders );
$configs = $this->configs;
$f = $configs->get('time_format','DD-MM-YYYY');
$f = str_replace( "-", "-%", $f );
$f = "%" . $f;
?>
<script language="javascript" type="text/javascript">
Joomla.submitbutton = function (pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'remove')
	{
		if (confirm("<?php echo JText::_("CONFIRM_ORDER_DELETE");?>"))
		{
			Joomla.submitform(pressbutton);
		}
		return;
	}

	Joomla.submitform(pressbutton);
}
</script>

<?php if (!empty( $this->sidebar)) : ?>
<div id="j-sidebar-container" class="">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="">
<?php else : ?>
<div id="j-main-container" class="">
<?php endif;?>
	<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_digicom&controller=orders'); ?>" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal">
		<div class="js-stools">
			<div class="clearfix">
				<div class="btn-wrapper input-append">
					<input type="text" id="filter_search" name="keyword" placeholder="<?php echo JText::_('DSKEYWORD'); ?>" value="<?php echo (strlen( trim( $this->keyword ) ) > 0 ? $this->keyword : ""); ?>" class="input-medium" />		
					<button type="submit" class="btn hasTooltip" title="" data-original-title="Search">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip js-stools-btn-clear" onclick="document.id('filter_search').value='';this.form.submit();">
						<i class="icon-remove"></i>	
					</button>
				</div>
				<div class="btn-wrapper input-append input-prepend pull-right">
					<label class="add-on"><?php echo JText::_( "DSFROM" ); ?>:</label>
					<?php echo JHTML::_( "calendar", $this->startdate > 0 ? date( $configs->get('time_format','DD-MM-YYYY'), $this->startdate ) : "", 'startdate', 'startdate', $f, array('class'=>'input-medium'), array('class'=>'span2'), array('class'=>'span2')); ?>&nbsp;
				
					<label class="add-on"><?php echo JText::_( "DSTO" ); ?>:</label>
					<?php echo JHTML::_( "calendar", $this->enddate > 0 ? date( $configs->get('time_format','DD-MM-YYYY'), $this->enddate ) : "", 'enddate', 'enddate', $f , array('class'=>'input-medium')); ?>

					<input type="submit" name="go" value="<?php echo JText::_( "DSGO" ); ?>" class="btn" />
					<button type="button" class="btn hasTooltip js-stools-btn-clear" onclick="document.id('startdate').value='';document.id('enddate').value='';this.form.submit();">
						<i class="icon-remove"></i>	
					</button>
				</div>

			</div>
		</div>
		<br>


		<div class="alert alert-info">
			<?php echo JText::_("HEADER_ORDERS"); ?>
		</div>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="5">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="20">
						<?php echo JText::_( 'VIEWORDERSID' ); ?>
					</th>

					<th>
						<?php echo JText::_( 'VIEWORDERSDATE' ); ?>
					</th>
					<th  <?php if ( $configs->get('showolics',0) == 0 ) echo $invisible; ?>>
						<?php echo JText::_( 'VIEWORDERSNOL' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'VIEWORDERSPRICE' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'VIEWORDERSUSERNAME' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'VIEWORDERSCUST' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'VIEWORDERSSTATUS' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'VIEWORDERSPAYMETHOD' ); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if($n > 0):  ?>
				<?php
				$z = 0;
				for ( $i = 0; $i < $n; $i++ ):
					++$z;
					$order =  $this->orders[$i];

					$id = $order->id;
					$checked = JHTML::_( 'grid.id', $i, $id );
					$link = JRoute::_( "index.php?option=com_digicom&controller=licenses&task=list&oid[]=" . $id );
					$olink = JRoute::_( "index.php?option=com_digicom&controller=orders&task=show&cid[]=" . $id );
					$customerlink = JRoute::_( "index.php?option=com_digicom&controller=customers&task=edit&cid[]=" . $order->userid );
					$order->published = 1;
					$published = JHTML::_( 'grid.published', $order, $i );
					$orderstatuslink = JRoute::_( "index.php?option=com_digicom&controller=orders&task=cycleStatus&cid[]=" . $id );
					$userlink = "index.php?option=com_users&view=users&filter_search=".$order->username;

				?>
					<tr class="row<?php echo $k; ?>">
						<td align="center">
							<?php echo $checked; ?>
						</td>
						<td align="center">
							<a href="<?php echo $olink; ?>"><?php echo $id; ?></a>
						</td>
						<td align="center">
							<?php echo date( $configs->get('time_format','DD-MM-YYYY'), $order->order_date ); ?>
						</td>
						<td align="center" <?php if ( $configs->get('showolics',0) == 0 )
								echo $invisible; ?>>
							<a href="<?php echo $link; ?>" ><?php echo $order->licensenum; ?></a>
						</td>
						<td align="center">
							<?php 
								
								if ($order->amount_paid == "-1") $order->amount_paid = $order->amount;
								//$refunds = DigiComAdminModelOrder::getRefunds($order->id);
								//$chargebacks = DigiComAdminModelOrder::getChargebacks($order->id);
								//$order->amount_paid = $order->amount_paid - $refunds - $chargebacks;
								$order->amount_paid = $order->amount_paid;
								echo DigiComAdminHelper::format_price($order->amount_paid, $configs->get('currency','USD'), true, $configs); 
								
							?>
						</td>
						<td align="center">
							<?php echo ($order->username); ?>
						</td>
						<td align="center">
							<a href="<?php echo $customerlink; ?>" ><?php echo ($order->firstname . " " . $order->lastname); ?></a>
						</td>
						<td align="center">
							<?php
								$a_style = "";
								if($order->status == "Pending"){
									$a_style = 'style="color:red;"';
								}
							?>
							<a href="<?php echo $orderstatuslink; ?>" <?php echo $a_style; ?> ><?php echo (trim( $order->status ) != "in_progres" ? $order->status : "Active"); ?></a>
						</td>
						<td align="center">
							<?php echo $order->processor; ?>
						</td>
						
					</tr>
					<?php
					$k = 1 - $k;
				endfor;
					?>
				<?php else: ?>
					<tr>
						<td colspan="9">
							<?php echo  JText::_('COM_DIGICOM_NO_ORDER_FOUND'); ?>
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="9">
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
		<input type="hidden" name="controller" value="orders" />
	</form>
</div>