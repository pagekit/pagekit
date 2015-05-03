var Debugbar = require('./debugbar.vue');

Debugbar.register('system', require('./components/system.vue'));
Debugbar.register('routes', require('./components/routes.vue'));
Debugbar.register('events', require('./components/events.vue'));
Debugbar.register('time', require('./components/time.vue'));
Debugbar.register('memory', require('./components/memory.vue'));
Debugbar.register('database', require('./components/database.vue'));
Debugbar.register('request', require('./components/request.vue'));
Debugbar.register('auth', require('./components/auth.vue'));
Debugbar.register('log', require('./components/log.vue'));

$(function () {

    new Debugbar().$appendTo('body');

});

module.exports = Debugbar;
