<?php
/**

 *
 * @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 421 $
 * @lastmodified	$LastChangedDate: 2013-11-16 11:22:16 +0100 (Sat, 16 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
 * @license


 */

defined ('_JEXEC') or die ("Go away.");

jimport('joomla.application.component.controller');

class DigiComControllerProfile extends DigiComController
{

	var $model = null;

	function __construct() {
		parent::__construct();

		$this->registerTask("add", "edit");
		$this->registerTask("", "edit");
		$this->registerTask("register", "edit");
		$this->registerTask("saveCustomer", "save");
		$this->registerTask ("login_register", "loginRegister");
		$this->_model = $this->getModel('Customer');
	}

	function edit() {
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		//$this->setRedirect(JRoute::_('index.php?option=com_users&view=profile&layout=edit'));
		if(!$user->id) {
			$app->redirect(JRoute::_('index.php?option=com_digicom&controller=profile&task=login&returnpage=licenses'));
		}
		$view = $this->getView("Profile", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("Config");
		$view->setModel($model);
		//$model = $this->getModel("Plugin");
		//$view->setModel($model);
		$view->editForm();
	}

	function loginRegister() {
		$view = $this->getView("Profile", "html");
		$view->setLayout("login_register");
		$view->loginRegister();
	}

	function login() {
		if(!$this->_customer->_user->id){
			$returnpage = JRequest::getVar("returnpage", "");
			$this->setRedirect('index.php?option=com_users&view=login'.($returnpage ? '&return='.base64_encode('index.php?option=com_digicom&controller='.$returnpage) : ''));
			$view = $this->getView("Profile", "html");
			$view->setLayout("login");
			$view->setModel($this->_model, true);
			$model = $this->getModel("Config");
			$view->setModel($model);
			$view->login();
		} else {
			//$link = "index.php?option=com_digicom&controller=cart&task=checkout";
			$link = $this->getLink();
			$this->setRedirect(JRoute::_($link, false));
		}
	}

	function checkNextAction($err) {
		$Itemid = JRequest::getVar("Itemid", "0");
		$processor = JRequest::getVar("processor", "");
		if (trim($err->message) != "" || $err === FALSE) {
			$link = JRoute::_("index.php?option=com_digicom&controller=profile&task=login_register&returnpage=login_register&Itemid=".$Itemid."&processor=".$processor);
			$this->setRedirect($link);
			return true;
		} else {
			$cart_model = $this->getModel("Cart");
			$config = $this->getModel("Config");
			$configs = $config->getConfigs();
			$this->_customer = new DigiComSessionHelper();
			$customer = $this->_customer;
			$items = $cart_model->getCartItems($customer, $configs);
			$tax = $cart_model->calc_price($items, $customer, $configs);
			$link = "";

			if($tax["shipping"] != "0" || $tax["value"] != "0") {
				$link = "index.php?option=com_digicom&controller=cart&task=summary&Itemid=".$Itemid."&processor=" . $processor;
			} else {
				$link = "index.php?option=com_digicom&controller=cart&task=wait&Itemid=".$Itemid."&processor=" . $processor;
			}
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

		$username = JRequest::getVar("username", "", 'request');
		$password = JRequest::getVar("passwd", "", 'request');

		$credentials = array();
		$credentials['username'] = $username; //JRequest::getVar('username', '', 'method', 'username');
		$credentials['password'] = $password; //JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);

		$err = $app->login($credentials, $options);
		$link = $this->getLink();

		if($returnpage == "login_register")
		{
			$this->checkNextAction($err);
			return true;
		}

		if(!isset($err->message))
		{
			// Set customer groups
			require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'helper.php' );
			$my = JFactory::getUser();
			DigiComAdminHelper::expireUserProduct($my->id);

			$this->setRedirect($link);
		}
		else
		{
			$cid = JRequest::getInt('cid', 0);
			$pid = JRequest::getInt('pid', 0);
			$this->setRedirect(JRoute::_("index.php?option=com_digicom&controller=profile&task=login&returnpage=checkout&cid=" . $cid . "&pid=" . $pid . "&Itemid=" . $Itemid . "&processor=" . $processor));
		}
	}

	function save()
	{
		global $Itemid;

		$conf = $this->getModel( "Config" );
		$configs = $conf->getConfigs();
		$returnpage = JRequest::getVar("returnpage", "");
		$redirect = 'licenses';

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
		$url = JRoute::_($link);
		$this->setRedirect($url);
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
			case "licensesreg":
				$licid = JRequest::getVar("licid", "0");
				$link = "index.php?option=com_digicom&controller=licenses&task=register&licid=".$licid."&no_html=1&tmpl=component&Itemid=" . $Itemid . "&processor=" . $processor;
				break;

			case "digicomLicenses":
			case "licenses":
				$link = "index.php?option=com_digicom&controller=licenses" . "&Itemid=" . $Itemid . "&processor=" . $processor;
				break;

			case "checkout":
				$link = "index.php?option=com_digicom&controller=cart&task=checkout" . "&Itemid=" . $Itemid . "&processor=" . $processor;
				break;

			case "cart":
				$link = "index.php?option=com_digicom&controller=cart&task=view"."&Itemid=".$Itemid . "&processor=" . $processor;
				break;

			case "orders":
				$link = "index.php?option=com_digicom&controller=orders&task=list" . "&Itemid=" . $Itemid . "&processor=" . $processor;
				break;

			case "login_register":
				$link = "index.php?option=com_digicom&controller=profile&task=login_register&returnpage=login_register&Itemid=" . $Itemid . "&processor=" . $processor;
				break;

			default:
				$link = "index.php?option=com_digicom&controller=profile&task=edit&Itemid=" . $Itemid . "&processor=" . $processor;
				break;
		}
		return $link;
	}

}

