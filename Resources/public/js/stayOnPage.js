(function ($) {
    "use strict";

    var $document = $(document);

    var linkSelectors = [
        'a[href^="' + location.origin + '"]:not([target])',
        'a[href^="/"]:not([target])'
    ];

    $document.on('click', linkSelectors.join(','), function (e) {
        console.log(this);
        location.href = this.href;
        e.preventDefault();
    });

})(jQuery);