<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

/**
 * DigiCom Main Controller
 *
 * @since  1.0.0
 */
class DigiComController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		DigiComHelperDigiCom::addAdminStyles();
		$view   = $this->input->get('view', 'digicom');
		$layout = $this->input->get('layout', 'default');

		// check for the view group permission
		if (!JFactory::getUser()->authorise('core.manage', 'com_digicom'))
		{
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		return parent::display();
	}

}
