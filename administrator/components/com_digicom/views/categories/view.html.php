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

jimport ("joomla.application.component.view");

class DigiComAdminViewCategories extends DigiComView {

	protected $items;
	protected $pagination;

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

		$categories = $this->get('Items');
		$pagination = $this->get('Pagination');

		$this->cats = $categories;
		$this->pagination = $pagination;
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
	
	public function addMediaScript(){
		
		// Load the modal behavior script.
		JHtml::_('behavior.modal');

		// Include jQuery
		JHtml::_('jquery.framework');

		// Build the script.
		$script = array();
		$script[] = '	function jInsertFieldValue(value, id) {';
		$script[] = '		var $ = jQuery.noConflict();';
		$script[] = '		var old_value = $("#" + id).val();';
		$script[] = '		if (old_value != value) {';
		$script[] = '			var $elem = $("#" + id);';
		$script[] = '			$elem.val(value);';
		$script[] = '			$elem.trigger("change");';
		$script[] = '			if (typeof($elem.get(0).onchange) === "function") {';
		$script[] = '				$elem.get(0).onchange();';
		$script[] = '			}';
		$script[] = '			jMediaRefreshPreview(id);';
		$script[] = '		}';
		$script[] = '	}';

		$script[] = '	function jMediaRefreshPreview(id) {';
		$script[] = '		var $ = jQuery.noConflict();';
		$script[] = '		var value = $("#" + id).val();';
		$script[] = '		var $img = $("#" + id + "_preview");';
		$script[] = '		if ($img.length) {';
		$script[] = '			if (value) {';
		$script[] = '				$img.attr("src", "' . JUri::root() . '" + value);';
		$script[] = '				$("#" + id + "_preview_empty").hide();';
		$script[] = '				$("#" + id + "_preview_img").show()';
		$script[] = '			} else { ';
		$script[] = '				$img.attr("src", "")';
		$script[] = '				$("#" + id + "_preview_empty").show();';
		$script[] = '				$("#" + id + "_preview_img").hide();';
		$script[] = '			} ';
		$script[] = '		} ';
		$script[] = '	}';

		$script[] = '	function jMediaRefreshPreviewTip(tip)';
		$script[] = '	{';
		$script[] = '		var $ = jQuery.noConflict();';
		$script[] = '		var $tip = $(tip);';
		$script[] = '		var $img = $tip.find("img.media-preview");';
		$script[] = '		$tip.find("div.tip").css("max-width", "none");';
		$script[] = '		var id = $img.attr("id");';
		$script[] = '		id = id.substring(0, id.length - "_preview".length);';
		$script[] = '		jMediaRefreshPreview(id);';
		$script[] = '		$tip.show();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		
		$options = array(
			'onShow' => 'jMediaRefreshPreviewTip',
		);
		JHtml::_('behavior.tooltip', '.hasTipPreview', $options);
	}
	
}

