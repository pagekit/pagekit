require('./components/image/image.js');
require('./components/link/link.js');
require('./components/video/video.js');
require('./components/url.js');
require('./components/util.js');

var $ = require('jquery');
var UIkit = require('uikit');

$(function () {

    $(document).on('htmleditor-save', function (e, editor) {
        if (editor.element[0].form) {
            $(editor.element[0].form).submit();
        }
    });

    $('textarea[data-editor]').each(function () {

        var options = $(this).data();

        options.markdown = ('markdown' in options) && (options.markdown === '' || options.markdown);
        UIkit.htmleditor(this, $.extend({}, { marked: marked, CodeMirror: CodeMirror }, options));
    });

});
