<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

// TODO : PHP visibility and proper naming convention

class DigiComSiteHelperTemplate extends JViewLegacy {

	protected $view = null;

	function __construct($view){

		$this->view = $view;
		$this->addScriptDeclaration('var digicom_site = "'. JUri::root() . '";');
		$this->addScriptDeclaration('var DIGI_ATENTION = "'. JText::_("COM_DIGICOM_REGISTER_NOTICE_ATTENTION") . '";');
		$this->addScriptDeclaration('var DSALL_REQUIRED_FIELDS = "'. JText::_("COM_DIGICOM_REGISTER_NOTICE_ALL_REQUIRED_FIELDS") . '";');
		$this->addScriptDeclaration('var DSCONFIRM_PASSWORD_MSG = "'. JText::_("COM_DIGICOM_REGISTER_NOTICE_CONFIRM_PASSWORD_UNMATCHED") . '";');
		$this->addScriptDeclaration('var DSINVALID_EMAIL = "'. JText::_("COM_DIGICOM_REGISTER_NOTICE_INVALID_EMAIL") . '";');
		$this->addScriptDeclaration('var ACCEPT_TERMS_CONDITIONS = "'. JText::_("COM_DIGICOM_REGISTER_NOTICE_ACCEPT_TERMS_CONDITIONS") . '";');

	}
	public function rander($layout = 'products', $template = null){

		$this->view->setLayout($layout);

		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_digicom');
		// Look for template files in component folders
		$this->view->_addPath('template', JPATH_COMPONENT . '/templates');
		$this->view->_addPath('template', JPATH_COMPONENT . '/templates/default');

		// Look for overrides in template folder (Joomla! template structure)
		$this->view->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates/default');
		$this->view->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates');

		// Look for specific DigiCom theme files
		if ($params->get('template','default'))
		{
			$this->view->_addPath('template', JPATH_COMPONENT . '/templates/' . $params->get('template','default'));
			$this->view->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates/' . $params->get('template','default'));
		}

		if($template){
			$this->view->_addPath('template', JPATH_COMPONENT . '/templates/' . $template);
			$this->view->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates/' . $template);
		}


		// CUSTOM CSS
		if (is_file( JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates/' . $params->get('template','default') . '/css/style.css')) {
			$this->addStyleSheet( JUri::root(true) . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates/' . $params->get('template','default') . '/css/style.css');
		}elseif( is_file(JPATH_COMPONENT . '/templates/' . $params->get('template','default') . '/css/style.css') ) {
			$this->addStyleSheet( JUri::root(true) . '/components/com_digicom/templates/' . $params->get('template','default') . '/css/style.css');
		}else{
			$this->addStyleSheet(JURI::root()."media/digicom/assets/css/digicom.css");
		}

		// CUSTOM JS
		if (is_file(JPATH_SITE .'/templates/' . $app->getTemplate() . '/html/com_digicom/templates/' . $params->get('template','default') . '/js/script.js')) {
			$this->addScript(JUri::root(true) . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates/' . $params->get('template','default') . '/js/script.js');
		}elseif( is_file( JPATH_COMPONENT . '/templates/' . $params->get('template','default') . '/js/script.js')) {
			$this->addScript(JUri::root(true) . '/components/com_digicom/templates/' . $params->get('template','default') . '/js/script.js');
		}else{
			$this->addScript(JURI::root()."media/digicom/assets/js/digicom.js");
		}

	}

	public function addScript($path){
		// Load specific css component
		JFactory::getDocument()->addScript($path);
	}

	public function addStyleSheet($path){
		// Load specific css component
		JFactory::getDocument()->addStyleSheet($path);
	}
	public function addScriptDeclaration($script){
		// Load specific css component
		JFactory::getDocument()->addScriptDeclaration($script);
	}



}
