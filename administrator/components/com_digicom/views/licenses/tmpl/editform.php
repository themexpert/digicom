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

JHTML::_('behavior.modal');
JHtml::_('behavior.tooltip');

$license = $this->license;

$configs = $this->configs;
$plugin_handler = $this->plugin_handler;
$currency_options = $this->currency_options;
$lists = $this->lists;
$optlen = $this->optlen;
$totalfields = $this->totalfields;
$nullDate = 0;

$my = JFactory::getUser();

$cid = JRequest::getVar("cid", array(), "array");
$cid = intval($cid["0"]);

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>

<?php $licexpired = $date = '0000-00-00 00:00:00'; ?>

<script language="javascript" type="text/javascript">
	/* Date format */

	// Simulates PHP's date function
	Date.prototype.format = function(format) {
		var returnStr = '';
		var replace = Date.replaceChars;
		for (var i = 0; i < format.length; i++) {
			var curChar = format.charAt(i);
			if (replace[curChar]) {
				returnStr += replace[curChar].call(this);
			} else {
				returnStr += curChar;
			}
		}
		return returnStr;
	};
	Date.replaceChars = {
		shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
		longMonths: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
		shortDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
		longDays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],

		// Day
		d: function() { return (this.getDate() < 10 ? '0' : '') + this.getDate(); },
		D: function() { return Date.replaceChars.shortDays[this.getDay()]; },
		j: function() { return this.getDate(); },
		l: function() { return Date.replaceChars.longDays[this.getDay()]; },
		N: function() { return this.getDay() + 1; },
		S: function() { return (this.getDate() % 10 == 1 && this.getDate() != 11 ? 'st' : (this.getDate() % 10 == 2 && this.getDate() != 12 ? 'nd' : (this.getDate() % 10 == 3 && this.getDate() != 13 ? 'rd' : 'th'))); },
		w: function() { return this.getDay(); },
		z: function() { return "Not Yet Supported"; },
		// Week
		W: function() { return "Not Yet Supported"; },
		// Month
		F: function() { return Date.replaceChars.longMonths[this.getMonth()]; },
		m: function() { return (this.getMonth() < 9 ? '0' : '') + (this.getMonth() + 1); },
		M: function() { return Date.replaceChars.shortMonths[this.getMonth()]; },
		n: function() { return this.getMonth() + 1; },
		t: function() { return "Not Yet Supported"; },
		// Year
		L: function() { return (((this.getFullYear()%4==0)&&(this.getFullYear()%100 != 0)) || (this.getFullYear()%400==0)) ? '1' : '0'; },
		o: function() { return "Not Supported"; },
		Y: function() { return this.getFullYear(); },
		y: function() { return ('' + this.getFullYear()).substr(2); },
		// Time
		a: function() { return this.getHours() < 12 ? 'am' : 'pm'; },
		A: function() { return this.getHours() < 12 ? 'AM' : 'PM'; },
		B: function() { return "Not Yet Supported"; },
		g: function() { return this.getHours() % 12 || 12; },
		G: function() { return this.getHours(); },
		h: function() { return ((this.getHours() % 12 || 12) < 10 ? '0' : '') + (this.getHours() % 12 || 12); },
		H: function() { return (this.getHours() < 10 ? '0' : '') + this.getHours(); },
		i: function() { return (this.getMinutes() < 10 ? '0' : '') + this.getMinutes(); },
		s: function() { return (this.getSeconds() < 10 ? '0' : '') + this.getSeconds(); },
		// Timezone
		e: function() { return "Not Yet Supported"; },
		I: function() { return "Not Supported"; },
		O: function() { return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + '00'; },
		P: function() { return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + ':' + (Math.abs(this.getTimezoneOffset() % 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() % 60)); },
		T: function() { var m = this.getMonth(); this.setMonth(0); var result = this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/, '$1'); this.setMonth(m); return result;},
		Z: function() { return -this.getTimezoneOffset() * 60; },
		// Full Date/Time
		c: function() { return this.format("Y-m-d") + "T" + this.format("H:i:sP"); },
		r: function() { return this.toString(); },
		U: function() { return this.getTime() / 1000; }
	};

	/* End / Date format */

	function submitbutton(pressbutton) {
		submitform( pressbutton );
	}

	function createplain() {

		var dstr = document.createElement('tr');

		var dstdzero = document.createElement('td');
		dstr.appendChild(dstdzero);

		var max = 999999;
		var id= Math.floor(Math.random()*max+1);

		var dstdamount = document.createElement('td');
		dstdamount.innerHTML = '<input type="text" class="text_area inputbox" name="payments[]" value="" size="30"/>';
		dstr.appendChild(dstdamount);

		var dstddate = document.createElement('td');
		//dstddate.innerHTML = '<?php //echo JHTML::calendar( $date, 'expire_dates[]', 'expire_dates[]', '%Y-%m-%d', 'size="30" class="inputbox" size="1""' ); ?>';

		var current_date = new Date();
		dstddate.innerHTML = '<input type="text" name="payments_dates[]" id="payments_dates[]" value="'+current_date.format('y-m-d H:i:s')+'" size="30" class="inputbox" size="1"" /><img class="calendar" src="/j1515/templates/system/images/calendar.png" alt="calendar" id="expire_dates[]_img" />';
		dstr.appendChild(dstddate);

		var dstdby = document.createElement('td');
		dstdby.innerHTML = "<?php echo $my->username; ?>";
		dstr.appendChild(dstdby);

		var tableplain = document.getElementById('tableplain');

		tableplain.appendChild(dstr);

		//window.parent.document.getElementById('sbox-window').close();
		window.parent.SqueezeBox.close();

	}

	function digiEscapeHtml(unsafe) {
		return unsafe
			.replace("&", "&")
			.replace("<", "<")
			.replace(">", ">")
			.replace("\"", "\"")
			.replace("'", "'");
	}


	function addNote(area_id, gen_number){
		var text = document.getElementById(area_id).value;
		var licenseId = <?php echo $cid; ?>;
		id_expire = "expire_"+gen_number;
		var expire = document.getElementById(id_expire).value;
		url = 'components/com_digicom/assets/js/functions.php?task=addnote&licid='+licenseId+'&text='+digiEscapeHtml(text)+'&expire='+expire;
		var req = new Request.HTML({
			method: 'get',
			url: url,
			data: { 'do' : '1' },
			//update: $('productincludes'),
			onComplete: function(response){
				td_id = "td_note_"+gen_number;
				delete_id = "td_delete_note_"+gen_number;
				document.getElementById(td_id).innerHTML = text;
				document.getElementById(delete_id).innerHTML = '<a href="index.php?option=com_digicom&amp;controller=licenses&amp;task=deletenote&amp;cid[]='+licenseId+'&amp;note='+response+'">Delete</a>';
			}
		}).send();
	}

	function createnote() {

		var rNumber =  Math.floor(Math.random()*(999-100))+100;

		var dstr = document.createElement('tr');

		var dstdzero = document.createElement('td');
		dstr.appendChild(dstdzero);

		var dstdnote = document.createElement('td');
		dstdnote.setAttribute("id", "td_note_"+rNumber);
		dstdnote.innerHTML = '<textarea name="notes[]" id="note_area_'+rNumber+'"></textarea><br/><input type="button" name="save_'+rNumber+'" onclick="javascript:addNote(\'note_area_'+rNumber+'\', \''+rNumber+'\')" value="<?php echo JText::_("DSSAVE"); ?>">';
		dstr.appendChild(dstdnote);

		var dstddate = document.createElement('td');
	   
		var current_date = new Date();
		dstddate.innerHTML = '<input type="text" name="expire[]" id="expire_'+rNumber+'" value="'+current_date.format('Y-m-d H:i:s')+'" size="30" class="inputbox" size="1"" /><img class="calendar" src="<?php echo JURI::root(); ?>/templates/system/images/calendar.png" alt="calendar" id="expire_'+rNumber+'_img" />';		dstr.appendChild(dstddate);

		var dstdby = document.createElement('td');
		dstdby.innerHTML = "<?php echo $my->username; ?>";
		dstr.appendChild(dstdby);

		var dstdzero2 = document.createElement('td');
		dstdzero2.setAttribute("id", "td_delete_note_"+rNumber);
		dstr.appendChild(dstdzero2);

		var tablenote = document.getElementById('tablenotes');

		tablenote.appendChild(dstr);

		//window.parent.document.getElementById('sbox-window').close();
		window.parent.SqueezeBox.close();

		window.addEvent('domready', function() {Calendar.setup({
				inputField	 :	"expire_"+rNumber,	 // id of the input field
				ifFormat	   :	"%Y-%m-%d",	  // format of the input field
				button		 :	"expire_"+rNumber+"_img",  // trigger for the calendar (button ID)
				align		  :	"Tl",		   // alignment (defaults to "Bl")
				singleClick	:	true
		});});

	}

	function ShowCancelledType()
	{
		document.getElementById("cancelled").style.display = "inline";
		document.getElementById("cancelled_amount").style.display = "inline";
		document.getElementById("cancelled_amount_lbl").style.display = "inline";
	}

	function HideCancelledType()
	{
		document.getElementById("cancelled").style.display = "none";
		document.getElementById("cancelled_amount").style.display = "none";
		document.getElementById("cancelled_amount_lbl").style.display = "none";
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<fieldset class="adminform">

		<legend><?php echo JText::_('VIEWLICDETAILS');?></legend>

		<table>
			<tr>
				<td colspan="5" align="right">
					<a class="modal digi_video" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_digicom&controller=about&task=vimeo&id=38437538">
						<img src="<?php echo JURI::base(); ?>components/com_digicom/assets/images/icon_video.gif" class="video_img" />
						<?php echo JText::_("COM_DIGICOM_VIDEO_LICENSES_MANAGER"); ?>				  
					</a>
				</td>
			</tr>
		</table>	   
	   
		<table class="admintable" cellpadding="0" cellspacing="3" border="0">

			<tr>
				<?php  if ( isset( $license->licenseid ) ) :?>
			<tr>
				<td width="20%">
						<?php  echo JText::_("VIEWLICLICID");?>:
				</td>
				<td>
						<?php  echo  ( $license->licenseid > 0) ? $license->licenseid : ""; ?>
						<input type="hidden" name="licenseid" value="<?php echo $license->licenseid; ?>"/>
						<?php
							echo JHTML::tooltip(JText::_("COM_DIGICOM_LICENSEID_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
						?>
				</td>
			</tr>
			<?php endif;?>
			<tr>
				<td>
					<?php echo JText::_("VIEWLICPUBLISH");?>
				</td>
				<td nowrap="nowrap">
					<?php echo JText::_("VIEWLICYES"); ?> <input type="radio" name="published" value="1" <?php echo (( $license->published != 0)?'checked':''); ?> />
					<?php echo JText::_("VIEWLICNO"); ?> <input type="radio" name="published" value="0" <?php echo (($license->published == 0 )?'checked':''); ?> />
					<?php
						echo JHTML::tooltip(JText::_("COM_DIGICOM_LICENSEPUBLISH_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
				</td>

			</tr>

			<tr>
				<td>
					<?php  echo JText::_("VIEWLICLICPROD")?>:
				</td>
				<td>
					<?php echo $license->productname; ?>
					<input type="hidden" name="productid" value="<?php echo $license->productid; ?>"/>
					<?php
						echo JHTML::tooltip(JText::_("COM_DIGICOM_LICENSEPRODUCT_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
				</td>
			</tr>

			<tr>
				<td>
					<?php  echo JText::_("VIEWLICLICPLAN")?>:
				</td>
				<td><?php

					if ($lists['subcription']->duration_count != -1) {
						echo DigiComAdminHelper::getDurationType($lists['subcription']->duration_count, $lists['subcription']->duration_type);
					} else {
						echo JText::_("DS_UNLIMITED");
					}
					echo "&nbsp;".JHTML::tooltip(JText::_("COM_DIGICOM_LICENSEPLAN_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
				?></td>
			</tr>

			<tr>
				<td>
					<?php  echo JText::_("VIEWLICLICCUSTOMER");?>:
				</td>
				<td>
					<input id="userid" type="hidden" name="userid" value="<?php  echo $license->userid;?>" />
					<input id="username" type="hidden" name="username" value="<?php  echo $license->username;?>" />
					<?php  echo $license->username ;?>
					<?php
						echo JHTML::tooltip(JText::_("COM_DIGICOM_LICENSECUSTOMER_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
				</td>
			</tr>

			<!-- May be need used to select plan_id -->
			<tr style="display:none;">
				<td>
					<?php //echo JText::_("VIEWLICLICSUBSCRIB");?>:
				</td>
				<td>
					<?php //echo $this->lists["subcriptions"]; ?>
					<input type="hidden" name="plan_id" value="<?php echo $license->plan_id; ?>"/>
					<input type="hidden" name="download_count" value="<?php echo $license->download_count; ?>"/>
				</td>
			</tr>

			<tr>
				<td>
					<?php  echo JText::_("VIEWLICLICORDERID")?>:
				</td>
				<td>
					<a href="index.php?option=com_digicom&controller=orders&task=show&cid[]=<?php echo $license->orderid; ?>&tmpl=component" class="modal" rel="{handler: 'iframe', size: {x: 750, y: 435}}"><?php echo $license->orderid; ?></a>
					<input type="hidden" name="orderid" value="<?php echo $license->orderid;?>"/>
					<?php
						echo JHTML::tooltip(JText::_("COM_DIGICOM_LICENSEORDER_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php  echo JText::_("LICENSE_CANCELLED")?>:
				</td>
				<td>
					<input type="radio" name="iscancelled" value="1" <?php echo (($license->cancelled != 0)?'checked':''); ?> onclick="ShowCancelledType();" /> <?php echo JText::_("VIEWLICYES"); ?>
					&nbsp;
					<input type="radio" name="iscancelled" value="0" <?php echo (($license->cancelled == 0 )?'checked':''); ?> onclick="HideCancelledType();" /> <?php echo JText::_("VIEWLICNO"); ?>
					<select id="cancelled" name="cancelled" style="float:none;margin-left:10px;<?php echo (($license->cancelled == 0 )?'display:none;':''); ?>">
						<option value="2" <?php echo (($license->cancelled == 2 )?'selected':''); ?>><?php echo JText::_("LICENSE_REFUND");?></option>
						<option value="1" <?php echo (($license->cancelled == 1 )?'selected':''); ?>><?php echo JText::_("LICENSE_CHARGEBACK");?></option>
					</select>
					<span id="cancelled_amount_lbl" style="<?php echo (($license->cancelled == 0 )?'display:none;':''); ?>"><?php echo JText::_("VIEWLICAMOUNTPAID") . ' ' . DigiComAdminHelper::get_currency($configs->get('currency','USD'));?></span>
					<input type="text" id="cancelled_amount" name="cancelled_amount" value="<?php echo $license->cancelled_amount;?>" style="float:none;margin-left:10px;width:75px;<?php echo (($license->cancelled == 0 )?'display:none;':''); ?>" />
					<?php
						echo JHTML::tooltip(JText::_("LICENSE_CANCELLED_TOOLTIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>

			<?php
			if (isset($license->id) && $license->id && !empty($license->orig_fields)):?>
			<tr><td><b><?php echo JText::_("VIEWLICLICORIGATTR");?>z1</b></td><td></td></tr>
			<tr>
				<td>
						<?php  foreach ($license->orig_fields as $field) {
							?>
							<?php  echo $field->fieldname; ?>:<br />
					<input readonly type="hidden" name="fields[]" value="<?php  echo $field->fieldname; ?>" />&nbsp;<br />
							<?php }?>
				</td>
				<td>
						<?php  foreach ($license->orig_fields as $field) {
							?>
					<input readonly type="hidden" name="options[]" value="<?php  echo $field->optioname; ?>" />&nbsp;<br />
							<?php  echo $field->optioname; ?> <br />
							<?php }?>

				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?php endif; ?>

			<?php if (isset($license->id) && ( $license->id) && isset($license->prod_fields)):?>
				<?php

				if ($totalfields > 0 ) { ?>

			<tr><td><b><?php echo JText::_("VIEWLICSETATTR");?></b></td><td></td></tr>

					<?php
					foreach ($license->prod_fields as $j => $v) { ?>
			<tr>
							<?php
							$v->optionid = -1;
							foreach($license->cur_fields as $j => $z) {
								if ($z->fieldid == $v->id) {
									$v->optionid = $z->optionid;
									break;
								}
							}
							echo "<td>".$v->name.":</td>";
							//echo ($v->mandatory == 1)?"<span class='error' style='color:red;'>*</span>":"";
							?>
				<td>
					<select style="width:<?php echo $optlen[$j]*15;?>px" name="field<?php echo $v->id;?>" id="attributes[<?php echo $v->id;?>]">
						<option value="-1" <?php echo ($v->optionid < 0|| "z".$v->optionid === "z")?"selected":""; ?>>Select <?php echo $v->name;?></option>
									<?php
									$options = explode ("\n", $v->options);
									foreach ($options as $i1 => $v1) {
										echo ("<option value='".$i1."'");
										echo ($i1==$v->optionid&&"z".$v->optionid!=="z")?"selected":"";
										echo (">".$v1."</option>");
									}

									?></select>
				</td>
			</tr>
						<?php } ?>

					<?php } ?>

			<?php endif; ?>
			<?php //if (count($plugin_handler->encoding_plugins) > 0):?>
			<?php //endif; ?>
			<tr style="display:none;">
				<td>
					<?php  echo JText::_("VIEWLICAMOUNTPAID");?>:
				</td>
				<td>
					<input class="text_area" type="text" name="amount_paid" size="30" maxlength="100" value="<?php
					//		   global $configs;
					$price_format = '%'.$configs->get('totaldigits',5).'.'.$configs->get('decimaldigits',2).'f';
					printf($price_format, (($license->amount_paid) ? $license->amount_paid : "" ));
						   ?>" />
					<select name="currency">
						<?php  for ( $i = 0;$i < count( $currency_options); $i++) {?>
						<option value="<?php  echo isset ( $currency_options[$i] ) ? $currency_options[$i]->currency_name : "";?>" <?php  if ( isset( $currency_options[$i] ) && isset( $license->currency ) && $currency_options[$i]->currency_name==$license->currency) echo "selected";?>><?php  echo isset ( $currency_options[$i] ) ? $currency_options[$i]->currency_name : "";?></option>
							<?php }?>
					</select>
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>

			<!-- Licence -->
			<?php
		   	if(intval($license->domainrequired) != "0"){
			?>
			<tr>
				<td colspan="2">
					<table id="tableplain" cellpadding="0" cellspacing="0">
						<tr style="background: #999999">
							<td width="20%"><?php echo JText::_("VIEWLICDOMAINBAR");?></td>
							<td width="80%"></td>
						</tr>
						<tr>
							<td>
								<?php  echo JText::_("VIEWLICDOMAIN");?>:
							</td>
							<td>
								<input class="text_area" type="text" name="domain" size="30" maxlength="100" value="<?php  echo ( $license->domain ) ? $license->domain : ""; ?>" />
								<?php
									echo JHTML::tooltip(JText::_("COM_DIGICOM_LICENSEDOMAIN_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
								?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php 
			}
			?>

			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>

			<!-- Notes -->
			<tr>
				<td>
					<?php
						echo JHTML::tooltip(JText::_("COM_DIGICOM_LICENSENOTES_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table id="tablenotes" cellpadding="0" cellspacing="0">
						<tr style="background: #999999">
							<td width="20%">Notes</td>
							<td width="20%">Note</td>
							<td width="20%">Date</td>
							<td>By</td>
							<td>Action</td>
						</tr>
						<?php foreach ($license->licence_notes as $licnote) { ?>
						<tr valign="top">
							<td>
							</td>

							<td>
							<?php 
								if(trim($licnote->notes) != ""){
									echo trim($licnote->notes);
								}
								else{
							?>
									<textarea name="notes[]" cols="20" rows="5"></textarea>
							<?php
								}
							?>
							</td>

							<td>
								<?php 
									if(trim($licnote->expires) != ""){
											echo JHTML::_('calendar', $licnote->expires, 'expires['.$licnote->id.']', 'expires_'.$licnote->id, '%Y-%m-%d', array('size'=>'30',  'maxlength'=>'19'));
									}; 
								?>
							</td>

							<td>admin</td>

							<td>
								<a href="index.php?option=com_digicom&controller=licenses&task=deletenote&cid[]=<?php echo $cid; ?>&note=<?php echo $licnote->id; ?>">Delete</a>
							</td>
						</tr>
						<?php } ?>
					</table>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3">
					<br/>
					<a href="javascript:void(0)" onclick="createnote()">Add New Note</a>
				</td>
			</tr>
			<!-- Notes -->

		</table>

	</fieldset>

	<input type="hidden" name="images" value="" />
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="id" value="<?php echo $license->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="Licenses" />
	<input type="hidden" name="keyword" value="<?php echo JRequest::getVar("keyword", ""); ?>" />
	<input type="hidden" name="status" value="<?php echo JRequest::getVar("status", ""); ?>" />
</form>
