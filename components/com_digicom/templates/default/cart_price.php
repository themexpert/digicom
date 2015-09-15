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
<table id="digicomcartpromo" width="100%">
  <tr valign="top">
    <td class="general_text" colspan="<?php echo $table_column+1; ?>" valign="bottom">
      <?php echo JText::_("COM_DIGICOM_CART_IF_PROMOCODE_LABEL"); ?>
    </td>
  </tr>

  <tr valign="top">
    <td colspan="<?php echo $table_column - 1; ?>" >
      <div class="input-append">
        <input type="text" data-digicom-id="promocode" name="promocode" size="15" value="<?php echo $this->promocode; ?>" />
        <button type="submit" class="btn" onclick="Digicom.refreshCart();"><i class="ico-gift"></i> <?php echo JText::_("COM_DIGICOM_CART_PROMOCODE_APPLY"); ?></button>
      </div>

    </td>
    <td nowrap="nowrap" style="text-align: center;">
      <ul style="margin: 0; padding: 0;list-style-type: none;">
        <?php if ($this->tax['discount_calculated']): ?>
          <li class="digi_cart_subtotal_title" style="font-size: 15px;text-align:right;">
            <?php echo JText::_("COM_DIGICOM_SUBTOTAL");?>
          </li>
          <li class="digi_cart_discount_title" style="font-size: 15px;text-align:right;">
          <?php echo JText::_("COM_DIGICOM_PROMO_DISCOUNT");?>
        </li>
        <?php endif; ?>

        <li class="digi_cart_total_title" style="font-weight: bold;font-size: 18px;text-align:right;">
          <?php echo JText::_("COM_DIGICOM_TOTAL");?>
        </li>
      </ul>
    </td>
    <td nowrap="nowrap" style="text-align: center;">
      <ul style="margin: 0; padding: 0;list-style-type: none;">
        <?php if ($this->tax['discount_calculated']): ?>
        <li class="digi_cart_subtotal_price" data-digicom-id="cart_subtotal" style="font-size: 15px;text-align:right;">
          <?php echo DigiComSiteHelperPrice::format_price($this->tax['price'], $this->tax['currency'], true, $this->configs); ?>
        </li>
        <li class="digi_cart_discount_price" data-digicom-id="cart_discount" style="font-size: 15px;text-align:right;">
          <?php echo DigiComSiteHelperPrice::format_price($this->tax['promo'], $this->tax['currency'], true, $this->configs); ?>
        </li>
        <?php endif; ?>

        <li class="digi_cart_total_price" data-digicom-id="cart_total" style="font-weight: bold;font-size: 18px;text-align:right;">
          <?php echo DigiComSiteHelperPrice::format_price($this->tax['taxed'], $this->tax['currency'], true, $this->configs); ?>
        </li>
      </ul>
    </td>
  </tr>
</table>

<div id="digicomcartcontinue" class="row-fluid continue-shopping">
  <div class="span8" style="margin-bottom:10px;">
    <?php if($this->configs->get('askterms',0) == '1' && ($this->configs->get('termsid') > 0)):?>
      <div class="accept-terms">
        <?php $agreeterms = JFactory::getApplication()->input->get("agreeterms", ""); ?>
        <input type="checkbox" name="agreeterms" data-digicom-id="agreeterms"<?php echo ($agreeterms? ' checked="checked"' : ''); ?> style="margin-top: 0;"/>

        <a href="#" data-digicom-id="showterms"><?php echo JText::_("COM_DIGICOM_CART_AGREE_TERMS"); ?></a>
      </div>
    <?php endif;?>
  </div>
  <div class="span4" style="margin-bottom: 10px;">
    <p><strong><?php echo JText::_('COM_DIGICOM_PAYMENT_METHOD'); ?></strong></p>

    <?php echo DigiComSiteHelperDigicom::getPaymentPlugins($this->configs, $processor); ?>

    <div id="html-container"></div>
    <button
      type="button"
      class="btn btn-warning"
      style="float:right;margin-top:10px;"
      onclick="Digicom.goCheckout();">
        <?php echo JText::_('COM_DIGICOM_CHECKOUT');?>
        <i class="ico-ok-sign"></i>
    </button>
  </div>
</div>
