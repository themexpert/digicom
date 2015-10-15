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
<div class="well dc-cart-coupon clearfix">
  <div class="pull-right input-group" style="width:50%;">
    <input
    type="text" data-digicom-id="promocode"
    name="promocode"
    class="form-control"
    placeholder="<?php echo JText::_("COM_DIGICOM_CART_COUPON"); ?>"
    value="<?php echo $this->promocode; ?>"
    />
    <div class="input-group-btn">
      <button class="btn btn-default" type="submit" onclick="Digicom.refreshCart();">
        <i class="ico-gift"></i> <?php echo JText::_("COM_DIGICOM_CART_PROMOCODE_APPLY"); ?>
      </button>
    </div>
  </div>
  <p class="lead no-margin">
    <?php echo JText::_("COM_DIGICOM_CART_IF_PROMOCODE_LABEL"); ?>
  </p>
</div>

<div class="dc-cart-price">
  <table id="dc-cart-price-table" class="table well" width="100%">
    <tr>
      <td>
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
          <?php echo DigiComSiteHelperDigicom::getPaymentPlugins($this->configs); ?>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="3">
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
