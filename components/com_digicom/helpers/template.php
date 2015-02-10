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

class DigiComTemplateHelper extends DigiComView {
	
	protected $view = null;
	
	function __construct($view){
		$this->view = $view;
	}
	public function rander($layout = 'products'){
		
		$this->view->setLayout($layout);
		
		$mainframe = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_digicom');
		// Look for template files in component folders
		$this->view->_addPath('template', JPATH_COMPONENT.DS.'templates');
		$this->view->_addPath('template', JPATH_COMPONENT.DS.'templates'.DS.'default');

		// Look for overrides in template folder (K2 template structure)
		$this->view->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom'.DS.'templates');
		$this->view->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom'.DS.'templates'.DS.'default');

		// Look for overrides in template folder (Joomla! template structure)
		$this->view->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom'.DS.'default');
		$this->view->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom');
		
		// Look for specific K2 theme files
		if ($params->get('template','default'))
		{
			$this->view->_addPath('template', JPATH_COMPONENT.DS.'templates'.DS.$params->get('template','default'));
			$this->view->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom'.DS.'templates'.DS.$params->get('template','default'));
			$this->view->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_digicom'.DS.$params->get('template','default'));
		}
		
	}
}