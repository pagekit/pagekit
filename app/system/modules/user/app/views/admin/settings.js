module.exports = {

    data: window.$data,

    methods: {

        save: function() {

            this.$http.post('admin/system/settings/config', { name: 'system/user', config: this.config }, function() {
                 this.$notify('Settings saved.');
            }).error(function(data) {
                 this.$notify(data, 'danger');
            });

        }

    }

};

$(function () {

    new Vue(module.exports).$mount('#settings');

});
