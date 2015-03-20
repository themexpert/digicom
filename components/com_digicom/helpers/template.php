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

class DigiComSiteHelperTemplate extends JViewLegacy {
	
	protected $view = null;
	
	function __construct($view){
		
		$this->addStyleSheet(JURI::root()."media/digicom/assets/css/digicom.css");

		$this->view = $view;
		$this->addScriptDeclaration('var digicom_site = "'. JUri::root() . '";');
		$this->addScriptDeclaration('var DIGI_ATENTION = "'. JText::_("DIGI_ATENTION") . '";');
		$this->addScriptDeclaration('var DSALL_REQUIRED_FIELDS = "'. JText::_("DSALL_REQUIRED_FIELDS") . '";');
		$this->addScriptDeclaration('var DSCONFIRM_PASSWORD_MSG = "'. JText::_("DSCONFIRM_PASSWORD_MSG") . '";');
		$this->addScriptDeclaration('var DSINVALID_EMAIL = "'. JText::_("DSINVALID_EMAIL") . '";');
		$this->addScriptDeclaration('var ACCEPT_TERMS_CONDITIONS = "'. JText::_("ACCEPT_TERMS_CONDITIONS") . '";');

	}
	public function rander($layout = 'products'){
		
		$this->view->setLayout($layout);
		
		$mainframe = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_digicom');
		// Look for template files in component folders
		$this->view->_addPath('template', JPATH_COMPONENT.DS.'templates');
		$this->view->_addPath('template', JPATH_COMPONENT.DS.'templates'.DS.'default');

		// Look for overrides in template folder (Joomla! template structure)
		$this->view->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom'.DS.'default');
		$this->view->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom');
		
		// Look for specific DigiCom theme files
		if ($params->get('template','default'))
		{
			$this->view->_addPath('template', JPATH_COMPONENT.DS.'templates'.DS.$params->get('template','default'));
			$this->view->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom'.DS.$params->get('template','default'));
		}
		
		// CUSTOM CSS
		if (is_file(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom'.DS.$params->get('template','default') . '/css/style.css')) {
			$this->addStyleSheet(JUri::root(true).DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom'.DS.$params->get('template','default') . '/css/style.css');
		}elseif(is_file(JPATH_COMPONENT.DS.'templates'.DS.$params->get('template','default') . '/css/style.css')) {
			$this->addStyleSheet(JUri::root(true).DS.'components'.DS.'com_digicom'.DS.'templates'.DS.$params->get('template','default') . '/css/style.css');
		}

		// CUSTOM JS
		if (is_file(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom'.DS.$params->get('template','default') . '/js/script.js')) {
			$this->addScript(JUri::root(true).DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom'.DS.$params->get('template','default') . '/js/script.js');
		}elseif(is_file(JPATH_COMPONENT.DS.'templates'.DS.$params->get('template','default') . '/js/script.js')) {
			$this->addScript(JUri::root(true).DS.'components'.DS.'com_digicom'.DS.'templates'.DS.$params->get('template','default') . '/js/script.js');
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