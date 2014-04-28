<ul class="uk-list uk-list-line">
    {{#each items}}
    <li>
        <a href="{{link}}">{{title}}</a> <span class="uk-text-muted uk-text-nowrap">{{publishedDate}}</span>
        {{#if content}}
        <p class="uk-margin-small-top">{{content}}</p>
        {{/if}}
    </li>
    {{/each}}
</ul>