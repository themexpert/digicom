<?php

class DCConfig {

	var $__dsconfig = null;

	function DCConfig( $directCall = true ) {

	if ( $directCall ) {
		trigger_error("You can not use the designer to create a class DCConfig. Use the static method DCConfig::get('configparam', 'defaultvalue')",E_USER_ERROR);
	}

		$comInfo = JComponentHelper::getComponent('com_digicom');
		$this->__dsconfig =  $comInfo->params;

	}

	public static function &getInstance() {
		static $instance;
		if ( !is_object( $instance ) ) {
			$instance = new DCConfig( false );
		}
		return $instance;
	}

	public static function get($param, $default=null){
		$config = DCConfig::getInstance();

		if(isset($config->__dsconfig->$param)){
			return $config->__dsconfig->$param;
		}
		elseif(!is_null($default)){
			return $default;
		}
		else{
			return null;
		}
	}
}

?>