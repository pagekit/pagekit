<div v-if="files || folders" class="uk-overflow-container">
    <table class="uk-table uk-table-hover uk-table-middle pk-finder-table">
        <thead>
            <th class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=name]"></th>
            <th colspan="2">{{ 'Name' | trans }}</th>
            <th class="pk-table-width-minimum uk-text-center">{{ 'Size' | trans }}</th>
            <th class="pk-table-width-minimum">{{ 'Modified' | trans }}</th>
        </thead>
        <tbody>

            <tr v-repeat="folders | searched" class="uk-visible-hover">
                <td><input type="checkbox" name="name" value="{{ name }}" v-checkbox="selected"></td>
                <td class="pk-table-width-minimum">
                    <i class="uk-icon-folder-o pk-finder-icon-folder"></i>
                </td>
                <td class="pk-table-text-break pk-table-min-width-200"><a v-on="click: loadPath(path)">{{ name }}</a></td>
                <td></td>
                <td></td>
            </tr>

            <tr v-repeat="files | searched" class="uk-visible-hover">
                <td><input type="checkbox" name="name" value="{{ name }}" v-checkbox="selected"></td>
                <td class="pk-table-width-minimum">
                    <i v-if="isImage(url)" class="pk-thumbnail-icon pk-finder-icon-file" style="background-image: url('{{ encodeURI(url) }}');"></i>
                    <i v-if="!isImage(url)" class="uk-icon-file-o pk-finder-icon-file"></i>
                </td>
                <td class="pk-table-text-break pk-table-min-width-200">{{ name }}</td>
                <td class="uk-text-right uk-text-nowrap">{{ size }}</td>
                <td class="uk-text-nowrap">{{ lastmodified }}</td>
            </tr>

        </tbody>
    </table>
</div>
