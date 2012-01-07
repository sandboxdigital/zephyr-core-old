/*
 * jQuery Core Functions 
 *
 * Copyright (c) 2006, 2007, 2008 Thomas Garrood
 * 
 * Depends:
 *
 */
//
(function($) { // hide the namespace

//	// Hack for Prototype bind function
//	// Hmm this breaks ExtJs
//	$.extend(Function.prototype, {
//		scope : function() {
//			var _func = this;
//			var _obj = arguments[0];
//			var _args = $.grep(arguments, function(v, i) {
//				return i > 1;
//			});
//
//			return function() {
//				return _func.apply(_obj, arguments);
//			};
//		}
//	})
	
	jQuery.extend({
	 inScope: function(fn, scope)
	 {
	  return function()
	  {
		//c(arguments);
		return fn.apply(scope, arguments);
	  }
	 }
	});
	
	$.fn.hasAttr = function(attr) { var attribVal = this.attr(attr); return (attribVal !== undefined) && (attribVal !== false); }

})(jQuery);

// trace a message to the browser console (must be enabled in firebug
function c(msg) {
	if (typeof console != 'undefined')
		console.log(msg);
};

String.prototype.repeat = function(l) {
	return new Array(l + 1).join(this);
};

String.prototype.ucWord = function() {
	var newVal = '';
	var val = this.split(' ');
	for ( var i = 0; i < val.length; i++) {
		newVal += val[i].substring(0, 1).toUpperCase()
				+ val[i].substring(1, val[i].length) + ' ';
	}
	return newVal;
};


function parseParams (url) 
{
	var params = url;
	var pHash = {};
	if (url.indexOf('?') > 0) {
		if (url.match(/\?(.+)$/)) {
			// in case it is a full query string with ?, only take
			// everything after the ?
			params = RegExp.$1;
		}
		var pArray = params.split("&");
		for ( var i = 0; i < pArray.length; i++) {
			var temp = pArray[i].split("=");
			pHash[temp[0]] = unescape(temp[1]);
		}
	}
	return pHash;
}


// functions required to deal with Date returned from MS ASP.NET MVC

function parseMSJSONString(data) {
    try {
        var newdata = data.replace(
            new RegExp('"\\\\\/Date\\\((-?[0-9]+)\\\)\\\\\/"', "g")
                        , "new Date($1)");
        newdata = eval('(' + newdata + ')');
        return newdata;
    }
    catch (e) { return null; }
}

Date.prototype.toMSJSON = function () {
    var date = '"\\\/Date(' + this.getTime() + ')\\\/"';
    return date;
};

/* Simple JavaScript Inheritance
 * By John Resig http://ejohn.org/, http://ejohn.org/blog/simple-javascript-inheritance/
 * MIT Licensed.
 */
// Inspired by base2 and Prototype
(function(){
  var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;
  // The base Class implementation (does nothing)
  this.Class = function(){};
  
  // Create a new Class that inherits from this class
  Class.extend = function(prop) {
    var _super = this.prototype;
    
    // Instantiate a base class (but only create the instance,
    // don't run the init constructor)
    initializing = true;
    var prototype = new this();
    initializing = false;
    
    // Copy the properties over onto the new prototype
    for (var name in prop) {
      // Check if we're overwriting an existing function
      prototype[name] = typeof prop[name] == "function" && 
        typeof _super[name] == "function" && fnTest.test(prop[name]) ?
        (function(name, fn){
          return function() {
            var tmp = this._super;
            
            // Add a new ._super() method that is the same method
            // but on the super-class
            this._super = _super[name];
            
            // The method only need to be bound temporarily, so we
            // remove it when we're done executing
            var ret = fn.apply(this, arguments);        
            this._super = tmp;
            
            return ret;
          };
        })(name, prop[name]) :
        prop[name];
    }
    
    // The dummy class constructor
    function Class() {
      // All construction is actually done in the init method
      if ( !initializing && this.init )
        this.init.apply(this, arguments);
    }
    
    // Populate our constructed prototype object
    Class.prototype = prototype;
    
    // Enforce the constructor to be what we expect
    Class.constructor = Class;

    // And make this class extendable
    Class.extend = arguments.callee;
    
    return Class;
  };
})();

var Tg = {};
Tg.Debug = {};
Tg.Debug.log = function (info)
{
//	c ('Tg.Debug.log '+info);
	var data = {
		'info':info
	};
	$.post('/admin/debug',data);
}

