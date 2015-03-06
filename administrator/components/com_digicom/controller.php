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

jimport ('joomla.application.component.controller');

class DigiComAdminController extends DigiComController {

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_digicom".DS."helpers".DS."chart.php");

		$view   = $this->input->get('view', 'DigiComAdmin');

		parent::display();

		return $this;
	}
	/**
	 * @TODO: get this function works on Joomla 3
	 */
	function createDigiComMenu(){
		$db = JFactory::getDBO();
		$sql = "SELECT COUNT(*) from #__menu_types WHERE `menutype` = 'DigiCom-Menu' AND `title` = 'DigiCom Menu'";
		$db->setQuery($sql);
		$count = $db->loadResult();
		if(intval($count) == 0){
			$sql = "
				INSERT IGNORE INTO #__menu_types(`menutype`, `title`, `description`)
				VALUES
					('DigiCom-Menu', 'DigiCom Menu', 'DigiCom Menu')
			";
			$db->setQuery($sql);
			if($db->query()){
				$sql = "SELECT `extension_id` FROM #__extensions WHERE `name`='com_digicom' AND `element`='com_digicom'";
				$db->setQuery($sql);
				$db->query();
				$componentid = intval($db->loadResult());
				$sql = "
							INSERT IGNORE INTO `#__menu` (`menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`)
							VALUES
								('DigiCom-Menu', 'Products', 'products', '', 'products', 'index.php?option=com_digicom&view=products&layout=list', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 289, 290, 0, '*', 0),
								('DigiCom-Menu', 'Cart', 'cart', '', 'cart', 'index.php?option=com_digicom&view=cart', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 291, 292, 0, '*', 0),
								('DigiCom-Menu', 'Categories', 'categories', '', 'categories', 'index.php?option=com_digicom&view=categories&layout=listthumbs', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 293, 294, 0, '*', 0),
								('DigiCom-Menu', 'My Downloads', 'my-licenses', '', 'my-licenses', 'index.php?option=com_digicom&view=licenses', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 295, 296, 0, '*', 0),
								('DigiCom-Menu', 'My Orders', 'my-orders', '', 'my-orders', 'index.php?option=com_digicom&view=orders', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 297, 298, 0, '*', 0),
								('DigiCom-Menu', 'My Profile', 'my-profile', '', 'my-profile', 'index.php?option=com_digicom&view=profile', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 299, 300, 0, '*', 0),
								('DigiCom-Menu', 'Login', 'digi-login', '', 'digi-login', 'index.php?option=com_digicom&view=profile&layout=login', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 301, 302, 0, '*', 0)
							";
				$db->setQuery($sql);
				if (!$db->query()) {
					echo "FIX ME: admin/controller.php, line: ".__LINE__.'<br />';
					echo $db->getErrorMsg();
				}
			}
		}
	}

}
