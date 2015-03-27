<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelCustomer extends JModelItem {
	var $_customers;
	var $_customer;
	var $_id = null;

	function __construct() {
		parent::__construct();
		$cids = JRequest::getVar( 'cid', 0, '', 'array' );
		$this->setId( (int) $cids[0] );
	}


	function setId( $id ) {
		$this->_id       = $id;
		$this->_customer = null;
	}


	function getlistCustomers() {
		if ( empty ( $this->_customers ) ) {
			$sql              = "select c.*, u.username, u.email from #__digicom_customers c left join #__users u on( u.id=c.id)";
			$this->_customers = $this->_getList( $sql );

		}

		return $this->_customers;

	}

	function getCustomer( $cid = 0 ) {
		if ( ! empty ( $this->_customer ) || $cid > 0 ) {
			$user            = JFactory::getUser();
			$this->_id       = intval( $cid );
			$this->_customer = $this->getTable( "Customer" );
			$this->_customer->load( $this->_id );

			if ( $this->_customer->firstname == '' ) {
				$name                       = $this->getPartName( $user->name );
				$this->_customer->firstname = $name->firstname;
				$this->_customer->lastname  = $name->lastname;
			}

			if ( intval( $cid ) == 0 ) {
				$reg             = JSession::getInstance( "none", array() );//new JSession();
				$x               = $reg->get( "tmp_profile" );
				$this->_customer = $this->getTable( "Customer" );
				$this->_customer->bind( $x );
				$this->_customer->username = $x['username'];
				$this->_customer->email    = $x['email'];
			}
		}

		return $this->_customer;

	}

	function store( &$error ) {

		jimport( "joomla.database.table.user" );
		$db = JFactory::getDBO();
		$my = JFactory::getUser();
		if ( ! $my->id ) {
			$new_user = 1;
		} else {
			$new_user = 0;
		}
		$item = $this->getTable( 'Customer' );
		$data = $_POST;
		/*if(count($data) <= 0 || !isset($data["username"])){
			$data = JRequest::get('get');
		}*/

		$data['password1'] = $data['password'];
		$data['password2'] = $data['password_confirm'];
		$data['name']      = $data['firstname'];
		$res               = array( "err" => true, "error" => "" );
		$reg               = JSession::getInstance( "none", array() );//new JSession();

		//$user = new JUser();
		$userId = ( ! empty( $data['id'] ) ) ? $data['id'] : (int) $this->getState( 'user.id' );
		$user   = new JUser( $userId );

		$groups         = array( "0" => "2" );
		$data["groups"] = $groups;

		if ( $my->id ) {
			unset( $data["groups"] );
			unset( $data["username"] );
			$data["email1"] = $data["email"];
			$data["email2"] = $data["email"];
		}

		$user->bind( $data );
		$user->gid      = 2;
		$user->usertype = "Registered";

		if ( ! $user->save() ) {
			$reg->set( "tmp_profile", $data );
			$error        = $user->getError();
			$res["err"]   = false;
			$res["error"] = $error;
			//$res = false;
		}

		if ( ! $user->id ) {
			return $res;
		}

		$data['id'] = $user->id;

		if ( ! $item->bind( $data ) ) {
			echo "bind failed";
			$error        = $user->getError();
			$res["err"]   = false;
			$res["error"] = $error;
			//$res = false;
		}

		if ( ! $item->check() ) {
			echo "check failed";
			$error        = $user->getError();
			$res["err"]   = false;
			$res["error"] = $error;
			//$res = false;
		}

		if ( ! $item->store() ) {
			echo "store failed";
			$error        = $user->getError();
			$res["err"]   = false;
			$res["error"] = $error;
			//$res = false;
		}

		//generate session;
		$customer = new DigiComSiteHelperSession();

		if ( ! $res["err"] ) {
			$reg->set( "tmp_profile", $data );

			return $res;
		}

		$app = JFactory::getApplication();
		if ( $return = JRequest::getVar( 'return', '', 'method', 'base64' ) ) {
			$return = base64_decode( $return );
		}

		if ( ! $my->id ) {
			$options                 = array();
			$options['remember']     = JRequest::getBool( 'remember', false );
			$options['return']       = $return;
			$credentials             = array();
			$credentials['username'] = $user->username;
			$credentials['password'] = $user->password_clear;
			$err                     = $app->login( $credentials, $options );
		}

		if ( $res["err"] ) {
			$reg->clear( "tmp_profile" );
		}

		if ( $res["err"] && $new_user ) {
			$this->sendRegConfirm( $data );
		}



		return $res;
	}


	function getlistCustomerClasses() {
		$sql = "select * from #__digicom_tax_customerclass order by ordering asc";
		$db  = JFactory::getDBO();
		$db->setQuery( $sql );

		return ( $db->loadObjectList() );
	}

	function sendRegConfirm( $customer ) {
		$cust_info = $customer;

		$my       = JFactory::getUser();
		$database = JFactory::getDBO();
		$cart     = $this->getInstance( "cart", "digicomModel" );
		$configs  = $this->getInstance( "Config", "digicomModel" );
		$configs  = $configs->getConfigs();

		$mes                  = new stdClass();

		$mes->body = "Template is empty";
		$sql       = "SELECT * FROM #__digicom_mailtemplates where `type`='register'";
		$database->setQuery( $sql );
		$db = JFactory::getDBO();
		$db->setQuery( $sql );
		$mes     = $db->loadObjectList();
		$mes     = $mes[0];
		$message = $mes->body;
		JTable::addIncludePath( JPATH_COMPONENT_SITE . DS . 'tables' );
		$email        = $this->getTable( "Mail" );
		$date         = JFactory::getDate();
		$timestamp    = $date->toUnix();
		$email->date  = $timestamp;
		$email->flag  = "register";
		$email->email = trim( $my->email );

		$subject = $mes->subject;
		// Replace all variables in template
		$flag  = "order";
		$promo = $cart->get_promo( $cust_info );
		if ( $promo->id > 0 ) {
			$promoid   = $promo->id;
			$promocode = $promo->code;
		} else {
			$promoid   = '0';
			$promocode = '0';
		}

		

		$app      = JFactory::getApplication();
		$sitename = ( trim( $configs->get('store_name','DigiCom Store') ) != '' ) ? $configs->get('store_name','DigiCom Store') : $app->getCfg( 'sitename' );
		$siteurl  = ( trim( $configs->get('store_url','') ) != '' ) ? $configs->get('store_url','') : $mosConfig_live_site;
		$ship_add = DigiComSiteHelperDigiCom::get_customer_shipping_add( $my->id );
		$message  = str_replace( "[SHIPPING_ADDRESS]", $ship_add, $message );
		$message  = str_replace( "[SITENAME]", $sitename, $message );
		$message  = str_replace( "[CUSTOMER_COMPANY_NAME]", $my->company, $message );
		$message  = str_replace( "../%5BSITEURL%5D", $siteurl, $message );
		$message  = str_replace( "%5BSITEURL%5D", $siteurl, $message );
		$message  = str_replace( "[SITEURL]", $siteurl, $message );

		$query = "select `lastname` from `#__digicom_customers` where `id`=" . $my->id;
		$database->setQuery( $query );
		$lastname = $database->loadResult();

		$message = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $message );
		$message = str_replace( "[CUSTOMER_FIRST_NAME]", $my->name, $message );
		$message = str_replace( "[CUSTOMER_LAST_NAME]", $lastname, $message );
		$message = str_replace( "[CUSTOMER_EMAIL]", $my->email, $message );

		$message      = str_replace( "[TODAY_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp ), $message );
		$message      = str_replace( "[CUSTOMER_PASSWORD]", $customer['password_confirm'], $message );
		$displayed    = array();
		$product_list = '';


		$email->body = $message;

		//subject
		$subject = str_replace( "[SHIPPING_ADDRESS]", $ship_add, $subject );
		$subject = str_replace( "[SITENAME]", $sitename, $subject );
		$subject = str_replace( "[CUSTOMER_COMPANY_NAME]", $my->copany, $subject );
		$subject = str_replace( "../%5BSITEURL%5D", $siteurl, $subject );
		$subject = str_replace( "%5BSITEURL%5D", $siteurl, $subject );
		$subject = str_replace( "[SITEURL]", $siteurl, $subject );

		$subject = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $subject );
		$subject = str_replace( "[CUSTOMER_FIRST_NAME]", $my->name, $subject );
		$subject = str_replace( "[CUSTOMER_LAST_NAME]", $lastname, $subject );
		$subject = str_replace( "[CUSTOMER_EMAIL]", $my->email, $subject );

		$subject = str_replace( "[TODAY_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp ), $subject );
		$subject = str_replace( "[CUSTOMER_PASSWORD]", $customer['password_confirm'], $subject );

		$subject = html_entity_decode( $subject, ENT_QUOTES );

		$message = html_entity_decode( $message, ENT_QUOTES );

		// Send email to user
//			global $mosConfig_mailfrom, $mosConfig_fromname, $configs;

		$mosConfig_mailfrom = $app->getCfg( "mailfrom" );
		$mosConfig_fromname = $app->getCfg( "fromname" );
		if ( $configs->get('usestoremail',1) == '1' && strlen( trim( $configs->get('store_name','DigiCom Store') ) ) > 0 && strlen( trim( $configs->get('store_email','') ) ) > 0 ) {
			$adminName2  = $configs->get('store_name','DigiCom Store');
			$adminEmail2 = $configs->get('store_email','');

		} else if ( $mosConfig_mailfrom != "" && $mosConfig_fromname != "" ) {
			$adminName2  = $mosConfig_fromname;
			$adminEmail2 = $mosConfig_mailfrom;

		} else {

			$query = "SELECT name, email"
			         . "\n FROM #__users"
			         . "\n WHERE LOWER( usertype ) = 'superadministrator'"
			         . "\n OR LOWER( usertype ) = 'super administrator'";
			$database->setQuery( $query );
			$rows        = $database->loadObjectList();
			$row2        = $rows[0];
			$adminName2  = $row2->name;
			$adminEmail2 = $row2->email;
		}


		$mailSender = JFactory::getMailer();
		$mailSender->IsHTML( true );
		$mailSender->addRecipient( $my->email );
		$mailSender->setSender( array( $adminEmail2, $adminName2 ) );
		$mailSender->setSubject( $subject );
		$mailSender->setBody( $message );

		if ( ! $mailSender->Send() ) {

//			<Your error code management>
		}else{
			$email->store();
		}
//			mosMail( $adminEmail2, $adminName2, $my->email, $subject, $message, 1 ); // Send mail
		if ( $configs->get('sendmailtoadmin',1) != 0 ) {
			$mailSender = JFactory::getMailer();
			$mailSender->IsHTML( true );
			$mailSender->addRecipient( $adminEmail2 );
			$mailSender->setSender( array( $adminEmail2, $adminName2 ) );
			$mailSender->setSubject( $subject );
			$mailSender->setBody( $message );
			if ( ! $mailSender->Send() ) {
//					<Your error code management>
			}else{
				$email->store();
			}
			$site_config = JFactory::getConfig();
			$tzoffset    = $site_config->get( 'offset' );
			$today       = date( 'Y-m-d H:i:s', time() + $tzoffset );
			$sql         = "insert into #__digicom_logs(`userid`, `emailname`, `to`, `subject`, `body`, `send_date`) values (" . $my->id . ", 'New Customer Email', '" . $my->email . "', '" . addslashes( trim( $subject ) ) . "', '" . addslashes( $message ) . "', '" . $today . "')";
			$db->setQuery( $sql );
			$db->query();
		}
	}

	/**
	 * Get firstname and lastname from a fullname
	 *
	 * @param unknown $name
	 * @param number $part
	 */
	function getPartName( $name ) {
		$results = array();
		preg_match( '#^(\w+\.)?\s*([\'\’\w]+)\s+([\'\’\w]+)\s*(\w+\.?)?$#', $name, $results );
		$nameObj = new stdClass();
		if ( ! count( $results ) ) {
			$nameObj->firstname = $name;
			$nameObj->lastname  = $name;
		} else {
			$nameObj->firstname = $results[2];
			$nameObj->lastname  = $results[3];
		}

		return $nameObj;
	}
}

