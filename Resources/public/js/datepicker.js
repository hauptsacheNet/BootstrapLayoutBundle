/**
 * Created by marco on 09.10.14.
 */

(function ($) {

    if (!$.isFunction($.fn.datepicker)) {
        window.console && console.warn("there is no $().datepicker");
        return;
    }

    var $document = $(document);

    $document.on('ready update', function (e) {
        var $target = $(e.target);

        $target.find('.js-datepicker').each(function () {
            var $inputGroup = $(this).closest('.input-group');
            var data = $inputGroup.find('input').data();
            $inputGroup.datetimepicker(data);
        });
    });

})(jQuery);