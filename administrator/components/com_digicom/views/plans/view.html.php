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

class DigiComAdminViewPlans extends DigiComView {

	function display ($tpl =  null )
	{
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.plans', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		JToolBarHelper::title(JText::_('Plans Manager'), 'generic.png');
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::divider();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();

		$plains = $this->get('Items');
		$pagination = $this->get('Pagination');

		$this->plains = $plains;
		$this->pagination = $pagination;

		$configs = $this->_models['config']->getConfigs();
		$this->assign("configs", $configs);
	   
		parent::display($tpl);

	}

	function editForm($tpl = null) {

		$db = JFactory::getDBO();

		$plain = $this->get('plain');

		$isNew = ($plain->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		$this->assign("action", $text);

		JToolBarHelper::title(JText::_('Plan').":<small>[".$text."]</small>");
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

		$this->assign("plain", $plain);

		$configs = $this->_models['config']->getConfigs();
		$lists = null;

/* subscr duration */

		$subcrub_duration[] = JHTML::_('select.option','-1','Unlimited');
		for($i = 1; $i <= 25; $i++) {
			$subcrub_duration[] = JHTML::_('select.option', $i, $i);
		}

		if ($isNew) {
			$alowed_change = true;
		} else {
			$sql = "SELECT count(id) FROM #__digicom_licenses WHERE plan_id = ".$plain->id;
			$db->setQuery($sql);
			$licenses_count = $db->loadResult();

			if ($licenses_count > 0) {
				$alowed_change = false;
			} else {
				$alowed_change = true;
			}
		}

		$lists['duration_count'] = JHTML::_('select.genericlist', $subcrub_duration, 'duration_count', (($alowed_change)?'':'disabled="disabled"').' class="inputbox" size="1" onchange="checkUnlimited(this)" ', 'value', 'text', $plain->duration_count);

/* subscr duration type */

		$subcrub_duration_type[] = JHTML::_('select.option', 0, JText::_('SUBCRUB_DURATION_DOWNLOADS'));
		$subcrub_duration_type[] = JHTML::_('select.option', 1, JText::_('SUBCRUB_DURATION_HOURS'));
		$subcrub_duration_type[] = JHTML::_('select.option', 2, JText::_('SUBCRUB_DURATION_DAYS'));
		$subcrub_duration_type[] = JHTML::_('select.option', 3, JText::_('SUBCRUB_DURATION_MONTHS'));
		$subcrub_duration_type[] = JHTML::_('select.option', 4, JText::_('SUBCRUB_DURATION_YEARS'));
		$lists['duration_type'] = JHTML::_('select.genericlist', $subcrub_duration_type, 'duration_type', (($alowed_change)?'':'disabled="disabled"').' class="inputbox" size="1" '.((($plain->duration_count == -1)||($plain->duration_count == 0))?'style="display:none;"':''), 'value', 'text', $plain->duration_type);

/* Published */

		$published[] = JHTML::_( 'select.option', '1', JText::_("DSYES") );
		$published[] = JHTML::_( 'select.option', '0', JText::_("DSNO") );

		$lists['published'] = JHTML::_('select.radiolist', $published, 'published', 'class="inputbox" ', 'value', 'text', $plain->published, 'published' );

		$this->assign("configs", $configs);
		$this->assign("lists", $lists);

		parent::display($tpl);

	}

	function planitem($tpl = null) {

		$configs = $this->_models['config']->getConfigs();

		$type = JRequest::getVar('type','');
		switch ( $type )
		{
		 case 'renewal':
			$plans = $this->_models['plain']->getPlanitemRenewalSelect();
		  break;

		 case 'new':
		 default:
			$plans = $this->_models['plain']->getPlanitemNewSelect();
		  break;
		}

		$plans_options = array();
		$default = 0;
		if ($plans)
		foreach ($plans as $plan) {
			$plans_options[] = JHTML::_( 'select.option', $plan->plan_id, $plan->name . ' - ' . DigiComAdminHelper::format_price( $plan->price, $configs->get('currency','USD'), true, $configs ) );
			if ($plan->default > 0) $default = $plan->plan_id;
		}

		$hid = JRequest::getVar('hid','none');

		if(empty($plans_options)){
			$plans_options[] = JHTML::_( 'select.option', -1, 'No settings plain for it product' );
		}

		$pid = JRequest::getVar("pid", "0");
		$db = JFactory::getDBO();
		$sql = "select `domainrequired` from #__digicom_products where id=".intval($pid);
		$db->setQuery($sql);
		$db->query();
		$domainrequired = $db->loadResult();
		$style = "";
		if($domainrequired == 3){// package
			$style = 'style="display:none;"';
		}

		$plans_select = JHTML::_('select.genericlist', $plans_options, 'subscr_plan_select['.$hid.']', $style.' class="inputbox" onchange="changePlain();" ', 'value', 'text', $default );

		$plans_select .= "&nbsp;".JHTML::tooltip(JText::_("COM_DIGICOM_ORDERSUBSCRPLAN_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');

		$this->assign('plans', $plans_select);

		parent::display($tpl);
	}

	function getPlainsByProductIDSelectHTML($tpl = null) {

		$configs = $this->_models['config']->getConfigs();

		$plans = $this->_models['plain']->getPlanitemNewSelect();
		$plans_options = array();
		$default = 0;
		if ($plans)
		foreach ($plans as $plan) {
			$plans_options[] = JHTML::_( 'select.option', $plan->plan_id, $plan->name . ' - ' . DigiComAdminHelper::format_price( $plan->price, $configs->get('currency','USD'), true, $configs ) );
			if ($plan->default > 0) $default = $plan->plan_id;
		}

		$hid = JRequest::getVar('hid','none');

		if (empty($plans_options)) $plans_options[] = JHTML::_( 'select.option', -1, 'No settings plain for it product' );

		$plans_select = JHTML::_('select.genericlist', $plans_options, 'plan_include_id['.$hid.']', ' class="inputbox" size="1" ', 'value', 'text', $default );
		$this->assign('plans',$plans_select);

		parent::display($tpl);
	}

}

?>