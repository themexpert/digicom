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

class DigiComAdminViewAttributes extends DigiComView {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('DSATTRMAN'), 'generic.png');

		JToolBarHelper::addNew();
		JToolBarHelper::editList();

		JToolBarHelper::divider();

		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();

		JToolBarHelper::divider();

		JToolBarHelper::deleteList();

		$attrs = $this->get('listAttributes');
		$this->assignRef('attrs', $attrs);
		parent::display($tpl);

	}

	function editForm($tpl = null) {

		$db = JFactory::getDBO();

		$attr = $this->get('Attribute');
		$isNew = ($attr->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('Attribute').":<small>[".$text."]</small>");
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::apply();
			JToolBarHelper::divider();
			JToolBarHelper::cancel ('cancel', 'CLOSE');
		}

		$this->assign("attr", $attr);

		$configs = $this->_models['config']->getConfigs();
		$lists = null;

		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		parent::display($tpl);

	}

}

?>