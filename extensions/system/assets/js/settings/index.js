require(['jquery', 'uikit!notify', 'domReady!'], function($, notify) {

   $('#clearCache').on('click', function(e) {
       e.preventDefault();

       modal = new $.UIkit.modal.Modal('#modal-clearcache');

       modal.element.find('form').on('submit', function(e) {
           e.preventDefault();

           $.post($(this).attr('action'), $(this).serialize(), function(data) {
               notify.notify(data.message);
           }).fail(function() {
               notify.notify('Clearing cache failed.');
           }).always(function() {
               modal.hide();
           });
       });

       modal.show();
   });

});