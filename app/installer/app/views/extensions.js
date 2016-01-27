window.Extensions = _.merge(require('../components/package-manager.js'), {name: 'extensions', el: '#extensions'});

Vue.ready(window.Extensions);
