<?php
/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

defined('_JEXEC') or die;


class TableDiscount extends JTable
{
	var $id = null;
	var $title = null;
	var $code = null;
	var $codelimit = null;
	var $amount = null;
	var $codestart = null;
	var $codeend = null;
	var $forexisting = null;
	var $published = null;
	var $aftertax = null;
	var $promotype = null;
	var $used = null;
	var $ordering = null;
	var $checked_out = null;
	var $checked_out_time = null;

	function __construct (&$db) {
		parent::__construct('#__digicom_promocodes', 'id', $db);
	}


	function store($updateNulls = false) {
		if ((int)$this->codeend != 0 ) {
//			$end_date = parse_date($dat);
		} else {

			$this->codeend = 0;
		}
		if (!parent::store($updateNulls = false)) return false;
		return true;

	}

	function storeProducts($promoid)
	{
		$db = JFactory::getDBO();

		if ($promoid)
		{
			$sql = "DELETE FROM `#__digicom_promocodes_products`
					WHERE `promoid`=$promoid";
			$db->setQuery($sql);
			$db->query();

			foreach($_POST['items_product_id'] as $item)
			{
				if ((int) $item)
				{
					$sql = "INSERT INTO `#__digicom_promocodes_products`(`promoid`, `productid`)
							VALUES($promoid, $item)";
					$db->setQuery($sql);
					$db->query();
				}
			}
		}
	}

	function storeOrders($promoid)
	{
		$db = JFactory::getDBO();

		if ($promoid)
		{
			$sql = "DELETE FROM `#__digicom_promocodes_orders`
					WHERE `promoid`=$promoid";
			$db->setQuery($sql);
			$db->query();

			foreach($_POST['orders_product_id'] as $item)
			{
				if ((int) $item)
				{
					$sql = "INSERT INTO `#__digicom_promocodes_orders`(`promoid`, `productid`)
							VALUES($promoid, $item)";
					$db->setQuery($sql);
					$db->query();
				}
			}
		}
	}
};
