<div v-component="v-mail"></div>

<script id="template-mail" type="x-template">

    <h2 class="pk-form-heading">{{ 'Email' | trans }}</h2>
    <div class="uk-form-row">
        <label for="form-emailaddress" class="uk-form-label">{{ 'From Email' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-emailaddress" class="uk-form-width-large" type="text" v-model="option['system/mail'].from_address">
        </div>
    </div>
    <div class="uk-form-row">
        <label for="form-fromname" class="uk-form-label">{{ 'From Name' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-fromname" class="uk-form-width-large" type="text" v-model="option['system/mail'].from_name">
        </div>
    </div>
    <div class="uk-form-row">
        <label for="form-mailer" class="uk-form-label">{{ 'Mailer' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-mailer" class="uk-form-width-large" v-model="option['system/mail'].driver">
                <option value="mail">{{ 'PHP Mailer' | trans }}</option>
                <option value="smtp">{{ 'SMTP Mailer' | trans }}</option>
            </select>
        </div>
    </div>
    <div class="uk-form-row" v-show="'smtp' == option['system/mail'].driver">
        <div class="uk-form-row">
            <label for="form-smtpport" class="uk-form-label">{{ 'SMTP Port' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-smtpport" class="uk-form-width-large" type="text" v-model="option['system/mail'].port">
            </div>
        </div>
        <div class="uk-form-row">
            <label for="form-smtphost" class="uk-form-label">{{ 'SMTP Host' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-smtphost" class="uk-form-width-large" type="text" v-model="option['system/mail'].host">
            </div>
        </div>
        <div class="uk-form-row">
            <label for="form-smtpuser" class="uk-form-label">{{ 'SMTP User' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-smtpuser" class="uk-form-width-large" type="text" v-model="option['system/mail'].username">
            </div>
        </div>
        <div class="uk-form-row">
            <label for="form-smtppassword" class="uk-form-label">{{ 'SMTP Password' | trans }}</label>
            <div class="uk-form-controls js-password">
                <div class="uk-form-password">
                    <input id="form-smtppassword" class="uk-form-width-large" type="password" v-model="option['system/mail'.password]">
                    <a href="" class="uk-form-password-toggle" data-uk-form-password>{{ 'Show' | trans }}</a>
                </div>
            </div>
        </div>
        <div class="uk-form-row">
            <label for="form-smtpencryption" class="uk-form-label">{{ 'SMTP Encryption' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-smtpencryption" class="uk-form-width-large" v-model="option['system/mail'].encryption">
                    <option value="">{{ 'None' | trans }}</option>
                    <option v-attr="disabled: !mail.ssl" value="ssl">{{ 'SSL' | trans }}</option>
                    <option v-attr="disabled: !mail.ssl" value="tls">{{ 'TLS' | trans }}</option>
                </select>
                <p v-if="!mail.ssl" class="uk-form-help-block">{{ 'Please enable the PHP Open SSL extension.' | trans }}</p>
            </div>
        </div>
    </div>
    <div class="uk-form-row">
        <div class="uk-form-controls">
            <button v-show="'smtp' == option['system/mail'].driver" type="button" class="uk-button" v-on="click: test('smtp')">{{ 'Check Connection' | trans }}</button>
            <button type="button" class="uk-button" v-on="click: test('email')">{{ 'Send Test Email' | trans }}</button>
        </div>
    </div>

</script>
