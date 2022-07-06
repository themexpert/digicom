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
<div>
	<h3>Logs</h3>
	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th>
					<?php echo JText::_( 'JGLOBAL_FIELD_ID_LABEL' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'COM_DIGICOM_TYPE' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'MESSAGE' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'JSTATUS' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'JDATE' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'COM_DIGICOM_IP' ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->order->logs as $key=>$log): ?>
			<tr>
				<td>
					<?php echo $log->id;?>
				</td>
				<td>
					<?php echo $log->type;?>
				</td>
				<td>
					<?php echo $log->message;?>
				</td>
				<td>
					<?php echo $log->status;?>
				</td>
				<td>
					<?php echo $log->created;?>
				</td>
				<td>
					<?php echo $log->ip;?>
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>

	</table>
</div>
