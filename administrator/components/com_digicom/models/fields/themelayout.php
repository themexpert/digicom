<?php
/**
 * @package     DigiCom
 * @author      ThemeXpert http://www.themexpert.com
 * @copyright   Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @since       1.0.0
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

/**
 * Form Field to display a list of the layouts for a component view from
 * the extension or template overrides.
 *
 * @package     Joomla.Legacy
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldThemelayout extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Themelayout';

	/**
	 * Method to get the field input for a component layout field.
	 *
	 * @return  string   The field input.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		jimport('joomla.filesystem.folder');
        $mainframe = JFactory::getApplication();
        $fieldName = $this->name;
        $componentPath = JPATH_SITE.DS.'components'.DS.'com_digicom'.DS.'templates';
        $componentFolders = JFolder::folders($componentPath);
		
        $db = JFactory::getDBO();
		$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
        $db->setQuery($query);
        $defaultemplate = $db->loadResult();

        if (JFolder::exists(JPATH_SITE.DS.'templates'.DS.$defaultemplate.DS.'html'.DS.'com_digicom'.DS.'templates'))
        {
            $templatePath = JPATH_SITE.DS.'templates'.DS.$defaultemplate.DS.'html'.DS.'com_digicom'.DS.'templates';
        }
        else
        {
            $templatePath = JPATH_SITE.DS.'templates'.DS.$defaultemplate.DS.'html'.DS.'com_digicom';
        }

        if (JFolder::exists($templatePath))
        {
            $templateFolders = JFolder::folders($templatePath);
            $folders = @array_merge($templateFolders, $componentFolders);
            $folders = @array_unique($folders);
        }
        else
        {
            $folders = $componentFolders;
        }

        $exclude = 'default';
        $options = array();
        foreach ($folders as $folder)
        {
            if (preg_match(chr(1).$exclude.chr(1), $folder))
            {
                continue;
            }
            $options[] = JHTML::_('select.option', $folder, ucfirst($folder));
        }

        array_unshift($options, JHTML::_('select.option', '', '-- '.JText::_('JDEFAULT').' --'));

        return JHTML::_('select.genericlist', $options, $fieldName, 'class="inputbox"', 'value', 'text', $this->value);
	}
}
