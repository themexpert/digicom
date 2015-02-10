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

class DigiComAdminViewTaxRules extends DigiComView {

	function display ($tpl =  null )
	{
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.taxes', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		JToolBarHelper::title(JText::_('DSTAXRULEMANAGER'), 'generic.png');
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();

		$rules = $this->get('Items');
		$pagination = $this->get('Pagination');

		$this->rules = $rules;
		$this->pagination = $pagination;

		parent::display($tpl);

	}

	function editForm($tpl = null) {
		$db = JFactory::getDBO();
		$rule = $this->get('rule');
		$isNew = ($rule->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('DSPRODUCTTAXRULE').":<small>[".$text."]</small>");
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::apply();
			JToolBarHelper::divider();
			JToolBarHelper::cancel ('cancel', 'Close');
		}

		$this->assign("rule", $rule);

		$editor = JFactory::getEditor();
		$this->assign("editor", $editor);

		$lists = array();
		// build the html select list for ordering
		$query = 'SELECT ordering AS value, name AS text'
		. ' FROM `#__digicom_tax_rule`'
		. ' ORDER BY ordering'
		;

		if ($isNew)
			$lists['published'] = JHTML::_('select.booleanlist',  'published', '', "1" );
		else 
			$lists['published'] = JHTML::_('select.booleanlist',  'published', '', $rule->published );

		if ($isNew) {
			$lists['ordering'] = JHtml::_('list.ordering','ordering', $query, '');
// 			$lists['ordering'] = JHTML::_('list.specificordering',  $rule, '', $query );
		}
		else {
			$lists['ordering'] = JHtml::_('list.ordering','ordering', $query, '', $rule->id);
// 			$lists['ordering'] = JHTML::_('list.specificordering',  $rule, $rule->id, $query );

		}

		$cclasses = explode("\n", $rule->cclass);
		$data = $this->get('listCustomerClasses');
		$select = '<select name="cclass[]" multiple>';
		if (count($data) > 0)
		foreach($data as $i => $v) {
			$select .= '<option value="'.$v->id.'" ';
			if (in_array($v->id, $cclasses)) {
				$select .= ' selected ' ;
			}
			$select .= ' > '.$v->name.'</option>';
		}
		$select .= '</select>';
		$lists['customer_classes'] = $select;

		$pclasses = explode("\n", $rule->pclass);
		$data = $this->get('listProductTaxClasses');
		$select = '<select name="pclass[]" multiple>';
		if (count($data) > 0)
		foreach($data as $i => $v) {
			$select .= '<option value="'.$v->id.'" ';
			if (in_array($v->id, $pclasses)) {
				$select .= ' selected ' ;
			}
			$select .= ' > '.$v->name.'</option>';
		}
		$select .= '</select>';
		$lists['product_tax_classes'] = $select;

		$trates = explode("\n", $rule->trate);
		$data = $this->get('listTaxRates');
		$select = '<select name="trate[]" multiple>';
		if (count($data) > 0)
		foreach($data as $i => $v) {
			$select .= '<option value="'.$v->id.'" ';
			if (in_array($v->id, $trates)) {
				$select .= ' selected ' ;
			}
			$select .= ' > '.$v->name.'</option>';
		}
		$select .= '</select>';
		$lists['tax_rates'] = $select;


		$ptype = explode("\n", $rule->ptype);
		$data = $this->get('listProductClasses');
		$select = '<select name="ptype[]" multiple>';
		if (count($data) > 0)
		foreach($data as $i => $v) {
			$select .= '<option value="'.$v->id.'" ';
			if (in_array($v->id, $ptype)) {
				$select .= ' selected ' ;
			}
			$select .= ' > '.$v->name.'</option>';
		}
		$select .= '</select>';
		$lists['product_classes'] = $select;

		$this->assign("lists", $lists);

		parent::display($tpl);

	}

};

?>