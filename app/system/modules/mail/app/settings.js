Vue.component('v-mail', {

    inherit: true,
    replace: true,

    template: '#mail-tmpl',

    methods: {

        test: function (driver) {

            this.$http.post('admin/system/mail/' + driver, { option: this.option['mail'] }, function (data) {
                UIkit.notify(data.message, data.success ? 'success' : 'danger');
            }).error(function () {
                UIkit.notify('Ajax request to server failed.', 'danger');
            });

        }

    }

});
