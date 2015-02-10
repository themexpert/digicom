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

jimport("joomla.application.component.view");

class DigiComViewLicenses extends DigiComView
{

	function display($tpl = null)
	{
		global $isJ25;
		$db = JFactory::getDBO();
		$licenses = $this->get('listLicenses');
		$Itemid = JRequest::getInt("Itemid", 0);
		$ga = JRequest::getInt("ga", 0);
		
		JHtml::_('bootstrap.framework');
		
		// It's returns from a success purchase so send to google analytics
		if ($ga)
		{
			require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'google.php';
			/*
				$order_id = JRequest::getVar('orderid');
				// Get order
				$sql = "SELECT *
						FROM `#__digicom_orders`
						WHERE `id`=" . $order_id;
				$db->setQuery($sql);
				$order = $db->loadObject();

				// Get order items
				$sql = "";
			*/
		}

		$optlen = 0;
		$show_domain = 0;

		if(isset($licenses))
		{
			foreach ($licenses as $row)
			{
				foreach ($row->fields as $field)
				{
					$optlen += strlen(trim($field->optioname));
				}

				if ($row->domainrequired < 2)
					$show_domain = 1;
			}
		}

		$this->assign("show_domain", $show_domain);
		$this->assignRef('optlen', $optlen);

		$configs = $this->_models['config']->getConfigs();
		$this->assign("configs", $configs);

		$database = JFactory::getDBO();
		$db = $database;
		$sql = "select params from #__modules where `module`='mod_digicom_cart'";
		$db->setQuery($sql);
		$d = $db->loadResult();
		$d = explode("\n", $d);
		$categ_digicom = '';

		foreach ($d as $i => $v) {
			$x = explode("=", $v);
			if ($x[0] == "digicom_category") {
				$categ_digicom = $x[1];
				break;
			}
		}

		if ($categ_digicom != '')
		{
			$sql = "select id from #__digicom_categories where title like '" . $categ_digicom . "' or name like '" . $categ_digicom . "'";
			$database->setQuery($sql);
			$id = $database->loadResult();
			$cat_url = JRoute::_("index.php?option=com_digicom&controller=products&task=list&licid=" . $id . "&Itemid=" . $Itemid);
		}
		else
		{
			$cat_url = JRoute::_("index.php?option=com_digicom&controller=categories&task=listCategories" . "&Itemid=" . $Itemid);
		}
		$this->assign("caturl", $cat_url);

		/* Plains */

		if(isset($licenses)){
			foreach ($licenses as $key => $license){
				$plans = array();
				foreach($license->plans as $plain){
					if(isset($plain->id)){
						if($plain->duration_count != -1){
							$plans[$plain->id] = DigiComHelper::getDurationType($plain->duration_count, $plain->duration_type);
						}
						else{
							$plans[$plain->id] = JText::_("Unlimited") . " - " . $plain->price;
						}
					}
				}
				$licenses[$key]->plans2 = $plans;
			}
		}

		/* Renewals */

		if(isset($licenses)){
			foreach($licenses as $key => $license){
				$renewals = array();
				if(isset($license->renewals) && !empty($license->renewals)){
					foreach($license->renewals as $renewal){
						if(isset($renewal->id)){
							if($renewal->duration_count != -1){
								$renewals[$renewal->id] = DigiComHelper::getDurationType($renewal->duration_count, $renewal->duration_type);
							}
							else{
								$renewals[$renewal->id] = JText::_('Unlimited');
							}
						}
					}
					$licenses[$key]->renewals2 = $renewals;
				}
				else{
					$licenses[$key]->renewals2 = '';
				}
			}
		}

		$this->assignRef('licenses', $licenses);
		$this->assignRef('ga',$ga);

		parent::display($tpl);
	}

	function editForm($tpl = null)
	{

		$db = JFactory::getDBO();
		$license = $this->_models['license']->getLicense();

		$isNew = ($license->id < 1);
		$text = $isNew ? JText::_('New') : JText::_('Edit');

		JToolBarHelper::title(JText::_('License') . ":<small>[" . $text . "]</small>");
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::cancel('cancel', 'Close');
		}

		$this->assign("license", $license);

		$configs = $this->_models['config']->getConfigs();
		$lists = array();

		$prods = $this->_models['product']->getListProducts();
		$opts = array();
		$opts[] = JHTML::_('select.option', "", JText::_("Select product"));
		foreach ($prods as $prod) {
			$opts[] = JHTML::_('select.option', $prod->id, $prod->name);
		}
		$lists['productid'] = JHTML::_('select.genericlist', $opts, 'productid', 'class="inputbox" ', 'value', 'text', $license->productid);

		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		$this->assign("currency_options", array());
		/*
		$plugin_handler = new stdClass;
		$plugin_handler->encoding_plugins = array();
		$this->assign("plugin_handler", $plugin_handler);
		*/
		parent::display($tpl);
	}

	function downloadForm($license, $product, $res, $platform_options)
	{

		$db = JFactory::getDBO();
		//$license = $this->_models['license']->getLicense();

		$this->assign("license", $license);
		$this->assign("product", $product);

		//$method = "ioncube";
		//$enc = $this->_models['plugin']->getEncPlatformsForMethod($method);
		//$this->assign("enc", $enc);

		$tpl = null;
		parent::display($tpl);
	}

	function showPackage($license, $product)
	{

		$this->assign("license", $license);
		$this->assign("product", $product);
		$contents = $this->_models['license']->getPackageContents($license, $product);
		$this->assign("contents", $contents);

		$tpl = null;
		parent::display($tpl);
	}

	function domainForm($tpl = null)
	{
		$db = JFactory::getDBO();
		$license = $this->_models['license']->getLicense();
		$this->assign("license", $license);
		$this->setLayout("domainForm");
		parent::display($tpll = null);
	}

	function downloadLicense($url){
		$this->assign("download_url", $url);
		$tpl = null;
		parent::display($tpl);
	}

	function getNrOrders($lic_id){
		$model = $this->getModel("License");
		$result = $model->getNrOrders($lic_id);
		return $result;
	}

}


?>