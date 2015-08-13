/**
 * Editor Link plugin.
 */

var Picker = Vue.extend(require('./link-picker.vue'));

module.exports = {

    plugin: true,

    created: function () {

        var vm = this, editor = this.editor;

        if (!editor || !editor.htmleditor) {
            return;
        }

        this.links = [];

        editor
            .off('action.link')
            .on('action.link', function (e, editor) {
                vm.openModal(_.find(vm.links, function (link) {
                    return link.inRange(editor.getCursor());
                }));
            })
            .on('render', function () {
                var regexp = editor.getMode() != 'gfm' ? /<a(?:.+?)>(?:[^<]*)<\/a>/gi : /<a(?:.+?)>(?:[^<]*)<\/a>|(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?/gi;
                vm.links = editor.replaceInPreview(regexp, vm.replaceInPreview);
            })
            .on('renderLate', function () {

                while (vm.$children.length) {
                    vm.$children[0].$destroy();
                }

                Vue.nextTick(function() {
                    vm.$compile(editor.preview[0]);
                });

            });

    },

    methods: {

        openModal: function (link) {

            var editor = this.editor, cursor = editor.editor.getCursor();

            if (!link) {
                link = {
                    replace: function (value) {
                        editor.editor.replaceRange(value, cursor);
                    }
                };
            }

            this.$addChild({
                    data: {
                        link: link
                    }
                }, Picker)
                .$mount()
                .$appendTo('body')
                .$on('select', function (link) {

                    link.replace(this.$interpolate(
                        (link.tag || editor.getCursorMode()) == 'html' ?
                            (link.outerHTML ? link.outerHTML : '<a href="{{ link.link }}">{{ link.txt }}</a>')
                            : '[{{ link.txt }}]({{ link.link }})'
                        )
                    );
                });
        },

        replaceInPreview: function (data, index) {

            if (data.matches[0][0] == '<') {

                var anchor = $(data.matches[0]);

                data.link      = anchor.attr('href');
                data.txt       = anchor.html();
                data.class     = anchor.attr('class') || '';

                data.outerHTML = anchor.attr('href', '{{ link.link }}').text('{{ link.txt }}')[0].outerHTML;

            } else {

                if (data.matches[data.matches.length - 1][data.matches[data.matches.length - 2] - 1] == '!') return false;

                data.link    = data.matches[2];
                data.txt     = data.matches[1];
                data.class   = '';

            }

            return '<link-preview index="'+index+'"></link-preview>';
        }

    },

    components: {

        'link-preview': require('./link-preview.vue')

    }

};
