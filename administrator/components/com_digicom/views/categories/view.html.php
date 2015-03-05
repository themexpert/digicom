<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

class DigiComAdminViewCategories extends DigiComView {

	protected $items;

	protected $pagination;

	protected $state;

	function display($tpl=null)
	{
		$layout=  JRequest::getCmd('layout','');
		if($layout){
			$this->setLayout($layout);
		}
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.categories', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$this->cats = $this->get('Items');

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->cats as &$item)
		{
			$this->ordering[$item->parent_id][] = $item->id;
		}

		//print_r($this->cats);die;

		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('state');
		//set toolber
		$this->addToolbar();
		
		DigiComAdminHelper::addSubmenu('categories');
		$this->sidebar = DigiComAdminHelper::renderSidebar();
		
		parent::display($tpl);
	}

	function editForm($tpl = null)
	{
		$form = $this->get('Form');
		$category = $this->get('category');
		
		// Bind the form to the data.
		if ($form && $category)
		{
			$form->bind($category);
		}
		
		$this->assign( "form", $form );
		$this->assign( "item", $category );
		
		$isNew = ($category->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('DSCATEGORY').":<small>[".$text."]</small>");
		// 		$title = JText::_('VIEWPRODPRODTYPEDR');
		$bar = JToolBar::getInstance('toolbar');
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_('DSCATEGORY').":<small>[".$text."]</small>",
			'class' => 'product'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::divider();
			JToolBarHelper::cancel();

		} else {
			JToolBarHelper::apply();
			JToolBarHelper::divider();
			JToolBarHelper::cancel ('cancel', 'Close');

		}		

		DigiComAdminHelper::addSubmenu('categories');
		$this->sidebar = DigiComAdminHelper::renderSidebar();

		parent::display($tpl);

	}
	
	public function getParentCategory($row){
		$db = JFactory::getDBO();
		if ($row->id == '') {
			$row->id = 0;
		}
		$query = 'SELECT s.id AS value, s.* FROM #__digicom_categories AS s  
					WHERE s.id NOT IN('.$row->id.')
					ORDER BY s.parent_id ASC ,s.ordering ASC';
		$db->setQuery($query);
		$mitems = $db->loadObjectList();

		$children = array();
		if ( $mitems )
		{
			foreach ( $mitems as $v )
			{
				$v->title 		= $v->name;
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
		$mitems = array();
		// @$mitems[] = JHTML::_('select.option', 0, JText::_('PLG_OBSS_INTER_HIKASHOP_ALL_CATEGORIES'));
		$msg = JText::_('HELPERTOP');
		$mitems[] = JHTML::_('select.option', 0, $msg );
		foreach ($list as $item)
		{
			$item->treename = JString::str_ireplace('&#160;', '- ', $item->treename);
			$mitems[] = JHTML::_('select.option', $item->id, $item->treename);
		}
		$output = JHTML::_('select.genericlist',  $mitems, 'parent_id', 'class="inputbox" size="10"', 'value', 'text', $row->parent_id );
		return $output;
	}

	
	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('VIEWDSADMINCATEGORIES'), 'generic.png');

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'VIEWDSADMINCATEGORIES' ),  
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::divider();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();
	}
	
	
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.name' => JText::_('JGLOBAL_TITLE'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.hits' => JText::_('JGLOBAL_HITS'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}

