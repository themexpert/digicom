<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class TableDiscount extends JTable
{
	public $id = null;
	public $title = null;
	public $code = null;
	public $codelimit = null;
	public $amount = null;
	public $codestart = null;
	public $codeend = null;
	public $forexisting = null;
	public $published = null;
	public $aftertax = null;
	public $promotype = null;
	public $used = null;
	public $ordering = null;
	public $checked_out = null;
	public $checked_out_time = null;

	public function __construct (&$db) {
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
