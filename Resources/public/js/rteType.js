(function ($) {

    $(document).on('ready update', function (e) {
        $(e.target).find('textarea.hn-layout-rte:not(.processed)').each(function () {

            var $textarea = $(this);
            var options = $textarea.data('tinymce-options');
            $textarea.addClass('processed');

            var id = $textarea.prop('id');
            options = $.extend(options, {
                selector: '#' + id
            });

            tinymce.baseURL = '/bower_components/tinymce';
            tinymce.init($.extend(options, {
                setup: function (ed) {

                    // transfer events to/from jquery
                    ed.on('change', function () {
                        $textarea.val(ed.getContent()).trigger('change');
                    });
                    $textarea.on('change', function () {
                        var value = $textarea.val();
                        console.log('textarea changed', value);
                        ed.setContent(value);
                    });
                }
            }));

        });
    });

})(jQuery);