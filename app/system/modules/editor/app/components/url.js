/**
 * URL resolver plugin
 */

module.exports = {

    plugin: true,

    created: function () {

        var editor = this.editor;

        if (!editor || !editor.htmleditor) {
            return;
        }

        editor.element.on('renderLate', function () {

            editor.replaceInPreview(/src=["'](.+?)["']/gi, function (data) {

                var replacement = data.matches[0];

                if (!data.matches[1].match(/^(\/|http:|https:|ftp:)/i)) {
                    replacement = replacement.replace(data.matches[1], Vue.url(data.matches[1], true));
                }

                return replacement;
            });

        });

    }

};
