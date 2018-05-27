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
<?php
$layoutData = array(
  'selector' => 'paymentAlertModal',
  'params'   => array(
                  'title' => JText::_("COM_DIGICOM_WARNING"),
                  'height' => '400px',
                  'width' => '1280',
                  'footer' => '<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>'
                ),
  'body'     => JText::_("COM_DIGICOM_CART_PAYMENT_METHOD_REQUIRED_NOTICE")
);
echo JLayoutHelper::render('bt3.modal.main', $layoutData);

if($this->configs->get('askterms',0) == '1' && ($this->configs->get('termsid',0) > 0)):

  $result = DigiComSiteHelperDigicom::getJoomlaArticle($this->configs->get('termsid',0));
  $terms_title    = $result->title;
  $terms_content  = $result->text;

  $layoutData = array(
    'selector' => 'termsShowModal',
    'params'   => array(
                    'title' => $terms_title,
                    'height' => 'auto',
                    'width' => '1280',
                    'footer' => '<button data-digicom-id="action-agree" class="action-agree btn btn-success" data-dismiss="modal" aria-hidden="true">' . JText::_("COM_DIGICOM_CART_AGREE_TERMS_BUTTON") . '</button> <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>'

                  ),
    'body'     => $terms_content
  );
  echo JLayoutHelper::render('bt3.modal.main', $layoutData);

  $layoutData = array(
    'selector' => 'termsAlertModal',
    'params'   => array(
                    'title' => JText::_("COM_DIGICOM_WARNING"),
                    'height' => '400px',
                    'width' => '1280',
                    'footer' => '<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>'
                  ),
    'body'     => JText::_("COM_DIGICOM_CART_ACCEPT_TERMS_CONDITIONS_REQUIRED_NOTICE")
  );
  echo JLayoutHelper::render('bt3.modal.main', $layoutData);

endif;

if($this->configs->get('askprivacy', 0) == '1' && ($this->configs->get('privacyid', 0) > 0)):

  $result = DigiComSiteHelperDigicom::getJoomlaArticle($this->configs->get('privacyid',0));
  $privacy_title = $result->title;
  $privacy_content = $result->text;

  $layoutData = array(
    'selector' => 'privacyShowModal',
    'params'   => array(
                    'title' => $privacy_title,
                    'height' => 'auto',
                    'width' => '1280',
                    'footer' => '<button data-digicom-id="action-agree-privacy" class="action-agree btn btn-success" data-dismiss="modal" aria-hidden="true">' . JText::_("COM_DIGICOM_CART_AGREE_PRIVACY_BUTTON") . '</button> <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>'

                  ),
    'body'     => $privacy_content
  );
  echo JLayoutHelper::render('bt3.modal.main', $layoutData);

  $layoutData = array(
    'selector' => 'privacyAlertModal',
    'params'   => array(
                    'title' => JText::_("COM_DIGICOM_WARNING"),
                    'height' => '400px',
                    'width' => '1280',
                    'footer' => '<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>'
                  ),
    'body'     => JText::_("COM_DIGICOM_CART_ACCEPT_PRIVACY_AGREEMENT_REQUIRED_NOTICE")
  );
  echo JLayoutHelper::render('bt3.modal.main', $layoutData);

endif;
?>
