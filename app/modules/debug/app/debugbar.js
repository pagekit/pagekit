var Debugbar = Vue.extend(require('./debugbar.vue'));

Debugbar.component('time', require('./components/time.vue'));
Debugbar.component('system', require('./components/system.vue'));
Debugbar.component('events', require('./components/events.vue'));
Debugbar.component('routes', require('./components/routes.vue'));
Debugbar.component('memory', require('./components/memory.vue'));
Debugbar.component('database', require('./components/database.vue'));
//Debugbar.component('request', require('./components/request.vue'));
Debugbar.component('auth', require('./components/auth.vue'));
Debugbar.component('log', require('./components/log.vue'));
Debugbar.component('profile', require('./components/profile.vue'));

Vue.ready(function () {

    new Debugbar().$mount().$appendTo('body');

});

module.exports = Debugbar;
