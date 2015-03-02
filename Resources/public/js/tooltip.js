(function ($) {
    "use strict";

    var $document = $(document);

    $document.on('ready update', function (e) {
        var $target = $(e.target);

        $target.find('[data-toggle="tooltip"]').tooltip();
    });

})(jQuery);