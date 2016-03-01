module.exports = {

    bind: function () {

        var self = this;

        this.dir       = '';
        this.active    = false;
        this.indicator = $('<i class="uk-icon-justify uk-margin-small-left"></i>');

        $(this.el).addClass('pk-table-order uk-visible-hover-inline').on('click.order', function (){

            self.dir = (self.dir == 'asc') ? 'desc':'asc';
            self.vm.$set(self.expression, [self.arg, self.dir].join(' '));

        }).append(this.indicator);
    },

    update: function (data) {

        var parts = data.split(' '),
            field = parts[0],
            dir   = parts[1] || 'asc';

        this.indicator.removeClass('pk-icon-arrow-up pk-icon-arrow-down');
        $(this.el).removeClass('uk-active');

        if (field == this.arg) {
            this.active = true;
            this.dir    = dir;

            $(this.el).addClass('uk-active');
            this.indicator.removeClass('uk-invisible').addClass(dir == 'asc' ? 'pk-icon-arrow-down':'pk-icon-arrow-up');
        } else {
            this.indicator.addClass('pk-icon-arrow-down uk-invisible');
            this.active = false;
            this.dir    = '';
        }
    },

    unbind: function () {
        $(this.el).removeClass('pk-table-order').off('.order');
        this.indicator.remove();
    }

};
