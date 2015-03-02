/**
 * Created by marco on 01.12.14.
 */

(function ($) {
    "use strict";

    var $document = $(document);

    $document.on('dp.change.schedule.entry dp.show.schedule.entry', '.date-range-type', function () {
        var $container = $(this);

        var $startDateInput = $container.find('input[type=text]:first');
        var $endDateInput = $container.find('input[type=text]:last');
        var $startDate = $startDateInput.closest('.js-datepicker');
        var $endDate = $endDateInput.closest('.js-datepicker');

        var startDatePicker = $startDate.data('DateTimePicker');
        var endDatePicker = $endDate.data('DateTimePicker');

        if (typeof startDatePicker !== 'object' || typeof endDatePicker !== 'object') {
            console.error("datepicker range could not be set", $startDate, $endDate);
            return;
        }

        if ($startDateInput.val() == '' || $endDateInput.val() == '') {
            startDatePicker.setMaxDate($startDate.data('data-max-date'));
            endDatePicker.setMinDate($endDate.data('data-min-date'));
        } else {
            startDatePicker.setMaxDate(endDatePicker.getDate());
            endDatePicker.setMinDate(startDatePicker.getDate());
        }

    });

})(jQuery);