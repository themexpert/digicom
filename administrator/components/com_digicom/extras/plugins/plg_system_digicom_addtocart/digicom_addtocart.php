<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
defined('DS') or define ('DS',DIRECTORY_SEPARATOR);
// Import library dependencies
jimport('joomla.event.plugin');
//$mainframe->registerEvent( 'onPrepareContent', 'botMosDigiCom'  );
//error_reporting(0);
require_once JPATH_SITE.DS.'components'.DS.'com_digicom'.DS.'helpers'.DS.'helper.php';

class plgSystemDigiCom_Addtocart extends JPlugin{
	
	function __construct( &$subject, $params ) {
		$this->loadLanguage('plg_system_digicom_addtocart', JPATH_SITE);
		parent::__construct( $subject, $params );
	}
	
	public $configs = null;
	
	public function getConfigs(){
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}
	
	public function onAfterInitialise(){
//		$document = JFactory::getDocument();
//		$document->addStyleSheet(JURI::root()."components/com_digicom/assets/css/digicom.css");
	}

	function onAfterRender(){
		$app 	= JFactory::getApplication();
		$db 	= JFactory::getDbo();
		if($app->isAdmin()){
			return;
		}
		global $Itemid;
		$document = JFactory::getDocument();
		$params = $this->params;		
		$orient = $params->get( 'orient', 0 );

		$body = JResponse::getBody();

		// simple performance check to determine whether bot should process further
		if (strpos($body, 'digicom') === false) {
			return true;
		} else {
			$regex = '/{digicom\s*.*?}/i';
			// find all instances of mambot and put in $matches
			preg_match_all($regex, $body, $matches_v);// $value->introtext
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

							//get field for this products 
							$where1[] = " f.published=1 ";
							$where1[] = " fp.publishing=1 ";
							$where1[] = " fp.productid=".$productid;
							$sql = "
								SELECT f.name, f.options, f.id, fp.publishing, fp.mandatory
								FROM
									#__digicom_customfields f
								LEFT JOIN #__digicom_prodfields fp
									on (f.id=fp.fieldid)"
							.(count ($where1)>0? "
							WHERE ".implode (" AND ", $where1):"");
							$db->setQuery($sql);
							$fields = $db->loadObjectList();
							$productfields[$productid] = $fields;
						}
						$html = '';

						if(!empty($product)){
							$onsubmit = "";
							if(isset($productfields[$productid]) && !empty($productfields[$productid])){
								//generate uniqueid
								$id_mambot = uniqid('');
								$script = 'function prodformsubmit'.$id_mambot.'(){
												var mandatory = new Object();
												var i;';
								foreach($productfields[$productid] as $j => $v){
									$script .= "mandatory[".$j."] = new Object();";
									$script .= "mandatory[".$j."]['fld'] = '".$id_mambot.$v->id."';\n";
									$script .= ($v->mandatory == 1)?"mandatory[".$j."]['req']=1;\n":"mandatory[".$j."]['req']=0;\n";
								}
								
								$script .= 'for (i in mandatory) {
											if (mandatory[i][\'req\'] == 1) {
												var el = document.getElementById(mandatory[i][\'fld\']);
												if (el.selectedIndex < 1) {
													alert ("Select all required fields");
													return false;
												}
											}
										}
										return true;
									}';
									
								$document = JFactory::getDocument();
								$document->addScriptDeclaration($script);
								$onsubmit = " onSubmit='return prodformsubmit".$id_mambot."();' ";
							}
							
							$produsid = $replace['id'];
							$sqls = 'SELECT catid FROM #__digicom_product_categories where productid=\''.$replace['id'].'\'';
							$db->setQuery($sqls);

							$categorieid = $db->loadResult();
							$link = JURI::root().'index.php';

							$html = '<form id="prform" name="prform" method="post" action="'.$link.'"'.$onsubmit.'>';
							if(isset($replace['align'])){
								$align = " align='".$replace['align']."' ";
							} else {
								$align = "";
							}
							foreach($productfields[$productid] as $j => $v){ ?>
								<?php 
									$optlen = strlen ("Select ".$v->name);
									$html .= $v->name;
									$html .= ($v->mandatory == 1)?"<span class='error' style='color:red'>*</span>":"";
									$html .=":<select style=\"width:".($optlen*10)."px\" name=\"field".$v->id."\" id=\"".$id_mambot.$v->id."\">
								<option value=\"-1\" selected>Select ".$v->name."</option>";
								$options = explode ("\n", $v->options);
								foreach($options as $i1 => $v1){
									$html .="<option value='".$i1."'>".$v1."</option>";
								}
								$html .="</select>";
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
							$plan = null;
							if( $plan_id ) {
								$sql = "SELECT 
											*
										FROM
											`#__digicom_products_plans`
										WHERE
											`product_id` = {$productid} AND plan_id={$plan_id}";
								$db->setQuery( $sql );
								$plan = $db->loadObject();
							}else{
								$sql = "SELECT 
											*
										FROM
											`#__digicom_products_plans`
										WHERE
											`product_id` = {$productid} AND `default`=1";
								$db->setQuery( $sql );
								$plan = $db->loadObject();
							}
							if ($show_price&&$plan) { // display price
								$html .= "<h3>".DigiComHelper::format_price2($plan->price,$configs->get('currency','USD'), true, $configs)."</h3>";
							}
							$html .= ($plan_id&&$plan)?"<input type=\"hidden\" name=\"plan_id\" value=\"".$plan_id."\"/>":"";
							$html .= "
								<input name=\"pid\" type=\"hidden\" id=\"product_id\" value=\"".$replace['id']."\">
								<input name=\"cid\" type=\"hidden\" value=\"".$categorieid."\">
								<input type=\"submit\" name=\"Button\" class=\"btn btn-foo btn-lg\" value=\"".JText::_('PLG_DIGICOM_ADDTOCART_ADD_TO_CART_BTN_LBL')."\">
								<input type=\"hidden\" name=\"controller\" value=\"digicomCart\"/>
								<input type=\"hidden\" name=\"task\" value=\"add\"/>
								<input type=\"hidden\" name=\"option\" value=\"com_digicom\"/>
								<input type=\"hidden\" name=\"Itemid\" value=\"".$Itemid."\"/>
								<input type=\"hidden\" name=\"from_add_plugin\" value=\"1\"/>
							";
							$html .= ( $plan_id && $plan )?"<input type=\"hidden\" name=\"plan_id\" value=\"".$plan_id."\"/>":"";
							
							$html .= "</form>";
						}
					} else {
						$html = $this->getProductsForm( $replace['cid'], $replace );
					}
					//replace in intro text mambots with html table
					$body = str_replace("{".$mambots_v."}",$html, $body);
					
					JResponse::setBody($body);
					unset($html);
					unset($replace);
				}//end foreach
			}
			
			
		}//end if
	}
	

	
	public function getProductsForm( $catid, $replace ) {
		$db 		= JFactory::getDbo();
		$configs 	= $this->getConfigs();
		global 		$Itemid;
		if(!$Itemid){
			$Itemid = JRequest::getVar('Itemid');
		}
		$sql = 'SELECT 
					`p`.*, 
					`pc`.catid,
					`pp`.`price`, 
					`pp`.`plan_id`
				FROM
					`#__digicom_products` AS `p`
						LEFT JOIN
					`#__digicom_product_categories` AS `pc` ON `p`.`id`= `pc`.`productid`
						LEFT JOIN
					`#__digicom_products_plans` AS `pp` ON `p`.`id` = `pp`.`product_id`
						AND `pp`.`default` = 1
				WHERE
					p.access = 1 and p.published = 1 AND
					`pc`.`catid`='.$catid.'
				ORDER BY `p`.`name`';
		$db->setQuery( $sql );
		$items = $db->loadObjectList();
		$productfields = array();
		if(!count($items)) return false;
		$html = '<ul class="list-unstyled">';
		foreach($items as $item){
			$html .= '<li class="col-md-3">';
			
			$productid = $item->id;
			// get field for this products 
				$where1[] = " f.published=1 ";
				$where1[] = " fp.publishing=1 ";
				$where1[] = " fp.productid=".$item->id;
				$sql = "select f.name, f.options, f.id, fp.publishing, fp.mandatory from #__digicom_customfields f left join #__digicom_prodfields fp on (f.id=fp.fieldid)"
						.(count ($where1)>0? " where ".implode (" and ", $where1):"");
				$db->setQuery($sql);
				$fields = $db->loadObjectList();
				$productfields[$item->id] = $fields;
			// 
			$onsubmit = "";
			if(isset($productfields[$productid]) && !empty($productfields[$productid])){
				//generate uniqueid
				$id_mambot = uniqid('');
				$script = 'function prodformsubmit'.$id_mambot.'(){
								var mandatory = new Object();
								var i;';

				foreach($productfields[$productid] as $j => $v){
					$script .= "mandatory[".$j."] = new Object();";
					$script .= "mandatory[".$j."]['fld'] = '".$id_mambot.$v->id."';\n";
					$script .= ($v->mandatory == 1)?"mandatory[".$j."]['req']=1;\n":"mandatory[".$j."]['req']=0;\n";
				}

				$script .= 'for (i in mandatory) {
							if (mandatory[i][\'req\'] == 1) {
								var el = document.getElementById(mandatory[i][\'fld\']);
								if (el.selectedIndex < 1) {
									alert ("Select all required fields");
									return false;
								}
							}
						}
						return true;
					}';

				$document = JFactory::getDocument();
				$document->addScriptDeclaration($script);
				$onsubmit = " onSubmit='return prodformsubmit".$id_mambot."();' ";
			}
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

			foreach($productfields[$productid] as $j => $v){ 
				$optlen = strlen ("Select ".$v->name);
				$html .= $v->name;
				$html .= ($v->mandatory == 1)?"<span class='error' style='color:red'>*</span>":"";
				$html .=":<select style=\"width:".($optlen*10)."px\" name=\"field".$v->id."\" id=\"".$id_mambot.$v->id."\">
				<option value=\"-1\" selected>Select ".$v->name."</option>";
				$options = explode ("\n", $v->options);
				foreach($options as $i1 => $v1){
					$html .="<option value='".$i1."'>".$v1."</option>";
				}
				$html .="</select>";
			}

			$html .= "<input name=\"qty\" type=\"hidden\" value=\"1\">";
//			$html .= "<span>".$item->id."</span>";
//			$html .= "<span>".$item->name."</span>";
			
			$html .= ( $item->plan_id )?"<input type=\"hidden\" name=\"plan_id\" value=\"".$item->plan_id."\"/>":"";
			$html .= "
				<input name=\"pid\" type=\"hidden\" id=\"product_id\" value=\"".$item->id."\">
				<input name=\"cid\" type=\"hidden\" value=\"".$catid."\">
				<input type=\"hidden\" name=\"controller\" value=\"digicomCart\"/>
				<input type=\"hidden\" name=\"task\" value=\"add\"/>
				<input type=\"hidden\" name=\"option\" value=\"com_digicom\"/>
				<input type=\"hidden\" name=\"Itemid\" value=\"".$Itemid."\"/>
				<input type=\"hidden\" name=\"from_add_plugin\" value=\"1\"/>
			";
			
			$html .= '
					<button type="submit" role="button" name="Button" class="btn btn-default btn-block">
			';
			if ( $item->price ) {
				$html .= DigiComHelper::format_price2($item->price,$configs->get('currency','USD'), true, $configs).' | ';
			}
			$html .= '
						'.JText::_("PLG_DIGICOM_ADDTOCART_ADD_TO_CART_BTN_LBL").'</button>
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
