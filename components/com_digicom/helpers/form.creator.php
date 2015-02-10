<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

if (!class_exists("formCreator")) {

	class formCreator {

		var $divID = '';
		var $divStyle = '';
		var $tableID = '';
		var $tableStyle = '';
		var $defaultSizeInputbox = '';
		var $maxSizeInputbox = '';
		var $imagePath = '';
		var $auto_bind_request = true;
		var $form_name = '';
		var $action = '';
		var $form_id = '';
		var $autoSubmit = false;
		var $submit = false;
		var $newSubmit = '';
		var $footer = '';
		var $header = '';
		var $needCVVCheckScript = false;
		var $needEmptyCheckScript = false;
		var $needCcnumCheckScript = false;
		var $needExpireCheckScript = false;
		var $needEmailCheckScript = false;
		var $needCounrtyStateScript = false;
		var $fields = array();
		var $checkList = array();

		function formCreator($form_name, $action, $form_id='') {
			$this->form_name = $form_name;
			$this->action = $action;
			$this->setSizeInputs( 30, 32 );
			$this->setImgPath(JURI::root().'components/com_digicom/assets/images/' );
			if (empty($form_id)) {
				$this->form_id = $form_name;
			} else {
				$this->form_id = $form_id;
			}
		}

		function setDivID($divID) {
			$this->divID = $divID;
		}

		function setSizeInputs($size, $maxsize) {
			$this->defaultSizeInputbox = $size;
			$this->maxSizeInputbox = $maxsize;
		}

		function setDivStyle($divStyle) {
			$this->divStyle = $divStyle;
		}

		function setImgPath($path) {
			$this->imagePath = $path;
		}

		function setTableID($tableID) {
			$this->tableID = $tableID;
		}

		function setTableStyle($tableStyle) {
			$this->tableStyle = $tableStyle;
		}

		function addTextField($name, $value='', $description='', $check=true, $size=0, $maxsize=0) {

			// auto_bind_request
			if ($this->auto_bind_request && isset($_REQUEST[$name]) && !empty($_REQUEST[$name])) $value = $_REQUEST[$name];

			if (empty($description)) $description = $name;

			$this->fields[] = array(
					'type' => 'text',
					'params' => array(
							'name' => $name,
							'value' => $value,
							'description' => $description,
							'size' => $this->defaultSizeInputbox,
							'maxsize' => $this->maxSizeInputbox,
							'check' => $check
					)
			);
			if ($check) {
				$this->needEmptyCheckScript = 'true';
				$this->checkList[] = "emptyCheck(document.$this->form_name.$name)";
			}
		}

		function addEmailField($value='', $name='Email',  $description='Email', $check=true) {

			// auto_bind_request
			if ($this->auto_bind_request && isset($_REQUEST[$name]) && !empty($_REQUEST[$name])) $value = $_REQUEST[$name];

			if (empty($description)) $description = $name;
			
			$this->fields[] = array(
					'type' => 'email',
					'params' => array(
							'name' => $name,
							'value' => $value,
							'description' => $description,
							'check' => $check,
							'size' => $this->defaultSizeInputbox,
							'maxsize' => $this->maxSizeInputbox
					)
			);
			if ($check) {
				$this->needEmailCheckScript = 'true';
				$this->checkList[] = "isValidEmail(document.$this->form_name.$name)";
			}
		}

		function getJavaScriptStateArray() {

			$states_js = "
var statescountry = new Array();\n";

			$countries = $this->getCountryList();
			$item = 0;
			foreach ($countries as $countrycode => $country) {
				$get_method_state = 'get_' . $countrycode . '_States';
				if (method_exists($this, $get_method_state)) {
					$states = $this->$get_method_state();
					foreach ($states as $statecode => $statename) {
						$states_js .= "statescountry[" . $item . "] = new Array('" . $countrycode . "', '" . $statecode . "', '" . $statename . "');\n";
						$item++;
					}
				}
			}

			return $states_js;
		}

		function addSelectCountryField($defCountryis = '', $defStateis = '', $checkContry = true, $checkState = true, $nameCountry='Country', $nameState='State', $descCountry='Country', $descState='State') {

			// auto_bind_request
			if ($this->auto_bind_request && isset($_REQUEST[$nameCountry]) && !empty($_REQUEST[$nameCountry])) $defCountryis = $_REQUEST[$nameCountry];
			if ($this->auto_bind_request && isset($_REQUEST[$nameState]) && !empty($_REQUEST[$nameState])) $defStateis = $_REQUEST[$nameState];

			$this->needCounrtyStateScript = true;

			$country_list = $this->getCountryList();

			$this->fields[] = array(
					'type' => 'selectcountry',
					'params' => array(
							'name' => $nameCountry,
							'nameFieldState' => $nameState,
							'value' => $defCountryis,
							'list' => $country_list,
							'description' => $descCountry.":",
							'check' => $checkContry
					)
			);

			if ($checkContry) {
				$this->needEmptyCheckScript = 'true';
				$this->checkList[] = "emptyCheck(document.".$this->form_name.".".$nameCountry.")";
			}

			$get_method_state = 'get_' . $defCountryis . '_States';
			if (method_exists($this, $get_method_state)) {
				$state_list = $this->$get_method_state();
			} else {
				$state_list = array('' => 'Select '.$nameState);
			}

			if (count($state_list) > 1) {
				$state_select_display = '';
				$state_text_display = 'display:none';
			} else {
				$state_select_display = 'display:none';
				$state_text_display = '';
			}

			$this->fields[] = array(
					'type' => 'select',
					'params' => array(
							'name' => $nameState."Code",
							'value' => $defStateis,
							'list' => $state_list,
							'description' => $descState.":",
							'check' => $checkState,
							'style_row' => $state_select_display,
							'AddToOnChange' => 'setState("'.$nameState.'", "'.$nameState.'Code");'
					)
			);

			$this->fields[] = array(
					'type' => 'text',
					'params' => array(
							'name' => $nameState."Title",
							'value' => $defStateis,
							'description' => $nameState.":",
							'check' => $checkState,
							'size' => $this->defaultSizeInputbox,
							'maxsize' => $this->maxSizeInputbox,
							'style_row' => $state_text_display,
							'AddToOnChange' => 'setState("'.$nameState.'", "'.$nameState.'Title");'
					)
			);

			$this->fields[] = array(
					'type' => 'hidden',
					'params' => array(
							'id' => $nameState,
							'name' => $nameState,
							'value' => $defStateis
					)
			);


			if ($checkState) {
				$this->needEmptyCheckScript = 'true';
				$this->checkList[] = "emptyCheckHidden(document.".$this->form_name.".".$nameState.")";
			}

		}


		function addSelectField($name, $value='', $list=array(), $description='', $check=true) {

			// auto_bind_request
			if ($this->auto_bind_request && isset($_REQUEST[$name]) && !empty($_REQUEST[$name])) $value = $_REQUEST[$name];

			if (empty($description)) $description = $name;
			
			$this->fields[] = array(
					'type' => 'select',
					'params' => array(
							'name' => $name,
							'value' => $value,
							'list' => $list,
							'description' => $description,
							'check' => $check
					)
			);

			if ($check) {
				$this->needEmptyCheckScript = 'true';
				$this->checkList[] = "emptyCheck(document.$this->form_name.$name)";
			}
		}


		function addCardNumberField($value='', $name='creditCardNumber', $description='Card Number', $size=0) {

			// auto_bind_request
			if ($this->auto_bind_request && isset($_REQUEST[$name]) && !empty($_REQUEST[$name])) $value = $_REQUEST[$name];
			
			$this->fields[] = array(
					'type' => 'ccnum',
					'params' => array(
							'name' => $name,
							'value' => $value,
							'description' => $description,
							'size' => $this->defaultSizeInputbox
					)
			);

			$this->needCcnumCheckScript = 'true';
			$this->checkList[] = "ccnumCheck(document.$this->form_name.$name)";
		}


		function addCvvField($value='', $name='CVV',  $description='CVV', $check=true, $size=4, $maxsize=4) {

			// auto_bind_request
			if ($this->auto_bind_request && isset($_REQUEST[$name]) && !empty($_REQUEST[$name])) $value = $_REQUEST[$name];

			$this->fields[] = array(
					'type' => 'cvv',
					'params' => array(
							'name' => $name,
							'value' => $value,
							'description' => $description,
							'size' => ( ($size == 0) ? $this->defaultSizeInputbox : $size ),
							'maxsize' => ( ($maxsize == 0) ? $this->maxSizeInputbox : $maxsize ),
							'check' => $check
					)
			);
			if ($check) {
				$this->needCVVCheckScript = true;
				$this->checkList[] = "isNumeric(document.$this->form_name.$name)";
			}
		}


		function addPhoneField($value='', $name='Phone', $description='', $check=true, $minlength=7, $size=0, $maxlength=0) {

			// auto_bind_request
			if ($this->auto_bind_request && isset($_REQUEST[$name]) && !empty($_REQUEST[$name])) $value = $_REQUEST[$name];

			if (empty($description)) $description = $name;
			
			$this->fields[] = array(
					'type' => 'phone',
					'params' => array(
							'name' => $name,
							'size' => $this->defaultSizeInputbox,
							'value' => $value,
							'check' => $check,
							'description' => $description,
							'minlength' => $minlength,
							'maxlength' => $this->maxSizeInputbox
					)
			);
			if ($check) {
				$this->needCcnumCheckScript = 'true';
				$this->checkList[] = "PhoneCheck(document.$this->form_name.$name , $minlength , $maxlength)";
			}
		}


		function addExpireDateField($monthName='expDateMonth', $yearName='expDateYear', $description='Expire Date') {

			$this->fields[] = array(
					'type' => 'expire',
					'params' => array(
							'month_name' => $monthName,
							'year_name' => $yearName,
							'description' => $description
					)
			);
			$this->needExpireCheckScript = 'true';
			$this->checkList[] = "checkExpire('$monthName', '$yearName')";
		}


		function addIssueDateField($monthName='issueDateMonth', $yearName='issueDateYear', $description='Issue Date'){
			$this->fields[] = array(
					'type' => 'issue',
					'params' => array(
							'month_name' => $monthName,
							'year_name' => $yearName,
							'description' => $description
					)
			);
		}


		function addHiddenField($name, $value) {

			// auto_bind_request
			if ($this->auto_bind_request && isset($_REQUEST[$name]) && !empty($_REQUEST[$name])) $value = $_REQUEST[$name];
			
			$this->fields[] = array(
					'type' => 'hidden',
					'params' => array(
							'id' => $name,
							'name' => $name,
							'value' => $value
					)
			);
		}


		function addCustomText($text='', $description='') {
			$this->fields[] = array(
					'type' => 'custom',
					'params' => array(
							'description' => $description,
							'value' => $text
					)
			);
		}


		function addSubmitButton($value='Pay', $valueOnClick='', $intable=true) {
			$this->fields[] = array(
					'type' => 'submit',
					'params' => array(
							'value' => $value,
							'valueOnClick' => $valueOnClick,
							'intable' => $intable,
							'size' => $this->defaultSizeInputbox
					)
			);
			$this->submit = true;
			if ($valueOnClick != '') {
				$this->newSubmit = $valueOnClick;
			}
		}


		function addAutoSubmit() {
			$this->autoSubmit = true;
		}


		function addHeader($text) {
			$this->header = $text;
		}


		function addFooter($text) {
			$this->footer = $text;
		}

		
		function toString() {
			$form = '';
			$hidden = '';

			if ($this->needEmailCheckScript) {
				$form .= "<script type='text/javascript'>

//function to check valid email address
function isValidEmail(obj){

  validRegExp = /^[^@]+@[^@]+.[a-z]{2,}$/i;
  strEmail = obj.value;

   // search email text for regular exp matches
	if (strEmail.search(validRegExp) == -1)
   {
	  return false;
	}
	return true;
}

function emailCheck(obj) {
	if(!isValidEmail(obj)){
		document.getElementById(obj.name+'_checker').src='{$this->imagePath}invalid.png';
		return false;
	}else{
		document.getElementById(obj.name+'_checker').src='{$this->imagePath}valid.png';
		return true;
	}
}

</script>
						";
			}

			if ($this->needCcnumCheckScript || $this->needEmptyCheckScript || $this->needExpireCheckScript) {
				$form .= "<script type='text/javascript'>

function checkAll(){

	correct = ";
				$conditions = '';
				foreach ($this->checkList as $check) {
					$conditions .= $check . " && ";
				}
				$conditions = substr($conditions, 0, strlen($conditions) - 4);
				$form .="$conditions;
	if(correct){
		if('$this->newSubmit'.length>0){
			document.$this->form_name.submitBtn.value='$this->newSubmit';
		}
		document.$this->form_name.submitBtn.disabled='disabled';
	} 
	return correct;
}
</script>\n";
				$check = " onSubmit='javascript:return checkAll()' ";
			} else {
				$check = "";
			}
			$form .= "<div ";
			if ($this->divID != '') {
				$form .= "id=\"{$this->divID}\" ";
			}
			if ($this->divStyle != '') {
				$form .= "style=\"{$this->divStyle}\" ";
			}
			$form .= ">\n";

			if ($this->header != '') {
				$form .= $this->header . "\n";
			}

			$form .= "<form action='$this->action' name='$this->form_name' $check";
			if ($this->form_id != '') {
				$form .= "id=\"{$this->form_id}\" ";
			}
			$form .= " method='post'>\n";

			$form .= "<table ";
			if ($this->tableID != '') {
				$form .= "id=\"{$this->tableID}\" ";
			}
			if ($this->tableStyle != '') {
				$form .= "style=\"{$this->tableStyle}\" ";
			}
			$form .= ">\n";



			foreach ($this->fields as $field) {
				$type = $field['type'];
				$params = $field['params'];

				$style = '';
				if (isset($params['style'])) {
					$style = " style='" . $params['style'] . "' ";
				}

				$style_row = '';
				if (isset($params['style_row']) && !empty($params['style_row'])) {
					$style_row = " style='" . $params['style_row'] . "' ";
				}

				$addtoonchange = '';
				if (isset($params['AddToOnChange'])) {
					$addtoonchange = $params['AddToOnChange'];
				}

				switch ( $type ) {

//**************TEXT FIELD**************************************************************************************************

					case "text":

						$form .= "
<tr id='" . $this->form_name . "{$params['name']}RowID'{$style_row}>
	<td id=desc_{$params['name']}>
								{$params['description']}
	</td>
	<td>
		<input type='text' class='inputbox' AUTOCOMPLETE='off' name='{$params['name']}' value='{$params['value']}' id='{$params['name']}' {$style}";
						if (isset($params['size']) && $params['size'] != 0) {
							$form .= "size='{$params['size']}' ";
						}

						if (isset($params['maxsize']) && $params['maxsize'] != 0) {
							$form .= "maxlength='{$params['maxsize']}' ";
						}
						if (isset($params['check']) && !empty($params['check'])) {
							$src = ($params['value'] == '' ) ? 'invalid.png' : 'valid.png';
							$form .= "onChange='javascript:emptyCheck(this);{$addtoonchange}'>\n
	</td>
	<td>\n		<img id='{$params['name']}_checker' alt='validate image'  src='$this->imagePath$src'>\n";
						} else {
							$form .= "></td>\n<td>";
						}
						$form .="
	</td>
</tr>\n";
						break;

//**************EMAIL FIELD**************************************************************************************************

					case "email":

						$form .= "
<tr id='" . $this->form_name . "{$params['name']}RowID'{$style_row}>
	<td id=desc_{$params['name']}>
								{$params['description']}
	</td>
	<td>
		<input type='text' class='inputbox' AUTOCOMPLETE='off' name='{$params['name']}' value='{$params['value']}' id='{$params['name']}' {$style}";
						if (isset($params['size']) && $params['size'] != 0) {
							$form .= "size='{$params['size']}' ";
						}

						if (isset($params['maxsize']) && $params['maxsize'] != 0) {
							$form .= "maxlength='{$params['maxsize']}' ";
						}
						if (!empty($params['check'])) {
							$src = ($params['value'] == '' ) ? 'invalid.png' : 'valid.png';
							$form .= "onChange='javascript:emailCheck(this);{$addtoonchange}'>\n
	</td>
	<td>\n		<img id='{$params['name']}_checker' alt='validate image'  src='$this->imagePath$src'>\n";
						} else {
							$form .= ">\n";
						}
						$form .="
	</td>
</tr>\n";
						break;

//**************SELECT FIELD**************************************************************************************************

					case "select":
						$form .= "
<tr id='" . $this->form_name . "{$params['name']}RowID'{$style_row}>
	<td id=desc_{$params['name']}>
								{$params['description']}
	</td>
	<td>
		<select class='inputbox' name='{$params['name']}' id='{$params['name']}' {$style} ";
						if ($params['check']) {
							$src = ($params['value'] == '') ? 'invalid.png' : 'valid.png';
							$form .= " onChange='javascript:emptyCheck(this);{$addtoonchange}'>\n";
						} else {
							$form .= ">\n";
						}
						$options = $params['list'];
						foreach ($options as $key => $value) {
							if ($key == $params['value']) {
								$selected = 'selected';
							} else {
								$selected = '';
							}
							$form .= "				<option value='$key' $selected>$value</option>\n";
						}
						$form .="		</select>\n";
						if ($params['check']) {
							$form .="
	</td>
	<td>\n<img id='{$params['name']}_checker' alt='validate image'  src='$this->imagePath$src'>\n";
						}
						$form .="
	</td>
</tr>\n";
						break;

//**************SELECT_FACTORY_FIELD**************************************************************************************************

					case "selectcountry":
						$form .= "
<tr{$style_row}>
	<td>
								{$params['description']}
	</td>
	<td>
		<select class='inputbox' name='{$params['name']}' id='{$params['name']}'";
						if ($params['check']) {
							$src = ($params['value'] == '') ? 'invalid.png' : 'valid.png';
							$form .= " onChange=\"javascript:emptyCheck(this);ChangeState( '".$params['nameFieldState']."', statescountry, document." . $this->form_name . ".Country.options[document." . $this->form_name . ".Country.selectedIndex].value, '', '');\">\n";
						} else {
							$form .= ">\n";
						}

						$options = $params['list'];

						foreach ($options as $key => $value) {

							if ($key == $params['value']) {
								$selected = 'selected';
							} else {
								$selected = '';
							}
							$form .= "<option value='$key' $selected>$value</option>\n";
						}

						$form .="</select>\n";

						if ($params['check']) {
							$form .="
	</td>
	<td>\n<img id='{$params['name']}_checker' alt='validate image'  src='$this->imagePath$src'>\n";
						}

						$form .="
	</td>
</tr>\n";
						break;

//**************PHONE FIELD**************************************************************************************************

					case "phone":
						$form .= "
<tr{$style_row}>
	<td>
								{$params['description']}
	</td>
	<td>
		<input class='inputbox' type='text' maxlength='19' id='{$params['name']}' AUTOCOMPLETE='on' name='{$params['name']}' value='{$params['value']}' ";
						if ($params['size'] != 0) {
							$form .= "size='{$params['size']}' ";
						}

						$src = 'invalid.png';

						if ($params['check']) {
						$form .= "onChange='javascript:PhoneCheck(this,{$params['minlength']} , {$params['maxlength']} )'>\n
	</td>
	<td>\n		<img id='{$params['name']}_checker' alt='validate image'  src='$this->imagePath$src'>\n";
						} else {
							$form .= ">\n</td><td>";
						}

						$form .="
	</td>
</tr>\n";
						break;

//**************CCNUM FIELD**************************************************************************************************

					case "ccnum":
						$form .= "
<tr{$style_row}>
	<td valign='top'>
								{$params['description']}
	</td>
	<td>
		<input class='inputbox' type='text' maxlength='19' id='{$params['name']}' AUTOCOMPLETE='off' name='{$params['name']}' value='{$params['value']}' ";
						if ($params['size'] != 0) {
							$form .= "size='{$params['size']}' ";
						}

						$src = 'invalid.png';
						$form .= "onKeyPress='javascript:ccnumCheck(this)' onKeyUp='javascript:ccnumCheck(this)' onKeyDown='javascript:ccnumCheck(this)' onBlur='javascript:ccnumCheck(this)' onMouseDown='javascript:ccnumCheck(this)' onClick='javascript:ccnumCheck(this)' onChange='javascript:ccnumCheck(this)'>";
if (false) {
						$form .= "
<div style='font-size:0.7em'>
	<a href='javascript:void(0);' onClick='document.getElementById(\"{$params['name']}\").value=4111111111111111'>4111111111111111</a>
	<br/>
	<a href='javascript:void(0);' onClick='document.getElementById(\"{$params['name']}\").value=4444333322221111'>4444333322221111</a>
</div>";
	
}
						$form .= "
	</td>
	<td valign='top'><img id='{$params['name']}_checker' alt='validate image'  src='$this->imagePath$src'></td>
</tr>\n";
						break;
//**************CVV FIELD**************************************************************************************************
					case "cvv":

						$form .= "
<tr>
	<td id=desc_{$params['name']}  >
								{$params['description']}
	</td>
	<td>
		<input type='text' style='' class='inputbox' AUTOCOMPLETE='off' name='{$params['name']}' value='{$params['value']}' id='{$params['name']}'";
						if ($params['size'] != 0) {
							$form .= "size='{$params['size']}' ";
						}

						if ($params['maxsize'] != 0) {
							$form .= "maxlength='{$params['maxsize']}' ";
						}
						if ($params['check']) {
							$src = ($params['value'] == '' ) ? 'invalid.png' : 'valid.png';
							$form .= " onChange='javascript:isNumeric(this)'  onClick='javascript:isNumeric(this)' onMouseDown='javascript:isNumeric(this)' onBlur='javascript:isNumeric(this)' onKeyDown='javascript:isNumeric(this)' onKeyUp='javascript:isNumeric(this)' onKeyPress='javascript:isNumeric(this)'>
	</td>
	<td><img id='{$params['name']}_checker' alt='validate image'  src='$this->imagePath$src'>\n";
						} else {
							$form .= ">\n";
						}
						$form .="
	</td>
</tr>\n";
						break;

//**************EXPIRE FIELD**************************************************************************************************

					case "expire":
						$mounth = "";
						$selected01 = "";
						$selected02 = "";
						$selected03 = "";
						$selected04 = "";
						$selected05 = "";
						$selected06 = "";
						$selected07 = "";
						$selected08 = "";
						$selected09 = "";
						$selected10 = "";
						$selected11 = "";
						$selected12 = "";
						if(isset($_SESSION["expDateMonth"])){
							$mounth = trim($_SESSION["expDateMonth"]);
							$selected01 = trim($mounth) == "01" ? 'selected="selected"' : "";
							$selected02 = trim($mounth) == "02" ? 'selected="selected"' : "";
							$selected03 = trim($mounth) == "03" ? 'selected="selected"' : "";
							$selected04 = trim($mounth) == "04" ? 'selected="selected"' : "";
							$selected05 = trim($mounth) == "05" ? 'selected="selected"' : "";
							$selected06 = trim($mounth) == "06" ? 'selected="selected"' : "";
							$selected07 = trim($mounth) == "07" ? 'selected="selected"' : "";
							$selected08 = trim($mounth) == "08" ? 'selected="selected"' : "";
							$selected09 = trim($mounth) == "09" ? 'selected="selected"' : "";
							$selected10 = trim($mounth) == "10" ? 'selected="selected"' : "";
							$selected11 = trim($mounth) == "11" ? 'selected="selected"' : "";
							$selected12 = trim($mounth) == "12" ? 'selected="selected"' : "";
						}
						$form .= "
<tr{$style_row}>
	<td>
								{$params['description']}
	</td>
	<td>
	 	<select class='inputbox' name='{$params['month_name']}' id='{$params['month_name']}' onChange=\"javascript:checkExpire('{$params['month_name']}', '{$params['year_name']}')\">
								<option value='01' ".$selected01." >01</option>
								<option value='02' ".$selected02." >02</option>
								<option value='03' ".$selected03." >03</option>
								<option value='04' ".$selected04." >04</option>
								<option value='05' ".$selected05." >05</option>
								<option value='06' ".$selected06." >06</option>
								<option value='07' ".$selected07." >07</option>
			 					<option value='08' ".$selected08." >08</option>
								<option value='09' ".$selected09." >09</option>
								<option value='10' ".$selected10." >10</option>
								<option value='11' ".$selected11." >11</option>
								<option value='12' ".$selected12." >12</option>
		</select>
		<select class='inputbox' id='{$params['year_name']}' name='{$params['year_name']}' onChange=\"javascript:checkExpire('{$params['month_name']}', '{$params['year_name']}')\">\n";
						$year = date('Y');
						$year_session = "";
						if(isset($_SESSION["expDateYear"])){
							$year_session = trim($_SESSION["expDateYear"]);
						}
						for ($i = 0; $i < 20; $i++) {
							$val = intval($year) + intval($i);
							$selected = "";
							if($val == $year_session){
								$selected = 'selected="selected"';
							}
							$form .= "<option value=".$val." ".$selected.">".$val."</option>\n";
						}

						$form .= "		</select>\n";
						$src = (intval(date('m')) > 1) ? 'invalid.png' : 'valid.png';
						$form .= "
   </td>
   <td><img id='{$params['month_name']}_{$params['year_name']}_checker' alt='validate image'  src='$this->imagePath$src'> </td>
</tr>\n";
						break;

//**************ISSUE FIELD**************************************************************************************************

					case "issue":
						$form .= "
<tr{$style_row}>
	<td>
								{$params['description']}
	</td>
	<td>
	 	<select class='inputbox' name='{$params['month_name']}'>
								<option value='01'>01</option>
								<option value='02'>02</option>
								<option value='03'>03</option>
								<option value='04'>04</option>
								<option value='05'>05</option>
								<option value='06'>06</option>
								<option value='07'>07</option>
			 					<option value='08'>08</option>
								<option value='09'>09</option>
								<option value='10'>10</option>
								<option value='11'>11</option>
								<option value='12'>12</option>
		</select>
		<select class='inputbox' name='{$params['year_name']}'>\n";
						$year = date('Y');
						$year = intval($year) - 20;
						for ($i = 20; $i > 0; $i--) {
							$val = intval($year) + intval($i);
							$form .= "								<option value=\"$val\">$val</option>\n";
						}

						$form .= "
		</select>\n";
						$form .= "
	</td>
	<td>&nbsp;</td>
</tr>\n";
						break;

//**************HIDDEN FIELD**************************************************************************************************

					case "hidden":
						$hidden .= "<input type='hidden' id='{$params['id']}' name='{$params['name']}' value='{$params['value']}'>\n";
						break;

//**************SUBMIT FIELD**************************************************************************************************

					case "submit":
						$form .= "
<tr{$style_row}>
	<td>
		<input type='button' class='digicom_cancel' onclick='history.go(-1);' value='	Back	' />&nbsp;
		<input type='submit' name='submitBtn' class='digicom_continue' value='{$params['value']}' size='30' maxsize='32'>
	</td>
	<td>&nbsp;

	</td>
	<td>&nbsp;</td>
</tr>";
						break;

//**************CUSTOM TEXT**************************************************************************************************

					case "custom":

						if (empty($params['description'])) $params['description'] = '&nbsp;';
						if (empty($params['value'])) $params['value'] = '&nbsp;';
						
						$form .= "
<tr{$style_row}>
	<td>{$params['description']}</td>
	<td>{$params['value']}</td>
	<td>&nbsp;</td>
</tr>";
						break;
				}
			}

			$form .= "
</table>";
			$form .= $hidden;
			$form .= "
</form>";
			if ($this->footer != '') {
				$form .= $this->footer . "\n";
			}
			//auto
			$form .= "
</div>\n";



			if ($this->needEmptyCheckScript) {
				$script = "<script type='text/javascript'>
function emptyCheck(obj){
	if(obj.value==''){
		document.getElementById(obj.name+'_checker').src='{$this->imagePath}invalid.png';
		return false;
	}else{
		document.getElementById(obj.name+'_checker').src='{$this->imagePath}valid.png';
		return true;
	}
}
function emptyCheckHidden(obj){
	if(obj.value==''){
		return false;
	}else{
		return true;
	}
}

</script>\n\n";
				$form = $script . $form;
			}

			if ($this->needCounrtyStateScript) {
				$script = "
<script type='text/javascript'>

	// States for Country
	" . $this->getJavaScriptStateArray() . "

	function setState(statename, statevalue){
		var state = eval( 'document." . $this->form_name . ".' + statename);
		var stateval = eval( 'document." . $this->form_name . ".' + statevalue);
		state.value = stateval.value;
	}

	function ChangeState( listname, source, key, orig_key, orig_val ) {

		var state_list = eval( 'document." . $this->form_name . ".' + listname + 'Code');
		var state_list_row = document.getElementById('" . $this->form_name . "' + listname + 'CodeRowID');
		var state_text = eval( 'document." . $this->form_name . ".' + listname + 'Title' );
		var state_text_row = document.getElementById('" . $this->form_name . "' + listname + 'TitleRowID');

		// empty the list
		for (i in state_list.options.length) {
			state_list.options[i] = null;
		}
		i = 0;
		for (x in source) {
			if (source[x][0] == key) {
				opt = new Option();
				opt.value = source[x][1];
				opt.text = source[x][2];

				if ((orig_key == key && orig_val == opt.value) || i == 0) {
					opt.selected = true;
				}
				state_list.options[i++] = opt;
			}
		}
		state_list.length = i;

		if (state_list.length <= 0) {
			state_list_row.style.display = 'none';
			state_text_row.style.display = '';
			//setState(listname,state_list.value);
		} else {
			state_list_row.style.display = '';
			state_list.selectedIndex = 0;
";

				if ($this->needEmptyCheckScript) {
					$script .= "
		emptyCheck(state_list);
	";
				}

				$script .= "
			state_text_row.style.display = 'none';
			state_text.value = '';
			//setState(listname,state_text.value);
";

				if ($this->needEmptyCheckScript) {
					$script .= "
		emptyCheck(state_text);
	";
				}

				$script .= "
		}

	}

</script> \n\n";

				$form = $script . $form;
			}

			if ($this->needCcnumCheckScript) {

				$script = "
<script type='text/javascript'>

	function ccnumCheck(obj)
	{
		if(! check(obj.value) ){
			document.getElementById(obj.name+'_checker').src='{$this->imagePath}invalid.png';
			return false;
		}else{
			document.getElementById(obj.name+'_checker').src='{$this->imagePath}valid.png';
			return true;
		}
	}

	function PhoneCheck(obj,min,max)
	{
		if( !Pcheck( obj.value , min , max ) )
		{
			document.getElementById(obj.name+'_checker').src='{$this->imagePath}invalid.png';
			return false;
		} else {
			document.getElementById(obj.name+'_checker').src='{$this->imagePath}valid.png';
			return true;
		}
	}


	function isCreditCard(cardNumber)
	{
		  if (cardNumber.length > 19)
			return (false);

		  if (cardNumber == 0)
			return (false);

		  sum = 0; mul = 1; l = cardNumber.length;
		  for (i = 0; i < l; i++) {
			digit = cardNumber.substring(l-i-1,l-i);
			tproduct = parseInt(digit ,10)*mul;
			if (tproduct >= 10)
			  sum += (tproduct % 10) + 1;
			else
			  sum += tproduct;
			if (mul == 1)
			  mul++;
			else
			  mul--;
		  }
		  if ((sum % 10) == 0)
			return (true);
		  else
			return (false);
	}

	function check(cardNumber)
	{
		var alpha_numeric_space = /^[a-zA-Z0-9\s\-]+$/;
		var numeric = /^[0-9\,\.]+$/;
		var alpha = /^[a-zA-Z]+$/;
		var numeric_brackets = /^[0-9\(\)\s\-]+$/;
		var numeric_space_dash = /^[0-9\s\-]+$/;
		var alpha_dot = /^[a-zA-Z\.]+$/;
		var space_numeric = /^[0-9\,\.\s]+$/;

		errorflag = false;
		cardNumber = cardNumber.replace(/,/g,'');
		cardNumber = cardNumber.replace(/-/g,'');
		cardNumber = cardNumber.replace(/ /g,'');

		if(cardNumber.length < 12 || cardNumber.length > 19)
		{
			errorflag = true;
		}
		else
		{
			if (!numeric.test(cardNumber))
			{
				if(!space_numeric.test(cardNumber))
				{
					errorflag = true;
				}
				else
				{
					errorflag = true;
				}
			}
			else
			{
				if (!isCreditCard(cardNumber))
				{
					errorflag = true;
				}
			}
		}
		if (errorflag){ return false; }
		else{ return true; }
	}

	function Pcheck(phone,min,max)
	{
		var re  =new RegExp('^\\\d{'+min+','+max+'}$');
		var rez = phone.match(re);
		return rez != null;
	}

</script>\n";
				$form = $script . $form;
			}

			if ($this->needCVVCheckScript) {
				$script = "
<script type='text/javascript'>
	function isNumeric(cText)
	{
	   var ValidChars = '0123456789';
	   var IsNumber=true;
	   var Char;
	   var sText = cText.value;
	   if(sText.length==0 || sText.length<3)
	   {
				IsNumber = false;
				document.getElementById(cText.name+'_checker').src='{$this->imagePath}invalid.png';
				return IsNumber;
	   }
	   for (i = 0; i < sText.length && IsNumber == true; i++)
		  {
		  Char = sText.charAt(i);
		  if (ValidChars.indexOf(Char) == -1)
			 {
				IsNumber = false;
				document.getElementById(cText.name+'_checker').src='{$this->imagePath}invalid.png';
			 }
			 else
			 {
				IsNumber = true;
				document.getElementById(cText.name+'_checker').src='{$this->imagePath}valid.png';
			 }
		  }
	   return IsNumber;
   }

</script>\n";
				$form = $script . $form;
			}

			if ($this->needExpireCheckScript) {
				$script = "
<script type='text/javascript'>

	function checkDateExpire(month, year){

		month = eval('document.$this->form_name.'+month+'.value');
		year  = eval('document.$this->form_name.'+year+'.value');

		d = new Date();
		curr_month = d.getMonth()+1;
		curr_year =  d.getFullYear();
		if( curr_month>month && curr_year==year ){ return false; }
		else {return true;}
	}

	function checkExpire(month, year){

		var CheckDateValue  = checkDateExpire(month, year);
		if( !CheckDateValue ){
				document.getElementById(month+'_'+year+'_checker').src='{$this->imagePath}invalid.png';
				return false;
		} else {
				document.getElementById(month+'_'+year+'_checker').src='{$this->imagePath}valid.png';
				return true;
		}
	}

</script>\n";
				$form = $script . $form;
			}
			if($this->needCcnumCheckScript){
				$script_validate = '<script>
										ccnumCheck(document.PayAutorizePayment.creditCardNumber);
										javascript:checkExpire(\'expDateMonth\', \'expDateYear\');
									</script>';
				 $form .= $script_validate;
			}

			if (true == $this->autoSubmit){
				/*$script = "<script type='text/javascript'> document." . $this->form_name . ".submit()</script>";*/

				$configs = $this->getConfigs();
				include_once(JPATH_SITE.DS."components".DS."com_digicom".DS."helpers".DS."helper.php");
				$form .= DigiComHelper::getSubmitForm($configs, 'paypaypal');
				$script = "<script type='text/javascript'> var t=setTimeout(\"document.".$this->form_name.".submit()\", 2000); </script>";
				
				$form = $form.$script;
			}
			return $form;
		}

		function getConfigs(){
			$comInfo = JComponentHelper::getComponent('com_digicom');
			return $comInfo->params;
		}

		function findCountryByName($pcountryname) {
			$countries = $this->getCountryList();
			$pcountryname = trim($pcountryname);
			$country_return = '';
			foreach($countries as $code => $countryname) {
				if ($countryname == $pcountryname) {
					$country_return = $code;
				}
			}
			return $country_return;
		}

		function findStateByName($pcountrycode, $pstatename) {

			$statecode_return = '';
			$pcountrycode = trim($pcountrycode);

			if (!empty($pcountrycode)) {

				$get_method_state = 'get_' . $pcountrycode . '_States';

				$pstatename = trim($pstatename);

				if (method_exists($this, $get_method_state)) {
					$states = $this->$get_method_state();
					foreach ($states as $statecode => $statename) {
						if ($statename == $pstatename) {
							$statecode_return = $statecode;
						}
					}
				} else {
					$statecode_return = $pstatename;
				}
			}

			return $statecode_return;
		}

		function getCountryList() {
			$country = array();

			$country[""] = "Select Country";

			$country["US"] = "United States";
			$country["AF"] = "Afghanistan";
			$country["AX"] = "Aland Islands";
			$country["AL"] = "Albania";
			$country["DZ"] = "Algeria";
			$country["AS"] = "American Samoa";
			$country["AD"] = "Andorra";
			$country["AO"] = "Angola";
			$country["AI"] = "Anguilla";
			$country["AQ"] = "Antarctica";
			$country["AG"] = "Antigua And Barbuda";
			$country["AR"] = "Argentina";
			$country["AM"] = "Armenia";
			$country["AW"] = "Aruba";
			$country["AU"] = "Australia";
			$country["AT"] = "Austria";
			$country["AZ"] = "Azerbaijan";
			$country["BS"] = "Bahamas";
			$country["BH"] = "Bahrain";
			$country["BD"] = "Bangladesh";
			$country["BB"] = "Barbados";
			$country["BY"] = "Belarus";
			$country["BE"] = "Belgium";
			$country["BZ"] = "Belize";
			$country["BJ"] = "Benin";
			$country["BM"] = "Bermuda";
			$country["BT"] = "Bhutan";

			$country["BO"] = "Bolivia";
			$country["BA"] = "Bosnia And Herzegovina";
			$country["BW"] = "Botswana";
			$country["BV"] = "Bouvet Island";
			$country["BR"] = "Brazil";
			$country["IO"] = "British Indian Ocean Territory";

			$country["BN"] = "Brunei Darussalam";
			$country["BG"] = "Bulgaria";
			$country["BF"] = "Burkina Faso";
			$country["BI"] = "Burundi";
			$country["KH"] = "Cambodia";
			$country["CM"] = "Cameroon";

			$country["CA"] = "Canada";
			$country["CV"] = "Cape Verde";
			$country["KY"] = "Cayman Islands";
			$country["CF"] = "Central African Republic";
			$country["TD"] = "Chad";
			$country["CL"] = "Chile";

			$country["CN"] = "China";
			$country["CX"] = "Christmas Island";
			$country["CC"] = "Cocos (keeling) Islands";
			$country["CO"] = "Colombia";
			$country["KM"] = "Comoros";
			$country["CG"] = "Congo";

			$country["CD"] = "Congo, Democratic Republic";
			$country["CK"] = "Cook Islands";
			$country["CR"] = "Costa Rica";
			$country["CI"] = "Cote D'ivoire";
			$country["HR"] = "Croatia";
			$country["CU"] = "Cuba";

			$country["CY"] = "Cyprus";
			$country["CZ"] = "Czech Republic";
			$country["DK"] = "Denmark";
			$country["DJ"] = "Djibouti";
			$country["DM"] = "Dominica";
			$country["DO"] = "Dominican Republic";

			$country["EC"] = "Ecuador";
			$country["EG"] = "Egypt";
			$country["SV"] = "El Salvador";
			$country["GQ"] = "Equatorial Guinea";
			$country["ER"] = "Eritrea";
			$country["EE"] = "Estonia";

			$country["ET"] = "Ethiopia";
			$country["FK"] = "Falkland Islands (malvinas)";
			$country["FO"] = "Faroe Islands";
			$country["FJ"] = "Fiji";
			$country["FI"] = "Finland";
			$country["FR"] = "France";

			$country["GF"] = "French Guiana";
			$country["PF"] = "French Polynesia";
			$country["TF"] = "French Southern Territories";
			$country["GA"] = "Gabon";
			$country["GM"] = "Gambia";
			$country["GE"] = "Georgia";

			$country["DE"] = "Germany";
			$country["GH"] = "Ghana";
			$country["GI"] = "Gibraltar";
			$country["GR"] = "Greece";
			$country["GL"] = "Greenland";
			$country["GD"] = "Grenada";

			$country["GP"] = "Guadeloupe";
			$country["GU"] = "Guam";
			$country["GT"] = "Guatemala";
			$country["GN"] = "Guinea";
			$country["GW"] = "Guinea-bissau";
			$country["GY"] = "Guyana";

			$country["HT"] = "Haiti";
			$country["HM"] = "Heard Island/mcdonald Islands";
			$country["VA"] = "Holy See (vatican City State)";
			$country["HN"] = "Honduras";
			$country["HK"] = "Hong Kong";
			$country["HU"] = "Hungary";

			$country["IS"] = "Iceland";
			$country["IN"] = "India";
			$country["ID"] = "Indonesia";
			$country["IR"] = "Iran";
			$country["IQ"] = "Iraq";
			$country["IE"] = "Ireland";

			$country["IL"] = "Israel";
			$country["IT"] = "Italy";
			$country["JM"] = "Jamaica";
			$country["JP"] = "Japan";
			$country["JO"] = "Jordan";
			$country["KZ"] = "Kazakhstan";

			$country["KE"] = "Kenya";
			$country["KI"] = "Kiribati";
			$country["KP"] = "Korea, Democratic Republic";
			$country["KR"] = "Korea, Republic Of";
			$country["KW"] = "Kuwait";
			$country["KG"] = "Kyrgyzstan";

			$country["LA"] = "Lao Democratic Republic";
			$country["LV"] = "Latvia";
			$country["LB"] = "Lebanon";
			$country["LS"] = "Lesotho";
			$country["LR"] = "Liberia";
			$country["LY"] = "Libyan Arab Jamahiriya";

			$country["LI"] = "Liechtenstein";
			$country["LT"] = "Lithuania";
			$country["LU"] = "Luxembourg";
			$country["MO"] = "Macao";
			$country["MK"] = "Macedonia";
			$country["MG"] = "Madagascar";

			$country["MW"] = "Malawi";
			$country["MY"] = "Malaysia";
			$country["MV"] = "Maldives";
			$country["ML"] = "Mali";
			$country["MT"] = "Malta";
			$country["MH"] = "Marshall Islands";

			$country["MQ"] = "Martinique";
			$country["MR"] = "Mauritania";
			$country["MU"] = "Mauritius";
			$country["YT"] = "Mayotte";
			$country["MX"] = "Mexico";
			$country["FM"] = "Micronesia";

			$country["MD"] = "Moldova";
			$country["MC"] = "Monaco";
			$country["MN"] = "Mongolia";
			$country["MS"] = "Montserrat";
			$country["MA"] = "Morocco";
			$country["MZ"] = "Mozambique";

			$country["MM"] = "Myanmar";
			$country["NA"] = "Namibia";
			$country["NR"] = "Nauru";
			$country["NP"] = "Nepal";
			$country["NL"] = "Netherlands";
			$country["AN"] = "Netherlands Antilles";

			$country["NC"] = "New Caledonia";
			$country["NZ"] = "New Zealand";
			$country["NI"] = "Nicaragua";
			$country["NE"] = "Niger";
			$country["NG"] = "Nigeria";
			$country["NU"] = "Niue";

			$country["NF"] = "Norfolk Island";
			$country["MP"] = "Northern Mariana Islands";
			$country["NO"] = "Norway";
			$country["OM"] = "Oman";
			$country["PK"] = "Pakistan";
			$country["PW"] = "Palau";

			$country["PS"] = "Palestinian Territory";
			$country["PA"] = "Panama";
			$country["PG"] = "Papua New Guinea";
			$country["PY"] = "Paraguay";
			$country["PE"] = "Peru";
			$country["PH"] = "Philippines";

			$country["PN"] = "Pitcairn";
			$country["PL"] = "Poland";
			$country["PT"] = "Portugal";
			$country["PR"] = "Puerto Rico";
			$country["QA"] = "Qatar";
			$country["RE"] = "Reunion";

			$country["RO"] = "Romania";
			$country["RU"] = "Russian Federation";
			$country["RW"] = "Rwanda";
			$country["SH"] = "Saint Helena";
			$country["KN"] = "Saint Kitts And Nevis";
			$country["LC"] = "Saint Lucia";

			$country["PM"] = "Saint Pierre And Miquelon";
			$country["WS"] = "Samoa";
			$country["SM"] = "San Marino";
			$country["ST"] = "Sao Tome And Principe";
			$country["SA"] = "Saudi Arabia";
			$country["SN"] = "Senegal";

			$country["CS"] = "Serbia And Montenegro";
			$country["SC"] = "Seychelles";
			$country["SL"] = "Sierra Leone";
			$country["SG"] = "Singapore";
			$country["SK"] = "Slovakia";
			$country["SI"] = "Slovenia";

			$country["SB"] = "Solomon Islands";
			$country["SO"] = "Somalia";
			$country["ZA"] = "South Africa";
			$country["GS"] = "South Georgia/sandwich Isles";
			$country["ES"] = "Spain";
			$country["LK"] = "Sri Lanka";

			$country["VC"] = "St Vincent & The Grenadines";
			$country["SD"] = "Sudan";
			$country["SR"] = "Suriname";
			$country["SJ"] = "Svalbard And Jan Mayen";
			$country["SZ"] = "Swaziland";
			$country["SE"] = "Sweden";

			$country["CH"] = "Switzerland";
			$country["SY"] = "Syrian Arab Republic";
			$country["TW"] = "Taiwan";
			$country["TJ"] = "Tajikistan";
			$country["TZ"] = "Tanzania";
			$country["TH"] = "Thailand";

			$country["TL"] = "Timor-leste";
			$country["TG"] = "Togo";
			$country["TK"] = "Tokelau";
			$country["TO"] = "Tonga";
			$country["TT"] = "Trinidad And Tobago";
			$country["TN"] = "Tunisia";

			$country["TR"] = "Turkey";
			$country["TM"] = "Turkmenistan";
			$country["TC"] = "Turks And Caicos Islands";
			$country["TV"] = "Tuvalu";
			$country["UG"] = "Uganda";
			$country["UA"] = "Ukraine";

			$country["AE"] = "United Arab Emirates";
			$country["GB"] = "United Kingdom";

			$country["UY"] = "Uruguay";
			$country["UM"] = "Us Minor Outlying Islands";
			$country["UZ"] = "Uzbekistan";

			$country["VU"] = "Vanuatu";
			$country["VE"] = "Venezuela";
			$country["VN"] = "Viet Nam";
			$country["VG"] = "Virgin Islands, British";
			$country["VI"] = "Virgin Islands, U.s.";
			$country["WF"] = "Wallis And Futuna";

			$country["EH"] = "Western Sahara";
			$country["YE"] = "Yemen";
			$country["ZM"] = "Zambia";
			$country["ZW"] = "Zimbabwe";
			return $country;
		}

		function get_US_States() {
			$states[""] = 'Select State';
			$states["AL"] = 'Alabama';
			$states["AK"] = 'Alaska';
			$states["AS"] = 'American Samoa';
			$states["AZ"] = 'Arizona';
			$states["AR"] = 'Arkansas';
			$states["CA"] = 'California';
			$states["CO"] = 'Colorado';
			$states["CT"] = 'Connecticut';
			$states["DE"] = 'Delaware';
			$states["DC"] = 'District of Columbia';
			$states["FM"] = 'Federated States of Micronesia';
			$states["FL"] = 'Florida';
			$states["GA"] = 'Georgia';
			$states["GU"] = 'Guam';
			$states["HI"] = 'Hawaii';
			$states["ID"] = 'Idaho';
			$states["IL"] = 'Illinois';
			$states["IN"] = 'Indiana';
			$states["IA"] = 'Iowa';
			$states["KS"] = 'Kansas';
			$states["KY"] = 'Kentucky';
			$states["LA"] = 'Louisiana';
			$states["ME"] = 'Maine';
			$states["MH"] = 'Marshall Islands';
			$states["MD"] = 'Maryland';
			$states["MA"] = 'Massachusetts';
			$states["MI"] = 'Michigan';
			$states["MN"] = 'Minnesota';
			$states["MS"] = 'Mississippi';
			$states["MO"] = 'Missouri';
			$states["MT"] = 'Montana';
			$states["NE"] = 'Nebraska';
			$states["NV"] = 'Nevada';
			$states["NH"] = 'New Hampshire';
			$states["NJ"] = 'New Jersey';
			$states["NM"] = 'New Mexico';
			$states["NY"] = 'New York';
			$states["NC"] = 'North Carolina';
			$states["ND"] = 'North Dakota';
			$states["MP"] = 'Northern Mariana Islands';
			$states["OH"] = 'Ohio';
			$states["OK"] = 'Oklahoma';
			$states["OR"] = 'Oregon';
			$states["PW"] = 'Palau';
			$states["PA"] = 'Pennsylvania';
			$states["PR"] = 'Puerto Rico';
			$states["RI"] = 'Rhode Island';
			$states["SC"] = 'South Carolina';
			$states["SD"] = 'South Dakota';
			$states["TN"] = 'Tennessee';
			$states["TX"] = 'Texas';
			$states["UT"] = 'Utah';
			$states["VT"] = 'Vermont';
			$states["VI"] = 'Virgin Islands';
			$states["VA"] = 'Virginia';
			$states["WA"] = 'Washington';
			$states["WV"] = 'West Virginia';
			$states["WI"] = 'Wisconsin';
			$states["WY"] = 'Wyoming';
			$states["AA"] = 'Armed Forces Americas';
			$states["AE"] = 'Armed Forces';
			$states["AP"] = 'Armed Forces Pacific';

			return $states;
		}

		//getStates
		function get_CA_States() {
			$states[''] = 'Select State';
			$states['AB'] = 'Alberta';
			$states['BS'] = 'British Columbia';
			$states['MB'] = 'Manitoba';
			$states['NB'] = 'New Brunswick';
			$states['NL'] = 'Newfoundland and Labrador';
			$states['NT'] = 'Northwest Territories';
			$states['NS'] = 'Nova Scotia';
			$states['NU'] = 'Nunavut';
			$states['ON'] = 'Ontario';
			$states['PE'] = 'Prince Edward Island';
			$states['QC'] = 'Quebec';
			$states['SK'] = 'Saskatchewan';
			$states['YT'] = 'Yukon';
			return $states;
		}

	}

//class formCreator;
}
