<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewOrderNew extends JViewLegacy
{

	protected $state;

	protected $item;

	protected $form;

	protected $configs;

	function display( $tpl = null )
	{
		if (!JFactory::getUser()->authorise('core.orders', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		

		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->configs 	= $this->get('configs');
		$this->promocode 	= $this->get('promocode');


		JToolBarHelper::title( JText::_( 'COM_DIGICOM_ORDER_CREATE_NEW_ORDER_TOOLBAR_TITLE_SITE' ), 'generic.png' );
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

		$layout = new JLayoutFile('toolbar.video');
		$bar->appendButton('Custom', $layout->render(array()), 'video');

		JToolBarHelper::save('ordernew.save');
		JToolBarHelper::cancel('order.cancel');

		DigiComHelperDigiCom::addSubmenu('orders');
		$this->sidebar = JHtmlSidebar::render();
		JFactory::getApplication()->input->set('hidemainmenu', true);

		parent::display( $tpl );
	}


}
