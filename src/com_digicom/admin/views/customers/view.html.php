<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

jimport ("joomla.application.component.view");

class DigiComViewCustomers extends JViewLegacy {

	public $custs;
	public $state;

	function display ($tpl =  null )
	{

		$layout = JRequest::getVar('layout','');
		if($layout){
			$this->setLayout($layout);
		}

		$this->Items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');

		$keyword = JRequest::getVar("keyword", "", "request");
		$this->assign ("keyword", $keyword);

		//set toolber
		$this->addToolbar();

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
		JToolBarHelper::title(JText::_('COM_DIGICOM_CUSTOMERS_TOOLBAR_TITLE_SITE'), 'generic.png');

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
	}

	/**
	 * get the total order number of a custoer
	 * @return toal number
	 *
	 * @since   1.6
	 */
	function getCustomerOrdersTotal ($userid)
	{
		$sql = "select count('id') as total from #__digicom_orders where userid='".$userid."' order by id desc";
		$db = JFactory::getDBO();
		$db->setQuery($sql);

		return $db->loadObject()->total;
	}


}
