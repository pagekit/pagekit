<?php $view->script('site', 'site:app/bundle/site.js', ['system', 'vue-validator', 'uikit-nestable']) ?>

<div id="site" v-cloak>

    <div class="uk-grid">

        <div class="uk-panel uk-panel-box uk-width-1-4" v-component="menu-list"></div>

        <div class="uk-panel uk-panel-box uk-width-3-4" v-component="node-edit" inline-template>

            <form v-show="node.type" class="uk-form uk-form-horizontal" name="form" v-on="valid: save">

                <div class="uk-clearfix uk-margin">

                    <div class="uk-float-left">

                        <h2 v-if="node.id" class="uk-h2">{{ node.title }} ({{ type.label }})</h2>
                        <h2 v-if="!node.id" class="uk-h2">{{ 'Add %type%' | trans {type:type.label} }}</h2>

                    </div>

                    <div class="uk-float-right">

                        <a class="uk-button" v-on="click: cancel()">{{ 'Cancel' | trans }}</a>
                        <button class="uk-button uk-button-primary" type="submit" v-attr="disabled: form.invalid">{{ 'Save' | trans }}</button>

                    </div>

                </div>

                <div v-el="edit"></div>

            </form>

        </div>

    </div>

</div>
