/*
// JoomlaWorks "AllVideos" Plugin for Joomla! 1.5.x - Version 3.3
// Copyright (c) 2006 - 2010 JoomlaWorks Ltd. All rights reserved.
// Released under the GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
// More info at http://www.joomlaworks.gr
// Designed and developed by the JoomlaWorks team
// *** Last update: February 18th, 2010 ***
*/

/* TO DO:
	check IE support
	close button for window
*/

var AllVideosLightBox = {

	Init: function(elm){
		var AVLBWidth = elm.AVLBWidth;
		var AVLBHeight = elm.AVLBHeight;
		var isIE = navigator.userAgent.toLowerCase().indexOf('msie') != -1;
		var isIE6 = navigator.userAgent.toLowerCase().indexOf('msie 6') != -1;
		var a = document.getElementsByTagName("a");

		for(var i=0; i<a.length; i++){
			if (a[i].className == "avLightbox") {

				if(isIE) a[i].style.display = 'none'; // hide for all IE browsers

				a[i].onclick = function(){

					var getVideoSource = this.getAttribute('href',2); // stupid IE
					var getVideoID = getVideoSource.replace(/#/, "");

					var getVideoElement = document.getElementById(getVideoID);

					if(getVideoElement.getElementsByTagName('script')[0]){
						var getVideoTagScript = getVideoElement.getElementsByTagName('script')[0];
						//getVideoTagScript.innerHTML = '';
						getVideoElement.removeChild(getVideoTagScript);
					}

					// Get the object tag
					var getVideoTagObject = getVideoElement.getElementsByTagName('object')[0];
					var videoTagObjectStyleW = getVideoTagObject.style.width;
					var videoTagObjectStyleH = getVideoTagObject.style.height;
					var videoTagObjectWidth = getVideoTagObject.width;
					var videoTagObjectHeight = getVideoTagObject.height;
					getVideoTagObject.width = AVLBWidth;
					getVideoTagObject.height = AVLBHeight;
					getVideoTagObject.style.width = AVLBWidth+'px';
					getVideoTagObject.style.height = AVLBHeight+'px';

					// Get the embed tag if it exists
					var getVideoTagEmbed = getVideoTagObject.getElementsByTagName('embed')[0];
					if(getVideoTagEmbed){
						var videoTagEmbedWidth = getVideoTagEmbed.width;
						var videoTagEmbedHeight = getVideoTagEmbed.height;
						getVideoTagEmbed.width = AVLBWidth;
						getVideoTagEmbed.height = AVLBHeight;
					}

					var getVideoTag = getVideoElement.innerHTML;

					document.getElementsByTagName('html')[0].style.overflowX = 'hidden';

					AllVideosLightBox.ModifyVisibility('span','avPlayerBlock','hidden');

					if(isIE6){
						document.getElementsByTagName('html')[0].style.overflow = 'hidden';
						var IEwidth = document.documentElement.clientWidth+'px';
						var IEheight = document.documentElement.clientHeight+'px';
						var IEdimensions = ' style="width:'+IEwidth+';height:'+IEheight+';"';
					} else {
						var IEdimensions = '';
					}

					var videoPopupHTML = '\
					<div id="AVLBExternalContainer"'+IEdimensions+'>\
						<a id="AVLBExternalContainerClose" class="AVLBClose" href="#">&nbsp;</a>\
					</div>\
					<div style="width:'+AVLBWidth+'px;height:'+(AVLBHeight+32)+'px;margin-top:-'+((AVLBHeight+32)/2)+'px;margin-left:-'+(AVLBWidth/2)+'px;" id="AVLBContainer">\
						'+getVideoTag+'\
						<a id="AVLBContainerClose" class="AVLBClose" href="#">&nbsp;</a>\
					</div>\
					';

					// Create and append the HTML
					var videoContainer = document.createElement('div');
					videoContainer.id = "AVLBOverlay";
					videoContainer.innerHTML = videoPopupHTML;
					document.getElementsByTagName("body")[0].appendChild(videoContainer);

					// Destroy HTML created for the popup
					var closeLinks = videoContainer.getElementsByTagName("a");
					for(var j=0; j<closeLinks.length; j++){
						if (closeLinks[j].className == "AVLBClose") {
							closeLinks[j].onclick = function(){
								videoContainer.style.display='none';
								document.getElementsByTagName("body")[0].removeChild(videoContainer);
								if(isIE6){
									document.getElementsByTagName('html')[0].style.overflow = '';
								}

								// Reset video dimensions
								getVideoTagObject.style.width = videoTagObjectStyleW;
								getVideoTagObject.style.height = videoTagObjectStyleH;
								getVideoTagObject.width = videoTagObjectWidth;
								getVideoTagObject.height = videoTagObjectHeight;
								if(getVideoTagEmbed){
									getVideoTagEmbed.width = videoTagEmbedWidth;
									getVideoTagEmbed.height = videoTagEmbedHeight;
								}

								AllVideosLightBox.ModifyVisibility('span','avPlayerBlock','visible');

								document.getElementsByTagName('html')[0].style.overflowX = 'auto';

								return false;
							}
						}
					}

					return false;

				}
			}
		}
	},

	ModifyVisibility: function(elmtag,elmclass,elmvisibility){
		var elmtag;
		var elmclass;
		var elmstyle;
		var getTag = document.getElementsByTagName(elmtag);
		for(var i=0; i<getTag.length; i++){
			if (getTag[i].className == elmclass) {
				getTag[i].style.visibility = elmvisibility;
			}
		}
	}

}

var AllVideosEmbed = {

	Init: function(){
		var span = document.getElementsByTagName("span");
		for(var i=0; i<span.length; i++){
			if (span[i].className == "avEmbed") {
				// Get video player element
				var getVideoSource = span[i].id;
				var getVideoID = getVideoSource.replace(/embed_/, "");
				var getVideoElement = document.getElementById(getVideoID);
				// Remove script tags
				if(getVideoElement.getElementsByTagName('script')[0]){
					var getVideoTagScript = getVideoElement.getElementsByTagName('script')[0];
					getVideoElement.removeChild(getVideoTagScript);
				}

				/*
				// Remove span tags
				if(getVideoElement.getElementsByTagName('span')[0]){
					var getVideoTag = getVideoElement.getElementsByTagName('span')[0].innerHTML;
				} else {
					var getVideoTag = getVideoElement.innerHTML;
				}
				*/

				var getVideoTag = getVideoElement.innerHTML;
				getVideoTag = AllVideosEmbed.htmlentities(getVideoTag);

				// Create the embed HTML
				var embedHTML = '<input class="embedInput" id="embedInput'+getVideoID+'" name="" type="text" value="'+getVideoTag+'" readonly="readonly" />';

				// Append the embed HTML
				span[i].innerHTML = embedHTML;

				// Auto select text when clicked on
				var inputElement = document.getElementById('embedInput'+getVideoID);
				inputElement.onclick = function(){
					this.focus();
					this.select();
				}
			}
		}
	},

	htmlentities: function(elm){
		elm = elm.replace( /\n/g, '' );
		elm = elm.replace( /\r/g, '' );
		elm = elm.replace( /\t/g, '' );
		elm = elm.replace( /\&/g, '&amp;' );
		elm = elm.replace( /\</g, '&lt;' );
		elm = elm.replace( /\>/g, '&gt;' );
		elm = elm.replace( /\"/g, '&quot;' );
		elm = elm.replace( /\'/g, "&apos;" );
		return elm;
	}

}

// End
