{{ #data }}
<div class="uk-overflow-container">
    <table class="uk-table uk-table-hover uk-table-middle pk-finder-table">
        <thead>
            {{ #writable }}
            <th class="pk-table-width-minimum"><input type="checkbox" data-cmd="selectAll"></th>
            {{ /writable }}
            <th colspan="2">Name</th>
            <th class="pk-table-width-minimum uk-text-center">Size</th>
            <th class="pk-table-width-minimum">Modified</th>
            {{ #writable }}
            <th class="pk-table-width-minimum"></th>
            {{ /writable }}
        </thead>
        <tbody>
            {{ #folders }}
            <tr class="uk-visible-hover" data-name="{{ name }}" data-type="folder" data-url="{{ url }}" data-row>
                {{ #writable }}
                <td><input type="checkbox" class="js-select" data-name="{{ name }}"></td>
                {{ /writable }}
                <td class="pk-table-width-minimum">
                    <i class="uk-icon-folder-o pk-finder-icon-folder"></i>
                </td>
                <td class="pk-table-text-break pk-table-min-width-200"><a href="#" data-cmd="loadPath" data-path="{{ path }}">{{ name }}</a></td>
                <td></td>
                <td></td>
                {{ #writable }}
                <td>
                    <ul class="uk-subnav pk-subnav-icon uk-invisible">
                        <li><a class="uk-icon-pencil" data-cmd="rename" data-name="{{ name }}"></a></li>
                        <li><a class="uk-icon-minus-circle" data-cmd="remove" data-name="{{ name }}"></a></li>
                    </ul>
                </td>
                {{ /writable }}
            </tr>
            {{ /folders }}

            {{ #files }}
            <tr class="uk-visible-hover" data-name="{{ name }}" data-url="{{ url }}" data-type="file" data-row>
                {{ #writable }}
                <td><input type="checkbox" class="js-select" data-name="{{ name }}"></td>
                {{ /writable }}
                <td class="pk-table-width-minimum">
                    <i class="uk-icon-file-o pk-finder-icon-file"></i>
                </td>
                <td class="pk-table-text-break pk-table-min-width-200">{{ name }}</td>
                <td class="uk-text-right uk-text-nowrap">{{ size }}</td>
                <td class="uk-text-nowrap">{{ lastmodified }}</td>
                {{ #writable }}
                <td>
                    <ul class="uk-subnav pk-subnav-icon uk-invisible">
                        <li><a class="uk-icon-pencil" data-cmd="rename" data-name="{{ name }}"></a></li>
                        <li><a class="uk-icon-minus-circle" data-cmd="remove" data-name="{{ name }}"></a></li>
                    </ul>
                </td>
                {{ /writable }}
            </tr>
            {{ /files }}
        </tbody>
    </table>
</div>
{{ /data }}

<div class="uk-placeholder uk-text-center uk-text-muted">
    <img src="@url.to('asset://system/images/icon-finder-droparea.svg')" width="22" height="22" alt="@trans('Droparea')"> @trans('Drop files here.')
</div>