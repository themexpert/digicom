<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

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


		JToolBarHelper::title( JText::_( 'COM_DIGICOM_DISCOUNTS_TOOLBAR_TITLE_SITE' ), 'generic.png' );
		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout

		JToolBarHelper::apply('discount.apply');
		JToolBarHelper::save('discount.save');
		JToolBarHelper::cancel('discount.cancel');

		DigiComHelperDigiCom::addSubmenu('discounts');
		$this->sidebar = JHtmlSidebar::render();
		JFactory::getApplication()->input->set('hidemainmenu', true);

		parent::display( $tpl );
	}


}
