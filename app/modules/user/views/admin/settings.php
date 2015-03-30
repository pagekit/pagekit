<h2 class="pk-form-heading">{{ 'User' | trans }}</h2>
<div class="uk-form-row">
    <span class="uk-form-label">{{ 'Registration' | trans }}</span>
    <div class="uk-form-controls uk-form-controls-text">
        <p class="uk-form-controls-condensed">
            <label><input type="radio" v-model="option['system/user'].registration" value="admin"> {{ 'Adminstrators only' | trans }}</label>
        </p>
        <p class="uk-form-controls-condensed">
            <label><input type="radio" v-model="option['system/user'].registration" value="guest"> {{ 'Guests' | trans }}</label>
        </p>
        <p class="uk-form-controls-condensed">
            <label><input type="radio" v-model="option['system/user'].registration" value="approval"> {{ 'Guests, but administrator approval is required' | trans }}</label>
        </p>
    </div>
</div>
<div class="uk-form-row">
    <label for="form-user-verification" class="uk-form-label">{{ 'Verification' | trans }}</label>
    <div class="uk-form-controls uk-form-controls-text">
        <label><input id="form-user-verification" type="checkbox" v-model="option['system/user'].require_verification"> {{ 'Require e-mail verification when a guest creates an account.' | trans }}</label>
    </div>
</div>
