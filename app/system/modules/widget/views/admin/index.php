<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
    <div data-uk-margin>

        <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
            <button class="uk-button uk-button-primary" type="button">{{ 'Add Widget' | trans }}</button>
            <div class="uk-dropdown uk-dropdown-small">
                <ul class="uk-nav uk-nav-dropdown">
                    <li v-repeat="type: types"><a v-on="click: add(type)">{{ type.name }}</a></li>
                </ul>
            </div>
        </div>

        <a class="uk-button pk-button-danger" v-show="selected.length" v-on="click: remove">{{ 'Delete' | trans }}</a>

        <div class="uk-button-dropdown" v-show="selected.length" data-uk-dropdown="{ mode: 'click' }">
            <button class="uk-button" type="button">{{ 'More' | trans }} <i class="uk-icon-caret-down"></i></button>
            <div class="uk-dropdown uk-dropdown-small">
                <ul class="uk-nav uk-nav-dropdown">
                    <li><a v-on="click: status(1)">{{ 'Enable' | trans }}</a></li>
                    <li><a v-on="click: status(0)">{{ 'Disable' | trans }}</a></li>
                    <li class="uk-nav-divider"></li>
                    <li><a v-on="click: copy">{{ 'Copy' | trans }}</a></li>
                </ul>
            </div>
        </div>

    </div>
    <div data-uk-margin>
        <input type="text" v-model="search" placeholder="{{ 'Search' | trans }}" v-on="keypress: preventSubmit">
    </div>
</div>

<div class="uk-overflow-container">

    <div class="pk-table-fake pk-table-fake-header pk-table-fake-header-indent pk-table-fake-border">
        <div class="pk-table-width-minimum"><input type="checkbox"  v-check-all="selected: input[name=id]"></div>
        <div class="pk-table-min-width-100">{{ 'Title' | trans }}</div>
        <div class="pk-table-width-100 uk-text-center">{{ 'Status' | trans }}</div>
        <div class="pk-table-width-150">{{ 'Position' | trans }}</div>
        <div class="pk-table-width-150">{{ 'Type' | trans }}</div>
    </div>

    <div v-repeat="position: positions | assignable" v-show="sorted[position.id]">

        <div class="pk-table-fake pk-table-fake-header pk-table-fake-subheading">
            <div>
                {{ position.name | trans }}
                <span v-if="position.description" class="uk-text-muted">{{ position.description | trans }}</span>
            </div>
        </div>

        <ul class="uk-nestable" v-component="widget-list" inline-template>

            <li data-id="{{ widget.id }}" v-repeat="widget: sorted[position.id]" class="uk-form uk-nestable-list-item">

                <div class="uk-nestable-item pk-table-fake" v-component="widget-item" inline-template>

                    <div class="pk-table-width-minimum">
                        <div class="uk-nestable-handle">​</div>
                    </div>
                    <div class="pk-table-width-minimum"><input type="checkbox" name="id" value="{{ widget.id }}"></div>
                    <div class="pk-table-min-width-100">
                        <a v-show="type" v-on="click: edit(widget)">{{ widget.title }}</a>
                        <span v-show="!type">{{ widget.title }}</span>
                    </div>
                    <div class="pk-table-width-100 uk-text-center">
                        <a class="uk-icon-circle" v-class="
                            uk-text-success: widget.status,
                            uk-text-danger: !widget.status
                        " v-on="click: toggleStatus" title="{{ widget.statusText }}"></a>
                    </div>
                    <div class="pk-table-width-150">
                        <div class="uk-form-select" v-el="select">
                            <a></a>
                            <select v-model="widget.position" class="uk-width-1-1" options="positionOptions" v-on="input: save(widget)"></select>
                        </div>
                    </div>
                    <div class="pk-table-width-150">{{ typeName }}</div>

                </div>

            </li>

        </ul>

    </div>

</div>

<div v-el="modal" class="uk-modal" v-on="close: cancel()">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <iframe v-attr="src: editUrl" class="uk-width-1-1"></iframe>
    </div>
</div>
