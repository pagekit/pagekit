(function(){

    var Local = {

        meta: {
            date: {
                short       : 'n/d/y',
                medium      : 'M d, Y',
                long        : 'F d, y',
                full        : 'l, F d, y',
                longdays    : null,
                shortdays   : null,
                shortdays   : null,
                longmonths  : null,
                shortmonths : null
            }
        },


        formatDate: function(timestamp, format){
            return this.date(format || this.meta.date.medium, timestamp);
        },

        date: function(format, timestamp){

            var meta  = this.meta.date,
                fdate = date(format || meta.medium, typeof(timestamp)=='string' ? strtotime(timestamp) : timestamp);

            // weekdays
            if(meta.longdays && meta.longdays) {
                fdate = str_replace(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'], meta.longdays, fdate);
                fdate = str_replace(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], meta.shortdays, fdate);
            }

            // months
            if(meta.longmonths && meta.shortmonths) {
                fdate = str_replace(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'], meta.longmonths, fdate);
                fdate = str_replace(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], meta.shortmonths, fdate);
            }

            return fdate;
        }
    };

    // AMD support
    if (typeof define === 'function' && define.amd) {
        define(function () { return Local; });
    } else {
        window.Local = Local;
    }

    // copyright: http://phpjs.org

    function str_replace(e,d,a,f){var b=0,c=0,g="",h="",k=0,l=0;e=[].concat(e);d=[].concat(d);var m="[object Array]"===Object.prototype.toString.call(d),n="[object Array]"===Object.prototype.toString.call(a);a=[].concat(a);f&&(this.window[f]=0);b=0;for(k=a.length;b<k;b++)if(""!==a[b])for(c=0,l=e.length;c<l;c++)g=a[b]+"",h=m?void 0!==d[c]?d[c]:"":d[0],a[b]=g.split(e[c]).join(h),f&&a[b]!==g&&(this.window[f]+=(g.length-a[b].length)/e[c].length);return n?a:a[0]};

    function date(k,l){var d,a,h="Sun Mon Tues Wednes Thurs Fri Satur January February March April May June July August September October November December".split(" "),f=/\\?(.?)/gi,g=function(b,c){return a[b]?a[b]():c},e=function(b,a){for(b=String(b);b.length<a;)b="0"+b;return b};a={d:function(){return e(a.j(),2)},D:function(){return a.l().slice(0,3)},j:function(){return d.getDate()},l:function(){return h[a.w()]+"day"},N:function(){return a.w()||7},S:function(){var b=a.j(),c=b%10;3>=c&&1==parseInt(b%
    100/10,10)&&(c=0);return["st","nd","rd"][c-1]||"th"},w:function(){return d.getDay()},z:function(){var b=new Date(a.Y(),a.n()-1,a.j()),c=new Date(a.Y(),0,1);return Math.round((b-c)/864E5)},W:function(){var b=new Date(a.Y(),a.n()-1,a.j()-a.N()+3),c=new Date(b.getFullYear(),0,4);return e(1+Math.round((b-c)/864E5/7),2)},F:function(){return h[6+a.n()]},m:function(){return e(a.n(),2)},M:function(){return a.F().slice(0,3)},n:function(){return d.getMonth()+1},t:function(){return(new Date(a.Y(),a.n(),0)).getDate()},
    L:function(){var b=a.Y();return 0===b%4&0!==b%100|0===b%400},o:function(){var b=a.n(),c=a.W();return a.Y()+(12===b&&9>c?1:1===b&&9<c?-1:0)},Y:function(){return d.getFullYear()},y:function(){return a.Y().toString().slice(-2)},a:function(){return 11<d.getHours()?"pm":"am"},A:function(){return a.a().toUpperCase()},B:function(){var a=3600*d.getUTCHours(),c=60*d.getUTCMinutes(),f=d.getUTCSeconds();return e(Math.floor((a+c+f+3600)/86.4)%1E3,3)},g:function(){return a.G()%12||12},G:function(){return d.getHours()},
    h:function(){return e(a.g(),2)},H:function(){return e(a.G(),2)},i:function(){return e(d.getMinutes(),2)},s:function(){return e(d.getSeconds(),2)},u:function(){return e(1E3*d.getMilliseconds(),6)},e:function(){throw"Not supported (see source code of date() for timezone on how to add support)";},I:function(){var b=new Date(a.Y(),0),c=Date.UTC(a.Y(),0),d=new Date(a.Y(),6),e=Date.UTC(a.Y(),6);return b-c!==d-e?1:0},O:function(){var a=d.getTimezoneOffset(),c=Math.abs(a);return(0<a?"-":"+")+e(100*Math.floor(c/
    60)+c%60,4)},P:function(){var b=a.O();return b.substr(0,3)+":"+b.substr(3,2)},T:function(){return"UTC"},Z:function(){return 60*-d.getTimezoneOffset()},c:function(){return"Y-m-d\\TH:i:sP".replace(f,g)},r:function(){return"D, d M Y H:i:s O".replace(f,g)},U:function(){return d/1E3|0}};this.date=function(a,c){d=void 0===c?new Date:c instanceof Date?new Date(c):new Date(1E3*c);return a.replace(f,g)};return this.date(k,l)};

    function strtotime(c,e){function m(a){var c=a.split(" ");a=c[0];var b=c[1].substring(0,3),e=/\d+/.test(a),f=("last"===a?-1:1)*("ago"===c[2]?-1:1);e&&(f*=parseInt(a,10));if(g.hasOwnProperty(b)&&!c[1].match(/^mon(day|\.)?$/i))return d["set"+g[b]](d["get"+g[b]]()+f);if("wee"===b)return d.setDate(d.getDate()+7*f);if("next"===a||"last"===a)c=f,b=l[b],"undefined"!==typeof b&&(b-=d.getDay(),0===b?b=7*c:0<b&&"last"===a?b-=7:0>b&&"next"===a&&(b+=7),d.setDate(d.getDate()+b));else if(!e)return!1;return!0}var a,
    h,d,l,g,k;if(!c)return null;c=c.trim().replace(/\s{2,}/g," ").replace(/[\t\r\n]/g,"").toLowerCase();if("now"===c)return null===e||isNaN(e)?(new Date).getTime()/1E3|0:e|0;if(!isNaN(a=Date.parse(c)))return a/1E3|0;if("now"===c)return(new Date).getTime()/1E3;if(!isNaN(a=Date.parse(c)))return a/1E3;if(a=c.match(/^(\d{2,4})-(\d{2})-(\d{2})(?:\s(\d{1,2}):(\d{2})(?::\d{2})?)?(?:\.(\d+)?)?$/))return h=0<=a[1]&&69>=a[1]?+a[1]+2E3:a[1],new Date(h,parseInt(a[2],10)-1,a[3],a[4]||0,a[5]||0,a[6]||0,a[7]||0)/1E3;
    d=e?new Date(1E3*e):new Date;l={sun:0,mon:1,tue:2,wed:3,thu:4,fri:5,sat:6};g={yea:"FullYear",mon:"Month",day:"Date",hou:"Hours",min:"Minutes",sec:"Seconds"};a=c.match(RegExp("([+-]?\\d+\\s(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?)|(last|next)\\s(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?))(\\sago)?",
    "gi"));if(!a)return!1;k=0;for(h=a.length;k<h;k++)if(!m(a[k]))return!1;return d.getTime()/1E3};

})();
