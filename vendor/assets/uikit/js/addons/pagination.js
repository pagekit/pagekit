(function(addon) {

    if (typeof define == "function" && define.amd) { // AMD
        define("uikit-pagination", ["uikit"], function(){
            return jQuery.UIkit.pagination || addon(window, window.jQuery, window.jQuery.UIkit);
        });
    }

    if(window && window.jQuery && window.jQuery.UIkit) {
        addon(window, window.jQuery, window.jQuery.UIkit);
    }

})(function(global, $, UI){

    "use strict";

    var Pagination = function(element, options) {

        var $element = $(element), $this = this;

        if ($element.data("pagination")) return;

        this.element       = $element;
        this.options       = $.extend({}, Pagination.defaults, options);
        this.pages         = this.options.pages ?  this.options.pages : Math.ceil(this.options.items / this.options.itemsOnPage) ? Math.ceil(this.options.items / this.options.itemsOnPage) : 1;
        this.currentPage   = this.options.currentPage - 1;
        this.halfDisplayed = this.options.displayedPages / 2;

        $element.data("pagination", this);

        this._draw();
    };


    $.extend(Pagination.prototype, {


        _getInterval: function() {

            return {
                start: Math.ceil(this.currentPage > this.halfDisplayed ? Math.max(Math.min(this.currentPage - this.halfDisplayed, (this.pages - this.options.displayedPages)), 0) : 0),
                end: Math.ceil(this.currentPage > this.halfDisplayed ? Math.min(this.currentPage + this.halfDisplayed, this.pages) : Math.min(this.options.displayedPages, this.pages))
            };
        },

        redraw: function(pages) {

            if(pages) {
                this.pages = pages;
            }

            this._draw();
        },

        selectPage: function(pageIndex) {
            this.currentPage = pageIndex;
            this._draw();
        },

        _draw: function() {

            var o = this.options, interval = this._getInterval(), i;

            this.element.empty();

            // Generate Prev link
            if (o.prevText) {
                this._appendItem(o.currentPage - 1, {text: o.prevText});
            }

            // Generate Next link (if option set for at front)
            if (o.nextText && o.nextAtFront) {
                this._appendItem(o.currentPage + 1, {text: o.nextText});
            }

            // Generate start edges
            if (interval.start > 0 && o.edges > 0) {
                var end = Math.min(o.edges, interval.start);
                for (i = 0; i < end; i++) {
                    this._appendItem(i);
                }
                if (o.edges < interval.start && (interval.start - o.edges != 1)) {
                    this.element.append('<li><span>...</span></li>');
                } else if (interval.start - o.edges == 1) {
                    this._appendItem(o.edges);
                }
            }

            // Generate interval links
            for (i = interval.start; i < interval.end; i++) {
                this._appendItem(i);
            }

            // Generate end edges
            if (interval.end < o.pages && o.edges > 0) {
                if (o.pages - o.edges > interval.end && (o.pages - o.edges - interval.end != 1)) {
                    this.element.append('<li><span>...</span></li>');
                } else if (o.pages - o.edges - interval.end == 1) {
                    this._appendItem(interval.end++);
                }
                var begin = Math.max(o.pages - o.edges, interval.end);
                for (i = begin; i < o.pages; i++) {
                    this._appendItem(i);
                }
            }

            // Generate Next link (unless option is set for at front)
            if (o.nextText && !o.nextAtFront) {
                this._appendItem(o.currentPage + 1, {text: o.nextText, classes: 'next'});
            }
        },

        _appendItem: function(pageIndex, opts) {

            var $this = this, item, link, options;

            pageIndex = pageIndex < 0 ? 0 : (pageIndex < this.pages ? pageIndex : this.pages - 1);

            var options = {
                text: pageIndex + 1
            };

            options = $.extend(options, opts || {});

            if (pageIndex == this.currentPage) {
                item = '<li class="uk-active"><span>' + (options.text) + '</span></li>';
            } else {

                link = $('<a href="#page-' + (pageIndex + 1) + '">' + options.text + '</a>').on('click', function(e){
                    e.preventDefault();
                    $this.selectPage(pageIndex);
                    $this.options.onSelectPage.apply($this, [pageIndex]);
                    $this.element.trigger('uk-select-page', [pageIndex, $this]);
                });

                item = $('<li></li>').append(link);
            }

            this.element.append(item);
        }
    });

    Pagination.defaults = {
        items          : 100,
        itemsOnPage    : 1,
        pages          : 0,
        displayedPages : 10,
        edges          : 3,
        currentPage    : 1,
        onSelectPage   : function() {}
    };

    UI["pagination"] = Pagination;

    // init code
    $(document).on("uk-domready", function(e) {

        $("[data-uk-pagination]").each(function(){
            var ele = $(this);

            if (!ele.data("pagination")) {
                var obj = new Pagination(ele, UI.Utils.options(ele.attr("data-uk-pagination")));
            }
        });
    });

    return Pagination;
});