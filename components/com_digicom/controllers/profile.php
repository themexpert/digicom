<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

// TODO : PHP property visibility need to be added

class DigiComControllerProfile extends JControllerLegacy
{

	var $model = null;

	function __construct() {
		parent::__construct();

		$this->registerTask("add", "edit");
		$this->registerTask("", "login");
		$this->registerTask("register", "edit");
		$this->registerTask("saveCustomer", "save");
		$this->registerTask ("register", "loginRegister");
		$this->_model = $this->getModel('Customer');
	}

	function checkNextAction($err) {

		$Itemid = JRequest::getVar("Itemid", "0");
		$processor = JRequest::getVar("processor", "");
		$return = JRequest::getVar("return", "");

		if ((isset($err->message) && trim($err->message) != "") || $err === FALSE) {
			$link = JRoute::_("index.php?option=com_digicom&view=profile&layout=register&return=".$return."&Itemid=".$Itemid."&processor=".$processor);
			$this->setRedirect($link);
			return true;
		} else {

			$cart_model = $this->getModel("Cart");

			$configs = JComponentHelper::getComponent('com_digicom')->params;
			
			$this->_customer = new DigiComSiteHelperSession();
			$customer = $this->_customer;
			$items = $cart_model->getCartItems($customer, $configs);
			$tax = $cart_model->calc_price($items, $customer, $configs);

			if($tax["shipping"] != "0" || $tax["value"] != "0") {
				$link = "index.php?option=com_digicom&view=cart&layout=summary&Itemid=".$Itemid."&processor=" . $processor;
			} else {
				$link = "index.php?option=com_digicom&view=cart&layout=wait&Itemid=".$Itemid."&processor=" . $processor;
			}

			$link = "index.php?option=com_digicom&view=cart&layout=summary&Itemid=".$Itemid."&processor=" . $processor;
			

			$this->setRedirect($link);

			return true;
		}
	}

	function logCustomerIn()
	{
		$app = JFactory::getApplication("site");
		$Itemid = JRequest::getInt('Itemid', 0);
		$processor = JRequest::getVar("processor", "");
		$returnpage = JRequest::getVar("returnpage", "");
		if($return = JRequest::getVar('return', '', 'request', 'base64'))
		{
			$return = base64_decode($return);
		}
		$options = array();
		$options['remember'] = JRequest::getBool('remember', false);
		$options['return'] = $returnpage;

		$username = JRequest::getVar("username", "", 'request','username');
		$password = JRequest::getVar("passwd", "", 'post',JREQUEST_ALLOWRAW);

		$credentials = array();
		$credentials['username'] = $username; //JRequest::getVar('username', '', 'method', 'username');
		$credentials['password'] = $password; //JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);

		$err = $app->login($credentials, $options);
		
		$link = $this->getLink();
		if($returnpage != 'checkout'){
			$this->setRedirect($link);
			return true;
		}
		$this->checkNextAction($err);
		
	}

	function save()
	{
		global $Itemid;

		$conf = $this->getModel( "Config" );
		$configs = $conf->getConfigs();
		$returnpage = JRequest::getVar("returnpage", "");
		$redirect = 'downloads';

		if($configs->get('afterpurchase',1)) {
			$redirect = 'orders';
		}

		$link = $this->getLink($redirect);
		$err = $this->_model->store($error);
		
		if($returnpage == "login_register"){

			if($err["err"] === FALSE){
				$_SESSION["login_register_invalid"] = "notok";

				$msg = JText::_("DIGI_REGISTRATION_INVALID");

				$error = $err["error"];
				if(strpos($error, 'email') !== FALSE){
					$msg = JText::_("DIGI_REGISTRATION_INVALID_EMAIL");
				}
				elseif(strpos($error, 'ser name') !== FALSE){
					$msg = JText::_("DIGI_REGISTRATION_INVALID_USERNAME");
				}

				$firstname = JRequest::getVar("firstname", "");
				$lastname = JRequest::getVar("lastname", "");
				$company = JRequest::getVar("company", "");
				$email = JRequest::getVar("email", "");
				$username = JRequest::getVar("username", "");
				$password = JRequest::getVar("password", "");
				$password_confirm = JRequest::getVar("password_confirm", "");
				$address = JRequest::getVar("address", "");
				$city = JRequest::getVar("city", "");
				$zipcode = JRequest::getVar("zipcode", "");
				$country = JRequest::getVar("country", "");
				$state = JRequest::getVar("state", "");
				$array = array("firstname"=>$firstname, "lastname"=>$lastname, "company"=>$company, "email"=>$email, "username"=>$username, "password"=>$password, "password_confirm"=>$password_confirm, "address"=>$address, "city"=>$city, "zipcode"=>$zipcode, "country"=>$country, "state"=>$state);
				$_SESSION["new_customer"] = $array;

				$this->setRedirect($link, $msg, "notice");
				return false;
			}
			$this->checkNextAction($err["err"]);
			return true;
		}

		global $Itemid;
		if($err)
		{
			$msg = JText::_('DSCUSTOMERSAVED');
		}
		else{
			$msg = JText::_('DSCUSTOMERSAVEERR');
			$msg .= $error;
			$return = JRequest::getVar("returnpage", "");
		}
		$link = $this->getLink();
		$this->setRedirect($link);
	}

	function getLink($preturn = '')
	{
		$Itemid = JRequest::getInt('Itemid', 0);
		$processor = JRequest::getVar("processor", "");
		$return = JRequest::getVar("returnpage", "", "request");

		if (empty($return)) {
			$return = $preturn;
		}

		switch($return)
		{
			case "downloads":
				$link = "index.php?option=com_digicom&view=downloads" . "&Itemid=" . $Itemid;
				break;

			case "checkout":
				$link = "index.php?option=com_digicom&view=cart&layout=summary" . "&Itemid=" . $Itemid . "&processor=" . $processor;
				break;

			case "cart":
				$link = "index.php?option=com_digicom&view=cart"."&Itemid=".$Itemid . "&processor=" . $processor;
				break;

			case "order":
			case "orders":
				$link = "index.php?option=com_digicom&view=orders" . "&Itemid=" . $Itemid . "&processor=" . $processor;
				break;

			case "register":
			case "login_register":
				$link = "index.php?option=com_digicom&view=profile&layout=register&returnpage=register&Itemid=" . $Itemid . "&processor=" . $processor;
				break;

			default:
				$link = "index.php?option=com_digicom&view=dashboard&Itemid=" . $Itemid;
				break;
		}
		return $link;
	}

}

