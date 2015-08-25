(function($){

    $(function(){

        // fit footer
        (function(footer, main, meta, fn){

            if (!footer.length || !main.length) return;

            fn = function() {

                meta = main.css('min-height','')[0].getBoundingClientRect();

                if (meta.height < window.innerHeight) {
                    main.css('min-height', (window.innerHeight - meta.top - footer[0].getBoundingClientRect().height)+'px');
                }

                return fn;
            };

            UIkit.$win.on('load resize', fn());

        })($('#tm-footer'), $('#tm-main'));

    });

})(jQuery);
