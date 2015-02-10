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

jimport ("joomla.application.component.view");

class DigiComAdminViewLicenses extends DigiComView {

	function display ($tpl =  null )
	{
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.licenses', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		JToolBarHelper::title(JText::_('Licenses Manager'), 'generic.png');
		JToolBarHelper::custom('export', 'export.png', 'export.png', 'Export', false, false);
		JToolBarHelper::custom('customerexport', 'export.png', 'export.png', 'Customers Export', false, false);
		JToolBarHelper::addNew();
		JToolBarHelper::divider();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();
		JToolBarHelper::spacer();

		$licenses = $this->get('Items');
		$pagination = $this->get('Pagination');

		$this->licenses = $licenses;
		$this->pagination = $pagination;

		$prods = $this->_models['license']->getProducts();
		$p = array();
		$p[] = JHTML::_('select.option', JRoute::_("index.php?option=com_digicom&controller=licenses&prd=0"), JText::_("DSSELECTPRODUCT"));
		foreach ( $prods as $prod ) {
			$p[] = JHTML::_('select.option', JRoute::_("index.php?option=com_digicom&controller=licenses&prd=".$prod->id), $prod->name);
		}
		$pjs = 'onchange="window.location=this.value"';
		$prd = JRequest::getVar("prd", 0, "request");
		$pselector = JHTML::_('select.genericlist',  $p, 'prods', 'class="inputbox" size="1" '. $pjs, 'value', 'text',  JRoute::_("index.php?option=com_digicom&controller=licenses&prd=".$prd));
		$this->assign("psel", $pselector);
		$this->assign("prd", $prd);

		$state = $this->get('state');
		$filter_prod = JRequest::getVar("filter_prod", "", "request");

		$cats = $this->_models['license']->getlistCategories();

		$cathtml = DigiComAdminHelper::getCatAndProductToLisenceIdHtml($cats, "id='filter_prod' name='filter_prod' class='inputbox' size='1' onchange='document.adminForm.submit()'", $filter_prod);

		$this->assign ("cathtml", $cathtml);		 

		$keyword = JRequest::getVar("keyword", "", "request");
		$this->assign ("keyword", $keyword);

		$status = JRequest::getVar("status", "", "request");
		$this->assign ("status", $status);

		$cancelled = JRequest::getInt("cancelled", 0);
		$this->assign ("cancelled", (int) $cancelled);

		$conf = $this->_models['config']->getConfigs();
		$this->assign ("configs", $conf);

		$all_products = $this->_models['license']->getAllProducts();
		$all_prod_select  = '<select name="filter_exp_product" onchange="document.adminForm.task.value=\'\'; document.adminForm.submit()">';
		$all_prod_select .= '<option value="0">'.JText::_("DSSELECTPRODUCT").'</option>';
		if(isset($all_products) && count($all_products) > 0){
			$filter_exp_product = JRequest::getVar("filter_exp_product", "0");
			foreach($all_products as $key=>$value){
				$selected = "";
				if($value["id"] == $filter_exp_product){
					$selected = 'selected="selected"';
				}
				$all_prod_select .= '<option value="'.$value["id"].'" '.$selected.'>'.$value["name"].'</option>';
			}
		}
		$all_prod_select .= '</select>';
		$this->assign("all_prod_select", $all_prod_select);
		$this->assign("models", $this->_models['license']);
		
		$doc = JFactory::getDocument();
		$doc->addScript(JURI::root() . 'components/com_digicom/assets/js/jquery.digicom.js');
		$doc->addScript(JURI::root() . 'components/com_digicom/assets/js/jquery.noconflict.digicom.js');

		parent::display($tpl);
	}

	function selectplain($tpl = null) {
		parent::display($tpl);
	}

	function licenseitem($tpl = null) {
		$userid = JRequest::getVar('userid', 0);
		$productid = JRequest::getVar('pid', 0);
		$licenses = $this->_models['license']->getLicenseitemSelect($productid, $userid);
		$licenses_options = array();
		$default = 0;
		if($licenses){
			foreach ($licenses as $license){
				$domain = "";
				if(empty($license->domain)){
					$domain = '( domain is not set )';
				}
				else{
					$domain = '('.$license->domain.')';
				}
				$licenses_options[] = JHTML::_( 'select.option', $license->id, '#'.$license->licenseid . ' ' . $license->purchase_date . $domain );
			}
		}

		$hid = JRequest::getVar('hid','none');

		if (empty($licenses_options)) {
			//small fix $licenses_options[] = JHTML::_( 'select.option', -1, 'No licenses yet' );
		} 

		$licenses_select = JHTML::_('select.genericlist', $licenses_options, 'licences_select['.$hid.']', 'class="inputbox" ', 'value', 'text', $default );

		// small fix
		if (empty($licenses_options)) {
			$this->assign('licenses','none');
		} else {
			$this->assign('licenses',$licenses_select);
		}

		parent::display($tpl);
	}

	function selectnote($tpl = null) {
		parent::display($tpl);
	}

	function editForm($tpl = null)
	{
		$db = JFactory::getDBO();
		$license = $this->_models['license']->getLicense();
		$isNew = isset($license->id)&&($license->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('License').":<small>[".$text."]</small>");
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::apply();
			JToolBarHelper::divider();
			JToolBarHelper::cancel ('cancel', 'Close');
		}

		$cathtml =& DigiComAdminHelper::getCatAndProductToLisenceIdHtml("id='productid' name='productid' class='inputbox' size='1'", isset($license->productid)?$license->productid:0);
		$this->assign ("cathtml", $cathtml);		 

		$this->assign("license", $license);

		$configs = $this->_models['config']->getConfigs();
		$lists = array();

		$prods =& DigiComAdminModelProduct::get("Items");
		$opts = array();
		$opts[] = JHTML::_('select.option',  "", JText::_("Select product") );
		if(isset($prods)){
			foreach($prods as $prod){
				$opts[] = JHTML::_('select.option',  $prod->id, $prod->name );
			}
		}
		$lists['productid'] = JHTML::_('select.genericlist',  $opts, 'productid', 'class="inputbox" size="1" ', 'value', 'text', isset($license->productid)?$license->productid:"");

		$db->setQuery("SELECT pl.*
			FROM #__digicom_products_plans pp
			LEFT JOIN #__digicom_plans pl ON ( pl.id = pp.plan_id )
			WHERE
			 pp.product_id = ".$license->productid." AND pl.id = ".$license->plan_id);
		$subcription = $db->loadObject();
		$lists['subcription'] = $subcription;

		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		$this->assign("currency_options", array());
		$plugin_handler = new stdClass;
		$plugin_handler->encoding_plugins = array();
		$this->assign("plugin_handler", $plugin_handler);
		$maxfields = 0;
		$maxfields = DigiComAdminHelper::check_fields($license->prod_fields, $totalfields, $optlen, $select_only, $maxfields, $license->productid);
		$this->assign("totalfields", $totalfields);
		$this->assign("optlen", $optlen);
		$this->assign("select_only", $select_only);
		$this->assign("maxfields", $maxfields);

		parent::display($tpl);
	}


	function downloadForm($license, $product, $res, $platform_options){
		$db = JFactory::getDBO();
		$this->assign("license", $license);
		$this->assign("product", $product);
		$tpl = null;
		parent::display($tpl);
	}

	function domainForm($tpl = null) {
		$db = JFactory::getDBO();
		$license = $this->_models['license']->getLicense();
		$this->assign("license", $license);
		$this->setLayout("domainForm");
		parent::display($tpl = null);
	}

	function decodeform($encoders, $input="", $output="") {
		$this->setLayout("decodeform");
		$this->assign("encoders", $encoders);
		$this->assign("input", $input);
		$this->assign("output", $output);
		parent::display($tpl = null);
	}

}

?>