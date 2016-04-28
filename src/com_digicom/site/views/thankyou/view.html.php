<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewThankYou extends JViewLegacy
{
	public $item;
	public $configs;
	public $customer;

	function display($tpl = null)
	{
		$app 			= JFactory::getApplication();
		$session 	= JFactory::getSession();
		$digicom_session = $session->get('com_digicom', array());
		
		if(isset($digicom_session['action']) && $digicom_session['action'] == 'payment_complete' && $digicom_session['id'])
		{
			$this->item 		= $this->get('Item');
			$session->set('com_digicom', array());
		}else{
			$msg = JText::_('COM_DIGICOM_THANKYOU_PAGE_WRONG_PID');
			$app->redirect('index.php?option=com_digicom&view=orders', $msg, 'warning');
			return true;
		}

		$this->configs 	= JComponentHelper::getComponent('com_digicom')->params;
		$this->customer	= JFactory::getUser($this->item->userid);

		// Triggre plugin event
		JPluginHelper::importPlugin('digicom');
		$dispatcher = JDispatcher::getInstance();
		$this->item->event = new stdClass;
		$results = $dispatcher->trigger('onDigicomBeforeThankyou', array('com_digicom.thankyou', &$this->item, &$this->customer));
		$this->item->event->beforeThankyou = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onDigicomAfterThankyou', array('com_digicom.thankyou', &$this->item, &$this->customer));
		$this->item->event->afterThankyou = trim(implode("\n", $results));

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('thankyou');

		parent::display($tpl);

	}


}
