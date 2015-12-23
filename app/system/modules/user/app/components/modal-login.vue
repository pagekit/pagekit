<template>

    <div>
        <v-modal v-ref:login>

            <form class="js-login js-toggle uk-form tm-form" @submit.prevent="login" v-el:login>

                <div class="uk-panel uk-panel-box">

                    <h3 class="uk-panel-title">{{ 'Session expired' | trans }}</h3>

                    <div class="uk-form-row">
                        <input class="uk-form uk-width-1-1" type="text" name="credentials[username]" placeholder="{{ 'Username' | trans }}" autofocus v-model="credentials.username">
                    </div>

                    <div class="uk-form-row">
                        <input class="uk-form uk-width-1-1" type="password" name="credentials[password]" placeholder="{{ 'Password' | trans }}" v-model="credentials.password">
                    </div>

                    <div class="uk-form-row">
                        <label class="uk-form"><input type="checkbox" v-model="remember"> {{ 'Remember Me' | trans }}</label>
                    </div>

                    <p class="uk-form-row tm-panel-marginless-bottom">
                        <button class="uk-button uk-button-primary uk-button-large uk-width-1-1">{{ 'Login' | trans }}
                        </button>
                    </p>

                </div>

            </form>

        </v-modal>
    </div>

</template>

<script>

    module.exports = {

        data: function () {
            return {
                credentials: {},
                remember: false
            };
        },

        created: function () {
            this.$mount().$appendTo('body');
            this.promise = this.$promise(function (fulfill, reject) {
                this.fulfill = fulfill;
                this.reject = reject;
            });
        },

        ready: function () {
            var vm = this;

            this.$refs.login.open();
            this.$refs.login.modal.on('hide.uk.modal', function () {
                vm.reject();
                vm.$destroy();
            });
        },

        methods: {

            login: function () {

                this.$http.post('user/authenticate', {
                    credentials: this.credentials,
                    _remember_me: this.remember
                }).then(function () {
                    this.fulfill();
                    this.$refs.login.close();
                }, function (res) {
                    this.$notify(res.data, 'danger');
                });

            }

        }

    }
    ;

</script>
