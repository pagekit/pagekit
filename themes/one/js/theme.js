(function($){

    $(function(){

        // fit footer
        (function(main, meta, fn){

            if (!main.length) return;

            fn = function() {

                main.css('min-height','');

                meta = document.body.getBoundingClientRect();

                if (meta.height < window.innerHeight) {
                    main.css('min-height', (main.outerHeight() + (window.innerHeight - meta.height))+'px');
                }

                return fn;
            };

            UIkit.$win.on('load resize', fn());

        })($('#tm-main'));

    });

})(jQuery);
