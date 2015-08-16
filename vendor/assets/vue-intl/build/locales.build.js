var fs = require('fs');
var md5 = require('md5');
var glob = require('glob');
var path = require('path');
var plural = path.join(__dirname, '../src/plural.js');
var output = path.join(__dirname, '../dist/locales/');
var source = path.join(__dirname, '../node_modules/angular-i18n/');

global.angular = {

    locales: [],
    plurals: {},

    module: function (name, requires, config) {

        var fn = config[1];

        fn.call(fn, this);
    },

    value: function (name, value) {

        var id = value.id.match(/^\w+/i)[0],
            fn = value.pluralCat.toString();
            key = md5(fn);

        if (this.locales.indexOf(id) === -1) {

            if (!this.plurals[key]) {
                this.plurals[key] = {fn: fn, locales: [id]};
            } else if (this.plurals[key].locales.indexOf(id) === -1) {
                this.plurals[key].locales.push(id);
            }

            this.locales.push(id);
        }

        delete value.pluralCat;

        var file = output + value.id + '.json';
        var data = JSON.stringify(value);

        fs.writeFileSync(file, data);
    }

};

console.log('> Generating locale files ...');

if (!fs.existsSync(output)) {
    fs.mkdirSync(output);
}

glob.sync('angular-locale*', {cwd: source}).forEach(function (file) {
    require(source + file);
});

console.log('> Updating plural.js ...');

var rules = [];
var locales = [];
var content = fs.readFileSync(plural, 'utf8');
var convert = function (value) {
    return "'" + value + "'";
};

Object.keys(angular.plurals).forEach(function (key) {

    var data = angular.plurals[key];

    // skip same locales from 'en', because it's the default
    if (data.locales.indexOf('en') !== -1) {
        data.locales = ['en'];
    }

    locales.push('[' + data.locales.map(convert).toString() + ']');
    rules.push(data.fn.replace(/opt_precision/gi, 'precision'));
});

content = content.replace(/(var PLURAL_LOCALES = \[)[^]*(\]; \/\/ END LOCALES)/gm, '$1' + locales.toString() + '$2');
content = content.replace(/(var PLURAL_RULES = \[)[^]*(\]; \/\/ END RULES)/gm, '$1' + rules.toString() + '$2');

fs.writeFileSync(plural, content);
console.log('');
