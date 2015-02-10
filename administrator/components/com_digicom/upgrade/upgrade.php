<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

//error_reporting(E_ALL);

class dsUpgrader {

	var $db = null;
	var $upgradefolder = '';
	var $log = array(); 
	var $error = array();

	function dsUpgrader() {
		$this->db = JFactory::getDBO();
		$this->upgradefolder = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_digicom' . DS . 'upgrade' . DS;
	}

	function getVersion()
	{
		$parser		= JFactory::getXMLParser('Simple');

		// Load the local XML file first to get the local version
		$xml		= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_digicom' . DS . 'manifest.xml';

		$parser->loadFile( $xml );
		$document	=& $parser->document;

		$element		=& $document->getElementByPath( 'version' );
		$version		= $element->data();

		return $version;
	}

	function getUpgradeVersions() {
		$upgrades = JFolder::folders( $this->upgradefolder );
		return $upgrades;
	}

	function run() {

		if( !ini_get('safe_mode') ) set_time_limit(0);

		$result = array();

		if ( file_exists($this->upgradefolder) ) {
			$upgrades = self::getUpgradeVersions();
			$db = $this->db;
			foreach( $upgrades as  $upgrade ) {
				$file = $this->upgradefolder . $upgrade . DS . 'upgrade.php';
				$result[] = $upgrade;
				if (file_exists( $file )) {
					include_once $file;
				}
			}
		}

		return $result;
	}

	function once(){

		if( !ini_get('safe_mode') ) set_time_limit(0);

		if ( file_exists($this->upgradefolder) ) {
			$upgrade = JRequest::getVar('upgrade', '');
			if (!empty($upgrade)) {
				$db = $this->db;
				$file = $this->upgradefolder . $upgrade . DS . 'upgrade.php';
				include_once $file;
			}
		}
	}

	// Check exist table 
	function ExistTable($table) {
		$prefix = $this->db->getPrefix();
		$table = str_replace('#__', $prefix, $table);
		$tables = $this->getListTables();
		if(in_array($table,$tables)) $result = true;
		else $result = false;
		return $result;
	}

	function log($query, $error) {
		//$this->log[] = array(	$query, $error );
	}

	function getListTables() {
		$sql = "SHOW TABLES";
		$this->db->setQuery($sql);
		$tables = $this->db->loadResultArray();
		$this->log($sql, $this->db->getErrorMsg());
		return $tables;
	}

	function getFieldsTable( $table, $field = null ) {

		$prefix = $this->db->getPrefix();
		$table = str_replace('#__', $prefix, $table);

		$result = false;

		if ( $this->ExistTable($table) ) {
			$sql ='DESCRIBE '.$table;
			$this->db->setQuery( $sql );
			$result = $fields = $this->db->loadObjectlist();
			$this->log($sql, $this->db->getErrorMsg());

			if ( !is_null($field) )  {
				$field_found = false;
				foreach($fields as $key => $tmp_field) {
					if ($tmp_field->Field == $field) {
						$field_found = $key;
					}
				}

				if ($field_found !== false) {
					$result = $fields[$field_found];
				} else {
					$result = null;
				}
			}
		}

		return $result;
	}

	function changeFieldDefault( $table, $field, $value=null ) {

		$prefix = $this->db->getPrefix();
		$table = str_replace('#__', $prefix, $table);

		$result = false;

		if ( $this->ExistTable($table) && !is_null($value) ) {

			$sql = "ALTER TABLE ".$table." ALTER ".$field." SET DEFAULT '".$value."'";

			if ( !empty($sql) ) {
				$this->db->setQuery( $sql );
				if($this->db->query()) {
					$result = true;
				}
				$this->log($sql, $this->db->getErrorMsg());
			}
		}

		return $result;
	}

	function changeFieldType( $table, $field_old, $field_new='', $type=null ) {

		$prefix = $this->db->getPrefix();
		$table = str_replace('#__', $prefix, $table);

		$sql = '';

		if (empty($field_new) && !is_null($type)) {
			$sql = "ALTER TABLE ".$table." MODIFY ".$field_old." ".$type;
		} 

		if( !empty($field_old) && !empty($field_new) ) {
			$sql = "ALTER TABLE ".$table." CHANGE ".$field_old." ".$field_new." ".(is_null($type)?"":$type);
		}

		$result = false;
		if ( !empty($sql) ) {
			$this->db->setQuery( $sql );
			if($this->db->query()) {
				$result = true;
			}
			$this->log($sql, $this->db->getErrorMsg());
		}

		return $result;
	}

	function addKeyUnique($table, $fields) {

		$result = false;

		if ( $this->ExistTable($table) ) {
			if (!empty($fields)) {

				$prefix = $this->db->getPrefix();
				$table = str_replace('#__', $prefix, $table);

				$sql = "ALTER TABLE ".$table." ADD UNIQUE ( " . implode(', ', $fields) . " ) ";
				$this->db->setQuery( $sql );
				if($this->db->query()) {
					$result = true;
				}
				$this->log($sql, $this->db->getErrorMsg());
			}
		}

		return $result;
	}

	function addPrimaryKey($table, $fields) {

		$result = false;

		if ( $this->ExistTable($table) ) {
			if (!empty($fields)) {

				$prefix = $this->db->getPrefix();
				$table = str_replace('#__', $prefix, $table);

				$sql = "ALTER TABLE ".$table." DROP PRIMARY KEY, ADD PRIMARY KEY(".implode(', ', $fields).");";
				$this->db->setQuery( $sql );

				if($this->db->query()) {
					$result = true;
				}

				$this->log($sql, $this->db->getErrorMsg());
			}
		}
		return $result;
	}


	function addField( $table, $field, $type, $null=null, $default=null, $extra=null ) {

		$prefix = $this->db->getPrefix();
		$table = str_replace('#__', $prefix, $table);
		$result = false;

		$table_exist = $this->ExistTable($table);

		if ( $table_exist ) {

			if (is_null($this->getFieldsTable($table, $field))) {
				$sql = 'ALTER TABLE '.$table.' ADD COLUMN '
					. $field . ' ' 
					. $type . ' ' . (($null)?"NULL":"NOT NULL") . ' '
					. ((!is_null($default))?" DEFAULT '".$default."'":"") . ' '
					. (($extra == true)?"AUTO_INCREMENT":"");

				$this->db->setQuery( $sql );

				if($this->db->query()) {
					$result = true;
				}

				$this->log($sql, $this->db->getErrorMsg());
			}
		}
		return $result;
	}

	function dropField( $table, $field ) {
		$prefix = $this->db->getPrefix();
		$table = str_replace('#__', $prefix, $table);

		$result = $this->getFieldsTable($table, $field);
		if ($result) {
			$sql = 'ALTER TABLE '.$table.' DROP COLUMN '.$field;
			$this->db->setQuery( $sql );
			if($this->db->query()) {
				$result = true;
			} else {
				$result = false;
			}
			$this->log($sql, $this->db->getErrorMsg());
		}
		return $result;
	}

	function getTableData($table, $where='', $fields = '*', $resulttype = 'object') {
		$prefix = $this->db->getPrefix();
		$table = str_replace('#__', $prefix, $table);
		$sql ="SELECT ".$fields." FROM ".$table." WHERE 1=1 ".(empty($where)?"":" and ".$where);
		$this->db->setQuery($sql);
		switch ( $resulttype ) {

			case 'result' :				 
				$result = $this->db->loadResult();
				break;

			case 'resultarray': 
				$result = $this->db->loadResultArray();
				break;

			case 'assoc' :				 
				$result = $this->db->loadAssoc();
				break;

			case 'assoclist' :				 
				$result = $this->db->loadAssocList();
				break;

			case 'row' :				 
				$result = $this->db->loadRow();
				break;

			case 'rowlist' :				 
				$result = $this->db->loadRowList();
				break;

			case 'object': 
				$result = $this->db->loadObject();
				break;

			case 'objectlist': 
			default: 
				$result = $this->db->loadObjectlist();
				break;
		}
		$this->log($sql, $this->db->getErrorMsg());

		return $result;
	}

	function updateTable( $table, $fields, $where='' ) {

		$prefix = $this->db->getPrefix();
		$table = str_replace('#__', $prefix, $table);

		$result = false;
		$set = array();
		foreach($fields as $field => $value) {
			if (is_string($value)) $value = '"'.$this->db->getEscaped($value).'"';
			$set[] = $field." = ".$value;
		}
		$sql = "UPDATE ".$table." SET ".implode(',', $set)." WHERE 1=1 ".(empty($where)?"":" and ".$where);
		$this->db->setQuery( $sql );
		if($this->db->query()) {
			$result = true;
		} else {
			$result = false;
		}
		$this->log($sql, $this->db->getErrorMsg());
		return $result;
	}

	function insertTable( $table, $fieldvalues ) {

		$prefix = $this->db->getPrefix();
		$table = str_replace('#__', $prefix, $table);

		$result = false;

		if ( $this->ExistTable($table) ) {

			$primary_keys = array();
			$pri_fields = $this->getFieldsTable($table);
			foreach($pri_fields as $pfield) {
				if ($pfield->Key == 'PRI') {
					$primary_keys[] = $pfield->Field;
				}
			}

			foreach($fieldvalues as $rownum => $row) {

				$fields = $values = array();

				$pri_check = $all_check = array();

				foreach( $row as $field => $value ) {
					if (is_string($value)) $value = '"'.$this->db->getEscaped($value).'"';
					$fields[] = $field;
					$values[] = $value;
					if (in_array($field, $primary_keys)) {
						$pri_check[] = $field . " = " . $value;
					}
					$all_check[] = $field . " = " . $value;
				}

				$check = (!empty($pri_check)) ? $pri_check : $all_check;

				$sql_check = "SELECT count(*) FROM ".$table." WHERE ".implode(' and ', $check);
				$this->db->setQuery( $sql_check );
				$check_result = $this->db->loadResult();

				$this->log($sql_check, $this->db->getErrorMsg());

				if ( !$check_result ) {
					$sql = "INSERT INTO ".$table." (".implode(', ',$fields).") VALUES (".implode(', ',$values).");";
					$this->db->setQuery( $sql );
					if($this->db->query()) {
						$result = true;
					}
					$this->log($sql, $this->db->getErrorMsg());
				}
			}
		}

		return $result;
	}

	function deleteFromTable($table, $where) {
		$prefix = $this->db->getPrefix();
		$table = str_replace('#__', $prefix, $table);

		$sql = "delete from ".$table." where 1=1 ".(empty($where)?"":" and ".$where).";";

		$this->db->setQuery( $sql );
		if($this->db->query()) {
			$result = true;
		} else {
			$result = false;
		}
		$this->log($sql, $this->db->getErrorMsg());
	}

	function cleanTable( $table ) {

		$prefix = $this->db->getPrefix();
		$table = str_replace('#__', $prefix, $table);

		$sql = "TRUNCATE TABLE ".$table.";";
		$this->db->setQuery( $sql );
		if($this->db->query()) {
			$result = true;
		} else {
			$result = false;
		}
		$this->log($sql, $this->db->getErrorMsg());
		return $result;
	}


	function dropTable( $table ) {

		$prefix = $this->db->getPrefix();
		$table = str_replace('#__', $prefix, $table);

		$result = false;

		if ( $this->ExistTable($table) ) {

			$sql = "DROP TABLE ".$table.";";
			$this->db->setQuery( $sql );
			if($this->db->query()) {
				$result = true;
			}
			$this->log($sql, $this->db->getErrorMsg());
		}
		return $result;
	}

	function createTable( $sql ) {
		$this->db->setQuery( $sql );
		if($this->db->query()) {
			$result = true;
		} else {
			$result = false;
		}
		$this->log($sql, $this->db->getErrorMsg());
		return $result;
	}

	function setError($msg) {
		$this->error[] = $msg;
	}
}

?>