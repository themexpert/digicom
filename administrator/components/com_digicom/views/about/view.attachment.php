<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewAbout extends JViewLegacy {

	/**
	* @var array somme system values
	*/
	protected $info = null;

	/**
	 * @var array informations about writable state of directories
	 */
	protected $directory = null;

	function display($tpl =  null){
		header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename=digicom_systeminfo.txt");

		$this->info			= $this->get('info');
		$this->directory	= $this->get('directory');
		$this->plugins	= $this->get('plugins');

		parent::display('systeminfo');
	}

}
