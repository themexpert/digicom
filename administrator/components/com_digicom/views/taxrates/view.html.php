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

class DigiComAdminViewTaxRates extends DigiComView {

	function display ($tpl =  null )
	{
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.taxes', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		JToolBarHelper::title(JText::_('DSTAXRATEMANAGER'), 'generic.png');
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();

		$rates = $this->get('Items');
		$pagination = $this->get('Pagination');

		$this->rates = $rates;
		$this->pagination = $pagination;
		parent::display($tpl);

	}

	function editForm($tpl = null) {
		require_once( JPATH_COMPONENT.DS.'helpers'.DS.'sajax.php' );
		$db = JFactory::getDBO();
		$rate = $this->get('rate');
		$isNew = ($rate->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('DSPRODUCTTAXCLASS').":<small>[".$text."]</small>");
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::apply();
			JToolBarHelper::divider();
			JToolBarHelper::cancel ('cancel', 'Close');
		}

		$this->assign("rate", $rate);

		$editor = JFactory::getEditor();
		$this->assign("editor", $editor);

		$lists = array();
		// build the html select list for ordering
		$query = 'SELECT ordering AS value, name AS text'
		. ' FROM `#__digicom_tax_rate`'
		. ' ORDER BY ordering'
		;

		if ($isNew) {
			$lists['published'] = JHTML::_('select.booleanlist',  'published', '', "1" );
		} else {
			$lists['published'] = JHTML::_('select.booleanlist',  'published', '', $rate->published );
		}

		if ($isNew) {
			$lists['ordering'] = JHtml::_('list.ordering','ordering', $query, '');
// 			$lists['ordering'] = JHTML::_('list.specificordering',  $rate, '', $query );
		}
		else {
			$lists['ordering'] = JHtml::_('list.ordering','ordering', $query, '', $rate->id);
// 			$lists['ordering'] = JHTML::_('list.specificordering',  $rate, $rate->id, $query );

		}
		$configs = $this->_models['config']->getConfigs();
		$country_option = DigiComAdminHelper::get_tax_country_options($rate, false, $configs);
		$lists['country_option'] = $country_option;

		$lists['location_option'] = DigiComAdminHelper::get_tax_province($rate);


		$this->assign("lists", $lists);

		parent::display($tpl);

	}

};

?>