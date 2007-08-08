<? 
/*
    Copyright (C) 2007  Nicaw

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
include('include.inc.php');
?>

//default adsense values $.$
google_ad_client = "pub-8407977271374637";
google_ad_width = 125;
google_ad_height = 125;
google_ad_format = "125x125_as";
google_ad_type = "text_image";
google_ad_channel ="9679671194";
google_color_border = "CCCCCC";
google_color_bg = "CCCCCC";
google_color_link = "000000";
google_color_text = "333333";
google_color_url = "666666";

logout_time = 15*60;

//mmmhmhm, have a cookie?
var Cookies = {
	init: function () {
		var allCookies = document.cookie.split('; ');
		for (var i=0;i<allCookies.length;i++) {
			var cookiePair = allCookies[i].split('=');
			this[cookiePair[0]] = cookiePair[1];
		}
	},
	get: function (cookie_name)
	{
	  var results = document.cookie.match ( cookie_name + '=(.*?)(;|$)' );

	  if ( results )
	    return ( unescape ( results[1] ) );
	  else
	    return null;
	},
	create: function (name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
		this[name] = value;
	},
	erase: function (name) {
		this.create(name,'',-1);
		this[name] = undefined;
	}
};
Cookies.init();

//Simple _POST AJAX
	function ajax_init()
	{
		try
		{
			//Normal browsers
			http_request=new XMLHttpRequest();
		}
		catch (e)
		{
			//Stupid explorer... can't support fucking standarts
			try
			{
				http_request=new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e)
			{
				try
				{
					http_request=new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (e)
				{
					alert("Your browser does not support AJAX!");
					return false;
				}
			}
		}
		return http_request;
	}

   function ajax(element_id, script_url, get, warn) {

	  http_request = ajax_init();
	  if (!http_request){
		window.location.href=script_url;
	}

      http_request.onreadystatechange = function()
        {
		  if (http_request.readyState == 1 && warn) {
			  document.getElementById(element_id).innerHTML = '<div style="position: absolute; background-color: #660000; color:white;">Loading... Please wait or click <a style="color: white;" href="'+script_url+'">here</a></div>' + document.getElementById(element_id).innerHTML;
		  }
          if (http_request.readyState == 4) {
            if (http_request.status == 200) {
				document.getElementById(element_id).innerHTML = http_request.responseText;
				if (element_id == 'form'){
					logout_time = 15*60;
					document.getElementById('iobox').style.left = Cookies.get('iobox_x');
					document.getElementById('iobox').style.top = Cookies.get('iobox_y');
					document.getElementById('iobox').style['visibility'] = 'visible';
				}
            }else if(warn){
              alert('Server failed to load script ('+http_request.status+')');
            }
          }
        }
      parameters = encodeURI(get) + '&ajax=true';
      http_request.open('POST', script_url, true);
      http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      http_request.setRequestHeader("Content-length", parameters.length);
      http_request.setRequestHeader("Connection", "close");
      http_request.send(parameters);
   }

function getRef(obj){
		return (typeof obj == "string") ?
			 document.getElementById(obj) : obj;
}

function setStyle(obj,style,value){
		getRef(obj).style[style]= value;
}

// start dragging
function startDrag(e){
 // determine event object
 if(!e){var e=window.event};
 // determine target element
 var targ=e.target?e.target:e.srcElement;
 if(targ.className!='draggable'){return};
 // calculate event X,Y coordinates
    offsetX=e.clientX;
    offsetY=e.clientY;
 // assign default values for top and left properties
 if(!targ.style.left){targ.style.left=offsetX+'px'};
 if(!targ.style.top){targ.style.top=offsetY+'px'};
 // calculate integer values for top and left properties
    coordX=parseInt(targ.style.left);
    coordY=parseInt(targ.style.top);
    drag_node=targ;
 // move element
    document.onmousemove=dragDiv;
	document.onmouseup=stopDrag;
}
// continue dragging
function dragDiv(e){
 if(!e){var e=window.event};
 if(!drag_node){return};
 // move element
 drag_node.style.left=coordX+e.clientX-offsetX+'px';
 drag_node.style.top=coordY+e.clientY-offsetY+'px';
 return false;
}
// stop dragging
function stopDrag(){
 drag_node=null;
}
document.onmousedown=startDrag;

//retrieves input data inside element
function getParams(obj) {
  var getstr = '';
  for (i=0; i<obj.childNodes.length; i++) {
     if (obj.childNodes[i].tagName == "INPUT") {
        if (obj.childNodes[i].type == "text" || obj.childNodes[i].type == "password") {
           getstr += obj.childNodes[i].name + "=" + obj.childNodes[i].value + "&";
        }
        if (obj.childNodes[i].type == "checkbox") {
           if (obj.childNodes[i].checked) {
              getstr += obj.childNodes[i].name + "=" + obj.childNodes[i].value + "&";
           } else {
              getstr += obj.childNodes[i].name + "=&";
           }
        }
        if (obj.childNodes[i].type == "radio") {
           if (obj.childNodes[i].checked) {
              getstr += obj.childNodes[i].name + "=" + obj.childNodes[i].value + "&";
           }
        }
     }   
     if (obj.childNodes[i].tagName == "SELECT") {
        var sel = obj.childNodes[i];
        getstr += sel.name + "=" + sel.options[sel.selectedIndex].value + "&";
     }
	 if (obj.childNodes[i].tagName == "TEXTAREA") {
        var sel = obj.childNodes[i];
        getstr += sel.name + "=" + sel.value + "&";
     }
     
  }
  return getstr;
}

function server_state()
{
	ajax('server_state','status.php','',false);
	setTimeout ("server_state()",60000);
}
setTimeout ("server_state()",60000);

<?if ($cfg['secure_session'] && !empty($_SESSION['account'])){?>
	function tick()
	{
		logout_time--;
		if (logout_time < 0){
			self.window.location.href = 'login.php?logout&redirect=account.php';
		}else{
			setTimeout ("tick()",1000);
		}
	}
	tick();
<?}?>