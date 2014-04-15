(function($, global){


    var RowSelect = function(container, options){

        var $this = this;

        this.container    = $(container);
        this.options      = $.extend({}, RowSelect.defaults, options);
        this.lastselected = null;

        this.container.on('click', this.options.rows, function(e){

            var target = $(e.target), row = $(this), select;

            if(!target.is('a, select, input, [data-action], .js-ignore-select') && !target.closest('[data-action]').length) {

                if (e.shiftKey && window.getSelection) {
                    window.getSelection()[window.getSelection().empty ? 'empty':'removeAllRanges']();
                }

                select = row.find($this.options.checkboxes+':first');

                if (select.length) {

                    select.prop('checked', !select.prop('checked'));

                    // shift select
                    if (e.shiftKey && $this.lastselected) {

                        var start = Math.min($this.rows.index(row), $this.rows.index($this.lastselected)), end = Math.max($this.rows.index(row), $this.rows.index($this.lastselected));

                        for(i = start; i <= end; i++) {
                            $this.rows.eq(i).find($this.options.checkboxes+':first').prop('checked', true);
                        }
                    }

                    if (!e.shiftKey && select.prop('checked')) {
                        $this.lastselected = row;
                    } else {
                        $this.lastselected = false;
                    }

                    $this.handleSelected();
                }
            }
        }).on('click', this.options.checkboxes, function() {
            $this.handleSelected();
        });

        this.fetchRows();
    };


    $.extend(RowSelect.prototype, {

        fetchRows: function(){
            this.rows = this.container.find(this.options.rows);
            this.handleSelected();
        },

        handleSelected: function() {

            this.rows.removeClass(this.options.selectclass);

            var selected = this.container.find(this.options.checkboxes+':checked'),
                rows     = selected.closest(this.options.rows).addClass(this.options.selectclass);

            if (!rows.length) {
                lastselected = false;
            }

            this.container.trigger('selected-rows', [rows]);
        }
    });

    RowSelect.defaults = {
        'checkboxes' : '.js-select',
        'rows'       : 'tbody tr',
        'selectclass': 'pk-table-selected'
    };


    // AMD support
    if (typeof define === 'function' && define.amd) {
        define(function () { return RowSelect; });
    } else {
        global.RowSelect = RowSelect;
    }

})(jQuery, this);