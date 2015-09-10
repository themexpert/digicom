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
	protected $params = null;

	function __construct($view)
	{
		// load jquery n core joomla js for language string as we require it
		JHtml::_('jquery.framework');
		JHtmlBehavior::core();
		JText::script('COM_DIGICOM_REGISTRATION_EMAIL_ALREADY_USED');
		JText::script('COM_DIGICOM_REGISTER_USERNAME_TAKEN');

		$this->params = JComponentHelper::getParams('com_digicom');
		$this->view = $view;

		// load core digicom js plugin
		$this->addScript(JURI::root()."media/digicom/assets/js/digicom.plugin.js?site=".JURI::root());

		// load core css file
		$core_css = $this->params->get('load_core_css',true);
		if($core_css){
			$this->addStyleSheet(JURI::root()."media/digicom/assets/css/digicom.css");
		}

		// load bootstrap3
		$load_bootstrap3 = $this->params->get('load_bootstrap3',true);
		if($load_bootstrap3){
			$this->addStyleSheet(JURI::root()."media/digicom/assets/css/digicom.css");
		}

	}

	public function rander($layout = 'products', $template = null){

		$this->view->setLayout($layout);

		$app = JFactory::getApplication();
		// Look for template files in component folders
		$this->view->_addPath('template', JPATH_COMPONENT . '/templates');
		$this->view->_addPath('template', JPATH_COMPONENT . '/templates/default');

		// Look for overrides in template folder (Joomla! template structure)
		$this->view->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates/default');
		$this->view->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates');

		// Look for specific DigiCom theme files
		if ($this->params->get('template','default'))
		{
			$this->view->_addPath('template', JPATH_COMPONENT . '/templates/' . $this->params->get('template','default'));
			$this->view->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates/' . $this->params->get('template','default'));
		}

		if($template){
			$this->view->_addPath('template', JPATH_COMPONENT . '/templates/' . $template);
			$this->view->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates/' . $template);
		}


		// CUSTOM CSS
		if (is_file( JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates/' . $this->params->get('template','default') . '/css/style.css')) {
			$this->addStyleSheet( JUri::root(true) . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates/' . $this->params->get('template','default') . '/css/style.css');
		}elseif( is_file(JPATH_COMPONENT . '/templates/' . $this->params->get('template','default') . '/css/style.css') ) {
			$this->addStyleSheet( JUri::root(true) . '/components/com_digicom/templates/' . $this->params->get('template','default') . '/css/style.css');
		}

		// CUSTOM JS
		if (is_file(JPATH_SITE .'/templates/' . $app->getTemplate() . '/html/com_digicom/templates/' . $this->params->get('template','default') . '/js/script.js')) {
			$this->addScript(JUri::root(true) . '/templates/' . $app->getTemplate() . '/html/com_digicom/templates/' . $this->params->get('template','default') . '/js/script.js');
		}elseif( is_file( JPATH_COMPONENT . '/templates/' . $this->params->get('template','default') . '/js/script.js')) {
			$this->addScript(JUri::root(true) . '/components/com_digicom/templates/' . $this->params->get('template','default') . '/js/script.js');
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
