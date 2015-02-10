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

class DigiComAdminViewProductClasses extends DigiComView {

	function display ($tpl =  null )
	{
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.products', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		JToolBarHelper::title(JText::_('DSPRODUCTCLASSESMANAGER'), 'generic.png');
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::divider();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();

		$prodclasses = $this->get('Items');
		$pagination = $this->get('Pagination');
		$this->pclasses = $prodclasses;
		$this->pagination = $pagination;

		parent::display($tpl);

	}

	function editForm($tpl = null) {

		$db = JFactory::getDBO();
		$pclass = $this->get('productClass');
		$isNew = ($pclass->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('DSPRODUCTCLASS').":<small>[".$text."]</small>");
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::apply();
			JToolBarHelper::divider();
			JToolBarHelper::cancel ('cancel', 'Close');
		}

		$this->assign("pclass", $pclass);

		$editor = JFactory::getEditor();
		$this->assign("editor", $editor);

		$lists = array();
		// build the html select list for ordering
		$query = 'SELECT ordering AS value, name AS text'
		. ' FROM `#__digicom_productclass`'
		. ' ORDER BY ordering'
		;

		if ($isNew)
			$lists['published'] = JHTML::_('select.booleanlist',  'published', '', "1" );
		else 
			$lists['published'] = JHTML::_('select.booleanlist',  'published', '', $pclass->published );

		if ($isNew) {
			$lists['ordering'] = JHtml::_('list.ordering','ordering', $query, '');
// 			$lists['ordering'] = JHTML::_('list.specificordering',  $pclass, '', $query );
		}
		else {
			$lists['ordering'] = JHtml::_('list.ordering','ordering', $query, '', $pclass->id);
// 			$lists['ordering'] = JHTML::_('list.specificordering',  $pclass, $pclass->id, $query );

		}



		$this->assign("lists", $lists);

		parent::display($tpl);

	}

};

?>