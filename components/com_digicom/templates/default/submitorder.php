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
JHtml::_('jquery.framework');
JHTML::_('behavior.formvalidation');

$pg_plugin = $this->pg_plugin;
$configs = $this->configs;
$data = $this->data;
//print_r($items);die;
if ($pg_plugin == 'paypal'){ ?>
<div class="digicom">
	<div class="container-fluid center">
		<div class="logo">
			<a href="<?php echo JURI::root() ;?>">
				<img src="<?php echo JURI::root() . trim($configs->get('store_logo',''));?>" alt="store_logo" border="0">
			</a>
		</div>
		<h3><?php echo JText::_("DSPAYMENT_WITH_PAYPAL") ;?></h3>
		<div class="progress progress-striped active" style="width: 50%; margin: 20px auto 40px auto;">
			<div  id="progressBar" class="bar" style="border-radius: 3px; margin: 0; width: 100%;"></div>
		</div>
		<?php
		echo $data[0];
		?>
	</div>
</div>
<?php } else { ?>
	<h2><?php echo JText::_("PAYMENT_DETAILS_PAGE"); ?></h2>
	<?php echo $data[0]; ?>
<?php } ?>

<?php
echo DigiComHelper::powered_by();
