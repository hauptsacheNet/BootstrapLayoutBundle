/**
 * Created by marco on 04.12.14.
 */

(function ($) {
    "use strict";

    if (!$.isFunction($.cookie)) {
        console.warn('saving tab state requires jquery-cookie');
        return;
    }

    var $document = $(document);

    $document.on('show.bs.tab', '[data-save-name] [data-name]', function (e) {
        var $link = $(this);
        var $container = $link.closest('[data-save-name]');

        var name = $container.data('save-name');
        var value = $link.data('name');
        var duration = $container.data('save-duration') || 7;
        var path = $container.data('save-path') || null;

        $.cookie(name, value, {expires: duration, path: path});
    });

})(jQuery);