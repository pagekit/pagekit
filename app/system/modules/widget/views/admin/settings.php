<div class="uk-form-row">
    <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
    <div class="uk-form-controls">
        <input id="form-title" class="uk-form-width-large" type="text" name="title" v-model="widget.title" v-valid="required">
        <p class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">{{ 'Title cannot be blank.' | trans }}</p>
    </div>
</div>
