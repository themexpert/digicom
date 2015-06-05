<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

//----------------------------------------------
// New Order Html Template 
//----------------------------------------------
?>

<!DOCTYPE html>
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <title>[EMAIL_TYPE]</title>
   </head>
   <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
      <div id="wrapper" style="background-color: [BASE_BG_COLOR]; margin: 0; padding: 70px 0 70px 0; -webkit-text-size-adjust: none !important; width: 100%;">
         <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
            <tr>
               <td align="center" valign="top">
                  <div id="template_header_image">
                    <p style="margin-top: 0;">
                      [HEADER_IMAGE]
                    </p>           
                  </div>
                  <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container" style="box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important; background-color: [TMPL_BG_COLOR];color: [TMPL_COLOR];border: 1px solid #dcdcdc; border-radius: 3px !important;">
                     <tr>
                        <td align="center" valign="top">
                           <!-- Header -->
                           <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header" style='background-color: [BASE_COLOR]; border-radius: 3px 3px 0 0 !important; color: #ffffff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;'>
                              <tr>
                                 <td>
                                    <h1 style='color: #ffffff; display: block; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; padding: 36px 48px; text-align: left; text-shadow: 0 1px 0 #7797b4; -webkit-font-smoothing: antialiased;'>
                                      [EMAIL_HEADER]
                                    </h1>
                                 </td>
                              </tr>
                           </table>
                           <!-- End Header -->
                        </td>
                     </tr>
                     <tr>
                        <td align="center" valign="top">
                           <!-- Body -->
                           <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
                              <tr>
                                 <td valign="top" id="body_content" style="background-color: [TMPL_BG_COLOR];">
                                    <!-- Content -->
                                    <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                       <tr>
                                          <td valign="top" style="padding: 48px;">
                                             <div id="body_content_inner" style='color: #737373; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: left;'>
                                                <p style="margin: 0 0 16px;">Hi, [CUSTOMER_USER_NAME]</p>
                                                <p style="margin: 0 0 16px;">This is an email to let you inform about your order from [SITENAME]. The order is as follows: </p>
                                                
                                                <h2 style='color: [BASE_COLOR]; display: block; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 16px 0 8px; text-align: left;'>
                                                  Order #[ORDER_ID] (<time datetime="[ORDER_DATE]">[ORDER_DATE]</time>)
                                                </h2>

                                                <p style='color: [BASE_COLOR]; display: block; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 15px; font-weight: bold; line-height: 130%; margin: 16px 0 8px; text-align: left;'>
                                                 [PRODUCTS]
                                                </p>

                                                <h3 style="color:[BASE_COLOR];display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:16px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left">
                                                  Store address
                                                </h3>
                                                <p>[STORE_ADDRESS]</p>
                                                <p>Phone:[STORE_PHONE]</p>

                                             </div>
                                          </td>
                                       </tr>
                                    </table>
                                    <!-- End Content -->
                                 </td>
                              </tr>
                           </table>
                           <!-- End Body -->
                        </td>
                     </tr>
                     <tr>
                        <td align="center" valign="top">
                           <!-- Footer -->
                           <table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
                              <tr>
                                 <td valign="top" style="padding: 0; -webkit-border-radius: 6px;">
                                    <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                       <tr>
                                          <td colspan="2" valign="middle" id="credit" style="padding: 0 48px 48px 48px; -webkit-border-radius: 6px; border: 0; color: #99b1c7; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center;">
                                             <p>[FOOTER_TEXT]</p>
                                          </td>
                                       </tr>
                                    </table>
                                 </td>
                              </tr>
                           </table>
                           <!-- End Footer -->
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
         </table>
      </div>
   </body>
</html>