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
require_once JPATH_SITE . '/components/com_digicom/helpers/digicom.php';

class plgContentDigiCom extends JPlugin{

	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	public $configs = null;

	function __construct( &$subject, $params ) {
		parent::__construct( $subject, $params );
	}

	/**
	 * Plugin that retrieves contact information for contact
	 *
	 * @param   string   $context  The context of the content being passed to the plugin.
	 * @param   mixed    &$row     An object with a "text" property
	 * @param   mixed    $params   Additional parameters. See {@see PlgContentContent()}.
	 * @param   integer  $page     Optional page number. Unused. Defaults to zero.
	 *
	 * @return  boolean	True on success.
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{

		$allowed_contexts = array('com_content.category', 'com_content.article', 'com_content.featured');

		if (!in_array($context, $allowed_contexts))
		{
			return true;
		}

		// Simple performance check to determine whether bot should process further
		if (strpos($article->text, 'digicom') === false && strpos($article->text, 'digicom') === false)
		{
			return true;
		}

		$db 	= JFactory::getDbo();
		$document = JFactory::getDocument();

		// Expression to search for(digicom id)
		$regex = '/{digicom\s*.*?}/i';
		// find all instances of mambot and put in $matches
		preg_match_all($regex, $article->text, $matches_v);// $value->introtext
		//if we have mambot on txt replace them
		if(!empty($matches_v)){
			$configs = $this->getConfigs();

			foreach($matches_v[0] as $row_v => $mambots_v){

				$mambots_v = str_replace("{", "", $mambots_v);
				$mambots_v = str_replace("}", "", $mambots_v);
				$search = explode(" ", $mambots_v);

				//an array with parameter of mambot
				//Array ( [0] => DigiCom [1] => id=1 [2] => align=right [3] => quantity=yes )
				foreach($search as $s_row => $s_value){
					$final = explode("=",$s_value);
					if(isset($final[1])){
						$replace[$final[0]] = $final[1];
					}
				}

				if(!isset($replace['cid'])){
					//perform replace
					if(isset($replace['id'])){
						//select product from digicom_product with id = $replace['id']
						$productid = $replace['id'];
						$query = "SELECT * FROM #__digicom_products WHERE id = '".$productid."'";
						$db->setQuery($query);
						$product = $db->loadAssoc();
					}

					$html = '';

					if(!empty($product)){
						$onsubmit = "";

						$produsid = $replace['id'];
						$categorieid = $product['catid'];
						$link = JURI::root().'index.php';

						$html = '<form id="prform" name="prform" method="post" action="'.$link.'"'.$onsubmit.'>';
						if(isset($replace['align'])){
							$align = " align='".$replace['align']."' ";
						} else {
							$align = "";
						}

						if(isset($replace['quantity']) && $replace['quantity'] == 'yes'){
							$html .= JText::_('Amount')."&nbsp;<select name=\"qty\">";
							for($jj = 1; $jj < 26; $jj++ ){
								$html .=  "<option value=\"".$jj."\">".$jj."</option>";
							}
							$html .= "</select>";
						}
						else{
							$html .= "<input name=\"qty\" type=\"hidden\" value=\"1\">";
						}

						if(isset($replace['close'])){
							$html .= "<input name=\"close\" type=\"hidden\" id=\"close\" value=\"".$replace['close']."\">";
						}

						$plan_id 	= isset($replace['plan_id']) ? $replace['plan_id'] : '';
						$show_price = isset($replace['show_price']) ? $replace['show_price']:1;

						if ($show_price) { // display price
							$html .= "<h3>".DigiComSiteHelperDigicom::format_price2($product['price'],$configs->get('currency','USD'), true, $configs)."</h3>";
						}
						$html .= "
							<input name=\"pid\" type=\"hidden\" id=\"product_id\" value=\"".$replace['id']."\">
							<input name=\"cid\" type=\"hidden\" value=\"".$categorieid."\">

							<input type=\"submit\" name=\"Button\" class=\"btn btn-foo btn-lg\" value=\"".JText::_('PLG_CONTENT_DIGICOM_ADD_TO_CART_BTN_LBL')."\">

							<input type=\"hidden\" name=\"view\" value=\"cart\"/>
							<input type=\"hidden\" name=\"task\" value=\"cart.add\"/>
							<input type=\"hidden\" name=\"option\" value=\"com_digicom\"/>
							<input type=\"hidden\" name=\"from_add_plugin\" value=\"1\"/>
						";

						$html .= "</form>";
					}
				} else {
					$html = $this->getProductsForm( $replace['cid'], $replace );
				}
				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$mambotsfull = '{'. $mambots_v . '}';
				$article->text = preg_replace("|$mambotsfull|", $html, $article->text, 1);

			}//end foreach
		}

		return true;

	}

	public function getConfigs(){
		if( !$this->configs ) {
			$config = JComponentHelper::getComponent('com_digicom');
			$this->configs = $config->params;
		}
		return $this->configs;
	}

	public function getProductsForm( $catid, $replace ) {
		$db 		= JFactory::getDbo();
		$configs 	= $this->getConfigs();

		$sql = 'SELECT
					`p`.*
				FROM
					`#__digicom_products` AS `p`
				WHERE
					p.access = 1 and p.published = 1 AND
					`p`.`catid`='.$catid.'
				ORDER BY `p`.`name`';
		//echo $sql;die;
		$db->setQuery( $sql );
		$items = $db->loadObjectList();
		$productfields = array();
		if(!count($items)) return false;

		$html = '<ul class="list-unstyled unstyled">';
		foreach($items as $item){
			$html .= '<li class="col-md-4 span4">';

			$productid = $item->id;
			$onsubmit = "";

			$html .= '
				<div class="thumbnail">
					<div class="caption">
						<h3>'.JHTML::_('string.truncate', ($item->name), 18).'</h3>
						<p data-toggle="tooltip" title="'.htmlentities($item->description).'">
							'.JHTML::_('string.truncate', ($item->description), 28).'
						</p>
			';

			$link = JUri::root()."index.php";
			$html .= "<form id=\"prform".$item->id."\" name=\"prform\" method=\"post\" action=\"$link\"".$onsubmit.">";
			if(isset($replace['align'])){
				$align = " align='".$replace['align']."' ";
			} else {
				$align = "";
			}

			$html .= "<input name=\"qty\" type=\"hidden\" value=\"1\">";

			$html .= "
				<input name=\"pid\" type=\"hidden\" id=\"product_id\" value=\"".$item->id."\">
				<input name=\"cid\" type=\"hidden\" value=\"".$catid."\">
				<input type=\"hidden\" name=\"view\" value=\"cart\"/>
				<input type=\"hidden\" name=\"task\" value=\"cart.add\"/>
				<input type=\"hidden\" name=\"option\" value=\"com_digicom\"/>
				<input type=\"hidden\" name=\"from_add_plugin\" value=\"1\"/>
			";

			$html .= '
					<button type="submit" role="button" name="Button" class="btn btn-default btn-block">
			';
			if ( $item->price ) {
				$html .= DigiComSiteHelperDigicom::format_price2($item->price,$configs->get('currency'), true, $configs).' | ';
			}
			$html .= '
						'.JText::_("PLG_CONTENT_DIGICOM_ADD_TO_CART_BTN_LBL").'</button>
					</div>
				</div>
			';
			$html .= "</form>";
			$html .='</li>';
		}

		$html .='</ul>';
		return $html;
	}
}
