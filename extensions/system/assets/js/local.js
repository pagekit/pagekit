define(['jquery', 'vendor/assets/local/local','jsonsource!local'], function($, local, sources) {

    $.extend(local.meta, sources.local);

    return local;
});