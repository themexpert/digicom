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
JHtml::script(JURI::root()."media/digicom/assets/js/category_layout.js", true);
?>
<div class="digicom-wrapper com_digicom categories">
	
	<h2 class="page-title category-title"><?php echo $this->category->name; ?></h2>
	<div class="category_info media">
		<div class="pull-left">
			<img class="img-rounded" src="<?php echo $this->category->image; ?>"/>
		</div>
		<div class="media-body">
			<?php echo $this->category->description; ?>
		</div>
	</div>

	<div class="products_list">
		<div id="viewcontrols">
			<a class="gridview">
				<i class="fa fa-th fa-2x"></i>Grid
			</a>
			<a class="listview active">
				<i class="fa fa-list fa-2x">List</i>
			</a>
		</div>
		<ul class="list">
			<li>
				<img src="images/barbie_8.jpg" height="250px" width="250px"/>
				<section class="list-left">
				<span class="title">PRODUCT NAME</span>
				<p>Product description goes here. Aliquam tincidunt diam varius 
				ultricies auctor. Vivamus faucibus risus tempus, 
				adipiscing justo
				</p>
				<div class="icon-group-btn">           
				<a title="Add to Cart" href="javascript:void(0);" class="btn-cart"> 
				<span class="icon-cart"></span>
				<span class="icon-cart-text">
				Add To Cart      
				</span>
				</a>
				<a title="Add to wishlist" href="#" class="btn-wishlist">
				<span class="icon-wishlist"></span>
				<span class="icon-wishlist-text">
				Add To Wishlist      
				</span>
				</a>
				<a title="Add to Compare" href="#" class="btn-compare">
				<span class="icon-compare"></span>
				<span class="compare-text">
				Add To Compare      
				</span>
				</a>
				</div>  
				</section>
				<section class="list-right">
				<span class="price">$50</span>
				<span class="detail"><a class="button">Details</a></span>
				</section>
			</li>
		</ul>


	</div>
</div>
<?php
echo DigiComHelper::powered_by();
