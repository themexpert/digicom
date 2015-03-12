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


?>
<div id="digicom">

	<h1 class="digi-page-title">Select plan for: <?php echo $this->lists['product']->name; ?></h1>

	<form action="index.php" method="post">

	<h2>Subcription plan</h2>
	<div>
		<?php echo $this->lists['plans']; ?>
	</div>

	<h2>Payment</h2>
	<div>
		<?php echo $this->lists['plugins']; ?>
	</div>

	<br/>
	<br/>

	<div>
		<input type="submit" value="Submit"/>
	</div>

		<input type="hidden" name="option" value="com_digicom">
		<input type="hidden" name="controller" value="Cart"/>
		<input type="hidden" name="pid" value="<?php echo $this->lists['product']->id; ?>"/>
		<input type="hidden" name="cid" value="<?php echo $this->cid; ?>"/>
		<input type="hidden" name="task" value="add"/>
		<input type="hidden" name="status" value="add"/>
		<!-- input type="hidden" name="task" value="add_subscription_product"/ -->
		<input name="Itemid" type="hidden" value="<?php global $Itemid; echo $Itemid; ?>">
	</form>

	<?php echo DigiComHelper::powered_by(); ?>

</div>
