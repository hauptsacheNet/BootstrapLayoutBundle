(function ($) {
    "use strict";

    var $document = $(document);

    $document.on('ready update', function (e) {
        var $target = $(e.target);

        $target.find('[data-toggle="tooltip"]').tooltip();
        $target.find('[data-toggle="popover"]').popover();
    });

})(jQuery);