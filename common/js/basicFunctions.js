function log_debug (args) {
  if(window.console) window.console.log(args)
}

function setCharAt(str,index,chr) {
<<<<<<< HEAD
	if(index > str.length-1) return str;
	return str.substr(0,index) + chr + str.substr(index+1);
=======
    if(index > str.length-1) return str;
    return str.substr(0,index) + chr + str.substr(index+1);
>>>>>>> 62f5d2252b428e533ffafa773842968fa47e686a
}

function print_r(ar){ //return ar;
	s = "";
	for (var p in ar) {
		s += p+" => "+ar[p]+"\n";
	}
	return s;
}

function nl2br (str, is_xhtml) {
<<<<<<< HEAD
	breakTag = '<br />';
	if (typeof is_xhtml != 'undefined' && !is_xhtml) {
		breakTag = '<br>';
	}
 
	return (str + '').replace(/([^>]?)\n/g, '$1'+ breakTag +'\n');
=======
    breakTag = '<br />';
    if (typeof is_xhtml != 'undefined' && !is_xhtml) {
        breakTag = '<br>';
    }
 
    return (str + '').replace(/([^>]?)\n/g, '$1'+ breakTag +'\n');
>>>>>>> 62f5d2252b428e533ffafa773842968fa47e686a
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
<<<<<<< HEAD
  	return _obj.toSource();
=======
      return _obj.toSource();
>>>>>>> 62f5d2252b428e533ffafa773842968fa47e686a
   }
   // Other browsers must do it the hard way
   switch (typeof _obj)
   {
<<<<<<< HEAD
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
=======
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
>>>>>>> 62f5d2252b428e533ffafa773842968fa47e686a
}

