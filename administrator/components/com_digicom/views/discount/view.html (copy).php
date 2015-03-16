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

class DigiComViewDiscount extends JViewLegacy
{

	function display ($tpl =  null )
	{
		$db = JFactory::getDBO();
		$promo = $this->get('Item');
		$isNew = ($promo->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		JHtml::_( 'behavior.modal' );

		JToolBarHelper::title(JText::_('COM_DIGICOM_VIDEO_PROMO_MANAGER').":<small>[".$text."]</small>");
		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_VIDEO_PROMO_MANAGER' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::cancel('discount.cancel');
		} else {
			JToolBarHelper::apply('discount.apply');
			JToolBarHelper::cancel ('discount.cancel', 'Close');
		}

		$this->assign("promo", $promo);
		$this->assign("promo_products", $this->get('promoProducts'));
		$this->assign("promo_orders", $this->get('promoOrders'));

		$configs = $this->get('Configs');
		$lists = null;

		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		
		DigiComHelperDigiCom::addSubmenu('discounts');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();
		
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

	
}
