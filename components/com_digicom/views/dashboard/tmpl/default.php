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

	<h3><?php echo JText::_("COM_DIGICOM_MY_ACCOUNT_DASHBOARD_TITLE"); ?></h3>
	<div class="row-fluid">
		<div class="span7">
			<p>Enjoy all the features that are available on your personal space to view, track and manage all your data.</p>
		</div>
		<div class="span5">
			<div class="customer-info">
				<h4>Abu Abrar</h4>
				<p>Customer ID: 98657<br>
				Email: digicom[at]demo.com<br>
				Customer since Feb 12, 2015
			</div>			
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