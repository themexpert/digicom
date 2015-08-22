<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class TableCustomer extends JTable
{
	function TableCustomer( &$db )
	{
		parent::__construct( '#__digicom_customers', 'id', $db );
	}

	function loadCustommer( $id = NULL, $reset = true )
	{
		parent::load( $id );
	}

	function create(){

		// Verify that the alias is unique
		$db 		= JFactory::getDbo();
		$table = JTable::getInstance('Customer', 'Table');

		if ($table->load(array('email' => $this->email)) && ($table->id != $this->id))
		{
			$query 	= $db->getQuery(true);
			// Fields to update.
			$fields = array(
			    $db->quoteName('id') . ' = ' . $this->id
			);
			// Conditions for which records should be updated.
			$conditions = array(
			    $db->quoteName('email') . ' = ' . $db->quote($this->email)
			);
			$query->update($db->quoteName('#__digicom_customers'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$result = $db->execute();

			return $this->store();

		}


		// Create a new query object.
		$query = $db->getQuery(true);

		// Insert columns.
		$columns = array(
			'id',
			'name',
			'email',
			'address',
			'city',
			'state',
			'zipcode',
			'country',
			'phone',
			'payment_type',
			'company',
			'person',
			'taxnum',
			'taxclass'
		);

		// Insert values.
		$values = array(
			$this->id,
			$db->quote($this->name),
			$db->quote($this->email),
			$db->quote($this->address),
			$db->quote($this->city),
			$db->quote($this->state),
			$db->quote($this->zipcode),
			$db->quote($this->country),
			$db->quote($this->phone),
			$db->quote($this->payment_type),
			$db->quote($this->company),
			$db->quote($this->person),
			$db->quote($this->taxnum),
			$db->quote($this->taxclass)
		);

		// Prepare the insert query.
		$query
		    ->insert($db->quoteName('#__digicom_customers'))
		    ->columns($db->quoteName($columns))
		    ->values(implode(',', $values));

		// Set the query using our newly populated query object and execute it.
		$db->setQuery($query);
		return $db->execute();
	}

}
