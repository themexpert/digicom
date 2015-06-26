<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

// Import library dependencies
jimport('joomla.event.plugin');
// load digicom helperfile
require_once JPATH_SITE . '/components/com_digicom/helpers/digicom.php';

class plgSystemDigiCom extends JPlugin{

	/**
	 * Load the language file on instantiation. Note this is only available in Joomla 3.1 and higher.
	 * If you want to support 3.0 series you must override the constructor
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/*
	* get the digicom configuration
	*
	* @var object
	* @since digicom 1.0.0-beta.5
	*/
	protected $configs = null;

	/**
	 * Plugin method with the same name as the event will be called automatically.
	 */

	public function onAfterSidebarMenu($params = array()) {

		JPluginHelper::importPlugin('digicom_pay');

		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger( 'onSidebarMenuItem', array());
		if(!$results) return;

		echo '<h3>' . JText::_('PLG_SYSTEM_DIGICOM_PLUGINS_LIST') . '</h3>';
		echo '<ul>';
			foreach ($results as $key => $value) {
					echo '<li>'. $value .'</li>';
			}
		echo '</ul>';

	}



	public function getConfigs(){
		if( !$this->configs ) {
			$config = JComponentHelper::getComponent('com_digicom');
			$this->configs = $config->params;
		}
		return $this->configs;
	}

}
