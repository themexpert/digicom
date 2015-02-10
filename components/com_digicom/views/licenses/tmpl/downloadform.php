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

$platform_options = $this->enc;
$product = $this->product;
$license = $this->license;
global $Itemid;
$q = "'";
define("_CONTINUE", "Continue >>");
define("_DOMAIN", "Domain");
define("_PRODUCT", "Product");
define("_WHEREINFO", "Info");
define("_DEV_DOMAIN","Dev Domain");
define("_HOSTER","Hosting Service");
define("_DOWNLOAD_PRODUCT","Download Product");
define("_DOWNLOAD_MESSAGE",'Highlight your site'.$q.'s PHP version and platform, and enter any sub-domains you'.$q.'d like to use it on <br />(sub domains are optional). <br />Then click the Continue button. <br />For help, click <a href="http://www.ijoomla.com/tutorials/magazine/versionphp.htm" target="_blank">here</a>.<br/>');
define("_PHP_VERSIONS","PHP Versions");
define("_PLATFORM","Platform");
define("_LICENSEONLY","Generate only license file");
define("_HOSTING_SERVICE_NAME", "Hosting Service Name") ;
define("_CONTROL_PANEL", "Control Panel Software") ;
define("_SUBDOMAIN","Subdomain(s)<br>One subdomain per line");
define("_FILL_DOWNLOAD_FORM","You have to select both PHP version and platform");
//JCommonHTML::loadOverlib();
?>
	<form name="form1" method="post" action="index.php">
	<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
<table width="100%">
<tr>
	<td width="100%" valign="top">

		<table width="100%"  border="0" cellspacing="0">
  <tr>
	<td colspan="2"><h2>
	  <?php echo _DOWNLOAD_PRODUCT?>
	</h2></td>
  </tr>
  <?php
  if (JRequest::getVar('submitted','','post') != '') {
	?>
	<tr>
	<td colspan="2"><b><font color="#FF0000">Error: </font></b>	  <?php echo _FILL_DOWNLOAD_FORM?></td>
  </tr>
	<?php
  }
  ?>
  <tr>
	<td colspan="2"><?php echo _DOWNLOAD_MESSAGE?> <br>
	  <br></td>
  </tr>
  <tr class="row1">
	<td width="32%"><b><?php echo _PRODUCT?>:</b></td>
	<td width="68%" height="30"><?php echo isset( $product->name ) ? $product->name : "internal error";?></td>
  </tr>
  <tr class="row2">
	<td><b><?php
	if ( JRequest::getVar('devdownload', '', 'request') != '' )
		echo _DOMAIN;
	else
		echo _DEV_DOMAIN;
	?>:</b></td>
	<td height="30"><?php
	if ( JRequest::getVar('dev_domain', '', 'request') != '' )
		echo isset( $license->domain ) ? $license->domain : "" ;
	else
		echo isset( $license->dev_domain ) ? $license->dev_domain : "" ;
	?></td>
  </tr>

  <tr class="row3">
	<td><b><?php echo _PHP_VERSIONS?>
	  :<span class="error">*</span><br>
	  <br>

	  <table width="100%"  border="0" cellspacing="2" cellpadding="0">
		<tr>
		  <td width="8%"><a href="http://www.ijoomla.com/tutorials/magazine/versionphp.htm" target="_blank"><img src="<?php echo $mosConfig_live_site;?>/components/com_digicom/assets/images/icon_video.gif" width="19" height="12" border="0"></a></td>
		  <td width="92%"><a href="http://www.ijoomla.com/tutorials/magazine/versionphp.htm" target="_blank">Help</a></td>
		</tr>
	  </table>
	  <b><font color="#FF0000">	  </font></b></td>
	<td valign="top" style="vertical-align:top" ><select name="phpVersions" size="5" id="phpVersions">
		<option value="4.0" <?php echo ($res[0]->phpversion == '4.0' ) ? "selected" : ""; ?>>PHP 4.0</option>
		<option value="4.1" <?php echo ($res[0]->phpversion == '4.1' ) ? "selected" : ""; ?>>PHP 4.1</option>
		<option value="4.2" <?php echo ($res[0]->phpversion == '4.2' ) ? "selected" : ""; ?>>PHP 4.2</option>
		<option value="4.3" <?php echo ($res[0]->phpversion == '4.3' ) ? "selected" : ""; ?>>PHP 4.3</option>
		<option value="4.4" <?php echo ($res[0]->phpversion == '4.4' ) ? "selected" : ""; ?>>PHP 4.4</option>
		<option value="5.0" <?php echo ($res[0]->phpversion == '5.0' ) ? "selected" : ""; ?>>PHP 5.0</option>
		<option value="5.1" <?php echo ($res[0]->phpversion == '5.1' ) ? "selected" : ""; ?>>PHP 5.1</option>
		<option value="5.2" <?php echo ($res[0]->phpversion == '5.2' ) ? "selected" : ""; ?>>PHP 5.2</option>
		<option value="5.3" <?php echo ($res[0]->phpversion == '5.3' ) ? "selected" : ""; ?>>PHP 5.3</option>
	  </select>
<?php
$mosConfig_live_site = DigiComHelper::getLiveSite();
?>
	<script language="javascript" type="text/javascript" src="<?php echo $mosConfig_live_site?>/includes/js/overlib_mini.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo $mosConfig_live_site?>/includes/js/overlib_hideform_mini.js"></script>

	  <a style="vertical-align:top" href="javascript: void(0);" onMouseOver="return overlib('<img src=\'<?php echo $mosConfig_live_site."/components/com_digicom/assets/images/sysinfo.jpg"; ?>\' />', CAPTION, '<?php echo _WHEREINFO; ?>', BELOW, LEFT);" onMouseOut="return nd();">
			<img src="<?php echo $mosConfig_live_site."/components/com_digicom/assets/images/tooltip.png";?>" width="12" height="12" border="0" alt="<?php echo _WHEREINFO; ?>" />
		</a>

	  </td>
  </tr>
  <tr class="row1">
	<td><b><?php echo _PLATFORM?>
	  :<span class="error">*</span><br>
	  <br>

	  <table width="100%"  border="0" cellspacing="2" cellpadding="0">
		<tr>
		  <td width="8%"><a href="http://www.ijoomla.com/tutorials/magazine/versionphp.htm" target="_blank"><img src="<?php echo $mosConfig_live_site;?>/components/com_digicom/assets/images/icon_video.gif" width="19" height="12" border="0"></a></td>
		  <td width="92%"><a href="http://www.ijoomla.com/tutorials/magazine/versionphp.htm" target="_blank">Help</a></td>
		</tr>
	  </table>
	  <b><font color="#FF0000">	  </font></b></td>
	<td><select name="platforms[]" size="5" id="platforms">
	<?php
		for ( $i = 0; $i < count ( $platform_options ); $i++ ) {
			echo "<option value=\"$i\" ";
			if (@in_array($i, $res[0]->platform) || $i == $res[0]->platform) echo "selected";
			echo ">{$platform_options[$i]['title']}</option>";
		}
	?>
	</select></td>
  </tr>

  <tr class="row2">
	<td><b><?php echo _HOSTING_SERVICE_NAME?>:</b></td>
	<td><input name="hosting_service_name" cols="20" rows="4" id="hosting_service_name" value="<?php echo (isset($res[0]->hoster)) ? $res[0]->hoster : "" ;?>" <?php echo ((isset($res[0]->panel)&& trim ($res[0]->panel) != '')?"readonly":""); ?>></td>
  </tr>
  <tr>
	<td><b><?php echo _CONTROL_PANEL?>:</b></td>
	<td><input name="control_panel" cols="20" rows="4" id="control_panel" value="<?php echo (isset($res[0]->panel) ? $res[0]->panel : "") ;?>" <?php echo ((isset($res[0]->panel)&& trim ($res[0]->panel) != '')?"readonly":""); ?>></td>
  </tr>

  <tr class="row3" >
	<td><b><?php echo _SUBDOMAIN?>:</b></td>
	<td><textarea name="subdomains" cols="20" rows="4" id="subdomains"></textarea></td>
  </tr>
  <tr>
	<td colspan="2"><div align="center">
	  <input type="submit" name="Submit" value="<?php echo _CONTINUE; ?>">
	</div></td>
  </tr>
</table>
</td>
</table>

<input name="submitted" type="hidden" id="submitted" value="1">
<input name="task" type="hidden" value="download">
<input name="option" type="hidden" value="com_digicom">
<input name="controller" type="hidden" value="Licenses" />
<input name="licid" type="hidden" id="cid" value="<?php echo $license->id?>">
<input name="pid" type="hidden" id="pid" value="<?php echo $product->id?>">
<input name="dev_domain" type="hidden"  value="<?php echo JRequest::getVar('dev_domain', '', 'request')?>">
</form>

<?php echo DigiComHelper::powered_by(); ?>
