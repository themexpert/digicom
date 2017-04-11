<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
JLoader::discover('DigiComSiteHelper', JPATH_SITE . '/components/com_digicom/helpers/');

class plgContentDigiCom extends JPlugin{

	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	public $configs = null;

	/**
	 * Plugin that retrieves contact information for contact
	 *
	 * @param   string   $context  The context of the content being passed to the plugin.
	 * @param   mixed    &$row     An object with a "text" property
	 * @param   mixed    $params   Additional parameters. See {@see PlgContentContent()}.
	 * @param   integer  $page     Optional page number. Unused. Defaults to zero.
	 *
	 * @return  boolean	True on success.
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{

		$allowed_contexts = array('com_content.category', 'com_content.article', 'com_content.featured', 'mod_custom.content');

		if (!in_array($context, $allowed_contexts))
		{
			// return true;
		}

		// Simple performance check to determine whether bot should process further
		if (strpos($article->text, 'digicom') === false && strpos($article->text, 'digicom') === false)
		{
			return true;
		}

		$db 	= JFactory::getDbo();
		$document = JFactory::getDocument();

		// Expression to search for(digicom id)
		$regex = '/{digicom\s*.*?}/i';
		// find all instances of content and put in $matches
		preg_match_all($regex, $article->text, $matches_v);// $value->introtext
		//if we have content on txt replace them
		if(!empty($matches_v)){
			$configs = $this->getConfigs();

			foreach($matches_v[0] as $row_v => $item){

				$item = str_replace("{", "", $item);
				$item = str_replace("}", "", $item);
				$search = explode(" ", $item);

				//an array with parameter of content
				//Array ( [0] => DigiCom [1] => id=1 [2] => align=right [3] => quantity=yes )
				foreach($search as $s_row => $s_value){
					$final = explode("=",$s_value);
					if(isset($final[1])){
						$replace[$final[0]] = $final[1];
					}
				}

				//perform replace
				if(isset($replace['id'])){
					//select product from digicom_product with id = $replace['id']
					$productid = $replace['id'];
					$query = 'SELECT * FROM #__digicom_products WHERE id = "'.$productid.'" and hide_public="0"';
					$db->setQuery($query);
					$product = $db->loadAssoc();
				}

				$html = '';

				if(!empty($product))
				{	
					$info = array(
						'params' 	=> $this->params,
						'product' 	=> $product,
						'configs' 	=> $configs
					);

					$html = $this->buildLayout($info);
				}
				// $html = $this->getProductsForm( $replace['cid'], $replace );
	
				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$text = '{'. $item . '}';
				$article->text = preg_replace("|$text|", $html, $article->text, 1);

			}//end foreach
		}

		return true;

	}

	/**
	 * Don't allow categories to be deleted if they contain items or subcategories with items
	 *
	 * @param   string  $context  The context for the content passed to the plugin.
	 * @param   object  $data     The data relating to the content that was deleted.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onContentBeforeDelete($context, $data)
	{

		// Skip plugin if we are deleting something other than categories
		if ($context != 'com_digicom.category')
		{
			return true;
		}

		// Check if this function is enabled.
		if (!$this->params->def('check_categories', 1))
		{
			return true;
		}

		$extension = JFactory::getApplication()->input->getString('extension');

		// Default to true if not a core extension
		$result = true;

		$tableInfo = array(
			'com_digicom' => array('table_name' => '#__digicom_products')
		);

		// Now check to see if this is a known core extension
		if (isset($tableInfo[$extension]))
		{
			// Get table name for known core extensions
			$table = $tableInfo[$extension]['table_name'];

			// See if this category has any content items
			$count = $this->_countItemsInCategory($table, $data->get('id'));

			// Return false if db error
			if ($count === false)
			{
				$result = false;
			}
			else
			{
				// Show error if items are found in the category
				if ($count > 0)
				{
					$msg = JText::sprintf('COM_DIGICOM_CATEGORIES_DELETE_NOT_ALLOWED', $data->get('title')) .
						JText::plural('COM_DIGICOM_CATEGORIES_N_ITEMS_ASSIGNED', $count);
					JError::raiseWarning(403, $msg);
					$result = false;
				}

				// Check for items in any child categories (if it is a leaf, there are no child categories)
				if (!$data->isLeaf())
				{
					$count = $this->_countItemsInChildren($table, $data->get('id'), $data);

					if ($count === false)
					{
						$result = false;
					}
					elseif ($count > 0)
					{
						$msg = JText::sprintf('COM_DIGICOM_CATEGORIES_DELETE_NOT_ALLOWED', $data->get('title')) .
							JText::plural('COM_DIGICOM_CATEGORIES_HAS_SUBCATEGORY_ITEMS', $count);
						JError::raiseWarning(403, $msg);
						$result = false;
					}
				}
			}

			return $result;
		}
	}

	/**
	 * Get count of items in a category
	 *
	 * @param   string   $table  table name of component table (column is catid)
	 * @param   integer  $catid  id of the category to check
	 *
	 * @return  mixed  count of items found or false if db error
	 *
	 * @since   1.6
	 */
	private function _countItemsInCategory($table, $catid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Count the items in this category
		$query->select('COUNT(id)')
			->from($table)
			->where('catid = ' . $catid);
		$db->setQuery($query);

		try
		{
			$count = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());

			return false;
		}

		return $count;
	}

	/**
	 * Get count of items in a category's child categories
	 *
	 * @param   string   $table  table name of component table (column is catid)
	 * @param   integer  $catid  id of the category to check
	 * @param   object   $data   The data relating to the content that was deleted.
	 *
	 * @return  mixed  count of items found or false if db error
	 *
	 * @since   1.6
	 */
	private function _countItemsInChildren($table, $catid, $data)
	{
		$db = JFactory::getDbo();

		// Create subquery for list of child categories
		$childCategoryTree = $data->getTree();

		// First element in tree is the current category, so we can skip that one
		unset($childCategoryTree[0]);
		$childCategoryIds = array();

		foreach ($childCategoryTree as $node)
		{
			$childCategoryIds[] = $node->id;
		}

		// Make sure we only do the query if we have some categories to look in
		if (count($childCategoryIds))
		{
			// Count the items in this category
			$query = $db->getQuery(true)
				->select('COUNT(id)')
				->from($table)
				->where('catid IN (' . implode(',', $childCategoryIds) . ')');
			$db->setQuery($query);

			try
			{
				$count = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JError::raiseWarning(500, $e->getMessage());

				return false;
			}

			return $count;
		}
		else
			// If we didn't have any categories to check, return 0
		{
			return 0;
		}
	}

	/*
	* method buildLayoutPath
	* @layout = ask for tmpl file name, default is default, but can be used others name
	* return propur file to take htmls
	*/
	function buildLayoutPath($layout)
	{
		if(empty($layout)) $layout = "default";

		$app = JFactory::getApplication();

		// core path
		$core_file 	= dirname(__FILE__) . '/' . $this->_name . '/tmpl/' . $layout . '.php';

		// override path from site active template
		$override	= JPATH_BASE .'/templates/' . $app->getTemplate() . '/html/plugins/' . $this->_type . '/' . $this->_name . '/' . $layout . '.php';

		if(JFile::exists($override))
		{
			$file = $override;
		}
		else
		{
	  		$file =  $core_file;
		}

		return $file;

	}

	/*
	* method buildLayout
	* @vars = object with product, order, user info
	* @layout = tmpl name
	* Builds the layout to be shown, along with hidden fields.
	* @return html
	*/

	function buildLayout($vars, $layout = 'default' )
	{

		//Load the layout & push variables
		ob_start();
		$layout = $this->buildLayoutPath($layout);
		include($layout);
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public function getConfigs(){
		if( !$this->configs ) {
			$config = JComponentHelper::getComponent('com_digicom');
			$this->configs = $config->params;
		}
		return $this->configs;
	}

	
}
