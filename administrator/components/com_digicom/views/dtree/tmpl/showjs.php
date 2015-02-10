<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 432 $
 * @lastmodified	$LastChangedDate: 2013-11-18 04:29:45 +0100 (Mon, 18 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

$task = JRequest::getVar("task", "");

if($task == "export"){
	return true;
}
?>

var d = new dTree('d');

d.add(0,-1,'&nbsp;<?php echo JText::_('VIEWTREEDSCP');?>','index.php?option=com_digicom','','','components/com_digicom/assets/images/icons/small/a.png');

d.add(840,0,'&nbsp;<?php echo JText::_('VIEWTREESETTINGMANAGER');?>','','VIEWDSADMINSETTINGS','','components/com_digicom/assets/images/icons/small/settings.png','components/com_digicom/assets/images/icons/small/settings.png');

d.add(841,840,'&nbsp;<?php echo JText::_('VIEWTREEGENERAL');?>','index.php?option=com_digicom&controller=configs&task2=general','','','components/com_digicom/assets/images/icons/small/settings.png','components/com_digicom/assets/images/icons/small/settings.png');
d.add(842,840,'&nbsp;<?php echo JText::_('VIEWCONFIGCURRENCY');?>','index.php?option=com_digicom&controller=configs&task2=payments','','','components/com_digicom/assets/images/icons/small/payments.png','components/com_digicom/assets/images/icons/small/payments.png');
d.add(842,840,'&nbsp;<?php echo JText::_('VIEWTREEEMAILS');?>','index.php?option=com_digicom&controller=configs&task2=email','','','components/com_digicom/assets/images/icons/small/settings.png','components/com_digicom/assets/images/icons/small/settings.png');
d.add(846,840,'&nbsp;<?php echo JText::_('VIEWTREECONTENT');?>','index.php?option=com_digicom&controller=configs&task2=content','','','components/com_digicom/assets/images/icons/small/orders.png');
d.add(849,840,'&nbsp;<?php echo JText::_('VIEWTREESTORE');?>','index.php?option=com_digicom&controller=configs&task2=store','','','components/com_digicom/assets/images/icons/small/orders.png');
d.add(847,840,'&nbsp;<?php echo JText::_('VIEWTREETAX');?>','index.php?option=com_digicom&controller=configs&task2=tax','','','components/com_digicom/assets/images/icons/small/payments.png');
d.add(848,840,'&nbsp;<?php echo JText::_('VIEWTREELAYOUTS');?>','index.php?option=com_digicom&controller=configs&task2=layout','','','components/com_digicom/assets/images/icons/small/payments.png');

//d.add(810,0,'&nbsp;<?php echo JText::_('VIEWTREEMANAGERS');?>','','','','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png');
d.add(811,0,'&nbsp;<?php echo JText::_('VIEWTREECATEGORIES');?>','index.php?option=com_digicom&controller=categories&section=com_digicom_product','','','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png');

d.add(812,0,'&nbsp;<?php echo JText::_('VIEWTREEPRODUCTS');?>','index.php?option=com_digicom&controller=products','<?php echo JText::_('VIEWTREEPRODUCTS');?>','','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png');
d.add(8121,812,'&nbsp;<?php echo JText::_('DIGICOM_NEWPRODUCT_DOWNLOADABLE');?>','index.php?option=com_digicom&controller=products&task=add&producttype=0','<?php echo JText::_('VIEWPRODPRODTYPEDNR');?>','','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png');
//d.add(8122,812,'&nbsp;<?php echo JText::_('DIGICOM_NEWPRODUCT_DOWNLOADABLE_DOMAIN');?>','index.php?option=com_digicom&controller=products&task=add&producttype=1','<?php echo JText::_('VIEWPRODPRODTYPEDR');?>','','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png');
//d.add(8123,812,'&nbsp;<?php echo JText::_('DIGICOM_NEWPRODUCT_SHIPABLE');?>','index.php?option=com_digicom&controller=products&task=add&producttype=2','<?php echo JText::_('VIEWPRODPRODTYPESP');?>','','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png');
d.add(8124,812,'&nbsp;<?php echo JText::_('DIGICOM_NEWPRODUCT_PACKAGE');?>','index.php?option=com_digicom&controller=products&task=add&producttype=3','<?php echo JText::_('VIEWPRODPRODTYPEPAK');?>','','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png');
//d.add(8125,812,'&nbsp;<?php echo JText::_('DIGICOM_NEWPRODUCT_SERVICE');?>','index.php?option=com_digicom&controller=products&task=add&producttype=4','<?php echo JText::_('VIEWPRODPRODTYPESERV');?>','','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png','components/com_digicom/assets/images/icons/small/packages.png');

d.add(913,0,'&nbsp;<?php echo JText::_('VIEWTREEFILEMANAGER');?>','index.php?option=com_digicom&controller=filemanager','','','components/com_digicom/assets/images/icons/small/packages.png');

d.add(912,0,'&nbsp;<?php echo JText::_('VIEWTREEPRODUCTCLASSES');?>','index.php?option=com_digicom&controller=productclasses','','','components/com_digicom/assets/images/icons/small/packages.png');
d.add(815,0,'&nbsp;<?php echo JText::_('VIEWTREECUSTOMERS');?>','index.php?option=com_digicom&controller=customers','','','components/com_digicom/assets/images/icons/small/customers.png','components/com_digicom/assets/images/icons/small/customers.png');
d.add(814,0,'&nbsp;<?php echo JText::_('VIEWTREEORDERS');?>','index.php?option=com_digicom&controller=orders','','','components/com_digicom/assets/images/icons/small/orders.png','components/com_digicom/assets/images/icons/small/orders.png');
d.add(813,0,'&nbsp;<?php echo JText::_('VIEWTREELICENCES');?>','index.php?option=com_digicom&controller=licenses','','','components/com_digicom/assets/images/icons/small/zones.png','components/com_digicom/assets/images/icons/small/zones.png');


//d.add(819,0,'&nbsp;<?php echo JText::_('VIEWTREELANGUAGES');?>','index.php?option=com_digicom&controller=languages','','','components/com_digicom/assets/images/icons/small/language.png','components/com_digicom/assets/images/icons/small/language.png');
d.add(820,0,'&nbsp;<?php echo JText::_('VIEWTREEPROMO');?>','index.php?option=com_digicom&controller=promos','','','components/com_digicom/assets/images/icons/small/language.png','components/com_digicom/assets/images/icons/small/language.png');

/**
   Subscriptions
 */
d.add(831,0,'&nbsp;<?php echo JText::_('VIEWTREESUBSCRIP');?>','','','','components/com_digicom/assets/images/icons/small/zones.png','components/com_digicom/assets/images/icons/small/zones.png');
d.add(832,831,'&nbsp;<?php echo JText::_('VIEWTREEPLANS');?>','index.php?option=com_digicom&controller=plans','Subscription Plans','','components/com_digicom/assets/images/icons/small/zones.png');
d.add(834,831,'&nbsp;<?php echo JText::_('VIEWTREEEMAILREMINDER');?>','index.php?option=com_digicom&controller=emailreminders','Email reminders','','components/com_digicom/assets/images/icons/small/other_components.png');

/**
  Logs
 */

d.add(833,0,'&nbsp;<?php echo JText::_('VIEWTREELOGS');?>','','','','components/com_digicom/assets/images/icons/small/support.png','components/com_digicom/assets/images/icons/small/support.png');
d.add(8331,833,'&nbsp;<?php echo JText::_('VIEWTREEPSYSTEMEMAILS');?>','index.php?option=com_digicom&controller=logs&task=systememails','System Emails','','components/com_digicom/assets/images/icons/small/forum.png');
d.add(8332,833,'&nbsp;<?php echo JText::_('VIEWTREEDOWNLOAD');?>','index.php?option=com_digicom&controller=logs&task=download','Download','','components/com_digicom/assets/images/icons/small/manual.png');
d.add(8333,833,'&nbsp;<?php echo JText::_('VIEWTREEPURCHASES');?>','index.php?option=com_digicom&controller=logs&task=purchases','Purchases','','components/com_digicom/assets/images/icons/small/payments.png');


d.add(800,0,'&nbsp;<?php echo JText::_('VIEWTREETAX');?>','','','','components/com_digicom/assets/images/icons/small/support.png','components/com_digicom/assets/images/icons/small/support.png');
d.add(801,800,'&nbsp;<?php echo JText::_('VIEWTREEPRODTAXCLASS');?>','index.php?option=com_digicom&controller=taxproductclasses','Product tax classes','','components/com_digicom/assets/images/icons/small/forum.png');
d.add(802,800,'&nbsp;<?php echo JText::_('VIEWTREECUSTTAXCLASS');?>','index.php?option=com_digicom&controller=taxcustomerclasses','Customer tax classes','','components/com_digicom/assets/images/icons/small/manual.png');
d.add(803,800,'&nbsp;<?php echo JText::_('VIEWTREETAXRATE');?>','index.php?option=com_digicom&controller=taxrates','Tax rates','','components/com_digicom/assets/images/icons/small/other_components.png');
d.add(804,800,'&nbsp;<?php echo JText::_('VIEWTREETAXRULE');?>','index.php?option=com_digicom&controller=taxrules','Tax rules','','components/com_digicom/assets/images/icons/small/campaigns.png');

d.add(851,0,'&nbsp;<?php echo JText::_('VIEWTREESTATS');?>','index.php?option=com_digicom&controller=stats','','','components/com_digicom/assets/images/icons/small/reports.png','components/com_digicom/assets/images/icons/small/reports.png');

d.add(830,0,'&nbsp;<?php echo JText::_('VIEWTREEABOUT');?>','index.php?option=com_digicom&controller=about','About DigiCom','','components/com_digicom/assets/images/icons/small/about.png');

function putDtree () {
	if(document.getElementById("dtreespan")){
		document.getElementById("dtreespan").innerHTML = d;
	}
}

window.onload = putDtree;
