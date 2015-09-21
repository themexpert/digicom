<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
$table_column = 4;
$processor 		= $this->session->get('processor','1');
?>
<div class="dc-cart-price table-responsive">
  <table id="dc-cart-price-table" class="table well" width="100%">
    <tr valign="top">
      <td class="general_text" colspan="<?php echo $table_column-1; ?>" valign="bottom">
        <?php echo JText::_("COM_DIGICOM_CART_IF_PROMOCODE_LABEL"); ?>
      </td>
      <td>
          <div class="dc-cart-subtotal-title text-right">
            <?php echo JText::_("COM_DIGICOM_SUBTOTAL");?>
          </div>
      </td>
      <td>
          <div class="dc-cart-subtotal-price text-right" data-digicom-id="cart_subtotal" style="font-size: 15px;text-align:right;">
            <?php echo DigiComSiteHelperPrice::format_price($this->tax['price'], $this->tax['currency'], true, $this->configs); ?>
          </div>
      </td>
    </tr>

    <tr valign="top">
      <td colspan="<?php echo $table_column - 1; ?>" >
        <div class="input-group">
          <input type="text" data-digicom-id="promocode" name="promocode" class="form-control" value="<?php echo $this->promocode; ?>" />
          <div class="input-group-btn">
            <button class="btn btn-default" type="submit" onclick="Digicom.refreshCart();">
              <i class="ico-gift"></i> <?php echo JText::_("COM_DIGICOM_CART_PROMOCODE_APPLY"); ?>
            </button>
          </div>
        </div>
      </td>
      <td nowrap="nowrap" style="text-align: center;">
        <ul class="list-unstyled">
          <?php if ($this->tax['discount_calculated']): ?>
            <li class="dc-cart-discount-title" style="font-size: 15px;text-align:right;">
            <?php echo JText::_("COM_DIGICOM_PROMO_DISCOUNT");?>
          </li>
          <?php endif; ?>

          <li class="dc-cart-total-title" style="font-weight: bold;font-size: 18px;text-align:right;">
            <?php echo JText::_("COM_DIGICOM_TOTAL");?>
          </li>
        </ul>
      </td>
      <td nowrap="nowrap" style="text-align: center;">
        <ul style="margin: 0; padding: 0;list-style-type: none;">
          <?php if ($this->tax['discount_calculated']): ?>
          <li class="dc-cart-discount-price" data-digicom-id="cart_discount" style="font-size: 15px;text-align:right;">
            <?php echo DigiComSiteHelperPrice::format_price($this->tax['promo'], $this->tax['currency'], true, $this->configs); ?>
          </li>
          <?php endif; ?>

          <li class="dc-cart-total-price" data-digicom-id="cart_total" style="font-weight: bold;font-size: 18px;text-align:right;">
            <?php echo DigiComSiteHelperPrice::format_price($this->tax['taxed'], $this->tax['currency'], true, $this->configs); ?>
          </li>
        </ul>
      </td>
    </tr>
    <tr>
      <td colspan="<?php echo $table_column - 1; ?>">
        <?php if($this->configs->get('askterms',0) == '1' && ($this->configs->get('termsid') > 0)):?>
          <div class="dc-accept-terms">
            <?php $agreeterms = JFactory::getApplication()->input->get("agreeterms", ""); ?>
            <input type="checkbox" name="agreeterms" data-digicom-id="agreeterms"<?php echo ($agreeterms? ' checked="checked"' : ''); ?> style="margin-top: 0;"/>

            <a href="#" data-digicom-id="showterms"><?php echo JText::_("COM_DIGICOM_CART_AGREE_TERMS"); ?></a>
          </div>
        <?php endif;?>
      </td>
      <td>
          <div class="text-right">
            <?php echo JText::_('COM_DIGICOM_PAYMENT_METHOD'); ?>
          </div>
      </td>
      <td>
        <div class="pull-right">
          <?php echo DigiComSiteHelperDigicom::getPaymentPlugins($this->configs, $processor); ?>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="<?php echo $table_column + 1; ?>">
        <div class="text-right">
          <button
            type="button"
            class="btn btn-warning"
            style="float:right;margin-top:10px;"
            onclick="Digicom.goCheckout();">
              <?php echo JText::_('COM_DIGICOM_CHECKOUT');?>
              <i class="ico-ok-sign"></i>
          </button>
        </div>
      </td>
    </tr>
  </table>
</div>
