<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewDiscounts extends JViewLegacy
{

	function display ($tpl =  null )
	{
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.discounts', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
        
		$condition = JRequest::getVar("condition", '1');
		$this->assign ("condition", $condition);

		$status = JRequest::getVar("status", '1');
		$this->assign ("status", $status);

		$this->promos = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->configs = $this->get('configs');

		
		//set toolber
		$this->addToolbar();
		
		DigiComHelperDigiCom::addSubmenu('discounts');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();
		
		parent::display($tpl);

	}

	function editForm($tpl = null)
	{
		$db = JFactory::getDBO();
		$promo = $this->get('promo');
		$isNew = ($promo->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		JHtml::_( 'behavior.modal' );

		JToolBarHelper::title(JText::_('COM_DIGICOM_DISCOUNTS_TOOLBAR_TITLE').":<small>[".$text."]</small>");
		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_DISCOUNTS_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::spacer();
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::spacer();
			JToolBarHelper::apply();
			JToolBarHelper::divider();

			JToolBarHelper::cancel ('cancel', 'Close');
		}

		$this->assign("promo", $promo);
		$this->assign("promo_products", $this->get('promoProducts'));
		$this->assign("promo_orders", $this->get('promoOrders'));

		$configs = $this->_models['config']->getConfigs();
		$lists = null;

		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		
		DigiComAdminHelper::addSubmenu('promos');
		$this->sidebar = DigiComAdminHelper::renderSidebar();
		
		parent::display($tpl);

	}

	function productitem( $tpl = null )
	{
		// Rand ID
		$id_rand = uniqid (rand ());
		$this->assign( "id_rand", $id_rand );

		// Customer
		$userid = JRequest::getVar('userid', 0);

		// Subcription plain
		$plans[] = JHTML::_('select.option',  'none',  'none' );
		$plans = JHTML::_('select.genericlist',  $plans, 'subscr_plan_select['.$id_rand.']', 'class="inputbox" size="1" ', 'value', 'text');
		$this->assign( "plans", $plans );

		$configs =  $this->_models['config']->getConfigs();
		$this->assign( "configs", $configs );

		parent::display( $tpl );
	}

	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	*/
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_DIGICOM_DISCOUNTS_TOOLBAR_TITLE'), 'generic.png');

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_DISCOUNTS_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		JToolBarHelper::addNew('discount.add');
//		JToolBarHelper::editList('discount.edit');
		JToolBarHelper::divider();
		JToolBarHelper::publishList('discounts.publish');
		JToolBarHelper::unpublishList('discounts.unpublish');

		JToolBarHelper::divider();

		JToolBarHelper::deleteList('discounts.delete');
	}
}
