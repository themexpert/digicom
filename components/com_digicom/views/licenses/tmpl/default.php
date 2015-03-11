<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 439 $
 * @lastmodified	$LastChangedDate: 2013-11-20 04:30:39 +0100 (Wed, 20 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");



function get_time_difference($start, $end){
	$uts['start'] = $start;
	$uts['end'] = $end;
	if( $uts['start'] !== -1 && $uts['end'] !== -1){
		if($uts['end'] >= $uts['start']){
			$diff = $uts['end'] - $uts['start'];
			if($days=intval((floor($diff/86400)))){
				$diff = $diff % 86400;
			}
				
			if($hours=intval((floor($diff/3600)))){
				$diff = $diff % 3600;
			}	
			
			if($minutes=intval((floor($diff/60)))){
				$diff = $diff % 60;
			}	
			$diff = intval($diff);
			return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff));
		}
		else{
			return false;
		}
	}
	return false;
}

$document = JFactory::getDocument();
$document->addScript(JURI::root() . DIGICOM_ASSET_PATH . '/js/licenses.js');

$k = 0;
$n = count($this->licenses);
$show_domain = $this->show_domain;
$optlen = $this->optlen;
$success = JRequest::getVar("success", 2, "request");
$configs = $this->configs;
$mosmsg = JRequest::getVar("mosmsg", '', "request");
global $mainframe;
$Itemid = JRequest::getVar("Itemid", "0");
$updatedLicenseId = JRequest::getVar("updated", "0");

$cart_itemid = DigiComHelper::getCartItemid();
$and_itemid = "";
if($cart_itemid != ""){
	$and_itemid = "&Itemid=".$cart_itemid;
}
$product_itemid = DigiComHelper::getProductItemid();
$andProdItem = "";
if($product_itemid != "0"){
	$andProdItem = "&Itemid=".$product_itemid;
}

$c = new DigiComSessionHelper();
$details = $c->getTransactionData();

$database = JFactory::getDBO();

$sql = "select `in_trans` from #__digicom_settings";
$database->setQuery($sql);
$database->query();
$in_trans = $database->loadResult();
$app = JFactory::getApplication('site');

if(isset($in_trans) && $in_trans > 0){
	$_SESSION['in_trans'] = 0;
	$db = JFactory::getDBO();
	$sql = "select transaction_details from #__digicom_session";
	
	if($success == 1){
		echo urldecode($mosmsg)."<br />".$configs->thankshtml."<br />";
		$non_taxed = $details['nontaxed'];
		//$orderid = $details["cart"]["orderid"];
		//$orderid = $_SESSION['current_order_id'];
		$orderid = intval($in_trans);
		 
		DigiComHelper::affiliate($non_taxed, $orderid, $configs);

		//show google tracking script
		if(DCConfig::get('conversion_id','') != '' && DCConfig::get('conversion_label','') != ''){
			echo GoogleHelper::trackingOrder($orderid);
			$sql = "update #__digicom_settings set `in_trans`=0";
			$database->setQuery($sql);
			$database->query();
		}
	}
	elseif($success == 0){
		echo $configs->ftranshtml."<br />";
		$mainframe->setPageTitle(JText::_("DSFAILEDPAYMENT"));
	}
}
$invisible = 'style="display:none;"';
?>

<script type="text/javascript">

// This will parse a delimited string into an array of
// arrays. The default delimiter is the comma, but this
// can be overriden in the second argument.
function CSVToArray( strData, strDelimiter ){
	// Check to see if the delimiter is defined. If not,
	// then default to comma.
	strDelimiter = (strDelimiter || ",");

	// Create a regular expression to parse the CSV values.
	var objPattern = new RegExp(
		(
			// Delimiters.
			"(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +

			// Quoted fields.
			"(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +

			// Standard fields.
			"([^\"\\" + strDelimiter + "\\r\\n]*))"
		),
		"gi"
		);


	// Create an array to hold our data. Give the array
	// a default empty first row.
	var arrData = [[]];

	// Create an array to hold our individual pattern
	// matching groups.
	var arrMatches = null;


	// Keep looping over the regular expression matches
	// until we can no longer find a match.
	while (arrMatches = objPattern.exec( strData )){

		// Get the delimiter that was found.
		var strMatchedDelimiter = arrMatches[ 1 ];

		// Check to see if the given delimiter has a length
		// (is not the start of string) and if it matches
		// field delimiter. If id does not, then we know
		// that this delimiter is a row delimiter.
		if (
			strMatchedDelimiter.length &&
			(strMatchedDelimiter != strDelimiter)
			){

			// Since we have reached a new row of data,
			// add an empty row to our data array.
			arrData.push( [] );

		}


		// Now that we have our delimiter out of the way,
		// let's check to see which kind of value we
		// captured (quoted or unquoted).
		if (arrMatches[ 2 ]){

			// We found a quoted value. When we capture
			// this value, unescape any double quotes.
			var strMatchedValue = arrMatches[ 2 ].replace(
				new RegExp( "\"\"", "g" ),
				"\""
				);

		} else {

			// We found a non-quoted value.
			var strMatchedValue = arrMatches[ 3 ];

		}


		// Now that we have our value string, let's add
		// it to the data array.
		arrData[ arrData.length - 1 ].push( strMatchedValue );
	}

	// Return the parsed data.
	return( arrData );
}

Array.prototype.clean = function(deleteValue) {
  for (var i = 0; i < this.length; i++) {
	if (this[i] == deleteValue) {		 
	  this.splice(i, 1);
	  i--;
	}
  }
  return this;
};

function ds_download_disabled(id)
{
	var $license = jQuery('#license' + id);
	$license.find('.licenseError').text('You must enter support domain and install domain(s) before you can download');
}

function ds_update_license(id)
{
	var $license = jQuery('#license' + id);
	var licenseDomains = $license.find('textarea[name=devdomain]').val();
	var prodDomain = $license.find('input[name=proddomain]').val();

	if(licenseDomains && prodDomain) {
		var licenseDomainArray = CSVToArray(licenseDomains);
		licenseDomainArray = licenseDomainArray[0].clean("");

		if(licenseDomainArray.length > 10) {
			var answer = confirm("Limit reached. Only the first 10 domains will be saved.")
			if (answer){
				$license.find('textarea[name=devdomain]').val(licenseDomainArray.slice(0,10).join(","));
				jQuery('#domainForm' + id).submit();
			}
			else{
				return false;
			}
		} else {
			$license.find('textarea[name=devdomain]').val(licenseDomainArray.join(","));
			jQuery('#domainForm' + id).submit();
		}
	} else {
		alert('Support domain and install domain fields are required');
	}
}
</script>
	

<div id="digicom">


	<div class="navbar">
		<div class="navbar-inner hidden-phone">
			<ul class="nav">
				<li class="active">
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=licenses&Itemid=".$Itemid); ?>"><i class="ico-download"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="ico-list-alt"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&controller=cart&task=showCart".$and_itemid); ?>"><i class="ico-shopping-cart"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
				</li>
			</ul>
		</div>
		<ul class="nav nav-pills hidden-desktop">
			<li class="active">
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=licenses&Itemid=".$Itemid); ?>"><i class="ico-download hidden-phone"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="ico-list-alt hidden-phone"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&controller=cart&task=showCart".$and_itemid); ?>"><i class="ico-shopping-cart hidden-phone"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
			</li>
		</ul>
	</div>

	<h1><?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></h1>
<?php
		if ($n < 1){
			$continue_url = DigiComHelper::DisplayContinueUrl($configs, $this->caturl);
?>

		<form action="<?php echo $continue_url;?>" name="adminForm" method="post">

			<div id="digicom_body">
					<div class="input-append">
						<input type="text" name="search" class="digi_textbox">
						<button type="submit" class="btn"><i class="ico-search"></i> <?php echo JText::_("DIGI_SEARCH"); ?></button>
					</div>
				<div class="digicom_orders">
				<?php
					echo JText::_('DSNOLIC');
				?>
				</div>
			</div>

			<input type="submit" value="<?php echo JText::_("DSCONTINUESHOPING");?>" class="btn"  />

		</form>

<?php
	} else {
?>
		<div id="digicom_body">
			<div class="input-append">
				<form action="index.php" method="get">
					<input type="hidden" name="option" value="com_digicom"/>
					<input type="hidden" name="controller" value="Licenses"/>
					<input type="text" id="dssearch" name="search" class="digi_textbox" value="<?php echo trim(JRequest::getVar('DIGI_SEARCH', '')); ?>" size="30"/>
					<button type="submit" class="btn"><i class="ico-search"></i> <?php echo JText::_("DIGI_SEARCH"); ?></button>
					<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
				</form>
			</div>
			<div>
				<table class="table table-bordered table-striped">
				<thead>
				<tr>
					<th width="25%" style="vertical-align: middle;"><?php echo JText::_("DIGI_LICENSE_DETAILS"); ?></th>
					<th width="17%">Support Domain
						<br /><span class="small error" style="font-weight: normal;">No 'www' or 'http://'</span>
					</th>
					<th width="28%">Domains you wish to install this product on
						<br /><span class="small error" style="font-weight: normal;">Separated by comma, no 'www' or 'http://'</span>
					</th>
					<th width="30%">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
<?php

			$jconfig = JFactory::getConfig();

			for ($i = 0; $i < $n; $i++):
				$license 		= $this->licenses[$i];
				$nr_orders 		= $this->getNrOrders($license->licenseid);
				$offerplans 	= $license->offerplans;

				$id = $license->id;

				$regdomain_link = JRoute::_("index.php?option=com_digicom&controller=licenses&task=register&licid=" . $id . "&Itemid=" . $Itemid . "&no_html=1");
				$download_regdomain_link_link = 'index.php?option=com_digicom&controller=licenses&task=register&licid=' . $id . '&Itemid=' . $Itemid . '&no_html=1';
				$regdevdomain_link = JRoute::_('index.php?option=com_digicom&controller=licenses&task=devregister&licid=' . $id . "&Itemid=" . $Itemid);
				$download_link = JURI::root().'index.php?option=com_digicom&controller=licenses&task=download&licid='.$id.'&Itemid='.$Itemid.'&action=download';
				$devdownload_link = JRoute::_("index.php?option=com_digicom&controller=licenses&task=devdownload&licid=" . $id . "&Itemid=" . $Itemid);

				$download_regdomain_link = '<a class="btn" href="javascript:;" onclick="ShowDomainModal(\''.JRoute::_($download_regdomain_link_link).'\');"><i class="ico-download-alt"></i> '.JText::_("DSDOWNLOAD").'</a>';
				$download_regdomain_link_modal = '<a class="btn" href="javascript:;" onclick="ShowDomainModal(\''.JRoute::_($regdomain_link).'\');"><i class="ico-download-alt"></i> '.JText::_("DSDOWNLOAD").'</a>';
				$regdomain_link = '<a href="javascript:;" onclick="ShowDomainModal(\'' . $regdomain_link . '\');"><i class="ico-edit ico-white"></i> ' . (JText::_("DSREGDOMAIN")) . '</a>';

				$regdevdomain_link = '<a href="' . $regdevdomain_link . '"><i class="ico-world"></i> ' . (JText::_("DSREGDEVDOMAIN")) . '</a>';

				//class="digicom_cancel"
				$download_btn_link = (($license->dev_domain && $license->domain) || $license->domainrequired == 0) ? 'href=' . $download_link : 'disabled onclick="ds_download_disabled(' . $license->id . ');"';
				$download_regdomain_link = $download_regdomain_link_modal = $download_link = '<a class="btn btn-success" ' . $download_btn_link . ' style="color:#fff;"><i class="ico-download-alt ico-white"></i> '.JText::_("DSDOWNLOAD").'</a>';

				// Domains
				$license_form_link = JRoute::_("index.php?option=com_digicom&controller=licenses&task=saveDomain&action=saveDomain&Itemid=".$Itemid);
				$license_dev_domain = trim($license->dev_domain);
				$license_domain = trim($license->domain);
				$col_update_button = ($license->domainrequired != 0) ? '<td style="border:0;"><button style="submit" class="btn" onclick="ds_update_license(' . $license->id . ');">' . JText::_("DIGI_UPDATE") . '</button></td>' : '';
				$license_domain_change = 3 - $license->domain_change;

				$db = JFactory::getDBO();
				$renew_plan = $license->plan_id;
				$sql = "select `plan_id` from #__digicom_products_renewals where `product_id`=".intval($license->productid)." and `default`=1";
				$db->setQuery($sql);
				$db->query();
				$result = $db->loadResult();
				if(intval($result) == "0"){
					$sql = "select `plan_id` from #__digicom_products_renewals where `product_id`=".intval($license->productid)." and `default`=1";
					$db->setQuery($sql);
					$db->query();
					$result = $db->loadResult();
				}
				if(intval($result) != "0"){
					$renew_plan = $result;
				}

				$button_renew = '
				<form action="'.JURI::root().'" method="post" name="addproduct">
					<input type="hidden" value="com_digicom" name="option">
					<input type="hidden" value="Cart" name="controller">
					<input type="hidden" value="add" name="task">
					<input type="hidden" value="1" name="renew">
					<input type="hidden" value="' . $license->id . '" name="renewlicid">
					<input type="hidden" value="' . $renew_plan . '" name="plan_id">
					<input type="hidden" value="' . $license->productid . '" name="pid">
					<input type="hidden" value="' . $Itemid . '" name="Itemid">';
				$button_renew .= '<button style="submit" class="btn btn-warning"><i class="ico-shopping-cart"></i> '.JText::_("DIGI_RENEW").'</button><br>';
				JRequest::setVar("renew", "1");

				$button_renew .= '</form>';

				$devdownload_link = '<a href="' . $devdownload_link . '">' . (JText::_("DSDEVDOWNLOAD")) . '</a>';

				$order_link = JRoute::_("index.php?option=com_digicom&controller=orders&task=view&orderid=" . $license->orderid . "&Itemid=" . $Itemid);
				$order_link = '<a href="' . $order_link . '">' . (JText::_("DSVIEWORDER")) . '</a>';

				// $pur_date = $expired;
				$lic_date = JFactory::getDate();
				
				// $lic_date->setOffset( $jconfig->get('offset') );
				$pur_date = $lic_date->toUnix(true);

				$purch_date = date( $configs->get('time_format','d-m-Y'), $pur_date );
				if ( $configs->hour24format == 0 ) {
					$purch_date .= " | " . date( "h:i A", $pur_date );
				} else {
					$purch_date .= " | " . date( "H:i", $pur_date );
				}

				$lic_plain = null;
				$plan_id = $license->plan_id;
				// Find Plains by ID
				foreach($license->plans as $tplan){
					if($tplan->id == $license->plan_id){
						$lic_plain = $tplan;
					}
				}
				if($lic_plain == NULL){
					foreach($license->renewals as $tplan){
						if($tplan->id == $license->plan_id){
							$lic_plain = $tplan;
						}
					}
				}
				if($lic_plain == NULL){
					$sql = "select * from #__digicom_plans where id=".intval($plan_id);
					$database->setQuery($sql);
					$database->query();
					$lic_plain = $database->loadObject();
				}
				$show_download_link = false;

				$plan_out = '';
				$plan_boolean = true;
				$buy_date_string = $license->purchase_date;
				$buy_date_int = strtotime($buy_date_string);

				$expire = JText::_("DIGI_EXPIRES");
				if (($lic_plain->duration_count == -1) && ($lic_plain->duration_type == 0)) {
					$plan_out = '<b>'.JText::_("DIGI_EXPIRES").'</b>: <span class="digi_active">'.JText::_("DIGI_UNLIMITED_PLAN").'</span>';
					$no_renew = true;
					//$plan_boolean = false;
					$show_download_link = true;
				} else {
					$jnow = JFactory::getDate();
					$date_current = $jnow->toSql();
					$int_current_date = strtotime($date_current);
					$bool_expired = false;
					$date_string = "";

					$purch_date = $lic_date->toUnix(true);
					//$pur_date = $expired;
					$expired = "";

					if(($lic_plain->duration_type != 0) && ($lic_plain->duration_count != -1)) {
						$expired = strtotime($license->expires);

						if($int_current_date > $expired){ //expired
							$bool_expired = true;
							$expire = JText::_("DIGI_EXPIRED");
							$date_int = $expired;
							$date_string = JHTML::_('date', $date_int, 'Y-m-d l:M:S p');
							//---------------------------
							$difference_int = get_time_difference($date_int, $int_current_date);
							$difference = $difference_int["days"]." ".JText::_("DIGI_REAL_DAYS");
							if($difference_int["days"] == 0){
								if($difference_int["hours"] == 0){
									if($difference_int["minutes"] == 0){
										$difference = "0";
									} else {
										$difference = $difference_int["minutes"]." ".JText::_("DIGI_REAL_MINUTES")." ".JText::_("DIGI_AGO");
									}
								} else {
									$difference = $difference_int["hours"]." ".JText::_("DIGI_REAL_HOURS").", ".
										$difference_int["minutes"]." ".JText::_("DIGI_REAL_MINUTES")." ".JText::_("DIGI_AGO");
								}
							} else {
								$difference = $difference_int["days"]." ".JText::_("DIGI_REAL_DAYS").", ".
									$difference = $difference_int["hours"]." ".JText::_("DIGI_REAL_HOURS").", ".
										$difference_int["minutes"]." ".JText::_("DIGI_REAL_MINUTES")." ".JText::_("DIGI_AGO");
							}
							$date = '<span class="digi_expired">'.$difference." (".JHTML::_('date', $date_int, 'm-d-Y').")".'</span>';
							//---------------------------
						} else {
							$bool_expired = false;
							$show_download_link = true;
							$expire = JText::_("DIGI_EXPIRES");
							$date_int = $expired;
							$date_string = "";

							if($configs->hour24format == 1){
								$date_string = JHTML::_('date', $date_int, 'Y-m-d H:M:S');
							} elseif($configs->hour24format == 0){
								$date_string = JHTML::_('date', $date_int, 'Y-m-d l:M:S p');
							}
							//---------------------------
							$difference_int = get_time_difference($int_current_date, $date_int);
							$difference = $difference_int["days"]." ".JText::_("DIGI_REAL_DAYS");
							if($difference_int["days"] == 0){
								if($difference_int["hours"] == 0){
									if($difference_int["minutes"] == 0){
										$difference = "0";
									} else {
										$difference = JText::_("DIGI_IN")." ".$difference_int["minutes"]." ".JText::_("DIGI_REAL_MINUTES");
									}
								} else {
									$difference = JText::_("DIGI_IN")." ".$difference_int["hours"]." ".JText::_("DIGI_REAL_HOURS").", ".
										$difference_int["minutes"]." ".JText::_("DIGI_REAL_MINUTES");
								}
							} else {
								$difference = JText::_("DIGI_IN")." ".$difference_int["days"]." ".JText::_("DIGI_REAL_DAYS").", ".
									$difference = $difference_int["hours"]." ".JText::_("DIGI_REAL_HOURS").", ".
										$difference_int["minutes"]." ".JText::_("DIGI_REAL_MINUTES");
							}
							$date = '<span class="digi_active">'.$difference.'</span>';
							//---------------------------
						}
					} else {//0 downloads
						if($lic_plain->duration_count > $license->download_count) {
							$expire = JText::_("DIGI_EXPIRES");
							$diff = (int)($lic_plain->duration_count - $license->download_count);
							$date = '<span class="general_text_larger" style="font-weight:bold; white-space:nowrap;">'.$diff." ".JText::_("DIGI_DOWNLOAD_LEFT").'</span>';
							$show_download_link = true;
						} else {
							$expire = JText::_("DIGI_EXPIRED");
							$duration_count = isset($lic_plain->duration_count)?$lic_plain->duration_count:0;
							$download_count = isset($license->download_count)?$license->download_count:0;
							$diff = (int)($duration_count - $download_count);
							$date = '<span class="general_text_larger" style="font-weight:bold; white-space:nowrap;">'.$diff." ".JText::_("DIGI_DOWNLOAD_LEFT").'</span>';
							$download_regdomain_link_modal = "";
						}
					}
					$plan_out = "<b>".$expire.":</b> ".$date;
				}

?>
				<tr id="license<?php echo $license->id; ?>">
					<td>
						<ul>
							<li class="general_text_larger">
								<?php if (!$license->hide_public):?>
								<a href="<?php echo JRoute::_("index.php?option=com_digicom&controller=products&task=view&cid=".$license->catid."&pid=".$license->productid.$andProdItem); ?>">
								<?php endif;?>
								<?php echo $license->productname; ?>
								<?php if (!$license->hide_public):?>
								</a>
								<?php endif;?>
								(<?php echo $license->licenseid; ?>)
							</li>
							<?php if (isset($license->fields) && !empty($license->fields)) : ?>
							<li class="digicom_details"><ul><?php
							foreach ($license->fields as $field) :
								echo "<li>". $field->fieldname . ": " . $field->optioname ."</li>";
							endforeach;
							?></ul></li>
							<?php endif; ?>
							<li class="digicom_details">
								<?php echo $plan_out; ?>
							</li>
							<li>
								<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&licid=".$license->licenseid."&Itemid=".$Itemid); ?>"><?php echo JText::_("DIGI_VIEW_ORDERS")."(".$nr_orders.")"; ?></a>
							</li>
						</ul>
					</td>
					<?php if($license->domainrequired && $license->domainrequired != 4 && $license->domainrequired != 2) { ?>
					<form id="domainForm<?php echo $license->id; ?>" action="<?php echo $license_form_link;?>" method="post">
					<td>
						<?php # if($license->domainrequired && $license->domainrequired != 4 && $license->domainrequired != 2) { ?>
							<input style="width: 85%;" type="text" name="proddomain" <?php echo strlen($license_domain)>0?'value="'.$license_domain.'"':'' ;?> 
								<?php echo ($license_domain_change <= 0) ? 'disabled="disabled"' : ''; ?>
							/> <span class="small error">*</span>
							<?php if($license_domain_change <= 0) { ?>
							<input type='hidden' name='proddomain' value='<?php echo $license_domain; ?>'>
							<?php } ?>
							<?php if($updatedLicenseId == $license->id && $license_domain_change < 3) { ?>
							<br /><span class="digi_active"><?php echo $license_domain_change; ?></span> domain changes left
							<?php } ?>
						<?php # } ?>
					</td>
					<td>
						<?php # if($license->domainrequired && $license->domainrequired != 4 && $license->domainrequired != 2) { ?>
						<textarea style="width: 85%;" rows="3" name="devdomain"><?php echo strlen($license_domain)>0 ? $license_dev_domain : ''; ?></textarea> <span class="small error">*</span>
						<?php #} ?>
					</td>
					<input type="hidden" name="licid" value="<?php echo $license->id;?>" />
					</form>
					<td>
						<?php

							if(!$show_download_link){
								$download_link = "";
							}

							if($plan_boolean === TRUE){
								$download_link = '<table><tr>' . $col_update_button . '<td style="border:0;">'.$download_link.'</td><td style="border:0;">'.$button_renew.'</td></tr></table>';
							}

							if(!$show_download_link){
								$download_link = '<table><tr><td style="border:0;">'.$button_renew.'</td></tr></table>';
							}

							if($license->domainrequired == 4){

							}
							elseif($license->domainrequired == 3){

							}
							elseif($license->domainrequired == 2){
								// echo JText::_("DSPRODUCT_NOT_DOWNLOADABLE");
							}
							elseif($license->domainrequired == 0){
								echo $download_link;
							}
							elseif($license->domainrequired == 1 && $license->domain){
								echo $download_link;
							}
							elseif($license->domainrequired == 1 && !$license->domain && $offerplans != "1"){
								echo '<table style="border:0 !important;"><tr>' . $col_update_button . '<td style="vertical-align:top;border:0;">'.$download_regdomain_link_modal.'</td><td style="vertical-align:top;border:0;">'.$button_renew.'</td></tr></table>';
							}
							else{
								if(($license->domain) && $license->domainrequired == 0){
									// echo $download_link;
								}
								elseif(($license->domain) || ($license->domain && $license->domainrequired == 1 )){
									// echo $download_link;
								}
								else{
									echo $download_link;
								}
							}

							?>
							<span class="licenseError small error"></span>
					</td>
					<?php } elseif($license->domainrequired != 2) { ?>
					<td colspan="2">
						<div class="digicom_invoice">
							<ul>
								<li><?php
									if($license->domainrequired == 4){

									}
									elseif($license->domainrequired == 3){

									}
									elseif($license->domainrequired == 2){
										echo JText::_("DSPRODUCT_NOT_DOWNLOADABLE");
									}
									elseif($license->domainrequired == 0){
										echo JText::_("DSNO_DOMAIN_REQUIRED") . "";
									}
									elseif($license->domainrequired == 1 && !$license->domain){
										// echo $regdomain_link . "<br />";
									}
									elseif($license->domainrequired == 1 && $license->domain){
										// echo JText::_("DSLIVE") . ": " . $license->domain . "";
									}
									else{
										if($license->domain){
											echo JText::_("DSLIVE") . ": " . $license->domain . "<br>";
										}
										elseif($license->domainrequired == 0){
											echo JText::_("NO_DOMAIN_REQUIRED") . "<br />";
										}
										else{
											// echo $regdomain_link . "<br />";
										}

										if($license->dev_domain){
											echo JText::_("DSDEV") . ": " . $license->dev_domain;
										}
										elseif($license->domainrequired == 0){
											//echo JText::_("DSNO_DOMAIN_REQUIRED");
										}
										else{
											echo $regdevdomain_link . "";
										}
									}
									?></li>
								</ul>
						</div>
					</td>
					<td>
						<div class="digicom_invoice">
						<?php

							if(!$show_download_link){
								$download_link = "";
							}

							if($plan_boolean === TRUE){
								$download_link = '<table><tr>' . $col_update_button . '<td style="border:0;">'.$download_link.'</td><td style="border:0;">'.$button_renew.'</td></tr></table>';
							}

							if(!$show_download_link){
								$download_link = '<table><tr><td style="border:0;">'.$button_renew.'</td></tr></table>';
							}

							if($license->domainrequired == 4){

							}
							elseif($license->domainrequired == 3){

							} elseif($license->domainrequired == 2){
								// echo JText::_("DSPRODUCT_NOT_DOWNLOADABLE");
							} elseif($license->domainrequired == 0){
								echo $download_link;
							} elseif($license->domainrequired == 1 && $license->domain){
								echo $download_link;
							} elseif($license->domainrequired == 1 && !$license->domain && $offerplans != "1"){
								echo '<table style="border:0 !important;"><tr>' . $col_update_button . '<td style="vertical-align:top;border:0;">'.$download_regdomain_link_modal.'</td><td style="vertical-align:top;border:0;">'.$button_renew.'</td></tr></table>';
							} else{
								if(($license->domain) && $license->domainrequired == 0){
									echo $download_link;
								}
								elseif(($license->domain) || ($license->domain && $license->domainrequired == 1 )){
									// echo $download_link;
								}
								else{
									echo $download_link;
								}
							}

							?>
							<span class="licenseError small error"></span>
							<?php 
							// $plg_multifiles = JPluginHelper::getPlugin('digicom','multifiles');
							// if( $plg_multifiles ){
							
							// $dispatcher	= JEventDispatcher::getInstance();
							JPluginHelper::importPlugin('digicom');
							$jv = new JVersion();
							$isJ25 = $jv->RELEASE == '2.5';
							if($isJ25){
								$dispatcher = JDispatcher::getInstance();
							} else {
								$dispatcher	= JEventDispatcher::getInstance();
							}
							$htmls = $dispatcher->trigger( 'onAfterDigiComShowDownloadLink', array( 'com_digicom.showdownload' , $license->id, $license->productid, $plan_boolean, $show_download_link ) );
							if(count($htmls)){
								?>
								<div class="after_show_download_link">
								<?php echo implode('',$htmls);?>
								</div>
								<?php 
							}
							?>
						</div>
					</td>
					<?php } else { ?>
					<td colspan="3">
						<div class="digicom_invoice">
						<?php echo JText::_("DSPRODUCT_NOT_DOWNLOADABLE"); ?>
						</div>
					</td>
					<?php } ?>
					
				</tr>
				<?php
				$k = 1 - $k;
			endfor; ?>
				</tbody>
				</table>
			</div>
		</div>

	<?php } ?>

	<div id="myModalDomain" class="modal" style="display:none;">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php echo JText::_("DSREGDOMAIN");?></h3>
		</div>
		<div class="modal-body">

		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("DIGI_CLOSE");?></button>
		</div>
	</div>

</div>

<script>
function ShowDomainModal(url)
{
	jQuery('#myModalDomain .modal-body').html('<iframe width="100%" height="100%" frameborder="0" scrolling="no" allowtransparency="true" src="' + url + '"></iframe>');
	jQuery('#myModalDomain').modal('show');
}
</script>
<?php
if($this->ga){
	if(DCConfig::get('conversion_id','') != '' && DCConfig::get('conversion_label','') != ''){
		echo GoogleHelper::trackingOrder();
	}
}
?>