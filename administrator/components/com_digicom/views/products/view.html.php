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

jimport( "joomla.application.component.view" );

class DigiComAdminViewProducts extends DigiComView
{

	function display( $tpl = null )
	{
		$layout = JRequest::getVar('layout','');
		if($layout){
			$this->setLayout($layout);
		}
		
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.products', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$configs = $this->_models['config']->getConfigs();
		$this->assign( "configs", $configs );

		$session = JFactory::getSession();
		$session->set('dsproducategory', 0, 'digicom');

		$products = $this->get('Items');
		$pagination = $this->get('Pagination');

		$this->prods = $products;
		$this->pagination = $pagination;

		$cats = $this->_models['category']->getlistCategories();
		$prc = JRequest::getVar( "prc", 0, "request" );
		$state_filter = JRequest::getVar("state_filter", "-1");

		$cselector = DigiComAdminHelper::getCatListProd2( new stdClass, $cats, 1, $prc );
		$this->assign("csel", $cselector);
		$this->assign("prc", $prc);
		$this->assign("state_filter", $state_filter);
		
		//set toolber
		$this->addToolbar();
		
		DigiComAdminHelper::addSubmenu('products');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display( $tpl );
	}


	function select( $tpl = null )
	{
		$configs = $this->_models['config']->getConfigs();
		$this->assign( "configs", $configs );

		$session = JFactory::getSession();
		$prc = $session->get('dsproducategory', 0, 'digicom');
		$prc = JRequest::getVar( "prc", $prc, "request" );
		$session->set('dsproducategory', $prc, 'digicom');
		$products = $this->get('Items');

		$db = JFactory::getDBO();
		foreach ( $products as $key => $prod ) {
			$price = 0;

			switch ( $prod->priceformat )
			{

				case '2': // Don't show price
					$price = '';
					break;

				case '3': // Price and up
					$sql = "SELECT pp.product_id, min(pp.price) as price FROM #__digicom_plans dp
					LEFT JOIN #__digicom_products_plans pp on (dp.id = pp.plan_id)
					WHERE pp.product_id = " . $prod->id . "
					GROUP BY pp.product_id";
					$db->setQuery( $sql );
					$prodprice = $db->loadObject();
					if ( !empty( $prodprice ) )
						$price = DigiComAdminHelper::format_price( $prodprice->price, $configs->get('currency','USD'), true, $configs ) . " and up";
					break;

				case '4': // Price range
					$sql = "SELECT pp.product_id, min(pp.price) as price_min, max(pp.price) as price_max FROM #__digicom_plans dp
					LEFT JOIN #__digicom_products_plans pp on (dp.id = pp.plan_id)
					WHERE pp.product_id = 1
					GROUP BY pp.product_id";
					$db->setQuery( $sql );
					$prodprice = $db->loadObject();
					if ( !empty( $prodprice ) )
						$price = DigiComAdminHelper::format_price( $prodprice->price_min, $configs->get('currency','USD'), true, $configs ) . " - " . DigiComAdminHelper::format_price( $prodprice->price_max, $configs->get('currency','USD'), true, $configs );
					break;

				case '5': // Minimal price
					$sql = "SELECT pp.product_id, min(pp.price) as price FROM #__digicom_plans dp
					LEFT JOIN #__digicom_products_plans pp on (dp.id = pp.plan_id)
					WHERE pp.product_id = " . $prod->id . "
					GROUP BY pp.product_id";
					$db->setQuery( $sql );
					$prodprice = $db->loadObject();
					if ( !empty( $prodprice ) )
						$price = DigiComAdminHelper::format_price( $prodprice->price, $configs->get('currency','USD'), true, $configs );
					break;

				case '1': // Default price
				default:
					$sql = "SELECT pp.product_id, pp.price as price FROM #__digicom_plans dp
					LEFT JOIN #__digicom_products_plans pp on (dp.id = pp.plan_id)
					WHERE pp.default = 1 and pp.product_id = " . $prod->id;
					$db->setQuery( $sql );
					$prodprice = $db->loadObject();
					if ( !empty( $prodprice ) )
						$price = DigiComAdminHelper::format_price( $prodprice->price, $configs->get('currency','USD'), true, $configs );
					break;
			}

			$products[$key]->price = $price;
		}
		$this->assignRef( 'prods', $products );

		$pagination = $this->get( 'Pagination' );
		$this->assignRef( 'pagination', $pagination );

		$cats = $this->_models['category']->getlistCategories();

		$cselector = DigiComAdminHelper::getSelectCatListProd( new stdClass, $cats, 1, $prc );
		$this->assign( "csel", $cselector );
		$this->assign( "prc", $prc );
		
		parent::display( $tpl );

	}

	function selectproductinclude($tpl = null){
		
		$configs = $this->_models['config']->getConfigs();
		$this->assign( "configs", $configs );

		$products = $this->get('Items');
		$this->assignRef( 'prods', $products );

		$pagination = $this->get('Pagination');

		$this->prods = $products;
		$this->pagination = $pagination;

		$cats = $this->_models['category']->getlistCategories();
		$prc = JRequest::getVar( "prc", 0, "request" );

		$cselector = DigiComAdminHelper::getSelectCatListProdInclude( new stdClass, $cats, 1, $prc );
		$this->assign( "csel", $cselector );
		$this->assign( "prc", $prc );
		
		parent::display( $tpl );

	}

	function addProduct( $tpl = null ) {

		JToolBarHelper::title( JText::_( 'Products Manager: select product type' ), 'generic.png' );
		JToolBarHelper::cancel();
		
		parent::display( $tpl );
	}

	function editForm( $tpl = null )
	{
		$db = JFactory::getDBO();
		
		$form = $this->get('Form');
		$product = $this->get('product');
		
		// Bind the form to the data.
		if ($form && $product)
		{
			$form->bind($product);
		}
		$cats = $this->get("listCategories");
		
		$this->assign( "form", $form );
		$this->assign( "item", $product );
		$this->assign( "cats", $cats );
		
		$configs = $this->_models['config']->getConfigs();
		
		$this->assign( "configs", $configs );
		
		//set toolber
		$this->addToolbarEdit($product);
		DigiComAdminHelper::addSubmenu('products');
		$this->sidebar = JHtmlSidebar::render();

		
		parent::display( $tpl );
	}

	function productincludeitem( $tpl = null ) {
		// Rand ID
		$id_rand = uniqid (rand ());
		$this->assign( "id_rand", $id_rand );

		$include = array(
			'id' => $id_rand,
			'name' => JText::_('Select product'),
			'plans' => null
		);

		$this->assign( "newinclude", $include );
		parent::display( $tpl );
	}

	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'VIEWDSADMINPRODUCTS' ), 'generic.png' );
		
		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
				'title' => JText::_( 'VIEWDSADMINPRODUCTS' ),
				'class' => 'product'
			);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.products');
		$bar->appendButton('Custom', $layout->render(array()), 'products');
		
		
		//JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::custom('copy', 'copy.png', 'copy.png', 'JLIB_HTML_BATCH_COPY', true, false);
		JToolBarHelper::divider();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();
	}
	
	public function addMediaScript(){
		
		// Load the modal behavior script.
		JHtml::_('behavior.modal');

		// Include jQuery
		JHtml::_('jquery.framework');

		// Build the script.
		$script = array();
		$script[] = '	function jInsertFieldValue(value, id) {';
		$script[] = '		var $ = jQuery.noConflict();';
		$script[] = '		var old_value = $("#" + id).val();';
		$script[] = '		if (old_value != value) {';
		$script[] = '			var $elem = $("#" + id);';
		$script[] = '			$elem.val(value);';
		$script[] = '			$elem.trigger("change");';
		$script[] = '			if (typeof($elem.get(0).onchange) === "function") {';
		$script[] = '				$elem.get(0).onchange();';
		$script[] = '			}';
		$script[] = '			jMediaRefreshPreview(id);';
		$script[] = '		}';
		$script[] = '	}';

		$script[] = '	function jMediaRefreshPreview(id) {';
		$script[] = '		var $ = jQuery.noConflict();';
		$script[] = '		var value = $("#" + id).val();';
		$script[] = '		var $img = $("#" + id + "_preview");';
		$script[] = '		if ($img.length) {';
		$script[] = '			if (value) {';
		$script[] = '				$img.attr("src", "' . JUri::root() . '" + value);';
		$script[] = '				$("#" + id + "_preview_empty").hide();';
		$script[] = '				$("#" + id + "_preview_img").show()';
		$script[] = '			} else { ';
		$script[] = '				$img.attr("src", "")';
		$script[] = '				$("#" + id + "_preview_empty").show();';
		$script[] = '				$("#" + id + "_preview_img").hide();';
		$script[] = '			} ';
		$script[] = '		} ';
		$script[] = '	}';

		$script[] = '	function jMediaRefreshPreviewTip(tip)';
		$script[] = '	{';
		$script[] = '		var $ = jQuery.noConflict();';
		$script[] = '		var $tip = $(tip);';
		$script[] = '		var $img = $tip.find("img.media-preview");';
		$script[] = '		$tip.find("div.tip").css("max-width", "none");';
		$script[] = '		var id = $img.attr("id");';
		$script[] = '		id = id.substring(0, id.length - "_preview".length);';
		$script[] = '		jMediaRefreshPreview(id);';
		$script[] = '		$tip.show();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		
		$options = array(
			'onShow' => 'jMediaRefreshPreviewTip',
		);
		JHtml::_('behavior.tooltip', '.hasTipPreview', $options);
	}
	
	
	
	/**
	 * Add the page title and toolbar for edit
		*
	 * @since   1.6
	 */
	protected function addToolbarEdit($product)
	{
		$isNew = ($product->id < 1);
		$text = $isNew ? JText::_( 'New' ) : JText::_( 'JACTION_EDIT' ) . ' : ' . $product->name;
		
		if (isset($product->product_type) && !empty($product->product_type)) {
			$product_type = $product->product_type;
		} else {
			$product_type = JRequest::getVar('product_type','reguler');
		}

		
		switch($product_type){
			case 1:
				$title = JText::_('VIEWPRODPRODTYPEDR');
				break;
			case 2:
				$title = JText::_('VIEWPRODPRODTYPESP');
				break;
			case 3:
				$title = JText::_('VIEWPRODPRODTYPEPAK');
				break;
			case 4:
				$title = JText::_('VIEWPRODPRODTYPESERV');
				break;
			case 0:
			default:
				$title = JText::_('VIEWPRODPRODTYPEDNR');
			break;
		}
		
		JToolBarHelper::title($title . ' ' . JText::_( 'DSPROD' ) . " : " . $text);
		// Instantiate a new JLayoutFile instance and render the layout
		$bar = JToolBar::getInstance('toolbar');
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => $text,
			'class' => 'product'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');

		JToolBarHelper::save();
		JToolBarHelper::save2new();
		
		if($product->id){
			JToolBarHelper::save2copy();
		}

		JToolBarHelper::spacer();
		JToolBarHelper::apply();
		JToolBarHelper::divider();
		JToolBarHelper::cancel( 'cancel', 'JTOOLBAR_CLOSE' );

	}
	
	
}

