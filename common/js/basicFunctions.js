function log_debug (args) {
  if(window.console) window.console.log(args)
}

function setCharAt(str,index,chr) {
	if(index > str.length-1) return str;
	return str.substr(0,index) + chr + str.substr(index+1);
}

function print_r(ar){ //return ar;
	s = "";
	for (var p in ar) {
		s += p+" => "+ar[p]+"\n";
	}
	return s;
}

function nl2br (str, is_xhtml) {
	breakTag = '<br />';
	if (typeof is_xhtml != 'undefined' && !is_xhtml) {
		breakTag = '<br>';
	}
 
	return (str + '').replace(/([^>]?)\n/g, '$1'+ breakTag +'\n');
}


function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}
 
function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}

function serialize(_obj)
{
   // Let Gecko browsers do this the easy way
   if (typeof _obj.toSource !== 'undefined' && typeof _obj.callee === 'undefined')
   {
	return _obj.toSource();
   }
   // Other browsers must do it the hard way
   switch (typeof _obj)
   {
	// numbers, booleans, and functions are trivial:
	// just return the object itself since its default .toString()
	// gives us exactly what we want
	case 'number':
	case 'boolean':
	case 'function':
		return _obj;
		break;

	// for JSON format, strings need to be wrapped in quotes
	case 'string':
		return "'" + _obj + "'";
		break;

	case 'object':
		var str;
		if (_obj.constructor === Array || typeof _obj.callee !== 'undefined')
		{
			str = '[';
			var i, len = _obj.length;
			for (i = 0; i < len-1; i++) { str += serialize(_obj[i]) + ','; }
			str += serialize(_obj[i]) + ']';
		}
		else
		{
			str = '{';
			var key;
			for (key in _obj) { str += key + ':' + serialize(_obj[key]) + ','; }
			str = str.replace(/\,$/, '') + '}';
		}
		return str;
		break;

	default:
		return 'UNKNOWN';
		break;
	}
}

function fireAlert(txt) {
	$("#alert").text(txt).fadeIn("fast");
	setTimeout("$('#alert').fadeOut('fast')", 3000);
}

if (!Object.keys) {
	Object.keys = function(obj) {
		var keys = new Array();
		for (k in obj) if (obj.hasOwnProperty(k)) keys.push(k);
		return keys;
	};
}

/**
*
*  UTF-8 data encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Utf8 = {
 
	// public method for url encoding
	encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	},
 
	// public method for url decoding
	decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
 
}


function strtr (str, from, to) {
	// Translates characters in str using given translation tables	
	// 
	// version: 905.3122
	// discuss at: http://phpjs.org/functions/strtr
	// +   original by: Brett Zamir (http://brett-zamir.me)
	// +	  input by: uestla
	// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +	  input by: Alan C
	// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// *	 example 1: $trans = {'hello' : 'hi', 'hi' : 'hello'};
	// *	 example 1: strtr('hi all, I said hello', $trans)
	// *	 returns 1: 'hello all, I said hi'
	// *	 example 2: strtr('äaabaåccasdeöoo', 'äåö','aao');
	// *	 returns 2: 'aaabaaccasdeooo'
	// *	 example 3: strtr('ääääääää', 'ä', 'a');
	// *	 returns 3: 'aaaaaaaa'
	// *	 example 4: strtr('http', 'pthxyz','xyzpth');
	// *	 returns 4: 'zyyx'
	// *	 example 5: strtr('zyyx', 'pthxyz','xyzpth');
	// *	 returns 5: 'http'
	var fr = '', i = 0, j = 0, lenStr = 0, lenFrom = 0;
	var tmpFrom = [];
	var tmpTo	= [];
	var ret = '';
	var match = false;

	// Received replace_pairs?
	// Convert to normal from->to chars
	if (typeof from === 'object') {
		for (fr in from) {
			tmpFrom.push(fr);
			tmpTo.push(from[fr]);
		}

		from = tmpFrom;
		to	 = tmpTo;
	}
	
	// Walk through subject and replace chars when needed
	lenStr	= str.length;
	lenFrom = from.length;
	for (i = 0; i < lenStr; i++) {
		match = false;
		for (j = 0; j < lenFrom; j++) {
			if (str.substr(i, from[j].length) == from[j]) {
				match = true;

				// Fast forward
				i = (i + from[j].length)-1;
				
				break;
			}
		}
		
		if (false !== match) {
			ret += to[j];
		} else {
			ret += str[i];
		}
	}

	return ret;
}
