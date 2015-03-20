/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

function changeLayoutType(type){
	switch(type){
		case "1" : 
			document.getElementById("div_cols").style.display = "block";
			document.getElementById("div_grid").style.display = "block";
			document.getElementById("div_list").style.display = "none";
			break;
		case "2" :
			document.getElementById("div_cols").style.display = "none";
			document.getElementById("div_grid").style.display = "none";
			document.getElementById("div_list").style.display = "block";
			break;
	}
}

function showUsername(){
	 var div = document.getElementById("user_name");
	 div.style.display = "block";
	 document.adminForm.action.value="new_existing_student";
	 return true;
}

function hideUsername(){
	 var div = document.getElementById("user_name");
	 div.style.display = "none";
	 document.adminForm.action.value="new_student";
	 return true;
}

function galeryColumns(value){
	if(value == 0){
		document.getElementById("galery_columns_td").style.display = "none";
	}
	else if(value == 1){
		document.getElementById("galery_columns_td").style.display = "table-cell";
	}
}