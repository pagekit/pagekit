(function(){

    var Local = {

        meta: {
            date: {
                short       : 'n/d/y',
                medium      : 'M d, Y',
                long        : 'F d, y',
                full        : 'l, F d, y',
                longdays    : ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                shortdays   : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                longmonths  : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                shortmonths : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },

            trans: {}
        },


        formatDate: function(timestamp, format){
            return this.date(format || this.meta.date.medium, timestamp);
        },

        date: function(format, timestamp){

            var meta  = this.meta.date,

            return date(format || meta.medium, typeof(timestamp)=='string' ? strtotime(timestamp) : timestamp);
        },

        trans: function(key) {
            var args = arguments.length ==1 ? [] : Array.prototype.slice.call(arguments, 1);

            if (!this.meta.trans[key]) {
                return sprintf(key, args);
            }

            return sprintf(String(this.meta.trans[key]), args);
        },

        register: function(key, data){

            switch(arguments.length) {
                case 1:
                    extend(this.meta.trans, key);
                    break;
                case 2:
                    this.meta.trans[key] = data;
                    break;
            }
        }
    };

    function extend(destination, source) {

        if (!destination || !source) return destination;

        for (var field in source) {
            if (destination[field] === source[field]) continue;
            destination[field] = source[field];
        }
        return destination;
    }

    // copyright: http://phpjs.org

    function str_replace(e,d,a,f){var b=0,c=0,g="",h="",k=0,l=0;e=[].concat(e);d=[].concat(d);var m="[object Array]"===Object.prototype.toString.call(d),n="[object Array]"===Object.prototype.toString.call(a);a=[].concat(a);f&&(this.window[f]=0);b=0;for(k=a.length;b<k;b++)if(""!==a[b])for(c=0,l=e.length;c<l;c++)g=a[b]+"",h=m?void 0!==d[c]?d[c]:"":d[0],a[b]=g.split(e[c]).join(h),f&&a[b]!==g&&(this.window[f]+=(g.length-a[b].length)/e[c].length);return n?a:a[0]};

    function strtotime(c,e){function m(a){var c=a.split(" ");a=c[0];var b=c[1].substring(0,3),e=/\d+/.test(a),f=("last"===a?-1:1)*("ago"===c[2]?-1:1);e&&(f*=parseInt(a,10));if(g.hasOwnProperty(b)&&!c[1].match(/^mon(day|\.)?$/i))return d["set"+g[b]](d["get"+g[b]]()+f);if("wee"===b)return d.setDate(d.getDate()+7*f);if("next"===a||"last"===a)c=f,b=l[b],"undefined"!==typeof b&&(b-=d.getDay(),0===b?b=7*c:0<b&&"last"===a?b-=7:0>b&&"next"===a&&(b+=7),d.setDate(d.getDate()+b));else if(!e)return!1;return!0}var a,
    h,d,l,g,k;if(!c)return null;c=c.trim().replace(/\s{2,}/g," ").replace(/[\t\r\n]/g,"").toLowerCase();if("now"===c)return null===e||isNaN(e)?(new Date).getTime()/1E3|0:e|0;if(!isNaN(a=Date.parse(c)))return a/1E3|0;if("now"===c)return(new Date).getTime()/1E3;if(!isNaN(a=Date.parse(c)))return a/1E3;if(a=c.match(/^(\d{2,4})-(\d{2})-(\d{2})(?:\s(\d{1,2}):(\d{2})(?::\d{2})?)?(?:\.(\d+)?)?$/))return h=0<=a[1]&&69>=a[1]?+a[1]+2E3:a[1],new Date(h,parseInt(a[2],10)-1,a[3],a[4]||0,a[5]||0,a[6]||0,a[7]||0)/1E3;
    d=e?new Date(1E3*e):new Date;l={sun:0,mon:1,tue:2,wed:3,thu:4,fri:5,sat:6};g={yea:"FullYear",mon:"Month",day:"Date",hou:"Hours",min:"Minutes",sec:"Seconds"};a=c.match(RegExp("([+-]?\\d+\\s(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?)|(last|next)\\s(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?))(\\sago)?",
    "gi"));if(!a)return!1;k=0;for(h=a.length;k<h;k++)if(!m(a[k]))return!1;return d.getTime()/1E3};

    function sprintf(){var l=arguments,r=0,s=function(b,a,f,c){f||(f=" ");a=b.length>=a?"":Array(1+a-b.length>>>0).join(f);return c?b+a:a+b},p=function(b,a,f,c,g,d){var e=c-b.length;0<e&&(b=f||!g?s(b,c,d,f):b.slice(0,a.length)+s("",e,"0",!0)+b.slice(a.length));return b},q=function(b,a,f,c,g,d,e){b>>>=0;f=f&&b&&{2:"0b",8:"0",16:"0x"}[a]||"";b=f+s(b.toString(a),d||0,"0",!1);return p(b,f,c,g,e)};return l[r++].replace(/%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuideEfFgG])/g,function(b,
    a,f,c,g,d,e){var h,m;if("%%"===b)return"%";var k=!1;m="";var n=g=!1;h=" ";for(var u=f.length,t=0;f&&t<u;t++)switch(f.charAt(t)){case " ":m=" ";break;case "+":m="+";break;case "-":k=!0;break;case "'":h=f.charAt(t+1);break;case "0":g=!0;break;case "#":n=!0}c=c?"*"===c?+l[r++]:"*"==c.charAt(0)?+l[c.slice(1,-1)]:+c:0;0>c&&(c=-c,k=!0);if(!isFinite(c))throw Error("sprintf: (minimum-)width must be finite");d=d?"*"===d?+l[r++]:"*"==d.charAt(0)?+l[d.slice(1,-1)]:+d:-1<"fFeE".indexOf(e)?6:"d"===e?0:void 0;
    a=a?l[a.slice(0,-1)]:l[r++];switch(e){case "s":return e=String(a),null!=d&&(e=e.slice(0,d)),p(e,"",k,c,g,h);case "c":return e=String.fromCharCode(+a),null!=d&&(e=e.slice(0,d)),p(e,"",k,c,g,void 0);case "b":return q(a,2,n,k,c,d,g);case "o":return q(a,8,n,k,c,d,g);case "x":return q(a,16,n,k,c,d,g);case "X":return q(a,16,n,k,c,d,g).toUpperCase();case "u":return q(a,10,n,k,c,d,g);case "i":case "d":return h=+a||0,h=Math.round(h-h%1),b=0>h?"-":m,a=b+s(String(Math.abs(h)),d,"0",!1),p(a,b,k,c,g);case "e":case "E":case "f":case "F":case "g":case "G":return h=
    +a,b=0>h?"-":m,m=["toExponential","toFixed","toPrecision"]["efg".indexOf(e.toLowerCase())],e=["toString","toUpperCase"]["eEfFgG".indexOf(e)%2],a=b+Math.abs(h)[m](d),p(a,b,k,c,g)[e]();default:return b}})};


    function date(format, timestamp) {

        var that = this;
        var jsdate, f;



        // trailing backslash -> (dropped)
        // a backslash followed by any character (including backslash) -> the character
        // empty string -> empty string
        var formatChr = /\\?(.?)/gi;
        var formatChrCb = function (t, s) {
            return f[t] ? f[t]() : s;
        };
        var _pad = function (n, c) {
            n = String(n);
            while (n.length < c) {
                n = '0' + n;
            }
            return n;
        };
        f = {
            // Day
            d: function () {
                // Day of month w/leading 0; 01..31
                return _pad(f.j(), 2);
            },
            D: function () {
                // Shorthand day name; Mon...Sun
                return date.meta.shortdays[f.w()];
            },
            j: function () {
                // Day of month; 1..31
                return jsdate.getDate();
            },
            l: function () {
                // Full day name; Monday...Sunday
                return date.meta.longdays[f.w()];
            },
            N: function () {
                // ISO-8601 day of week; 1[Mon]..7[Sun]
                return f.w() || 7;
            },
            S: function () {
                // Ordinal suffix for day of month; st, nd, rd, th
                var j = f.j();
                var i = j % 10;
                if (i <= 3 && parseInt((j % 100) / 10, 10) == 1) {
                    i = 0;
                }
                return ['st', 'nd', 'rd'][i - 1] || 'th';
            },
            w: function () {
                // Day of week; 0[Sun]..6[Sat]
                return jsdate.getDay();
            },
            z: function () {
                // Day of year; 0..365
                var a = new Date(f.Y(), f.n() - 1, f.j());
                var b = new Date(f.Y(), 0, 1);
                return Math.round((a - b) / 864e5);
            },

            // Week
            W: function () {
                // ISO-8601 week number
                var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3);
                var b = new Date(a.getFullYear(), 0, 4);
                return _pad(1 + Math.round((a - b) / 864e5 / 7), 2);
            },

            // Month
            F: function () {
                // Full month name; January...December
                return date.meta.longmonths[f.n() - 1];
            },
            m: function () {
                // Month w/leading 0; 01...12
                return _pad(f.n(), 2);
            },
            M: function () {
                // Shorthand month name; Jan...Dec
                return date.meta.shortmonths[f.n() - 1];
            },
            n: function () {
                // Month; 1...12
                return jsdate.getMonth() + 1;
            },
            t: function () {
                // Days in month; 28...31
                return (new Date(f.Y(), f.n(), 0)).getDate();
            },

            // Year
            L: function () {
                // Is leap year?; 0 or 1
                var j = f.Y();
                return j % 4 === 0 & j % 100 !== 0 | j % 400 === 0;
            },
            o: function () {
                // ISO-8601 year
                var n = f.n();
                var W = f.W();
                var Y = f.Y();
                return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0);
            },
            Y: function () {
                // Full year; e.g. 1980...2010
                return jsdate.getFullYear();
            },
            y: function () {
                // Last two digits of year; 00...99
                return f.Y().toString().slice(-2);
            },

            // Time
            a: function () {
                // am or pm
                return jsdate.getHours() > 11 ? 'pm' : 'am';
            },
            A: function () {
                // AM or PM
                return f.a().toUpperCase();
            },
            B: function () {
                // Swatch Internet time; 000..999
                var H = jsdate.getUTCHours() * 36e2;
                // Hours
                var i = jsdate.getUTCMinutes() * 60;
                // Minutes
                // Seconds
                var s = jsdate.getUTCSeconds();
                return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
            },
            g: function () {
                // 12-Hours; 1..12
                return f.G() % 12 || 12;
            },
            G: function () {
                // 24-Hours; 0..23
                return jsdate.getHours();
            },
            h: function () {
                // 12-Hours w/leading 0; 01..12
                return _pad(f.g(), 2);
            },
            H: function () {
                // 24-Hours w/leading 0; 00..23
                return _pad(f.G(), 2);
            },
            i: function () {
                // Minutes w/leading 0; 00..59
                return _pad(jsdate.getMinutes(), 2);
            },
            s: function () {
                // Seconds w/leading 0; 00..59
                return _pad(jsdate.getSeconds(), 2);
            },
            u: function () {
                // Microseconds; 000000-999000
                return _pad(jsdate.getMilliseconds() * 1000, 6);
            },

            // Timezone
            e: function () {
                // Timezone identifier; e.g. Atlantic/Azores, ...
                // The following works, but requires inclusion of the very large
                // timezone_abbreviations_list() function.
                /*              return that.date_default_timezone_get();
                */
                throw 'Not supported (see source code of date() for timezone on how to add support)';
            },
            I: function () {
                // DST observed?; 0 or 1
                // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
                // If they are not equal, then DST is observed.
                var a = new Date(f.Y(), 0);
                // Jan 1
                var c = Date.UTC(f.Y(), 0);
                // Jan 1 UTC
                var b = new Date(f.Y(), 6);
                // Jul 1
                // Jul 1 UTC
                var d = Date.UTC(f.Y(), 6);
                return ((a - c) !== (b - d)) ? 1 : 0;
            },
            O: function () {
                // Difference to GMT in hour format; e.g. +0200
                var tzo = jsdate.getTimezoneOffset();
                var a = Math.abs(tzo);
                return (tzo > 0 ? '-' : '+') + _pad(Math.floor(a / 60) * 100 + a % 60, 4);
            },
            P: function () {
                // Difference to GMT w/colon; e.g. +02:00
                var O = f.O();
                return (O.substr(0, 3) + ':' + O.substr(3, 2));
            },
            T: function () {
                return 'UTC';
            },
            Z: function () {
                // Timezone offset in seconds (-43200...50400)
                return -jsdate.getTimezoneOffset() * 60;
            },

            // Full Date/Time
            c: function () {
                // ISO-8601 date.
                return 'Y-m-d\\TH:i:sP'.replace(formatChr, formatChrCb);
            },
            r: function () {
                // RFC 2822
                return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
            },
            U: function () {
                // Seconds since UNIX epoch
                return jsdate / 1000 | 0;
            }
        };

        this.date = function (format, timestamp) {
            that = this;
            jsdate = (timestamp === undefined ? new Date() : // Not provided
            (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
            new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
            );
            return format.replace(formatChr, formatChrCb);
        };

        return this.date(format, timestamp);
    }

    date.meta = Local.meta.date;

    // AMD support
    if (typeof define === 'function' && define.amd) {
        define(function () { return Local; });
    } else {
        window.Local = Local;
    }

})();
