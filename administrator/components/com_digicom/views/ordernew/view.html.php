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

class DigiComViewOrderNew extends JViewLegacy
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
		$this->promocode 	= $this->get('promocode');

		
		JToolBarHelper::title( JText::_( 'COM_DIGICOM_ORDER_CREATE_NEW_ORDER_TOOLBAR_TITLE' ), 'generic.png' );
		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_ORDER_CREATE_NEW_ORDER_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		JToolBarHelper::save('ordernew.save');
		JToolBarHelper::cancel('order.cancel');
		
		DigiComHelperDigiCom::addSubmenu('orders');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display( $tpl );
	}

	
}
