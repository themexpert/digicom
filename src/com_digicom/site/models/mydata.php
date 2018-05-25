<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelMyData extends JModelList
{
    protected $_item = null;
    /**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$this->setState('params', $params);

		parent::populateState($ordering, $direction);
	}
	/**
	 * Get the master query for retrieving a list of products subject to the model state.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	function getItem($pk = null)
	{
        $user	= JFactory::getUser();

        $pk = $user->id; //(!empty($pk)) ? $pk : (int) $this->getState('mydata.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
                $item = new \stdClass();
                $item->id = $user->id;
                $item->user = $user;
                $item->customer = new DigiComSiteHelperSession();
                $item->license = DigiComSiteHelperLicense::getLicenses();
                $item->logs = DigiComSiteHelperLog::getLogs();

                // log
                // orders
                //     orderdetails
                // states
                // cart
                // event
                $this->_item[$pk] = $item;
            }
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else
				{
                                        
                    $result = new \stdClass();
                    $result->id = 1;

					$this->setError($e);
                    $this->_item[$pk] = $result;
				}
			}
		}

		return $this->_item[$pk];
	}
	
}
