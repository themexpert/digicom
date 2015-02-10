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

class DigiComAdminViewFields extends DigiComView {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('DSATTRMAN'), 'generic.png');
		JToolBarHelper::deleteList();
		JToolBarHelper::editList();
		JToolBarHelper::addNew();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();

		$fields = $this->get('listFields');
		$this->assignRef('fields', $fields);
		parent::display($tpl);

	}

	function editForm($tpl = null) {

		$db = JFactory::getDBO();

		$field = $this->get('Field');
		$isNew = ($attr->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('DS_FIELD').":<small>[".$text."]</small>");
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::apply();
			JToolBarHelper::divider();
			JToolBarHelper::cancel ('cancel', 'Close');
		}

		$this->assign("field", $field);

		$configs = $this->_models['config']->getConfigs();
		$lists = null;

		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		parent::display($tpl);
	}
}

?>