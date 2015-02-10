// JavaScript Document
// Color Picker Script from Flooble.com
// For more information, visit 
//	http://www.flooble.com/scripts/colorpicker.php
// Copyright 2003 Animus Pactum Consulting inc.
//---------------------------------------------------------
	var AgntUsr=navigator.userAgent.toLowerCase();
	var DomYes=document.getElementById?1:0;
	var NavYes=AgntUsr.indexOf('mozilla')!=-1&&AgntUsr.indexOf('compatible')==-1?1:0;
	var ExpYes=AgntUsr.indexOf('msie')!=-1?1:0;
	var Opr=AgntUsr.indexOf('opera')!=-1?1:0;
	var Opr6orless=window.opera && navigator.userAgent.search(/opera.[1-6]/i)!=-1;
	var DomNav=DomYes&&NavYes?1:0;
	var DomExp=DomYes&&ExpYes?1:0;
	var Nav4=NavYes&&!DomYes&&document.layers?1:0;
	var Exp4=ExpYes&&!DomYes&&document.all?1:0;
	 var perline = 18;
	 var divSet = false;
	 var curId;
	 var colorLevels = Array('0', '3', '6', '9', 'C', 'F');
	 var colorArray = Array();
	 var ie = false;
	 var nocolor = 'none';
	 if (document.all) { ie = true; nocolor = ''; }
	 
	 
	 function getObj(id) {
		if (Exp4){return document.all[id];}
	else if (Nav4){return document.layers[id];}
	else{return document.getElementById(id);}
	 }

	 function addColor(r, g, b) {
	 	var red = colorLevels[r];
	 	var green = colorLevels[g];
	 	var blue = colorLevels[b];
	 	addColorValue(red, green, blue);
	 }

	 function addColorValue(r, g, b) {
	 	colorArray[colorArray.length] = '#' + r + r + g + g + b + b;
	 }
	 
	 function setColor(color,show, div_id) {
		if(div_id == "head_background_color" || div_id == "head_text_color"){
			if(div_id == "head_background_color"){
				document.getElementById("head_demo").style.backgroundColor=color;
			}
			else if(div_id == "head_text_color"){
				document.getElementById("head_demo").style.color=color;
			}
		}
		else if(div_id == "label_color"){
			document.getElementById("text_demo").style.color=color;
		}
		else if(div_id == "label_bg_color"){
			document.getElementById("text_demo").style.background=color;
		}

	 	var link = getObj(curId);
	 	var field = getObj(curId + 'field');
	 	var picker = getObj('colorpicker');
	 	field.value = color;
	 	if (color == '') {
		 	link.style.background = nocolor;
		 	link.style.color = nocolor;
		 	color = nocolor;
	 	} else {
		 	link.style.background = color;
		 	link.style.color = color;
		}
	 	picker.style.display = 'none';
		eval(getObj(curId + 'field').title);

		if(div_id == "element_border_color"){
			var elelementcolor = document.getElementById("element_border_colorfield").value;
			document.getElementById("text_demo").style.borderColor = elelementcolor;
		}
	 }
		
	 function setDiv(show, div_id) {
		 
		if((typeof(document.getElementById("colorpicker")) != "undefined") && (document.getElementById("colorpicker") != null)){
			var element = document.getElementById("colorpicker");
	 		element.parentNode.removeChild(element);
		}


		if (!document.createElement) { return; }
		var elemDiv = document.createElement('div');
		if (typeof(elemDiv.innerHTML) != 'string') { return; }
		genColors();
		elemDiv.id = 'colorpicker';
		elemDiv.style.position = 'absolute';
		elemDiv.style.float = 'none';

		 if(Exp4){
			 elemDiv.style.zIndex = 10001;}
		 else{
			 elemDiv.style.zIndex = 10001;}

		elemDiv.style.display = 'none';
		elemDiv.style.border = '#000000 1px solid';
		elemDiv.style.background = '#FFFFFF';
		//elemDiv.style.width = '210';

		elemDiv.innerHTML = '<center>'
		+ '<table border="0" cellspacing="0" cellpadding="2"><tr><td>Please select a color:</td><td align="right">(<a href="javascript:setColor(\'\','+show+');">No color</a>)<br></td></tr><tr><td colspan="2" align="middle">'
		+ getColorTable(show, div_id)
		+ '</td></tr></table></center>';

		document.body.appendChild(elemDiv);

		divSet = true;
	 }
	 
	 function pickColor(id, show) {
	 	//if (!divSet){
			setDiv(show, id);
		//}
	 	var picker = getObj('colorpicker');	 
		if (id == curId && picker.style.display == 'block') {
			picker.style.display = 'none';
			return;
		}
	 	curId = id;
	 	var thelink = getObj(id);

		if (ExpYes) {
			picker.style.top = getAbsoluteOffsetTop(thelink) + 5;
		 	picker.style.left = getAbsoluteOffsetLeft(thelink) + 5; 
		}
		else {
			picker.style.top = getAbsoluteOffsetTop(thelink) + 5 + 'px';
			picker.style.left = getAbsoluteOffsetLeft(thelink) + 5 + 'px'; 
		}
	 		
	picker.style.display = 'block';
	 }
	 
	 function genColors() {
		addColorValue('0','0','0');
		addColorValue('3','3','3');
		addColorValue('6','6','6');
		addColorValue('8','8','8');
		addColorValue('9','9','9');				
		addColorValue('A','A','A');
		addColorValue('C','C','C');
		addColorValue('E','E','E');
		addColorValue('F','F','F');								

		for (a = 1; a < colorLevels.length; a++)
			addColor(0,0,a);
		for (a = 1; a < colorLevels.length - 1; a++)
			addColor(a,a,5);

		for (a = 1; a < colorLevels.length; a++)
			addColor(0,a,0);
		for (a = 1; a < colorLevels.length - 1; a++)
			addColor(a,5,a);

		for (a = 1; a < colorLevels.length; a++)
			addColor(a,0,0);
		for (a = 1; a < colorLevels.length - 1; a++)
			addColor(5,a,a);


		for (a = 1; a < colorLevels.length; a++)
			addColor(a,a,0);
		for (a = 1; a < colorLevels.length - 1; a++)
			addColor(5,5,a);

		for (a = 1; a < colorLevels.length; a++)
			addColor(0,a,a);
		for (a = 1; a < colorLevels.length - 1; a++)
			addColor(a,5,5);

		for (a = 1; a < colorLevels.length; a++)
			addColor(a,0,a);
		for (a = 1; a < colorLevels.length - 1; a++)
			addColor(5,a,5);

	   	return colorArray;
	 }
	 function getColorTable(show, div_id) {
		 //var colors = colorArray;
		 
		 var colors = new Array("#000000","#000033","#000066","#000099","#0000CC","#0000FF","#330000","#330033","#330066","#330099","#3300CC",
	"#3300FF","#660000","#660033","#660066","#660099","#6600CC","#6600FF","#990000","#990033","#990066","#990099",
	"#9900CC","#9900FF","#CC0000","#CC0033","#CC0066","#CC0099","#CC00CC","#CC00FF","#FF0000","#FF0033","#FF0066",
	"#FF0099","#FF00CC","#FF00FF","#003300","#003333","#003366","#003399","#0033CC","#0033FF","#333300","#333333",
	"#333366","#333399","#3333CC","#3333FF","#663300","#663333","#663366","#663399","#6633CC","#6633FF","#993300",
	"#993333","#993366","#993399","#9933CC","#9933FF","#CC3300","#CC3333","#CC3366","#CC3399","#CC33CC","#CC33FF",
	"#FF3300","#FF3333","#FF3366","#FF3399","#FF33CC","#FF33FF","#006600","#006633","#006666","#006699","#0066CC",
	"#0066FF","#336600","#336633","#336666","#336699","#3366CC","#3366FF","#666600","#666633","#666666","#666699",
	"#6666CC","#6666FF","#996600","#996633","#996666","#996699","#9966CC","#9966FF","#CC6600","#CC6633","#CC6666",
	"#CC6699","#CC66CC","#CC66FF","#FF6600","#FF6633","#FF6666","#FF6699","#FF66CC","#FF66FF","#009900","#009933",
	"#009966","#009999","#0099CC","#0099FF","#339900","#339933","#339966","#339999","#3399CC","#3399FF","#669900",
	"#669933","#669966","#669999","#6699CC","#6699FF","#999900","#999933","#999966","#999999","#9999CC","#9999FF",
	"#CC9900","#CC9933","#CC9966","#CC9999","#CC99CC","#CC99FF","#FF9900","#FF9933","#FF9966","#FF9999","#FF99CC",
	"#FF99FF","#00CC00","#00CC33","#00CC66","#00CC99","#00CCCC","#00CCFF","#33CC00","#33CC33","#33CC66","#33CC99",
	"#33CCCC","#33CCFF","#66CC00","#66CC33","#66CC66","#66CC99","#66CCCC","#66CCFF","#99CC00","#99CC33","#99CC66",
	"#99CC99","#99CCCC","#99CCFF","#CCCC00","#CCCC33","#CCCC66","#CCCC99","#CCCCCC","#CCCCFF","#FFCC00","#FFCC33",
	"#FFCC66","#FFCC99","#FFCCCC","#FFCCFF","#00FF00","#00FF33","#00FF66","#00FF99","#00FFCC","#00FFFF","#33FF00",
	"#33FF33","#33FF66","#33FF99","#33FFCC","#33FFFF","#66FF00","#66FF33","#66FF66","#66FF99","#66FFCC","#66FFFF",
	"#99FF00","#99FF33","#99FF66","#99FF99","#99FFCC","#99FFFF","#CCFF00","#CCFF33","#CCFF66","#CCFF99","#CCFFCC",
	"#CCFFFF","#FFFF00","#FFFF33","#FFFF66","#FFFF99","#FFFFCC","#FFFFFF");
		 
		 
		 
	  	 var tableCode = '';
		 tableCode += '<table border="0" cellspacing="1" cellpadding="1">';
		 for (i = 0; i < colors.length; i++) {
			  if (i % perline == 0) { tableCode += '<tr>'; }			  
			  tableCode += '<td bgcolor="#000000"><a style="outline: 1px solid #000000; color: ' 
			  	  + colors[i] + '; background: ' + colors[i] + ';font-size: 10px;" title="' 
			  	  + colors[i] + '" href="javascript:setColor(\'' + colors[i] + '\', '+show+', \''+div_id+'\');">&nbsp;&nbsp;</a></td>';				  
			  if (i % perline == perline - 1) { tableCode += '</tr>'; }
		 }
		 if (i % perline != 0) { tableCode += '</tr>'; }
		 tableCode += '</table>';
	  	 return tableCode;
	 }
	 function relateColor(id, color) {
	 	var link = getObj(id);
	 	if (color == '') {
		 	link.style.background = nocolor;
		 	link.style.color = nocolor;
		 	color = nocolor;
	 	} else {
		 	link.style.background = color;
		 	link.style.color = color;
		}
		eval(getObj(id + 'field').title);
	 }
	 function getAbsoluteOffsetTop(obj) {
	 	var top = obj.offsetTop;
	 	var parent = obj.offsetParent;
		while (parent != document.body) {
	 		top += parent.offsetTop;
	 		parent = parent.offsetParent;
	 	}
	 	return top;
	 }
	 
	 function getAbsoluteOffsetLeft(obj) {
	 	var left = obj.offsetLeft;
	 	var parent = obj.offsetParent;
	 	while (parent != document.body) {
	 		left += parent.offsetLeft;
	 		parent = parent.offsetParent;
	 	}
	 	return left;
	 }

	function setDemoStyle(element){
		document.getElementById("text_demo").style.fontFamily = element.value;
	}

	function setDemoWidth(){
		value = document.getElementById("label_width").value;
		type = document.getElementById("label_width_type").value;
		type_2 = ""
		if(type == "0"){
			type_2 = "px";
		}
		else{
			type_2 = "%";
		}
		if(value != "0"){
			document.getElementById("text_demo").style.width = value+type_2+"";
		}
	}

	function setDemoHeight(){
		value = document.getElementById("label_height").value;
		type = document.getElementById("label_height_type").value;
		type_2 = ""
		if(type == "0"){
			type_2 = "px";
		}
		else{
			type_2 = "%";
		}
		if(value != "0"){
			document.getElementById("text_demo").style.fontSize = value+type_2+"";
		}
	}

	function setDemoText(){
		text = document.getElementById("label").value;
		document.getElementById("text_demo").innerHTML = text;
	}

	function setHeadDemoSize(value){
		size = document.getElementById("head_size").value;
		document.getElementById("head_demo").innerHTML = "<"+size+">"+value+"</"+size+">";
	}

	function setElementPadding(element, direction){
		if(direction == "top"){
			document.getElementById("text_demo").style.paddingTop = element.value+"px";
		}
		if(direction == "right"){
			document.getElementById("text_demo").style.paddingRight = element.value+"px";
		}
		if(direction == "bottom"){
			document.getElementById("text_demo").style.paddingBottom = element.value+"px";
		}
		if(direction == "left"){
			document.getElementById("text_demo").style.paddingLeft = element.value+"px";
		}
	}

	function setElementBorder(element, direction){
		var color = document.getElementById("element_border_colorfield").value;
		var borderStyle = document.getElementById("element_border_style").value;
		if(direction == "top"){
			document.getElementById("text_demo").style.borderTop = element.value+"px solid "+color;
			document.getElementById("text_demo").style.borderStyle = borderStyle;
		}
		if(direction == "right"){
			document.getElementById("text_demo").style.borderRight = element.value+"px solid "+color;
			document.getElementById("text_demo").style.borderStyle = borderStyle;
		}
		if(direction == "bottom"){
			document.getElementById("text_demo").style.borderBottom = element.value+"px solid "+color;
			document.getElementById("text_demo").style.borderStyle = borderStyle;
		}
		if(direction == "left"){
			document.getElementById("text_demo").style.borderLeft = element.value+"px solid "+color;
			document.getElementById("text_demo").style.borderStyle = borderStyle;
		}
	}

	function setBorderStyle(element){
		document.getElementById("text_demo").style.borderStyle = element.value;
	}