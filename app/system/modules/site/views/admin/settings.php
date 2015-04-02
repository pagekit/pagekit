<div class="uk-form-row">
    <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
    <div class="uk-form-controls">
        <input id="form-title" class="uk-form-width-large" type="text" name="title" v-model="node.title" v-valid="alphaNum">
        <span class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">{{ 'Invalid name.' | trans }}</span>
    </div>
</div>

<div class="uk-form-row">
    <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>
    <div class="uk-form-controls">
        <span>{{ getPath() }}</span><br>
        <input id="form-slug" class="uk-form-width-large" type="text" name="slug" v-model="node.slug" v-valid="alphaNum">
        <span class="uk-form-help-block uk-text-danger" v-show="form.slug.invalid">{{ 'Invalid slug.' | trans }}</span>
    </div>
</div>
