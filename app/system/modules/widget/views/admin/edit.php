<form id="js-widget-edit" class="uk-form uk-container uk-container-center" name="form" v-on="valid: save" v-cloak>

    <div class="uk-clearfix uk-margin" data-uk-margin>

        <div class="uk-float-left">

            <h2 v-if="widget.id" class="uk-h2">{{ widget.title }} ({{ typeName }})</h2>
            <h2 v-if="!widget.id" class="uk-h2">{{ 'Add %type%' | trans {type:typeName} }}</h2>

        </div>

        <div class="uk-float-right">

            <a class="uk-button" v-on="click: cancel()">{{ 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>

    </div>

    <div class="uk-grid" data-uk-grid-margin>

        <div class="uk-width-medium-3-4 uk-form-horizontal">

            <ul class="uk-tab" v-el="tab">
                <?php foreach ($sections as $name => $section) : ?>
                    <li><a><?= __($name) ?></a></li>
                <?php endforeach ?>
            </ul>

            <ul class="uk-switcher uk-margin" v-el="content">
                <?php foreach ($sections as $subsections) : ?>
                    <li>
                        <?php
                        foreach ($subsections as $section) {
                            $params = array_merge($section, ['widget' => $widget]);
                            if (is_callable($section['view'])) {
                                echo call_user_func($section['view'], $widget);
                            } else {
                                echo $view->render($section['view'], ['widget' => $widget]);
                            }
                        }
                        ?>
                    </li>
                <?php endforeach ?>
            </ul>

        </div>

        <div class="uk-width-medium-1-4 uk-form-stacked">

            <div class="uk-panel uk-panel-divider">

                <div class="uk-form-row">
                    <label for="form-status" class="uk-form-label">{{ 'Status' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-status" class="uk-width-1-1" v-model="widget.status">
                            <option value="1">{{ 'Enabled' | trans }}</option>
                            <option value="0">{{ 'Disabled' | trans }}</option>
                        </select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label for="form-position" class="uk-form-label">{{ 'Position' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-position" v-model="widget.position" class="uk-width-1-1" options="positionOptions"></select>

                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Restrict Access' | trans }}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p v-repeat="role: roles" class="uk-form-controls-condensed">
                            <label><input type="checkbox" value="{{ role.id }}" v-checkbox="widget.roles"> {{ role.name }}</label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Options' | trans }}</span>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" v-model="widget.settings.show_title"> {{ 'Show Title' | trans }}</label>
                    </div>
                </div>

            </div>

        </div>

    </div>

</form>
