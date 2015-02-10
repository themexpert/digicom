<?php
/*
// JoomlaWorks "AllVideos" Plugin for Joomla! 1.5.x - Version 3.3
// Copyright (c) 2006 - 2010 JoomlaWorks Ltd. All rights reserved.
// Released under the GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
// More info at http://www.joomlaworks.gr
// Designed and developed by the JoomlaWorks team
// *** Last update: February 18th, 2010 ***
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

?>

<span class="avPlayerContainer">
	<span style="width:<?php echo $output->playerWidth; ?>px;" class="avPlayerSubContainer<?php if(!$output->lightboxLink && !$output->downloadLink && !$output->embedLink): ?> avPlayerSubContainerClean<?php endif; ?>">

		<span id="<?php echo $output->playerID; ?>" class="avPlayerBlock"<?php if($output->lightboxLink || $output->downloadLink || $output->embedLink) echo ' style="padding-bottom:8px;"'; ?>>
			<?php echo $output->player; ?>
		</span>

		<?php if($output->lightboxLink): ?>
		<a class="avLightbox" href="<?php echo $output->lightboxLink; ?>" title="<?php echo JText::_('Click to view video in a lightbox popup'); ?>"<?php if(!$output->downloadLink && !$output->embedLink) echo ' style="margin:0;padding:0;border:none;"'; ?>><?php echo JText::_('Dim lights'); ?></a>
		<?php endif; ?>

		<?php if($output->downloadLink): ?>
		<a class="avDownload" href="<?php echo $output->downloadLink; ?>" title="<?php echo JText::_('Click to download media'); ?>"<?php if(!$output->embedLink) echo ' style="margin:0;padding:0;border:none;"'; ?>><?php echo JText::_('Download'); ?></a>
		<?php endif; ?>

		<?php if($output->embedLink): ?>
		<?php echo JText::_('Embed'); ?> <span class="avEmbed" id="<?php echo $output->embedLink; ?>" title="<?php echo JText::_('Click to select'); ?>"><b><?php echo JText::_('Embed this video on your site'); ?></b></span>
		<?php endif; ?>

	</span>
</span>
