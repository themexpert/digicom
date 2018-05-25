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
                $item->orders = $this->getOrders();
                                
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
    
    function getOrders(){

        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Orders', 'DigicomModel', array('ignore_request' => true));
        // Set application parameters in model
		$app       = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('filter.userid', JFactory::getuser()->id);
		$model->setState('list.start', 0);
        $model->setState('list.limit', 999999999);
        
        $items = $model->getItems();

        foreach($items as $key=>$item)
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('p').'.*')
                    ->select($db->quoteName('od.quantity'))
                    ->select($db->quoteName('od.package_type'))
                    ->select($db->quoteName('od.price', 'price'))
                    ->select($db->quoteName('od.amount_paid', 'amount_paid'))
                    ->select($db->quoteName('od.userid'))
                    ->from($db->quoteName('#__digicom_products','p'))
                    ->from($db->quoteName('#__digicom_orders_details','od'))
                    ->where($db->quoteName('p.id').'='.$db->quoteName('od.productid'))
                    ->where($db->quoteName('od.orderid').'='.$db->quote($item->id));
				$db->setQuery($query);
				$item->details = $db->loadObjectList();
        }

        return $items;
    }
	
}
