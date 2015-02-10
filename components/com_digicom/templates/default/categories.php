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
	
	<div class="products_list clearfix">
		<div id="viewcontrols" class="well">
			<a href="javascript::" class="gridview">
				<i class="fa fa-th"></i>Grid
			</a>
			<a href="javascript::" class="listview active">
				<i class="fa fa-list">List</i>
			</a>
		</div>
		
		<ul class="list unstyled">
		<?php foreach($this->prods as $key=>$item): ?>
			<li>
				<?php if(!empty($item->images)): ?>
				<div class="pull-left">
					<img src="<?php echo $item->images; ?>" class="img-responsive img-rounded" width="200px" height="200px"/>
				</div>
				<?php endif; ?>
				<div class="pull-left" style="width:49%">
					<h2><?php echo $item->name; ?></h2>
					<div class="text-muted">
						<span class="label label-info">Category</span>
					</div>
					<div class="description">
						<?php echo $item->description; ?>
					</div>
					<a href="#" class="btn btn-mini">Details</a>
				</div>
				<div class="pull-right" style="width:20%">
					<p class="price text-success"><?php echo $item->price; ?></p>
					<a href="#" class="btn btn-success">Add</a>
				</div>
			</li>
		<?php endforeach; ?>
		</ul>


	</div>
	<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?> </div>
</div>
<?php
echo DigiComHelper::powered_by();
