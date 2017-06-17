var VES_Vendor_Livechat_Cookie = {
    key: 'ves_vendors_livechat_cookies',
    set: function(key, value) {
        var cookies = this.getCookies();
        cookies[key] = value;
        var src = Object.toJSON(cookies).toString();
        this.setCookie(this.key, src);
        },

    get: function(key){
        if (this.exists(key)) {
            var cookies = this.getCookies();
            return cookies[key];
        }
       //if (arguments.length == 2) {
              //  return arguments[1];
       //  }
        return;
    },

    exists: function(key){
        return key in this.getCookies();
        },

    clear: function(key){
        var cookies = this.getCookies();
        delete cookies[key];
        var src = Object.toJSON(cookies).toString();
        this.setCookie(this.key, src);
        },

    getCookies: function() {
        return this.hasCookie(this.key) ? this.getCookie(this.key).evalJSON() : {};
    },

    hasCookie: function(key) {
        return this.getCookie(key) != null;
        },

    setCookie: function(key,value) {
        var expires = new Date();
        expires.setTime(expires.getTime()+1000*60*60*24*365)
        document.cookie = key+'='+escape(value)+'; expires='+expires+'; path=/';
        },

    getCookie: function(key) {
        var cookie = key+'=';
        var array = document.cookie.split(';');
        for (var i = 0; i < array.length; i++) {
        var c = array[i];
        while (c.charAt(0) == ' '){
        c = c.substring(1, c.length);
        }
    if (c.indexOf(cookie) == 0) {
        var result = c.substring(cookie.length, c.length);
        return unescape(result);
        };
    }
    return null;
    }
}


var addEvent = (function () {
    if (document.addEventListener) {
        return function (el, type, fn) {
            if (el && el.nodeName || el === window) {
                el.addEventListener(type, fn, false);
            } else if (el && el.length) {
                for (var i = 0; i < el.length; i++) {
                    addEvent(el[i], type, fn);
                }
            }
        };
    } else {
        return function (el, type, fn) {
            if (el && el.nodeName || el === window) {
                el.attachEvent('on' + type, function () { return fn.call(el, window.event); });
            } else if (el && el.length) {
                for (var i = 0; i < el.length; i++) {
                    addEvent(el[i], type, fn);
                }
            }
        };
    }
})();