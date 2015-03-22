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

class DigiComViewDiscount extends JViewLegacy
{

	protected $state;

	protected $item;

	protected $form;
	
	protected $configs;
	
	

	function display( $tpl = null )
	{

		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->configs 	= $this->get('configs');

		
		JToolBarHelper::title( JText::_( 'COM_DIGICOM_DISCOUNTS_TOOLBAR_TITLE' ), 'generic.png' );
		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_DISCOUNTS_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		JToolBarHelper::apply('discount.apply');
		JToolBarHelper::save('discount.save');
		JToolBarHelper::cancel('discount.cancel');
		
		DigiComHelperDigiCom::addSubmenu('discounts');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display( $tpl );
	}

	
}
