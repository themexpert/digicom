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

class DigiComViewCustomers extends JViewLegacy {

	function display ($tpl =  null )
	{
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.customers', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$layout = JRequest::getVar('layout','');
		if($layout){
			$this->setLayout($layout);
		}
		
		$customers = $this->get('Items');
		$pagination = $this->get('Pagination');

		$this->custs = $customers;
		$this->pagination = $pagination;

		$prd = JRequest::getVar("prd", 0, "request");
		$this->assign("prd", $prd);

		$keyword = JRequest::getVar("keyword", "", "request");
		$this->assign ("keyword", $keyword);
		
		//set toolber
		$this->addToolbar();
		
		DigiComHelperDigiCom::addSubmenu('customers');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();
		
		parent::display($tpl);

	}

	function settypeform($tpl = null){
		$id = JRequest::getVar("id", "0");
		if($id == "0"){
			JToolBarHelper::title(JText::_('VIEWLICCUSTOMER').":<small>[".trim(JText::_("DIGI_NEW"))."]</small>");
		}
		else{
			JToolBarHelper::title(JText::_('VIEWLICCUSTOMER').":<small>[".trim(JText::_("DIGI_EDIT"))."]</small>");
		}

		JToolBarHelper::custom('next','forward.png','forward_f2.png','Next',false);
		JToolBarHelper::cancel();
		parent::display($tpl);
	}

	function editForm($tpl = null) {
		
		//require_once( JPATH_COMPONENT.DS.'helpers'.DS.'sajax.php' );
		$db = JFactory::getDBO();
		$customer = $this->get('customer');
		$user = $this->get('User');
		$isNew = ($customer->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit')." : ".$customer->firstname;

		JToolBarHelper::title(JText::_('Customer').":<small>[".$text."]</small>");

		$bar = JToolBar::getInstance('toolbar');
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_('Customer').":<small>[".$text."]</small>",
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::apply();
			JToolBarHelper::divider();
			JToolBarHelper::cancel ('cancel', 'Close');
		}

		$this->assign("cust", $customer);
		$this->assign("user", $user);

		$configs = $this->get("Configs");
		//$country_option = DigiComHelperDigiCom::get_country_options($customer, false, $configs);
		//$lists['country_option'] = $country_option;

		$profile = new StdClass();
		$profile->country = $customer->shipcountry;
		$profile->state = $customer->shipstate;
		//$shipcountry_option = DigiComHelperDigiCom::get_country_options($customer, true, $configs);
		//$lists['shipcountry_options'] = $shipcountry_option;

		//$lists['customerlocation'] = DigiComHelperDigiCom::get_store_province($customer);

		$profile = new StdClass();
		$profile->country = $customer->shipcountry;
		$profile->state = $customer->shipstate;
		//$lists['customershippinglocation'] = DigiComHelperDigiCom::get_store_province($profile, true, $configs);

		$cclasses = explode("\n", $customer->taxclass);

		//$this->assign("lists", $lists);
		$keyword = JRequest::getVar("keyword", "", "request");
		$this->assign ("keyword", $keyword);
		
		$this->assign("configs", $configs);
		
		DigiComHelperDigiCom::addSubmenu('customers');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();

		parent::display($tpl);
	}
	
	
	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_DIGICOM_CUSTOMERS_TOOLBAR_TITLE'), 'generic.png');

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_CUSTOMERS_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		

	}
	
	
}
