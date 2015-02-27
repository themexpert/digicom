<?php 
/**
 * @package Social Ads
 * @copyright Copyright (C) 2009 -2010 themexpert.com, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.themexpert.com.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.modal');

if(!isset($vars->order_id)){
	$vars->order_id = $vars->user_id;
}

$error			= JRequest::getInt("error", 0);
$errormsg		= JRequest::getVar("errormsg", "", "", "string");
$cardfname		= JRequest::getVar("cardfname", "", "", "string");
$cardlname		= JRequest::getVar("cardlname", "", "", "string");
$cardaddress1	= JRequest::getVar("cardaddress1", "", "", "string");
$cardcity		= JRequest::getVar("cardcity", "", "", "string");
$cardstate		= JRequest::getVar("cardstate", "", "", "string");
$cardzip		= JRequest::getVar("cardzip", "", "", "string");
$cardcountry	= JRequest::getVar("cardcountry", "", "", "string");
$cardemail		= JRequest::getVar("cardemail", "", "", "string");
$activated		= JRequest::getVar("activated", "0", "", "string");
$cardnum		= JRequest::getVar("cardnum", "", "", "string");
$cardcvv		= JRequest::getVar("cardcvv", "", "", "string");
$expmonth		= JRequest::getVar("expmonth", "", "", "string");
$expyear		= JRequest::getVar("expyear", "", "", "string");

require_once(JPATH_SITE.DS."components".DS."com_digicom".DS."models".DS."config.php");

$conf_model		= new DigiComModelConfig();
$configs		= $conf_model->getConfigs();

$db = JFactory::getDBO();
if ($configs->topcountries != '')
{
	$countries = explode(",", $configs->topcountries);
	$where = '';
	foreach ($countries as $country)
	{
		$where.= "`country`<>'" . $country . "' AND ";
	}
	$where = substr($where, 0, strlen($where)-4);
	$sql = "SELECT DISTINCT(`country`)
			FROM `#__digicom_states`
			WHERE $where
			ORDER BY `country`";
}
else
{
	$sql = "SELECT DISTINCT(`country`)
			FROM `#__digicom_states`
			ORDER BY `country`";
}
$db->setQuery($sql);
$countries = $db->loadObjectList();

$sql = "SELECT `csym`
		FROM `#__digicom_currency_symbols`
		WHERE `ccode`='" . $configs->currency . "'";
$db->setQuery($sql);
$currency_symbol = $db->loadResult();
$currency_symbol = explode(",", $currency_symbol);

?>
<script type="text/javascript">
function myValidate(f)
{
	if (document.formvalidator.isValid(f)) {
		f.check.value='<?php echo JSession::getFormToken(); ?>';
		jQuery("#authorizebutton").addClass("disabled");
		return true; 
	}
	else {
		var msg = 'Some values are not acceptable.  Please retry.';
		alert(msg);
	}
	return false;
}

function getStates()
{
	var url1 = '<?php echo JURI::root();?>index.php?option=com_digicom&controller=cart&task=getcountries&tmpl=component&format=raw&cardstate=<?php echo $cardstate;?>&ct=' + jQuery('#cardcountry').val();
	jQuery.ajax(
			{
				url:url1,
				type:'GET',
				dataType:'html',
				success:function(response){
					jQuery('#cardstate').html(response);
				}
			});
}

window.addEvent('domready', function() {
	SqueezeBox.assign($$('a[rel=modalimage]'));
	getStates();
});
</script>

<div class="digicom" style="<?php echo $configs->shopping_cart_style ? 'width: 70%;margin-left: auto;margin-right: auto;padding: 20px 0 0 0;background: #fff;' : ''; ?>">
	<div class="container-fluid">

		<?php if($configs->shopping_cart_style) {
			echo '<a href="' . JURI::root() . '">
				<img src="' . JURI::root() . 'images/stories/digicom/store_logo/' . trim($configs->store_logo) . '" alt="store_logo" border="0">
			</a>';
			}
		?>

		<?php
			if($configs->show_steps == 0){
		?>
				<div class="bar">
					<span class="inactive-step">
					<?php
						echo JText::_("DIGI_STEP_ONE");
					?>
					</span>
					
					<span class="inactive-step">
					<?php
						echo JText::_("DIGI_STEP_TWO");
					?>
					</span>
					
					<span class="active-step">
					<?php
						echo JText::_("DIGI_STEP_THREE");
					?>
					</span>
				</div>
		<?php
			}
		?>

		<?php echo '<h2>' . JText::_("PAYMENT_DETAILS_PAGE") . '</h2>'; ?>

		<div style="background:#f7f7f7;border-radius:5px;-webkit-border-radius:5px;-moz-border-radius:5px;padding-top:10px;">
			<div class="row-fluid">
				<div class="span6">
					&nbsp;&nbsp;&nbsp;
					<span style="color:#666;">Your Total:</span> <b><?php echo (!$configs->currency_position ? '&#' . $currency_symbol[0] . ';' . ' ' : '') . $vars->amount . ($configs->currency_position ? ' ' . '&#' . $currency_symbol[0] . ';' : '');?></b>
				</div>
				<div class="span6">
					<img style="float:right;margin-right:10px;" src="<?php echo JURI::root();?>/components/com_digicom/assets/images/we-accept.png" alt="" />
				</div>
			</div>
		</div>
	</div>
	<p>&nbsp;</p>

	<?php if($error):?>
		<div class="alert alert-error">
			<strong><?php echo $errormsg;?></strong>
		</div>
	<?php endif;?>

	<form action="<?php echo $vars->url; ?>" name="adminForm" id="adminForm" onSubmit="return myValidate(this);"  class="form-validate form-horizontal"  method="post">
	<input id="cardaddress2" type="hidden" name="cardaddress2" size="35" value="" />

	<div class="container-fluid">

		<div class="row-fluid">
			<div class="span6">
				<h4 style="color:#666;"><img src="<?php echo JURI::root();?>/components/com_digicom/assets/images/lock.png" align="absmiddle" style="margin-right:10px;" alt="" /> Credit Card Information:</h4>
			</div>
			<div class="span6">
				<p style="text-align:right;"><?php echo JText::_("PAYMENT_FIELDS_REQUIRED");?></p>
			</div>
		</div>
		<div style="background:#f7f7f7;border-radius:5px;-webkit-border-radius:5px;-moz-border-radius:5px;padding-top:25px;">
			<div class="row-fluid">
				<div class="span6">
					<div class="control-group">
						<label for="" class="control-label"><?php echo JText::_( 'CREDIT_CARD_TYPE' ) ?> <span style="color:#ff0000;">*</span></label>
						<div class="controls" style="margin-left:5px;"><?php $types = array();
							$types[] = JHTML::_('select.option', 'Visa', JText::_( "VISA" ) );
							$types[] = JHTML::_('select.option', 'Mastercard', JText::_( "MASTERCARD" ) );
							$types[] = JHTML::_('select.option', 'AmericanExpress', JText::_( "AMERICAN_EXPRESS" ) );
							$types[] = JHTML::_('select.option', 'Discover', JText::_( "DISCOVER" ) );
							$types[] = JHTML::_('select.option', 'DinersClub', JText::_( "DINERS_CLUB" ) );
							$types[] = JHTML::_('select.option', 'JCB', JText::_( "AUT_JCB" ) );
							$return = JHTML::_('select.genericlist', $types,'activated', 'style="width:200px;" tabindex="1"', 'value','text', $activated);
							echo $return; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="control-group">
						<label for="cardexp" class="control-label"><?php echo JText::_( 'EXPIRATION_DATE_IN_FORMAT_MMYY' ) ?> <span style="color:#ff0000;">*</span></label>
						<div class="controls" style="margin-left:5px;">
							<select id="expmonth" name="expmonth" style="width:100px;" class="inputbox required" tabindex="2">
								<option value=""><?php echo JText::_("MONTH");?></option>
								<option value="01" <?php echo ($expmonth == '01' ? 'selected' : '');?>><?php echo JText::_("JANUARY");?> (01)</option>
								<option value="02" <?php echo ($expmonth == '02' ? 'selected' : '');?>><?php echo JText::_("FEBRUARY");?> (02)</option>
								<option value="03" <?php echo ($expmonth == '03' ? 'selected' : '');?>><?php echo JText::_("MARCH");?> (03)</option>
								<option value="04" <?php echo ($expmonth == '04' ? 'selected' : '');?>><?php echo JText::_("APRIL");?> (04)</option>
								<option value="05" <?php echo ($expmonth == '05' ? 'selected' : '');?>><?php echo JText::_("MAY");?> (05)</option>
								<option value="06" <?php echo ($expmonth == '06' ? 'selected' : '');?>><?php echo JText::_("JUNE");?> (06)</option>
								<option value="07" <?php echo ($expmonth == '07' ? 'selected' : '');?>><?php echo JText::_("JULY");?> (07)</option>
								<option value="08" <?php echo ($expmonth == '08' ? 'selected' : '');?>><?php echo JText::_("AUGUST");?> (08)</option>
								<option value="09" <?php echo ($expmonth == '09' ? 'selected' : '');?>><?php echo JText::_("SEPTEMBER");?> (09)</option>
								<option value="10" <?php echo ($expmonth == '10' ? 'selected' : '');?>><?php echo JText::_("OCTOBER");?> (10)</option>
								<option value="11" <?php echo ($expmonth == '11' ? 'selected' : '');?>><?php echo JText::_("NOVEMBER");?> (11)</option>
								<option value="12" <?php echo ($expmonth == '12' ? 'selected' : '');?>><?php echo JText::_("DECEMBER");?> (12)</option>
							</select>&nbsp;
							<select id="expyear" name="expyear" style="width:80px;" class="inputbox required" tabindex="3">
								<option value=""><?php echo JText::_("YEAR");?></option>
								<?php for($i=date("Y"); $i<(date("Y")+10); $i++):?>
								<option value="<?php echo $i;?>" <?php echo ($expyear == $i ? 'selected' : '');?>><?php echo $i;?></option>
								<?php endfor;?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="control-group">
						<label for="cardnum" class="control-label"><?php echo JText::_( 'CARD_NUMBER' ) ?> <span style="color:#ff0000;">*</span></label>
						<div class="controls" style="margin-left:5px;"><input class="inputbox required" id="cardnum" type="text" name="cardnum" tabindex="4" size="35" style="width:190px;" value="<?php echo $cardnum;?>" /></div>
					</div>
				</div>
				<div class="span6">
					<div class="control-group">
						<label for="cardcvv" class="control-label"><?php echo JText::_( 'CARD_CVV_NUMBER' ) ?> <span style="color:#ff0000;">*</span></label>
						<div class="controls" style="margin-left:5px;"><input class="inputbox required" id="cardcvv" type="text" name="cardcvv" tabindex="5" maxlength="5" size="10" style="width:50px;" value="<?php echo $cardcvv;?>" /> <a href="#cvv" data-toggle="modal"><small>What's this?</small></a></div>

						<style type="text/css">.modal-backdrop {background: none;}</style>
						<section id="cvv" class="modal hide">
						  <div id="printThis">
						  	<div class="modal-header" style="border-bottom: none;">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							</div>
							<div class="modal-body">
								<img src="<?php echo JURI::root();?>components/com_digicom/assets/images/cvv3.jpg" style="max-width: 98%;">
							</div>
						  </div>
						</section>

					</div>
				</div>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span6">
				<h4 style="color:#666;"><img src="<?php echo JURI::root();?>/components/com_digicom/assets/images/home.png" align="absmiddle" style="margin-right:10px;" alt="" /> Billing Information:</h4>
			</div>
		</div>
		<div style="background:#f7f7f7;border-radius:5px;-webkit-border-radius:5px;-moz-border-radius:5px;padding-top:25px;">
			<div class="row-fluid">
				<div class="span6">
					<div class="control-group">
						<label for="cardfname" class="control-label"><?php echo JText::_( 'FIRST_NAME' ) ?> <span style="color:#ff0000;">*</span></label>
						<div class="controls" style="margin-left:5px;"><input class="inputbox required" id="cardfname" type="text" name="cardfname" tabindex="6" size="35" style="width:190px;" value="<?php echo $cardfname != '' ? $cardfname : $vars->user_firstname;?>" /></div>
					</div>
				</div>
				<div class="span6">
					<div class="control-group">
						<label for="cardcountry" class="control-label"><?php echo JText::_( 'COUNTRY' ) ?> <span style="color:#ff0000;">*</span></label>
						<div class="controls" style="margin-left:5px;">
							<select id="cardcountry" name="cardcountry" class="inputbox required" onchange="getStates();" tabindex="10" style="width:200px;">
								<option value=""></option><?php
								if ($configs->topcountries != '')
								{
									$countries_top = explode(",", $configs->topcountries);
									$where = '';
									foreach ($countries_top as $country)
									{ ?>
										<option value="<?php echo $country;?>" <?php echo $country == $cardcountry ? 'selected' : '';?>><?php echo $country;?></option><?php
									}
								} ?>
								<?php for($i=0; $i<count($countries); $i++):?>
								<option value="<?php echo $countries[$i]->country;?>"><?php echo $countries[$i]->country;?></option>
								<?php endfor;?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="control-group">
						<label for="cardlname" class="control-label"><?php echo JText::_( 'LAST_NAME' ) ?> <span style="color:#ff0000;">*</span></label>
						<div class="controls" style="margin-left:5px;"><input class="inputbox required" id="cardlname" type="text" name="cardlname" tabindex="7" size="35" style="width:190px;" value="<?php echo $cardlname != '' ? $cardlname : $vars->user_lastname;?>" /></div>
					</div>
				</div>
				<div class="span6">
					<div class="control-group">
						<label for="cardstate" class="control-label"><?php echo JText::_( 'STATE' ) ?> <span style="color:#ff0000;">*</span></label>
						<div class="controls" style="margin-left:5px;">
							<select id="cardstate" name="cardstate" class="inputbox required" tabindex="11" style="width:200px;"></select>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="control-group">
						<label for="email" class="control-label"><?php echo JText::_( 'EMAIL_ADDRESS' ) ?> <span style="color:#ff0000;">*</span></label>
						<div class="controls" style="margin-left:5px;"><input class="inputbox required" id="email" type="text" name="email" tabindex="8" size="35" style="width:190px;" value="<?php echo (isset( $email ) && $email!= '') ? $email : $vars->user_email;?>" /></div>
					</div>
				</div>
				<div class="span6">
					<div class="control-group">
						<label for="cardcity" class="control-label"><?php echo JText::_( 'CITY' ) ?> <span style="color:#ff0000;">*</span></label>
						<div class="controls" style="margin-left:5px;"><input class="inputbox required" id="cardcity" type="text" name="cardcity" tabindex="12" size="35" style="width:190px;" value="<?php echo $cardcity;?>" /></div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="control-group">
						<label for="cardaddress1" class="control-label"><?php echo JText::_( 'STREET_ADDRESS' ) ?> <span style="color:#ff0000;">*</span></label>
						<div class="controls" style="margin-left:5px;"><input class="inputbox required" id="cardaddress1" type="text" name="cardaddress1" tabindex="9" size="35" style="width:190px;" value="<?php echo $cardaddress1;?>" /></div>
					</div>
				</div>
				<div class="span6">
					<div class="control-group">
						<label for="cardzip" class="control-label"><?php echo JText::_( 'POSTAL_CODE' ) ?> <span style="color:#ff0000;">*</span></label>
						<div class="controls" style="margin-left:5px;"><input class="inputbox required" id="cardzip" type="text" name="cardzip" tabindex="13" size="10" style="width:190px;" value="<?php echo $cardzip;?>" /></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="form-actions">
			<input type="hidden" name="item_name" value="<?php echo $vars->item_name;?>" />
			<input type="hidden" name="amount" value="<?php echo $vars->amount;?>" />
			<input type="hidden" name="user_id" value="<?php echo $vars->user_id;?>" />
			<input type="hidden" name="return" value="<?php echo $vars->return;?>" />
			<input type="hidden" name="order_id" value="<?php echo $vars->order_id;?>" />
			<input type="hidden" name="plugin_payment_method" value="onsite" />
			<input type="submit" id="authorizebutton" name="submit" class="btn btn-warning btn-large" value="<?php echo JText::_('SUBMIT');?>" />
		</div>
	</div>

	</form>
</div>