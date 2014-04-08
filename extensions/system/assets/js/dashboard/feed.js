require(['jquery', 'tmpl!feed.list,feed.error'], function($, tmpl) {

    require(['goog!feeds,1', 'domReady!'], function() {

        $('[data-feed]').each(function() {

            var widget = $(this), config = widget.data('feed'), feed = new google.feeds.Feed(config.url);

            feed.setNumEntries(config.count);
            feed.load(function(result) {

                if (!result.error) {

                    var items = [];

                    $.each(result.feed.entries, function(key, entry) {
                        items.push({
                            link: entry.link,
                            title: entry.title,
                            publishedDate: new Date(entry.publishedDate).toLocaleDateString(),
                            content: (config.content == 1 || (config.content == 2 && key === 0)) ? entry.contentSnippet : null
                        });
                    });

                    widget.find('.js-spinner').addClass('uk-hidden');

                    widget.replaceWith(tmpl.render('feed.list', { items: items }));
                }

            });
        });
    }, function(error) {
        $('[data-feed]').each(function() {
            $(this).replaceWith(tmpl.get('feed.error'));
        });
    });
});