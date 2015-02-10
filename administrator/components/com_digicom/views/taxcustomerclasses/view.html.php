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

class DigiComAdminViewTaxCustomerClasses extends DigiComView {

	function display ($tpl =  null )
	{
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.taxes', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		JToolBarHelper::title(JText::_('DSTAXCUSTOMERCLASSESMANAGER'), 'generic.png');
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::divider();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();

		$custclasses = $this->get('Items');
		$pagination = $this->get('Pagination');

		$this->cclasses = $custclasses;
		$this->pagination = $pagination;

		parent::display($tpl);

	}

	function editForm($tpl = null) {

		$db = JFactory::getDBO();
		$cclass = $this->get('customerClass');
		$isNew = ($cclass->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('DSTAXCUSTOMERCLASS').":<small>[".$text."]</small>");
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::apply();
			JToolBarHelper::divider();
			JToolBarHelper::cancel ('cancel', 'Close');
		}

		$this->assign("cclass", $cclass);

		$editor = JFactory::getEditor();
		$this->assign("editor", $editor);

		$lists = array();
		// build the html select list for ordering
		$query = 'SELECT ordering AS value, name AS text'
		. ' FROM `#__digicom_tax_customerclass`'
		. ' ORDER BY ordering'
		;

		if ($isNew)
			$lists['published'] = JHTML::_('select.booleanlist',  'published', '', "1" );
		else 
			$lists['published'] = JHTML::_('select.booleanlist',  'published', '', $cclass->published );

		if ($isNew) {
			$lists['ordering'] = JHtml::_('list.ordering','ordering', $query, '');
// 			$lists['ordering'] = JHTML::_('list.ordering',  $cclass, '', $query );
		}
		else {
			$lists['ordering'] = JHtml::_('list.ordering','ordering', $query, '', $cclass->id);
// 			$lists['ordering'] = JHTML::_('list.specificordering',  $cclass, $cclass->id, $query );

		}



		$this->assign("lists", $lists);

		parent::display($tpl);

	}

};

?>