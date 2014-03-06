<ul class="uk-list uk-list-line">
    {{ #items }}
    <li>
        <a href="{{link}}">{{title}}</a> <span class="uk-text-muted uk-text-nowrap">{{publishedDate}}</span>
        {{ #content }}
        <p class="uk-margin-small-top">{{content}}</p>
        {{ /content }}
    </li>
    {{ /items }}
</ul>