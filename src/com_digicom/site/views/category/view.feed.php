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

/**
 * HTML View class
 *
 * @since  1.2.2
 */
class DigiComViewCategory extends JViewCategoryfeed
{
  /**
	 * @var    string  The name of the view to link individual items to
	 * @since  1.2.2
	 */
	protected $viewName = 'product';

	/**
	 * Method to reconcile non standard names from components to usage in this class.
	 * Typically overriden in the component feed view class.
	 *
	 * @param   object  $item  The item for a feed, an element of the $items array.
	 *
	 * @return  void
	 *
	 * @since   1.2.2
	 */
	protected function reconcileNames($item)
	{
		// Get description, author and date
		$app               = JFactory::getApplication();
		$params            = $app->getParams();
    $item->introtext   = '<p>' . $item->introtext . '</p>';
		$item->description = $params->get('feed_summary', 0) ? $item->introtext . $item->fulltext : $item->introtext;

		// Add readmore link to description if introtext is shown, show_readmore is true and fulltext exists
		if (!$item->params->get('feed_summary', 0) && $item->params->get('feed_show_readmore', 1) && $item->fulltext)
		{
      // Compute the article slug
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

			// URL link to article
			$link = JRoute::_(DigiComSiteHelperRoute::getProductRoute($item->id, $item->catid, $item->language));

			$item->description .= '<p class="feed-readmore"><a target="_blank" href ="' . $link . '">' . JText::_('COM_DIGICOM_BUTTON_DETAILS') . '</a></p>';
		}
    $metadata = json_decode($item->metadata);

    $item->author = $metadata->author ? $metadata->author : $item->author;
    
	}
}
