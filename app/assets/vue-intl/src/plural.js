/**
 * Pluralization rules.
 */

module.exports = function (_) {

    function getDecimals(n) {
      n = n + '';
      var i = n.indexOf('.');
      return (i == -1) ? 0 : n.length - i - 1;
    }

    function getVF(n, precision) {
      var v = precision;

      if (undefined === v) {
        v = Math.min(getDecimals(n), 3);
      }

      var base = Math.pow(10, v);
      var f = ((n * base) | 0) % base;
      return {v: v, f: f};
    }

    var PLURAL_CACHE = {};
    var PLURAL_CATEGORY = {ZERO: 'zero', ONE: 'one', TWO: 'two', FEW: 'few', MANY: 'many', OTHER: 'other'};
    var PLURAL_LOCALES = [['en'],['af','az','bg','chr','el','es','eu','gsw','haw','hu','ka','kk','ky','ml','mn','nb','ne','no','or','sq','ta','te','tr','uz'],['am','bn','fa','gu','hi','kn','mr','zu'],['ar'],['be'],['br'],['bs','hr','sr'],['cs','sk'],['cy'],['da'],['fil','tl'],['fr','hy'],['ga'],['he','iw'],['id','in','ja','km','ko','lo','my','th','vi','zh'],['is'],['ln','pa'],['lt'],['lv'],['mk'],['ms'],['mt'],['pl'],['pt'],['ro'],['ru','uk'],['si'],['sl']]; // END LOCALES
    var PLURAL_RULES = [function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  if (i == 1 && vf.v == 0) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  if (n == 1) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  if (i == 0 || n == 1) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  if (n == 0) {    return PLURAL_CATEGORY.ZERO;  }  if (n == 1) {    return PLURAL_CATEGORY.ONE;  }  if (n == 2) {    return PLURAL_CATEGORY.TWO;  }  if (n % 100 >= 3 && n % 100 <= 10) {    return PLURAL_CATEGORY.FEW;  }  if (n % 100 >= 11 && n % 100 <= 99) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  if (n % 10 == 1 && n % 100 != 11) {    return PLURAL_CATEGORY.ONE;  }  if (n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 12 || n % 100 > 14)) {    return PLURAL_CATEGORY.FEW;  }  if (n % 10 == 0 || n % 10 >= 5 && n % 10 <= 9 || n % 100 >= 11 && n % 100 <= 14) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  if (n % 10 == 1 && n % 100 != 11 && n % 100 != 71 && n % 100 != 91) {    return PLURAL_CATEGORY.ONE;  }  if (n % 10 == 2 && n % 100 != 12 && n % 100 != 72 && n % 100 != 92) {    return PLURAL_CATEGORY.TWO;  }  if ((n % 10 >= 3 && n % 10 <= 4 || n % 10 == 9) && (n % 100 < 10 || n % 100 > 19) && (n % 100 < 70 || n % 100 > 79) && (n % 100 < 90 || n % 100 > 99)) {    return PLURAL_CATEGORY.FEW;  }  if (n != 0 && n % 1000000 == 0) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  if (vf.v == 0 && i % 10 == 1 && i % 100 != 11 || vf.f % 10 == 1 && vf.f % 100 != 11) {    return PLURAL_CATEGORY.ONE;  }  if (vf.v == 0 && i % 10 >= 2 && i % 10 <= 4 && (i % 100 < 12 || i % 100 > 14) || vf.f % 10 >= 2 && vf.f % 10 <= 4 && (vf.f % 100 < 12 || vf.f % 100 > 14)) {    return PLURAL_CATEGORY.FEW;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  if (i == 1 && vf.v == 0) {    return PLURAL_CATEGORY.ONE;  }  if (i >= 2 && i <= 4 && vf.v == 0) {    return PLURAL_CATEGORY.FEW;  }  if (vf.v != 0) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  if (n == 0) {    return PLURAL_CATEGORY.ZERO;  }  if (n == 1) {    return PLURAL_CATEGORY.ONE;  }  if (n == 2) {    return PLURAL_CATEGORY.TWO;  }  if (n == 3) {    return PLURAL_CATEGORY.FEW;  }  if (n == 6) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  var wt = getWT(vf.v, vf.f);  if (n == 1 || wt.t != 0 && (i == 0 || i == 1)) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  if (vf.v == 0 && (i == 1 || i == 2 || i == 3) || vf.v == 0 && i % 10 != 4 && i % 10 != 6 && i % 10 != 9 || vf.v != 0 && vf.f % 10 != 4 && vf.f % 10 != 6 && vf.f % 10 != 9) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  if (i == 0 || i == 1) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  if (n == 1) {    return PLURAL_CATEGORY.ONE;  }  if (n == 2) {    return PLURAL_CATEGORY.TWO;  }  if (n >= 3 && n <= 6) {    return PLURAL_CATEGORY.FEW;  }  if (n >= 7 && n <= 10) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  if (i == 1 && vf.v == 0) {    return PLURAL_CATEGORY.ONE;  }  if (i == 2 && vf.v == 0) {    return PLURAL_CATEGORY.TWO;  }  if (vf.v == 0 && (n < 0 || n > 10) && n % 10 == 0) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  var wt = getWT(vf.v, vf.f);  if (wt.t == 0 && i % 10 == 1 && i % 100 != 11 || wt.t != 0) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  if (n >= 0 && n <= 1) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var vf = getVF(n, precision);  if (n % 10 == 1 && (n % 100 < 11 || n % 100 > 19)) {    return PLURAL_CATEGORY.ONE;  }  if (n % 10 >= 2 && n % 10 <= 9 && (n % 100 < 11 || n % 100 > 19)) {    return PLURAL_CATEGORY.FEW;  }  if (vf.f != 0) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var vf = getVF(n, precision);  if (n % 10 == 0 || n % 100 >= 11 && n % 100 <= 19 || vf.v == 2 && vf.f % 100 >= 11 && vf.f % 100 <= 19) {    return PLURAL_CATEGORY.ZERO;  }  if (n % 10 == 1 && n % 100 != 11 || vf.v == 2 && vf.f % 10 == 1 && vf.f % 100 != 11 || vf.v != 2 && vf.f % 10 == 1) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  if (vf.v == 0 && i % 10 == 1 || vf.f % 10 == 1) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},function (n) {  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  if (n == 1) {    return PLURAL_CATEGORY.ONE;  }  if (n == 0 || n % 100 >= 2 && n % 100 <= 10) {    return PLURAL_CATEGORY.FEW;  }  if (n % 100 >= 11 && n % 100 <= 19) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  if (i == 1 && vf.v == 0) {    return PLURAL_CATEGORY.ONE;  }  if (vf.v == 0 && i % 10 >= 2 && i % 10 <= 4 && (i % 100 < 12 || i % 100 > 14)) {    return PLURAL_CATEGORY.FEW;  }  if (vf.v == 0 && i != 1 && i % 10 >= 0 && i % 10 <= 1 || vf.v == 0 && i % 10 >= 5 && i % 10 <= 9 || vf.v == 0 && i % 100 >= 12 && i % 100 <= 14) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  if (n >= 0 && n <= 2 && n != 2) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  if (i == 1 && vf.v == 0) {    return PLURAL_CATEGORY.ONE;  }  if (vf.v != 0 || n == 0 || n != 1 && n % 100 >= 1 && n % 100 <= 19) {    return PLURAL_CATEGORY.FEW;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  if (vf.v == 0 && i % 10 == 1 && i % 100 != 11) {    return PLURAL_CATEGORY.ONE;  }  if (vf.v == 0 && i % 10 >= 2 && i % 10 <= 4 && (i % 100 < 12 || i % 100 > 14)) {    return PLURAL_CATEGORY.FEW;  }  if (vf.v == 0 && i % 10 == 0 || vf.v == 0 && i % 10 >= 5 && i % 10 <= 9 || vf.v == 0 && i % 100 >= 11 && i % 100 <= 14) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  if ((n == 0 || n == 1) || i == 0 && vf.f == 1) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},function (n, precision) {  var i = n | 0;  var vf = getVF(n, precision);  if (vf.v == 0 && i % 100 == 1) {    return PLURAL_CATEGORY.ONE;  }  if (vf.v == 0 && i % 100 == 2) {    return PLURAL_CATEGORY.TWO;  }  if (vf.v == 0 && i % 100 >= 3 && i % 100 <= 4 || vf.v != 0) {    return PLURAL_CATEGORY.FEW;  }  return PLURAL_CATEGORY.OTHER;}]; // END RULES

    PLURAL_LOCALES.map(function (locales, i) {
        locales.map(function (locale) {
            PLURAL_CACHE[locale] = PLURAL_RULES[i];
        });
    });

    _.pluralCat = function (id, num, precision) {

        var match = id.match(/^\w+/i);

        if (match) {
            id = match[0];
        }

        if (!PLURAL_CACHE[id]) {
            id = 'en';
        }

        return PLURAL_CACHE[id](num, precision);
    };

};
