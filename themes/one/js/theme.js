(function($){

    $(function(){

        // fit footer
        (function(footer, meta, fn){

            if (!footer.length) return;

            fn = function() {

                meta = footer.css('min-height','')[0].getBoundingClientRect();

                if (meta.top < window.innerHeight) {
                    footer.css('min-height', (window.innerHeight - meta.top)+'px');
                }

                return fn;
            };

            UIkit.$win.on('load resize', fn());

        })($('#tm-footer'));

    });

})(jQuery);
