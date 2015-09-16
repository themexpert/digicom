<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
?>
<div class="dc-cart-items table-responsive">
  <table class="dc-cart-items-table table table-striped table-bordered" width="100%">
    <thead>
      <tr valign="top">
        <th width="30%"><?php echo JText::_("COM_DIGICOM_PRODUCT");?></th>
        <th><?php echo JText::_("COM_DIGICOM_PRICE_PLAN");?></th>
        <th><?php echo JText::_("COM_DIGICOM_QUANTITY"); ?></th>

        <?php if ($this->tax['item_discount']){?>
          <th><?php echo JText::_("COM_DIGICOM_PROMO_DISCOUNT"); ?></th>
        <?php } ?>

        <th><?php echo JText::_("COM_DIGICOM_SUBTOTAL");?></th>
        <th><?php echo JText::_("COM_DIGICOM_CART_REMOVE_ITEM");?></th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach($this->items as $itemnum => $item ):
        $item_link = JRoute::_(DigiComSiteHelperRoute::getProductRoute($item->id, $item->catid, $item->language));
        ?>
        <tr>
          <td>

            <a href="<?php echo $item_link; ?>" target="blank"><?php echo $item->name; ?></a>
            <?php if ($this->configs->get('show_validity',1) == 1) : ?>
              <div class="muted">
                <small><?php echo JText::_('COM_DIGICOM_PRODUCT_VALIDITY'); ?> : <?php echo DigiComSiteHelperPrice::getProductValidityPeriod($item); ?></small>
              </div>
            <?php endif; ?>
          </td>

          <td nowrap="nowrap">
            <span data-digicom-id="price<?php echo $item->cid; ?>">
              <?php echo DigiComSiteHelperPrice::format_price($item->price, $item->currency, true, $this->configs); ?>
            </span>
          </td>

          <td align="center" nowrap="nowrap">
            <span class="dc-digicom-details">
              <strong>
                <?php if($this->configs->get('show_quantity',0) == "1") { ?>
                  <input data-digicom-id="quantity<?php echo $item->cid; ?>" type="number" onchange="Digicom.updateCart(<?php echo $item->cid; ?>);" name="quantity[<?php echo $item->cid; ?>]" min="1" class="input-small" value="<?php echo $item->quantity; ?>" size="2" placeholder="<?php echo JText::_('COM_DIGICOM_QUANTITY'); ?>">
                <?php } else {
                  echo $item->quantity;
                } ?>
              </strong>
            </span>
          </td>

          <?php if($this->tax['item_discount']) : ?>
          <td align="center" nowrap="nowrap">
            <span data-digicom-id="discount<?php echo $item->cid; ?>" class="dc-cart-amount">
              <?php
              $value_discount = 0;
              if ( $item->discount > 0)
              {
                $value_discount = $item->discount;
              }
              elseif ( isset($item->percent_discount) && $item->percent_discount > 0)
              {
                $value_discount = ($item->price * $item->percent_discount) / 100;
              }
              echo DigiComSiteHelperPrice::format_price($value_discount, $item->currency, true, $this->configs);?>
            </span>
          </td>
          <?php endif; ?>

          <td nowrap>
            <span data-digicom-id="total<?php echo $item->cid; ?>" class="dc-cart-amount">
              <?php echo DigiComSiteHelperPrice::format_price($item->subtotal-(isset($value_discount) ? $value_discount : 0), $item->currency, true, $this->configs); ?>
            </span>
          </td>

          <td nowrap="nowrap">
            <a href="#" class="btn btn-small btn-danger" onclick="Digicom.deleteFromCart(<?php echo $item->cid;?>);"><i class="glyphicon glyphicon-trash glyphicon-white"></i></a>
          </td>
        </tr>
        <?php
      endforeach;
      ?>
    </tbody>
  </table>
</div>
