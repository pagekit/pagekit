var Debug = require('./debugbar.vue');

Debug.register('system', require('./components/system.vue'));
Debug.register('routes', require('./components/routes.vue'));
Debug.register('events', require('./components/events.vue'));
Debug.register('time', require('./components/time.vue'));
Debug.register('memory', require('./components/memory.vue'));
Debug.register('database', require('./components/database.vue'));
Debug.register('request', require('./components/request.vue'));
Debug.register('auth', require('./components/auth.vue'));

$(function () {

    new Debug().$appendTo('body');

});

module.exports = Debug;
