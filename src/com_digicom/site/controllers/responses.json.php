<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_digicom
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Responses controller class.
 *
 * @since  1.6
 */
class DigicomControllerResponses extends JControllerLegacy
{
    public $context = 'digicom.responses';
    public $event_json_request = 'onDigicomJsonResponses';

    /**
     * Method to responses to json
     *
     * @return  output from plugin, json return.
     *
     * @since   1.2.3
     */
    public function execute($task)
    {
        header('Content-type: application/json');
        
        $source = JFactory::getApplication()->input->get('source', '', 'string');
        try
        {
            $dispatcher = JEventDispatcher::getInstance();

            // Include the digicom plugin group
      	    JPluginHelper::importPlugin('digicom');

            // Trigger the before delete event.
  			    $result = $dispatcher->trigger($this->event_json_request, array($this->context, $source));

            if($source){
              $result = $result[0];
            }

            echo new JResponseJson($result, JText::_('COM_DIGICOM_RESPONSE_SUCCESS'));
        }
        catch(Exception $e)
        {
            echo new JResponseJson($e);
        }

        JFactory::getApplication()->close();
    }


}
