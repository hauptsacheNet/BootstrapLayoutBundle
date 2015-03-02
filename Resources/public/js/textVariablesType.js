(function ($) {

    var $document = $(document);

    function getTextsForInput($input) {
        var $container = $input.closest('.text-variables-type');

        var variable = $input.data('variable-field');
        if (variable === null || variable === undefined) {
            console.warn('input has no data-variable-field', $input, $container);
            return $();
        }

        return $container.find('[data-variable="' + variable + '"]');
    }

    $document.on('input change blur', '.text-variables-type :input[data-variable-field]', function () {
        var $input = $(this);
        var $targets = getTextsForInput($input);

        var val = $input.val();

        if (val) {
            $targets.text(val);
            $targets.removeClass('initial');
        } else {
            $targets.text($input.data('variable-field'));
            $targets.addClass('initial');
        }

    });

    $document.on('mouseenter mouseleave focusin focusout', '.text-variables-type :input[data-variable-field]', function (e) {
        var $input = $(this);
        var $targets = getTextsForInput($input);

        var showHover = $input.is(':focus')
            || e.type === 'mouseenter'
            || e.type === 'focusin';
        $targets.toggleClass('hover', showHover);
    });

    $document.on('click', '.text-variables-type [data-variable]', function () {
        var $span = $(this);
        var $container = $span.closest('.text-variables-type');

        $container.find('[data-variable-field="' + $span.data('variable') + '"]').select();
    });

})(jQuery);