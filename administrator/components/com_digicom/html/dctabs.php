<?php
/**
  @package	 Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license	 GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
  Utility class for Tabs elements.
 *
 * @package	 Joomla.Libraries
 * @subpackage  HTML
 * @since	   1.6
 */
jimport( 'joomla.html.html.tabs' );

abstract class JHtmlDCTabs
{
	public static $isJ25 = null;

	public static function isJ25()
	{
		if(self::$isJ25 === null){
			$jv= new JVersion();
			self::$isJ25 = ($jv->RELEASE == '2.5');
		}
		return self::$isJ25;
	}
	/**
	 * Creates a panes and creates the JavaScript object for it.
	 *
	 * @param   string  $group   The pane identifier.
	 * @param   array   $params  An array of option.
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	public static function start($group = 'tabs', $params = array())
	{
		if(self::isJ25()){
			return JHtml::_( 'tabs.start',$group, $params );
		} else {
			return JHtml::_('bootstrap.startTabSet', $group, $params );
		}
	}

	/**
	 * Close the current pane
	 *
	 * @return  string  HTML to close the pane
	 *
	 * @since   1.6
	 */
	public static function end()
	{
		if(self::isJ25()){
			return JHtml::_( 'tabs.end' );
		} else {
			return JHtml::_('bootstrap.endTabSet');
		}
	}

	public static function addTab($selector, $id, $title) {
		if(self::isJ25()){
			return JHtml::_( 'tabs.panel', $title, $id );
		} else {
			return JHtml::_('bootstrap.addTab', $selector, $id, $title );
		}
	}

	public static function endTab()
	{
		if(self::isJ25()){
			return '';
		} else {
			return JHtml::_('bootstrap.endTab');
		}
	}
}
