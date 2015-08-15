var fs = require('fs');
var glob = require('glob');
var path = require('path');
var output = path.join(__dirname, '../dist/locales/');
var source = path.join(__dirname, '../node_modules/angular-i18n/');

global.angular = {

    module: function (name, requires, config) {

        var fn = config[1];

        fn.call(fn, this);
    },

    value: function (name, value) {

        delete value.pluralCat;

        var file = output + value.id + '.json';
        var data = JSON.stringify(value);

        fs.writeFileSync(file, data);
    }

};

console.log('Generating locale files ...');

if (!fs.existsSync(output)) {
    fs.mkdirSync(output);
}

glob.sync('angular-locale*', {cwd: source}).forEach(function (file) {
    require(source + file);
});

