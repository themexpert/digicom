<?php
/**
  DigiCom component
 * http://themexpert.com
 *
 * @copyright  (C) 2006-2012 ThemeXpert.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

defined ('DS') or define('DS', DIRECTORY_SEPARATOR);
/**
  Script file of DigiCom component
 */
class Com_DigiComInstallerScript
{

	private $installation_queue = array (
		// plugins => { (folder) => { (element) => (published) }* }*
		'plugins'=>array(
			'system'=>array(
				'jgive_api'=>1
			),
			'community'=>array(
				'jgive'=>0
			),
			'payment'=>array(
				'2checkout'=>0,
				'alphauserpoints'=>0,
				'authorizenet'=>0,
				'bycheck'=>0,
				'byorder'=>0,
				'ccavenue'=>0,
				'jomsocialpoints'=>0,
				'linkpoint'=>0,
				'paypal'=>1,
				'paypalpro'=>0,
				'payu'=>0
			)
		)
	);

	/**
	 *
	 * method to run before an install/update/unistall method
	 *
	 * return void
	 *
	 */
	function preflight($type, $parent)
	{
	}

	/**
	 *
	 * method to install the component
	 *
	 * return void
	 *
	 */
	function install($parent)
	{
		$db = JFactory::getDBO();

		// set Defailt Billing Address
		$sql = "UPDATE `#__digicom_settings` SET `tax_base` = '1' WHERE `id` = 1;";
		$db->setQuery($sql);
		$db->query();

		$template = '
			<table width="100%"  border="0" cellspacing="0" cellpadding="5">
				<tr valign="top">
					<td width="20%">{image} </td>
					<td width="52%">
						<table width="100%"  border="0" cellspacing="0" cellpadding="5">
							<tr valign="top">
								<td><b>Name: </b></td>
								<td>{name} </td>
							</tr>
				
							<tr valign="top">
								<td><b>Price:</b></td>
								<td>{price} </td>
							</tr>
							<tr valign="top">
								<td><b>Quantity:</b></td>
								<td>{qty}</td>
					
							</tr>
							<tr valign="top">
								<td><b>Short description:</b></td>
								<td>{short_description}</td>
							  </tr>
							<tr valign="top">
								<td><b>Full description:</b></td>
								<td>{full_description}</td>
							</tr>
					
							<tr valign="top">
								<td><b>Fields</b></td>
								<td>{fields}</td>
					
							</tr>
							<tr valign="top">
								<td>{addtocart}</td>
								<td>&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		';
		$mosConfig_mailfrom = "";
		$thankshtml = "Thank you for your order! We\'ve just sent you a confirmation email with your order details. Please download your product below and thank you for shopping at our store";
		$ftranshtml = "We are sorry, but it looks like your transaction failed. If you are sure that it is our fault and not one of choosen payment system, please feel free to <a href=\'mailto:".$mosConfig_mailfrom."\'>contact</a> our site\'s administrator.";
		$pendinghtml = "Thank you for submitting your order. We will charge you off line and you will get an email when your order is confirmed. ";

		$sql = "SELECT COUNT(*) FROM #__digicom_settings";
		$db->setQuery($sql);
		$num = $db->loadColumn();
		if ($num < 1) {
			$config = JFactory::getConfig();
			$sql = "
			INSERT IGNORE INTO `#__digicom_settings`
				(`id`, `currency`, `store_name`, `store_url`, `store_email`, `product_per_page`, `google_account`, 
				`country`, `state`, `city`, `tax_option`, `tax_rate`, `tax_type`, `totaldigits`, `decimaldigits`, 
				`ftp_source_path`, `time_format`, `afteradditem`, `showreplic`, `idevaff`, 
				`askterms`, `termsid`, `termsheight`, `termswidth`, `topcountries`, `usestoremail`, 
				`catlayoutstyle`, `catlayoutcol`, `catlayoutrow`, 
				`prodlayouttype`, `prodlayoutstyle`, `prodlayoutcol`, `prodlayoutrow`, 
				`orderidvar`, `ordersubtotalvar`, `idevpath`, `askforship`, `person`, `taxnum`, 
				`modbuynow`, `usecimg`, `showthumb`, `showsku`, `sendmailtoadmin`, `directfilelink`, 
				`debugstore`, `dumptofile`, `dumpvars`, `ftranshtml`, `thankshtml`, `showprodshort`, 
				`pendinghtml`, `address`, `zip`, `phone`, `fax`, `afterpurchase`, `showoid`, 
				`showoipurch`, `showolics`, `showopaid`, `showodate`, `showorec`, `showlid`, 
				`showlprod`, `showloid`, `showldate`, `showldown`, `showcam`, `showcpromo`, 
				`showcremove`, `showccont`, `showldomain`, 
				`tax_classes`, `tax_base`, `tax_catalog`, `tax_shipping`, `tax_discount`, `discount_tax`, 
				`tax_country`, `tax_state`, `tax_zip`, `tax_price`, `tax_summary`, `shipping_price`, 
				`product_price`, `tax_zero`, `tax_apply`, `usestorelocation`, `allowcustomerchoseclass`, 
				`takecheckout`, `continue_shopping_url`, `currency_position`, `showlterms`, `showlexpires`, 
				`storedesc`, `displaystoredesc`, `showfeatured`, `showrelated`, `hour24format`, 
				`imagecatsizevalue`, `imagecatsizetype`, `imageprodsizefullvalue`, `imageprodsizefulltype`, 
				`imageprodsizethumbvalue`, `imageprodsizethumbtype`, `imagecatdescvalue`, `imagecatdesctype`, 
				`imageproddescvalue`, `imageproddesctype`, `mailchimplistid`, `showpowered`, 
				`catlayoutimagesize`, `catlayoutimagetype`, `catlayoutdesclength`, `catlayoutdesctype`, 
				`prodlayoutdesclength`, `prodlayoutdesctype`, `showfeatured_prod`, `prodlayoutthumbnails`, 
				`prodlayoutthumbnailstype`, `prodlayoutlargeimgprev`, `prodlayoutlargeimgprevtype`, 
				`prodlayoutlightimgprev`, `prodlayoutlightimgprevtype`, `showshortdescription`, 
				`showlongdescription`, `showrelatedprod`)
			VALUES
				(1, 'USD', 'My Store Name', '".JURI::root()."', '".$config->get('mailfrom')."', 10, '', '', '', '', '', 0, '', 5, 2, 
				'media', 'MM-DD-YYYY', 2, 0, 'standalone', 0, 0, -1, -1, 'Canada,United-States', 1, 
				1, 3, 5,
				0, 1, 3, 10,
				'order_id', 'order_subtotal', '/aff', 0, 1, 0, 1, 0, 1, 1, 0, 0, 0, 0, '',
				'<p>We are sorry, but it looks like your transaction failed. It''s possible that our server didn''t receive the payment notification back from PayPal or Authorize.</p>\r\n<p>If you were charged, but no license was added to your account, please contact us and we will add a license to your account ASAP.</p>',
				'<p>Thank you for your order! We''ve just sent you a confirmation email with your order details. Please download your product below and thank you for shopping at our store.</p>\r\n<p><strong><span style=\"color: #ff0000\">NOTE:</span></strong> If you can''t see your license below, please wait 5 minutes and refresh the page. If you still can''t see it, please contact us, we will add the license to your account ASAP.</p>', 
				0, 
				'<p>Thank you for submitting your order. We will charge you off line and you will get an email when your order is confirmed.</p>',
				'', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 1, 0, 0, 1, 0,
				'United-States', 'All', '', 1, 1, 1, 1, 1, '0', 0, 2, 2, '', 0, 0, 0, 'Welcome to our store', 
				1, 0, 0, 0, 100, 0, 800, 1, 65, 0, 100, 1, 20,
				1, '2', 1, 100, 0, 20, 1, 20, 0, 1, 50, 1, 200, 0, 600, 0, 1, 1, 1);
			";

			$db->setQuery($sql);
			$db->query();
		}

		$sql = "SELECT pendinghtml FROM #__digicom_settings";
		$db->setQuery($sql);
		$ph = $db->loadResult();
		if (strlen(trim($ph)) < 1) {
			$sql = "UPDATE #__digicom_settings SET `pendinghtml`='".$pendinghtml."'";
			$db->setQuery($sql);
			$db->query();
		}


		$sql = "SELECT COUNT(*) FROM #__digicom_languages";
		$db->setQuery($sql);
		$lang_count = $db->loadColumn();
		if(intval($lang_count) <= 0){
			$code = "en-GB";
			$lang_file = "en-GB.com_digicom.ini";
			$sql = "INSERT INTO #__digicom_languages(`name`, `fefilename`, `befilename`) VALUES ('".$code."', '".$lang_file."', '".$lang_file."')";
			$db->setQuery($sql);
			$db->query();
		}

		$sql = "SELECT COUNT(*) FROM #__digicom_mailtemplates WHERE type='register'";
		$db->setQuery ($sql);
		$mailtemplates_count = $db->loadColumn();
		if ($mailtemplates_count == 0) {
			$confirmail = str_replace ("\n", "<br />", "Dear [CUSTOMER_FIRST_NAME],

						Thanks for registering at the [SITENAME] store! The next time you visit, use the following username and password to log in. You only have to register once. You can now also access all the features of [SITENAME] using the information below.

						Username: [CUSTOMER_USER_NAME]
						Password: [CUSTOMER_PASSWORD]

						Once again, thanks for registering at [SITENAME]. We hope to see you often.

						Sincerely,
						[SITENAME]
						[SITEURL]
						");
			//echo ("mail template issue");
			$sql = "INSERT INTO `#__digicom_mailtemplates` ( `id` , `type` , `subject`, `body` )
				VALUES (
 					1, 'register', 'Registration details', '".$confirmail."'
				);";
			$db->setQuery ($sql);
			$db->query();
		}

		$query = "SELECT COUNT(*) FROM #__digicom_mailtemplates WHERE type='order'";
		$db->setQuery ($query);
		$mailtemplates_count = $db->loadColumn();
		if ($mailtemplates_count == 0) {
			$ordermail = str_replace ("\n", "<br />","Dear [CUSTOMER_FIRST_NAME]

					Thank you for purchasing from [SITENAME]. We hope you'll enjoy our products.

					[ORDER_AMOUNT] has been charged to the account you used to make your purchase. Your order confirmation number is [ORDER_ID].

					Visit [SITEURL] often to check out new products and get the latest news. Remember, your username and password get you access to all the features of [SITENAME].

					Once again, thanks for buying from [SITENAME]. We value your business, and we hope to see you again soon.

					Best Regards,


					[SITENAME]
					[SITEURL] ");

			$sql = "INSERT INTO `#__digicom_mailtemplates` ( `id` , `type` , `subject`, `body` )
				VALUES (
 					2, 'order', 'Order details',  '".mysql_escape_string($ordermail)."'
				);";

			$db->setQuery ($sql);
			$db->query();
		}

		$query = "SELECT COUNT(*) FROM #__digicom_mailtemplates WHERE type='approved'";
		$db->setQuery ($query);
		$mailtemplates_count = $db->loadColumn();
		if ($mailtemplates_count == 0) {
			$approvedmail = str_replace ("\n", "<br />","We've received your order and your products are on their way ");

			$sql = "INSERT INTO `#__digicom_mailtemplates` ( `id` , `type` , `subject`, `body` )
				VALUES (
 					3, 'approved', 'Your order has been approved',  '".mysql_escape_string($approvedmail)."'
				);";

			$db->setQuery ($sql);
			$db->query();
		}

		jimport('joomla.filesystem.archive');
		$fe_path = JPATH_SITE.DS."components".DS."com_digicom".DS;
		//extract ioncube
		$be_path = JPATH_SITE.DS."administrator".DS."components".DS."com_digicom".DS; //dirname(__FILE__)."/administrator/components/com_digicom/";

// 		$licenses_updated = self::give_orders_to_licenses();
// 		if ($licenses_updated > 0){
// 			echo $licenses_updated." licenses were assigned orders.";
// 		}

		// self::updateAllTables2_0_0();
		//createDigiComMenu();
		// self::installDigiComPlugins();

		echo '
			<img src="'.JURI::root().'administrator/components/com_digicom/assets/images/logo.png"><br />
			<strong>Thanks for installing DigiCom!</strong>
			<div>
				<strong>For sample data:</strong>
				<ul>
					<li>Click to install <a href="http://themexpert.com/joomla/digicom/downloads/download/9_6f0f5a256ce478c1fd14b765f862d084?ebooks.zip" onclick="installFromUrl(this.href); return false;" />Ebook</a></li>
					<li>Click to install  <a href="http://themexpert.com/joomla/digicom/downloads/download/10_882c177d643950b952b2052c745a35b9?vm.zip" onclick="installFromUrl(this.href); return false;" />Virtuemart data</a></li>
				</ul>
			</div>
			<script>
				function installFromUrl(url){
					document.getElementById("install_url").value=url;
					Joomla.submitbutton4();
				}
			</script>
		';

		$db = JFactory::getDBO();
		$sql = "SELECT COUNT(*) FROM #__digicom_categories";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		if($result == 0){
			echo "<br/><br/><br/>";
			echo 	'<span style="font-size:18px; color:red;">Would you like to start selling awesome eBooks today? If yes, we will set up our default ebooks with the stores. You\'d have to still download the ebooks and upload them via FTP.
				</span>
				<br><br/>
				<span style="font-size:14px">
					Click <a href=index.php?option=com_digicom&controller=configs&task=install>here</a> to continue with eBooks included
					<br>
					Click <a href=index.php?option=com_digicom>here</a> to continue without eBooks</td>
				</span>';
		}
	}

	/**
	 *
	 * method to uninstall the component
	 *
	 * return void
	 *
	 */
	function uninstall($parent)
	{
		self::_uninstallModules();
		self::_uninstallPlugins();
		self::_uninstallPayments();
	}

	/**
	 *
	 * method to update the component
	 *
	 * return void
	 *
	 */
	function update($parent)
	{
		// self::updateAllTables2_0_0();
	}

	/**
	 *
	 * method to run after an install/update/unistall method
	 *
	 * return void
	 *
	 */
	function postflight($type, $parent)
	{
		if( $type == 'update' ) {
			self::updateAllTables2_0_0();
		}
		self::_installPlugins($type,$parent);
		self::_installModules($type,$parent);
	}


	function updateAllTables2_0_0()
	{
		$db = JFactory::getDBO();
		$sqlfile = dirname(__FILE__).DS.'admin'.DS.'sql'.DS.'update.sql';
		self::executeSqlFile( $sqlfile );
		$sql = "CREATE TABLE IF NOT EXISTS `#__digicom_product_groups` (`id_product` INT( 11 ) NOT NULL ,`id_group` INT( 11 ) NOT NULL ,PRIMARY KEY (  `id_product` ,  `id_group` ));";
		$db->setQuery($sql);
		$db->query();

		$sql = "CREATE TABLE IF NOT EXISTS `#__digicom_product_groups_exp` (`id_product` INT( 11 ) NOT NULL ,`id_group` INT( 11 ) NOT NULL ,PRIMARY KEY (  `id_product` ,  `id_group` ));";
		$db->setQuery($sql);
		$db->query();

		$sql = "SHOW COLUMNS FROM #__digicom_cart";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if(!in_array("plan_id", $result)){
			$sql = "ALTER TABLE `#__digicom_cart` ADD `plan_id` INT(11) NOT NULL DEFAULT '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("plugin_id", $result)){
			$sql = "ALTER TABLE `#__digicom_cart` ADD `plugin_id` INT(11) NOT NULL DEFAULT '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("renew", $result)){
			$sql = "ALTER TABLE `#__digicom_cart` ADD `renew` INT(11) NOT NULL DEFAULT '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("renewlicid", $result)){
			$sql = "ALTER TABLE `#__digicom_cart` ADD `renewlicid` INT(11) NOT NULL DEFAULT '0'";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------------
		$sql = "SHOW COLUMNS FROM #__digicom_categories";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if(!in_array("thumb", $result)){
			$sql = "ALTER TABLE `#__digicom_categories` ADD `thumb` VARCHAR(255) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------------
		$sql = "SHOW COLUMNS FROM #__digicom_emailreminders";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if(!in_array("period", $result)){
			$sql = "ALTER TABLE  `#__digicom_emailreminders` ADD `period` VARCHAR( 5 ) NULL ,
					ADD `calc` VARCHAR( 6 ) NULL ,
					ADD `date_calc` VARCHAR( 10 ) NULL";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------------
		$sql = "SHOW COLUMNS FROM #__digicom_featuredproducts";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if(!in_array("planid", $result)){
			$sql = "ALTER TABLE `#__digicom_featuredproducts` ADD `planid` INT(11) NOT NULL DEFAULT '0'";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------------
		$sql = "SHOW COLUMNS FROM #__digicom_licenses";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if(in_array("email", $result)){
			$sql = "ALTER TABLE #__digicom_licenses DROP COLUMN `email`";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("package_id", $result)){
			$sql = "ALTER TABLE `#__digicom_licenses` ADD `package_id` int(11) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("purchase_date", $result)){
			$sql = "ALTER TABLE `#__digicom_licenses` ADD `purchase_date` datetime NOT NULL default '0000-00-00 00:00:00'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("expires", $result)){
			$sql = "ALTER TABLE `#__digicom_licenses` ADD `expires` datetime NOT NULL default '0000-00-00 00:00:00'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("renew", $result)){
			$sql = "ALTER TABLE `#__digicom_licenses` ADD `renew` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("renewlicid", $result)){
			$sql = "ALTER TABLE `#__digicom_licenses` ADD `renewlicid` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("download_count", $result)){
			$sql = "ALTER TABLE `#__digicom_licenses` ADD `download_count` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("plan_id", $result)){
			$sql = "ALTER TABLE `#__digicom_licenses` ADD `plan_id` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("old_orders", $result)){
			$sql = "ALTER TABLE `#__digicom_licenses` ADD `old_orders` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("cancelled", $result)){
			$sql = "ALTER TABLE `#__digicom_licenses` ADD `cancelled` tinyint(1) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("cancelled_amount", $result)){
			$sql = "ALTER TABLE `#__digicom_licenses` ADD `cancelled_amount` float NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("domain_change", $result)){
			$sql = "ALTER TABLE `#__digicom_licenses` ADD `domain_change` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}

		$sql = "ALTER TABLE `#__digicom_licenses` MODIFY `dev_domain` text NOT NULL";
		$db->setQuery($sql);
		$db->query();

		//------------------------------------------------------------------------------------------
		$sql = "SHOW COLUMNS FROM #__digicom_orders";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if(in_array("payment_method", $result)){
			$sql = "ALTER TABLE #__digicom_orders DROP COLUMN `payment_method`";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("amount_paid", $result)){
			$sql = "ALTER TABLE `#__digicom_orders` ADD `amount_paid` float NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("processor", $result)){
			$sql = "ALTER TABLE `#__digicom_orders` ADD `processor` varchar(100) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("processor", $result)){
			$sql = "ALTER TABLE `#__digicom_orders` ADD `published` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("chargeback", $result)){
			$sql = "ALTER TABLE `#__digicom_orders` ADD `chargeback` tinyint(1) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("analytics", $result)){
			$sql = "ALTER TABLE `#__digicom_orders` ADD `analytics` tinyint(1) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
			$sql = "UPDATE `#__digicom_orders` SET `analytics`='1'";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------------
		$sql = "SHOW COLUMNS FROM #__digicom_products";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if(!in_array("sku", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `sku` varchar(100) NOT NULL default ''";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showqtydropdown", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `showqtydropdown` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("priceformat", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `priceformat` int(11) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("featured", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `featured` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prodimages", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `prodimages` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("defprodimage", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `defprodimage` varchar(500) NOT NULL default ''";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("mailchimplistid", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `mailchimplistid` varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("subtitle", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `subtitle` varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("mailchimpapi", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `mailchimpapi` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("mailchimplist", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `mailchimplist` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("mailchimpregister", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `mailchimpregister` int(3) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("mailchimpgroupid", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `mailchimpgroupid` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("video_url", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `video_url` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("video_width", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `video_width` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("video_height", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `video_height` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("offerplans", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `offerplans` int(3) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("hide_public", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `hide_public` tinyint(1) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("cartlinkuse", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `cartlinkuse` int(3) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("cartlink", $result)){
			$sql = "ALTER TABLE `#__digicom_products` ADD `cartlink` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------------
		$sql = "SHOW COLUMNS FROM #__digicom_session";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if(!in_array("processor", $result)){
			$sql = "ALTER TABLE `#__digicom_session` ADD `processor` varchar(250) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------------
		$sql = "SHOW COLUMNS FROM #__digicom_promocodes";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if(!in_array("validfornew", $result)){
			$sql = "ALTER TABLE `#__digicom_promocodes` ADD `validfornew` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();

			$sql = "UPDATE `#__digicom_promocodes` set `validfornew` = 1";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("validforrenewal", $result)){
			$sql = "ALTER TABLE `#__digicom_promocodes` ADD `validforrenewal` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}

		//------------------------------------------------------------------------------------------
		$sql = "ALTER TABLE `#__digicom_settings` MODIFY `in_trans` INT(10)";
		$db->setQuery($sql);
		$db->query();

		$sql = "SHOW COLUMNS FROM #__digicom_settings";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if(!in_array("catalogue", $result)){
			$sql = $sql = "ALTER TABLE `#__digicom_settings` ADD COLUMN `catalogue` INT(2) NULL DEFAULT 0  AFTER `currency`";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showviewproducts", $result)){
			$sql = $sql = "ALTER TABLE `#__digicom_settings` ADD COLUMN `showviewproducts` INT(2) NULL DEFAULT 1  AFTER `catalogue`";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("price_groups_separation", $result)){
			$sql = $sql = "ALTER TABLE `#__digicom_settings` ADD COLUMN `price_groups_separation` VARCHAR(2) NULL DEFAULT ','  AFTER `showviewproducts`";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showproductdetails", $result)){
			$sql = $sql = "ALTER TABLE `#__digicom_settings` ADD COLUMN `showproductdetails` INT(2) NOT NULL DEFAULT '1' AFTER `price_groups_separation`";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("continue_shopping_url", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `continue_shopping_url` varchar(255) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("currency_position", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `currency_position` int(1) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showlterms", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `showlterms` int(1) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showlexpires", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `showlexpires` int(1) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("storedesc", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `storedesc` mediumtext NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("displaystoredesc", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `displaystoredesc` int(11) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showfeatured", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `showfeatured` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showrelated", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `showrelated` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("hour24format", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `hour24format` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("imagecatsizevalue", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `imagecatsizevalue` int(11) NOT NULL default '100'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("imagecatsizetype", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `imagecatsizetype` int(11) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("imageprodsizefullvalue", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `imageprodsizefullvalue` int(11) NOT NULL default '300'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("imageprodsizefulltype", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `imageprodsizefulltype` int(11) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("imageprodsizethumbvalue", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `imageprodsizethumbvalue` int(11) NOT NULL default '65'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("imageprodsizethumbtype", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `imageprodsizethumbtype` int(11) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("imagecatdescvalue", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `imagecatdescvalue` int(11) NOT NULL default '10'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("imagecatdesctype", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `imagecatdesctype` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("imageproddescvalue", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `imageproddescvalue` int(11) NOT NULL default '10'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("imageproddesctype", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `imageproddesctype` int(11) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("mailchimplistid", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `mailchimplistid` varchar(255) default NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showpowered", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `showpowered` tinyint(4) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("catlayoutimagesize", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `catlayoutimagesize` int(10) NOT NULL default '100'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("catlayoutimagetype", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `catlayoutimagetype` int(10) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("catlayoutdesclength", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `catlayoutdesclength` int(10) NOT NULL default '20'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("catlayoutdesctype", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `catlayoutdesctype` int(10) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prodlayoutdesclength", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prodlayoutdesclength` int(10) NOT NULL default '20'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prodlayoutdesctype", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prodlayoutdesctype` int(10) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showfeatured_prod", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `showfeatured_prod` int(10) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prodlayoutthumbnails", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prodlayoutthumbnails` int(10) NOT NULL default '50'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prodlayoutthumbnailstype", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prodlayoutthumbnailstype` int(10) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prodlayoutlargeimgprev", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prodlayoutlargeimgprev` int(10) NOT NULL default '200'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prodlayoutlargeimgprevtype", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prodlayoutlargeimgprevtype` int(10) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prodlayoutlightimgprev", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prodlayoutlightimgprev` int(10) NOT NULL default '600'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prodlayoutlightimgprevtype", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prodlayoutlightimgprevtype` int(10) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showshortdescription", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `showshortdescription` int(10) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showlongdescription", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `showlongdescription` int(10) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showrelatedprod", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `showrelatedprod` int(10) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("last_check_date", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `last_check_date` datetime NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prodlayoutsort", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prodlayoutsort` int(10) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("relatedrows", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `relatedrows` int(10) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("relatedcolumns", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `relatedcolumns` int(10) NOT NULL default '3'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("grid_image_align", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `grid_image_align` int(10) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("grid_title_align", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `grid_title_align` int(10) NOT NULL default '2'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("grid_subtitle_align", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `grid_subtitle_align` int(10) NOT NULL default '2'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("grid_description_align", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `grid_description_align` int(10) NOT NULL default '2'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("grid_quantity_align", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `grid_quantity_align` int(10) NOT NULL default '2'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("grid_add_to_cat_align", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `grid_add_to_cat_align` int(10) NOT NULL default '2'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("list_multi_selection", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `list_multi_selection` int(10) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("list_orientation", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `list_orientation` int(10) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("featured_row", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `featured_row` int(10) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("featured_col", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `featured_col` int(10) NOT NULL default '3'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("store_logo", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `store_logo` varchar(255) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("shopping_cart_style", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `shopping_cart_style` int(3) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("cart_width", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `cart_width` varchar(255) NOT NULL default '100'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("cart_width_type", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `cart_width_type` int(10) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("cart_alignment", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `cart_alignment` int(10) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prod_short_desc_class", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prod_short_desc_class` varchar(255) NOT NULL default 'digi_short_desc_page'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prod_long_desc_class", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prod_long_desc_class` varchar(255) NOT NULL default 'digi_long_desc_page'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prods_short_desc_class", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prods_short_desc_class` varchar(255) NOT NULL default 'digi_short_desc_list'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prods_price_class", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prods_price_class` varchar(255) NOT NULL default 'digi_price_list'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("prods_name_class", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `prods_name_class` varchar(255) NOT NULL default 'digi_name_list'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("cart_popoup_image", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `cart_popoup_image` int(10) NOT NULL default '50'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("gallery_style", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `gallery_style` int(3) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("gallery_columns", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `gallery_columns` int(3) NOT NULL default '3'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("in_trans", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `in_trans` int(3) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("show_bradcrumbs", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `show_bradcrumbs` int(3) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("mailchimpapi", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `mailchimpapi` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("mailchimplist", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `mailchimplist` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showfacebook", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `showfacebook` int(2) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showtwitter", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `showtwitter` int(2) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showretwitter", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `showretwitter` int(2) NOT NULL default '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("tax_eumode", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `tax_eumode` TINYINT(1) NOT NULL DEFAULT '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("askforbilling", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `askforbilling` int(2) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("show_steps", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `show_steps` int(3) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("askforcompany", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `askforcompany` int(3) NOT NULL default '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("conversion_id", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `conversion_id` varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("conversion_language", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `conversion_language` varchar(255) NOT NULL default 'en'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("conversion_format", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `conversion_format` varchar(255) NOT NULL default '2'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("conversion_color", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `conversion_color` varchar(255) NOT NULL default 'ffffff'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("conversion_label", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `conversion_label` varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("default_payment", $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `default_payment` varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array('thousands_group_symbol', $result)){
			$sql = "ALTER TABLE `#__digicom_settings` ADD `thousands_group_symbol` varchar(5) NOT NULL default ','";
			$db->setQuery($sql);
			$db->query();
			
		}
		//------------------------------------------------------------------------------------------
		$sql = "SELECT COUNT(*) FROM #__digicom_plans";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		if($count <= 0){
			$sql = "INSERT INTO `#__digicom_plans` (`id`, `name`, `duration_count`, `duration_type`, `ordering`, `published`) VALUES
					(1, '5 Downloads Access', 5, 0, 6, 1),
					(2, '1 Month Access', 1, 3, 5, 1),
					(3, '3 Months Access', 3, 3, 4, 1),
					(4, '6 Months Access', 6, 3, 3, 1),
					(5, '1 Year Access', 1, 4, 2, 1),
					(6, 'Unlimited Access', -1, 0, 1, 1);";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------------
		$sql = "SELECT COUNT(*) FROM #__digicom_emailreminders";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		if($count <= 0){
			$sql = "INSERT INTO `#__digicom_emailreminders` (`id`, `name`, `type`, `subject`, `body`, `ordering`, `published`) VALUES
					(1, '2 days before expiration', 2, '[CUSTOMER_FIRST_NAME], your subscription to [PRODUCT_NAME] is about to expire', '<p>Dear [CUSTOMER_FIRST_NAME], <br /> <br /> Just a quick note to let you know that your subscription to  [PRODUCT_NAME], with license number [LICENSE_NUMBER], will expire in 2  days ([EXPIRE_DATE]). Please visit [SITENAME] and renew your  subscription here: <br /> <br /> [RENEW_URL]  <br /> <br /> Your username to login is: [CUSTOMER_USER_NAME] and your email in case your''ve forgotten your password is: [CUSTOMER_EMAIL]  <br /> <br /> We offer the following renewal plans: <br /> <br /> [RENEW_TERM] 	 <br /> <br /> You can always renew your subscription or view your licenses here: <br /> <br /> [MY_LICENSES] <br /> <br /> Thank you, <br /> [SITENAME] <br /> Admin</p>', 3, 1),
					(2, 'On expiration', 0, '[CUSTOMER_FIRST_NAME], your subscription to [PRODUCT_NAME] has expired', '<p>Dear [CUSTOMER_FIRST_NAME], <br /> <br /> Just a quick note to let you know that your subscription to  [PRODUCT_NAME], with license number [LICENSE_NUMBER], has expired on  ([EXPIRE_DATE]). Please visit [SITENAME] and renew your subscription  here: <br /> <br /> [RENEW_URL]  <br /> <br /> Your username to login is: [CUSTOMER_USER_NAME] and your email in case your''ve forgotten your password is: [CUSTOMER_EMAIL]  <br /> <br /> We offer the following renewal plans: <br /> <br /> [RENEW_TERM] 	 <br /> <br /> You can always renew your subscription or view your licenses here: <br /> <br /> [MY_LICENSES] <br /> <br /> Thank you, <br /> [SITENAME] <br /> Admin</p>', 2, 1),
					(3, '2 days after expiration', 7, '[CUSTOMER_FIRST_NAME], your subscription to [PRODUCT_NAME] is about to expire', '<p>Dear [CUSTOMER_FIRST_NAME], <br /> <br /> Just a quick note to let you know that your subscription to  [PRODUCT_NAME], with license number [LICENSE_NUMBER], has expired on  ([EXPIRE_DATE]). Please visit [SITENAME] and renew your subscription  here: <br /> <br /> [RENEW_URL]  <br /> <br /> Your username to login is: [CUSTOMER_USER_NAME] and your email in case your''ve forgotten your password is: [CUSTOMER_EMAIL]  <br /> <br /> We offer the following renewal plans: <br /> <br /> [RENEW_TERM] 	 <br /> <br /> You can always renew your subscription or view your licenses here: <br /> <br /> [MY_LICENSES] <br /> <br /> Thank you, <br /> [SITENAME] <br /> Admin</p>', 1, 1);";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------------
		// @TODO: Phong > doan nay khong biet nhet vao .SQL kieu j, P xem co can thiet khong!!!
		$sql = "SELECT *
			FROM #__digicom_emailreminders
			WHERE (`calc`='' OR `calc` IS NULL)
			  AND (`period`='' OR `period` IS NULL)
			  AND (`date_calc`='' OR `date_calc` IS NULL)";
		$db->setQuery($sql);
		$db->query();
		$emails = $db->loadObjectList();
		for ($i=0; $i<count($emails); $i++)
		{
			$email = $emails[$i];
			switch($email->type)
			{
				case 0:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='before',
							`period`='day',
							`date_calc`='expiration',
							`type`=0
						WHERE `id`=" . $email->id;
					break;
				case 1:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='before',
							`period`='day',
							`date_calc`='expiration',
							`type`=1
						WHERE `id`=" . $email->id;
					break;
				case 2:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='before',
							`period`='day',
							`date_calc`='expiration',
							`type`=2
						WHERE `id`=" . $email->id;
					break;
				case 3:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='before',
							`period`='day',
							`date_calc`='expiration',
							`type`=3
						WHERE `id`=" . $email->id;
					break;
				case 4:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='before',
							`period`='week',
							`date_calc`='expiration',
							`type`=1
						WHERE `id`=" . $email->id;
					break;
				case 5:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='before',
							`period`='week',
							`date_calc`='expiration',
							`type`=2
						WHERE `id`=" . $email->id;
					break;
				case 6:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='after',
							`period`='day',
							`date_calc`='expiration',
							`type`=1
						WHERE `id`=" . $email->id;
					break;
				case 7:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='after',
							`period`='day',
							`date_calc`='expiration',
							`type`=2
						WHERE `id`=" . $email->id;
					break;
				case 8:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='after',
							`period`='day',
							`date_calc`='expiration',
							`type`=3
						WHERE `id`=" . $email->id;
					break;
				case 9:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='after',
							`period`='week',
							`date_calc`='expiration',
							`type`=1
						WHERE `id`=" . $email->id;
					break;
				case 10:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='after',
							`period`='week',
							`date_calc`='expiration',
							`type`=2
						WHERE `id`=" . $email->id;
					break;
				case 11:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='after',
							`period`='week',
							`date_calc`='purchase',
							`type`=1
						WHERE `id`=" . $email->id;
					break;
				case 12:
					$sql = "UPDATE `#__digicom_emailreminders`
						SET `calc`='after',
							`period`='week',
							`date_calc`='purchase',
							`type`=2
						WHERE `id`=" . $email->id;
					break;
			}
			$db->setQuery($sql);
			$db->query();
		}


		$app = JFactory::getApplication();
		$db_prefix = $db->getPrefix();

		$sql = "SHOW TABLES LIKE '".$db_prefix."digicom_%plans'";
		$db->setQuery($sql);
		$tables = $db->loadColumn();

		if(!in_array( $db_prefix.'digicom_plans', $tables )) {
			$sql = 'ALTER TABLE `'.$db_prefix.'digicom_plains` 
						RENAME TO  `'.$db_prefix.'digicom_plans`';
			$db->setQuery($sql);
			$db->query();
			if($db->getErrorNum()){
				$app->enqueueMessage($db->getErrorMsg(),'error');
			}
		}

		if(!in_array( $db_prefix.'digicom_products_plans', $tables )) {
			$sql = 'ALTER TABLE `'.$db_prefix.'digicom_products_plains` 
						CHANGE COLUMN `plain_id` `plan_id` INT(11) NOT NULL  , 
						RENAME TO `'.$db_prefix.'digicom_products_plans`';
			$db->setQuery($sql);
			$db->query();
			if($db->getErrorNum()){
				$app->enqueueMessage($db->getErrorMsg(),'error');
			}
		}
		
		$sql = 'SHOW COLUMNS FROM '.$db_prefix.'digicom_products_renewals LIKE "plan_id"';
		$db->setQuery($sql);
		$column = $db->loadResult();
		if(!$column) {
			$sql = 'ALTER TABLE `'.$db_prefix.'digicom_products_renewals` CHANGE COLUMN `plain_id` `plan_id` INT(11) NOT NULL';
			$db->setQuery($sql);
			$db->query();
			if($db->getErrorNum()){
				$app->enqueueMessage($db->getErrorMsg(),'error');
			}
		}
		
		$sql = 'SHOW KEYS FROM `'.$db_prefix.'digicom_product_categories` WHERE Key_name = "PRIMARY"';
		$db->setQuery($sql);
		$keys = $db->loadColumn();
		if ( !count($keys)||!$keys ) {
			$sql = 'CREATE TABLE `'.$db_prefix.'digicom_product_categories2` AS (SELECT distinct * FROM `'.$db_prefix.'digicom_product_categories`)';
			$db->setQuery($sql);
			$db->query();
			if ( !$db->getErrorNum() ) {
				$sql = 'DROP TABLE `'.$db_prefix.'digicom_product_categories`';
				$db->setQuery($sql);
				$db->query();
				if( !$db->getErrorNum() ) {
					$sql = 'ALTER TABLE '.$db_prefix.'digicom_product_categories2 RENAME '.$db_prefix.'digicom_product_categories ,ADD PRIMARY KEY (`productid`, `catid`)';
					$db->setQuery($sql);
					$db->query();
				} else {
					$app->enqueueMessage($db->getErrorMsg(),'error');
				}
			} else {
				$app->enqueueMessage($db->getErrorMsg(),'error');
			}
		}

		$sql = "CREATE TABLE IF NOT EXISTS `#__digicom_products_files` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `product_id` int(11) NOT NULL DEFAULT '0',
				  `filename` varchar(255) NOT NULL,
				  `version` varchar(10) NOT NULL,
				  `changelog` varchar(1000),
				  `published` tinyint(1) NOT NULL DEFAULT '0',
				  `default` int(3) NOT NULL,
				  `ordering` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
		$db->setQuery($sql);
		$db->query();

//		$sql = 'SHOW COLUMNS FROM `#__digicom_customers`';
//		$db->setQuery($sql);
//		$fields = $db->loadColumn();
//		if(count($fields) && in_array('shipfirstname', $fields)){
//			$sql = 'ALTER TABLE `#__digicom_customers` ADD COLUMN `shipfirstname` VARCHAR(50) NOT NULL DEFAULT \'\'  AFTER `taxclass` , ADD COLUMN `shiplastname` VARCHAR(50) NOT NULL DEFAULT \'\'  AFTER `shipfirstname` , ADD COLUMN `shipphone` VARCHAR(45) NOT NULL DEFAULT \'\'  AFTER `shiplastname` , ADD COLUMN `phone` VARCHAR(45) NOT NULL DEFAULT \'\'  AFTER `shipphone`';
//			$db->setQuery($sql);
//			$db->execute();
//
//			$sql = 'ALTER TABLE `#__digicom_customers` 
//						ADD COLUMN `phone` VARCHAR(45) NOT NULL DEFAULT \'\' AFTER `payment_type`,
//						ADD COLUMN `shipprovince` VARCHAR(100) NULL DEFAULT NULL AFTER `shipstate`,
//						ADD COLUMN `shipphone` VARCHAR(45) NOT NULL DEFAULT \'\' AFTER `shipcountry`,
//						CHANGE COLUMN `taxnum` `taxnum` VARCHAR(11) NULL DEFAULT \'1\' AFTER `phone`,
//						CHANGE COLUMN `taxclass` `taxclass` INT(11) NOT NULL DEFAULT \'0\' AFTER `taxnum`,
//						CHANGE COLUMN `person` `person` INT(2) NOT NULL DEFAULT \'1\' AFTER `taxclass`,
//						CHANGE COLUMN `shipfirstname` `shipfirstname` VARCHAR(50) NOT NULL DEFAULT \'\' AFTER `person`,
//						CHANGE COLUMN `shiplastname` `shiplastname` VARCHAR(50) NOT NULL DEFAULT \'\' AFTER `shipfirstname`,
//						CHANGE COLUMN `shipaddress` `shipaddress` VARCHAR(200) NULL DEFAULT \'\';';
//			$db->setQuery($sql);
//			$db->execute();
//
//		}
		$sql ='SELECT count(*) FROM `#__digicom_currency_symbols`;';
		$db->setQuery($sql);
		$count = $db->loadResult();
		if(!$count){
			$sql = "insert into `#__digicom_currency_symbols` (`ccode`, `csym`, `cimg`) values 
							('ALL', '76, 101, 107', 'curSymbol76-101-107.gif'),
							('USD', '36', 'curSymbol36.gif'),
							('AFN', '1547', 'curSymbol1547.gif'),
							('ARS', '36', 'curSymbol36.gif'),
							('AWG', '402', 'curSymbol402.gif'),
							('AUD', '36', 'curSymbol36.gif'),
							('AZN', '1084, 1072, 1085', 'curSymbol1084-1072-1085.gif'),
							('BSD', '36', 'curSymbol36.gif'),
							('BBD', '36', 'curSymbol36.gif'),
							('BYR', '112, 46', 'curSymbol112-46.gif'),
							('BEF', '8355', 'curSymbol8355.gif'),
							('BZD', '66, 90, 36', 'curSymbol66-90-36.gif'),
							('BMD', '36', 'curSymbol36.gif'),
							('BOB', '36, 98', 'curSymbol36-98.gif'),
							('BAM', '75, 77', 'curSymbol75-77.gif'),
							('BWP', '80', 'curSymbol80.gif'),
							('BGN', '1083, 1074', 'curSymbol1083-1074.gif'),
							('BRL', '82, 36', 'curSymbol82-36.gif'),
							('BRC', '8354', 'curSymbol8354.gif'),
							('GBP', '163', 'curSymbol163.gif'),
							('BND', '36', 'curSymbol36.gif'),
							('KHR', '6107', 'curSymbol6107.gif'),
							('CAD', '36', 'curSymbol36.gif'),
							('KYD', '36', 'curSymbol36.gif'),
							('CLP', '36', 'curSymbol36.gif'),
							('CNY', '20803', 'curSymbol20803.gif'),
							('COP', '36', 'curSymbol36.gif'),
							('CRC', '8353', 'curSymbol8353.gif'),
							('HRK', '107, 110', 'curSymbol107-110.gif'),
							('CUP', '8369', 'curSymbol8369.gif'),
							('CYP', '163', 'curSymbol163.gif'),
							('CZK', '75, 269', 'curSymbol75-269.gif'),
							('DKK', '107, 114', 'curSymbol107-114.gif'),
							('DOP', '82, 68, 36', 'curSymbol82-68-36.gif'),
							('XCD', '36', 'curSymbol36.gif'),
							('EGP', '163', 'curSymbol163.gif'),
							('SVC', '36', 'curSymbol36.gif'),
							('GBP', '163', 'curSymbol163.gif'),
							('EEK', '107, 114', 'curSymbol107-114.gif'),
							('EUR', '8364', 'curSymbol8364.gif'),
							('XEU', '8352', 'curSymbol8352.gif'),
							('FKP', '163', 'curSymbol163.gif'),
							('FJD', '36', 'curSymbol36.gif'),
							('FRF', '8355', 'curSymbol8355.gif'),
							('GHC', '162', 'curSymbol162.gif'),
							('GIP', '163', 'curSymbol163.gif'),
							('GRD', '8367', 'curSymbol8367.gif'),
							('GTQ', '81', 'curSymbol81.gif'),
							('GGP', '163', 'curSymbol163.gif'),
							('GYD', '36', 'curSymbol36.gif'),
							('NLG', '402', 'curSymbol402.gif'),
							('HNL', '76', 'curSymbol76.gif'),
							('HKD', '72, 75, 36', 'curSymbol72-75-36.gif'),
							('HKD', '22291', 'curSymbol22291.gif'),
							('HKD', '22291', 'curSymbol22291.gif'),
							('HKD', '20803', 'curSymbol20803.gif'),
							('HUF', '70, 116', 'curSymbol70-116.gif'),
							('ISK', '107, 114', 'curSymbol107-114.gif'),
							('INR', '8360', 'curSymbol8360.gif'),
							('IDR', '82, 112', 'curSymbol82-112.gif'),
							('IRR', '65020', 'curSymbol65020.gif'),
							('IEP', '163', 'curSymbol163.gif'),
							('IMP', '163', 'curSymbol163.gif'),
							('ILS', '8362', 'curSymbol8362.gif'),
							('ITL', '8356', 'curSymbol8356.gif'),
							('JMD', '74, 36', 'curSymbol74-36.gif'),
							('JPY', '165', 'curSymbol165.gif'),
							('JEP', '163', 'curSymbol163.gif'),
							('KZT', '1083, 1074', 'curSymbol1083-1074.gif'),
							('KPW', '8361', 'curSymbol8361.gif'),
							('KRW', '8361', 'curSymbol8361.gif'),
							('KGS', '1083, 1074', 'curSymbol1083-1074.gif'),
							('LAK', '8365', 'curSymbol8365.gif'),
							('LVL', '76, 115', 'curSymbol76-115.gif'),
							('LBP', '163', 'curSymbol163.gif'),
							('LRD', '36', 'curSymbol36.gif'),
							('CHF', '67, 72, 70', 'curSymbol67-72-70.gif'),
							('LTL', '76, 116', 'curSymbol76-116.gif'),
							('LUF', '8355', 'curSymbol8355.gif'),
							('MKD', '1076, 1077, 1085', 'curSymbol1076-1077-1085.gif'),
							('MYR', '82, 77', 'curSymbol82-77.gif'),
							('MTL', '76, 109', 'curSymbol76-109.gif'),
							('MUR', '8360', 'curSymbol8360.gif'),
							('MXN', '36', 'curSymbol36.gif'),
							('MNT', '8366', 'curSymbol8366.gif'),
							('MZN', '77, 84', 'curSymbol77-84.gif'),
							('NAD', '36', 'curSymbol36.gif'),
							('NPR', '8360', 'curSymbol8360.gif'),
							('ANG', '402', 'curSymbol402.gif'),
							('NLG', '402', 'curSymbol402.gif'),
							('NZD', '36', 'curSymbol36.gif'),
							('NIO', '67, 36', 'curSymbol67-36.gif'),
							('NGN', '8358', 'curSymbol8358.gif'),
							('KPW', '8361', 'curSymbol8361.gif'),
							('NOK', '107, 114', 'curSymbol107-114.gif'),
							('OMR', '65020', 'curSymbol65020.gif'),
							('PKR', '8360', 'curSymbol8360.gif'),
							('PAB', '66, 47, 46', 'curSymbol66-47-46.gif'),
							('PYG', '71, 115', 'curSymbol71-115.gif'),
							('PEN', '83, 47, 46', 'curSymbol83-47-46.gif'),
							('PHP', '80, 104, 112', 'curSymbol80-104-112.gif'),
							('PLN', '122, 322', 'curSymbol122-322.gif'),
							('QAR', '65020', 'curSymbol65020.gif'),
							('RON', '108, 101, 105', 'curSymbol108-101-105.gif'),
							('RUB', '1088, 1091, 1073', 'curSymbol1088-1091-1073.gif'),
							('SHP', '163', 'curSymbol163.gif'),
							('SAR', '65020', 'curSymbol65020.gif'),
							('RSD', '1044, 1080, 1085, 46', 'curSymbol1044-1080-1085-46.gif'),
							('SCR', '8360', 'curSymbol8360.gif'),
							('SGD', '36', 'curSymbol36.gif'),
							('SKK', '83, 73, 84', 'curSymbol83-73-84.gif'),
							('EUR', '8364', 'curSymbol8364.gif'),
							('SBD', '36', 'curSymbol36.gif'),
							('SOS', '83', 'curSymbol83.gif'),
							('ZAR', '82', 'curSymbol82.gif'),
							('KRW', '8361', 'curSymbol8361.gif'),
							('ESP', '8359', 'curSymbol8359.gif'),
							('LKR', '8360', 'curSymbol8360.gif'),
							('SEK', '107, 114', 'curSymbol107-114.gif'),
							('CHF', '67, 72, 70', 'curSymbol67-72-70.gif'),
							('SRD', '36', 'curSymbol36.gif'),
							('SYP', '163', 'curSymbol163.gif'),
							('TWD', '78, 84, 36', 'curSymbol78-84-36.gif'),
							('THB', '3647', 'curSymbol3647.gif'),
							('TTD', '84, 84, 36', 'curSymbol84-84-36.gif'),
							('TRY', '89, 84, 76', 'curSymbol89-84-76.gif'),
							('TRL', '8356', 'curSymbol8356.gif'),
							('TVD', '36', 'curSymbol36.gif'),
							('UAH', '8372', 'curSymbol8372.gif'),
							('GBP', '163', 'curSymbol163.gif'),
							('USD', '36', 'curSymbol36.gif'),
							('UYU', '36, 85', 'curSymbol36-85.gif'),
							('UZS', '1083, 1074', 'curSymbol1083-1074.gif'),
							('VAL', '8356', 'curSymbol8356.gif'),
							('VEB', '66, 115', 'curSymbol66-115.gif'),
							('VND', '8363', 'curSymbol8363.gif'),
							('YER', '65020', 'curSymbol65020.gif'),
							('ZWD', '90, 36', 'curSymbol90-36.gif');";
			$db->setQuery($sql);
			$db->execute();
		}
		
		
	}

	/**
	 * Install associated plugins to work with DigiCom
	 * @param unknown $type
	 * @param unknown $parent
	 */
	private function _installPlugins($type, $parent){
		$exts = array(
			'digicomcron'=>0,
			'jw_allvideos'=>0,
			'plg_search_dsc'=>0,
			'plg_search_dsp'=>0,
			'plg_system_digicom_addtocart'=>0
		);

		foreach ($exts as $ext => $enable ) {
			$installer 	= new JInstaller();
			$path 	= JPath::clean(dirname(__FILE__).DS.'admin'.DS.'extras'.DS.'plugins'.DS.$ext);
			if(JFolder::exists($path)){
				$res = $installer->install($path);
				if ( !$res ) {
					echo $path;
				} elseif( $enable ) {
					#TODO: enable plugin
				}
			} else {
				echo $path.'<br/>';
			}
		}
		self::_installPayments($type);
	}
	
	private function _installPayments($type){
		$default = array('paypal','paypalpro');
		$exts_path = dirname(__FILE__).DS.'admin'.DS.'extras'.DS.'plugins'.DS.'payment';
		$exts = JFolder::folders( $exts_path, '.', false, true );
		foreach( $exts as $ext ) {
			if(JFolder::exists($ext)){
				$installer 	= new JInstaller();
				$installer->install( $ext );
			}else{
				echo $ext.'<br/>';
			}
		}
		if($type=='install'){
			#TODO enable default payment
			$app 	= JFactory::getApplication();
			$db 	= JFactory::getDbo();

			$default_exts = "'".implode("','",$default)."'";
			$sql = 'UPDATE 
						#__extensions
					SET `enabled` = 1
					WHERE
						`type` = "plugin"
							AND `folder` = "payment"
							AND `element` in ('.$default_exts.')';

			$db->setQuery($sql);
			$db->query();
			if($db->getErrorNum()){
				$app->enqueueMessage($db->getErrorMsg());
			}
		}
	}
	
	/**
	 * Install associated modules to work with DigiCom
	 * @param unknown $type
	 * @param unknown $parent
	 */
	private function _installModules($type,$parent){
		$exts_path 	= dirname(__FILE__).DS.'admin'.DS.'extras'.DS.'modules';
		$exts 	= JFolder::folders( $exts_path, '.', false, true );
		foreach( $exts as $ext ) {
			
			if(JFolder::exists($ext)){
				$installer 	= new JInstaller();
				$res = $installer->install( $ext );
				if(!$res){
					echo $ext;
				}
			}else{
				echo $ext.'<br/>';
			}
		}
	}
	
	private function _uninstallModules(){
		$db = JFactory::getDbo();
		$sql = 'SELECT * FROM 
					`#__extensions` 
				WHERE 
					`type` = "module"
						AND `element` in ("mod_digicom_cart",
											"mod_digicom_categories",
											"mod_digicom_google",
											"mod_digicom_manager");';
		$db->setQuery($sql);
		$exts = $db->loadObjectList();
		if(count($exts)){
			foreach( $exts as $ext ){
				$installer 	= new JInstaller();
				$installer->uninstall( $ext->type, $ext->extension_id );
			}
		}
	}

	private function _uninstallPlugins(){
		$db = JFactory::getDbo();
		$sql = 'SELECT * FROM 
					`#__extensions` 
				WHERE 
					(`type` = "plugin"
						AND `folder`="system"
						AND `element` in ("cron",
											"digicom_addtocart"))
					OR
					(`type` = "plugin"
						AND `folder`="search"
						AND `element` in ("dsc", "dsp"))';
		$db->setQuery($sql);
		$exts = $db->loadObjectList();
		if(count($exts)){
			foreach( $exts as $ext ){
				$installer 	= new JInstaller();
				$installer->uninstall( $ext->type, $ext->extension_id );
			}
		}
	}

	private function _uninstallPayments(){
		$db = JFactory::getDbo();
		$sql = 'SELECT * FROM 
					`#__extensions` 
				WHERE 
					`type` = "plugin"
						AND `folder` = "payment";';
		$db->setQuery($sql);
		$exts = $db->loadObjectList();
		if(count($exts)){
			foreach( $exts as $ext ){
				$installer 	= new JInstaller();
				$installer->uninstall( $ext->type, $ext->extension_id );
			}
		}
	}
	
	private function executeSqlFile( $sqlfile ){
		$app 	= JFactory::getApplication();
		$db 	= JFactory::getDbo();
		$sql 	= JFile::read( $sqlfile );
		$queries = $db->splitSql($sql);
		if( $queries && count($queries) ) {
			foreach ( $queries AS $query ) {
				$db->setQuery($query);
				$db->query();
				if($db->getErrorNum()){
					$app->enqueueMessage($db->getErrorMsg(),'error');
				}
			}
		}
	}
}
