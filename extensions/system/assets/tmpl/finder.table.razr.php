<ul class="uk-breadcrumb pk-breadcrumb">
    <li><a href="#/" data-cmd="loadPath" data-path="/">@trans('Home')</a></li>
    {{ #breadcrumbs }}
        {{#last}}
        <li class="uk-active"><span>{{ name }}</span></li>
        {{ /last }}
        {{ ^last }}
        <li><a href="" data-cmd="loadPath" data-path="{{ path }}">{{ name }}</a></li>
        {{ /last }}
    {{ /breadcrumbs }}
</ul>

{{ #data }}
<table class="uk-table uk-table-hover uk-table-middle">
    <thead>
        {{ #writable }}
        <th class="pk-table-width-minimum"><input type="checkbox" data-cmd="selectAll"></th>
        {{ /writable }}
        <th>Name</th>
        <th class="pk-table-width-minimum uk-text-center">Size</th>
        <th class="pk-table-width-minimum">Modified</th>
        {{ #writable }}
        <th class="pk-table-width-minimum"></th>
        {{ /writable }}
    </thead>
    <tbody>
        {{ #folders }}
        <tr class="uk-visible-hover" data-name="{{ name }}" data-type="folder" data-url="{{ url }}">
            {{ #writable }}
            <td><input type="checkbox" class="js-select" data-name="{{ name }}"></td>
            {{ /writable }}
            <td><i class="uk-icon-folder-o pk-finder-icon-folder uk-margin-small-right"></i> <a href="#" data-cmd="loadPath" data-path="{{ path }}">{{ name }}</a></td>
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
        <tr class="uk-visible-hover" data-name="{{ name }}" data-url="{{ url }}" data-type="file">
            {{ #writable }}
            <td><input type="checkbox" class="js-select" data-name="{{ name }}"></td>
            {{ /writable }}
            <td><i class="uk-icon-file-o pk-finder-icon-file uk-margin-small-right"></i> <span>{{ name }}</span></td>
            <td class="uk-text-nowrap uk-text-right">{{ size }}</td>
            <td class="uk-text-nowrap">{{ lastmodified }}</td>
            {{ #writable }}
            <td class="uk-text-nowrap">
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
{{ /data }}