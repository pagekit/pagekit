Vue.component('v-mail', {

    inherit: true,
    replace: true,

    template: '#template-mail',

    methods: {

        test: function(driver) {
            $.getJSON(this.$url('admin/system/mail/test/'+driver), { option: this.option['mail'] }, function(data) {
                if (data) {
                    UIkit.notify(data.message, data.success ? 'success' : 'danger');
                }
            }).fail(function () {
                UIkit.notify('Ajax request to server failed.', 'danger');
            });
        }

    }

});
