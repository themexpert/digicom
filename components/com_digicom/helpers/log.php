<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComSiteHelperLog {

    /*
     * ip, userip
     */
    protected $ip;

    /*
     * setLog method
     * use to trigger event to set log
     * @type = download,email,purchase,status
     * @hook = from where event triggered or who did it
     * @message = short message about the event
     * @status = quick status about log event, if its complted or not
     * @info = log details or extra info encoded by json formet
     * */
    public function setLog($type, $hook, $message, $info, $status = 'complete')
    {
        $logTable = JTable::getInstance('log');
        $logTable->type     = $type;
        $logTable->hook     = $hook;
        $logTable->message  = $message;
        $logTable->info     = json_encode($info);
        $logTable->status   = $status;

        $logTable->store();

        return true;

    }

}