<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 375 $
 * @lastmodified	$LastChangedDate: 2013-10-21 11:33:34 +0200 (Mon, 21 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

jimport ("joomla.application.component.view");

class DigiComViewProducts extends DigiComView {

	function display($tpl = null) {
		
		$catid =  JFactory::getApplication()->input->get('cid',0);
		$pid =  JFactory::getApplication()->input->get('pid',0);
		
		$conf = $this->_models['config']->getConfigs();
		$prod = $this->_models['product']->getProduct($pid);
		
		$this->assignRef("configs", $conf);
		$this->assign("catid", $catid);
		$this->assignRef('prod', $prod);
		
		$this->triggerPlugin($prod);
		$this->setMetaData($prod);
		
		//$template = new DigiComTemplateHelper($this);
		//$template->rander('products');
		
		parent::display();
	}

	public static function getRelatedCategory($product_id){
		$db = JFactory::getDBO();
		$sql = "select `catid`
				from `#__digicom_product_categories`
				where `productid`=".intval($product_id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}

	public static function getPageURL(){
		$pageURL = 'http';
		if(@$_SERVER["HTTPS"] == "on"){
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if($_SERVER["SERVER_PORT"] != "80"){
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}
		else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	public static function setMetaData($prod){
		$document = JFactory::getDocument();
		$document->setTitle($prod->metatitle);
		$document->setMetaData('keywords', $prod->metakeywords); 
		$document->setMetaData('description', $prod->metadescription);
	}
	public static function triggerPlugin($prod){
		
		$app			= JFactory::getApplication("site");
		$dispatcher		= JDispatcher::getInstance();
		$params			= $app->getParams('com_digicom');
		$limitstart = 0;
		
		JPluginHelper::importPlugin('content');
		$article = new stdClass;
		$article->text = $prod->description;
		$article->fulltext = $prod->description;
		$results = $dispatcher->trigger('onPrepareContent', array (& $article, & $params, $limitstart));
		$prod->description = $article->text;

		$article = new stdClass;
		$article->text = $prod->fulldescription;
		$article->fulltext = $prod->description;
		$results = $dispatcher->trigger('onPrepareContent', array (& $article, & $params, $limitstart));
		$prod->fulldescription = $article->text;
		
		return true;
	}
}
