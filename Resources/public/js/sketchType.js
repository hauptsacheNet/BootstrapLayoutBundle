/**
 * Created by marco on 26.11.14.
 */

(function ($) {

    var $document = $(document);

    $document.on('ready update', function (e) {
        var $target = $(e.target);

        $target.find('.sketch-type:not(.initialized):not(.read-only)').each(function () {
            var $sketch = $(this).addClass('initialized');
            var $canvas = $sketch.find('canvas');
            var $input = $sketch.find('input');
            var $preview = $sketch.find('.preview');
            var previewImage = new Image;

            if ($canvas.length === 0) {
                console.error("canvas not found", $sketch);
                return;
            }

            if ($input.length === 0) {
                console.error("input not found", $sketch);
                return;
            }

            // prepare preview image
            var val = $input.val();
            if (val) {
                previewImage.src = val;
                $preview.empty().append(previewImage);
            }

            // prepare canvas
            var canvas = $canvas[0];
            var ctx = canvas.getContext('2d');

            // init sketch
            $canvas.sketch($canvas.data());
            console.log('initialized sketch', $canvas);

            var resetCanvas = function () {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                $canvas.data('sketch').actions = [];
                $canvas.data('sketch').action = [];
            };

            var saveImage = function () {
                ctx.globalCompositeOperation = "destination-over";

                // save old sketch too
                if ($canvas.css('background-image') !== 'none') {
                    ctx.drawImage(previewImage, 0, 0, canvas.width, canvas.height);
                }

                // make transparent white for jpeg
                ctx.fillStyle = "#ffffff";
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                var dataUrl = canvas.toDataURL('image/jpeg', $canvas.data('quality'));
                if (previewImage !== null) {
                    previewImage.src = dataUrl;
                    $preview.empty().append(previewImage);
                }
                $input.val(dataUrl).trigger('change');
            };

            $sketch.on('click', '[data-sketch]', function () {
                var action = $(this).data('sketch');
                switch (action) {

                    case 'open':
                    case 'reset':
                        resetCanvas();
                        if ($input.val()) {
                            $canvas.css({backgroundImage: 'url(' + $input.val() + ')'});
                        } else {
                            $canvas.css({backgroundImage: 'none'});
                        }
                        break;

                    case 'clear':
                    case 'close':
                    case 'dismiss':
                        resetCanvas();
                        $canvas.css({backgroundImage: 'none'});
                        break;

                    case 'save':
                        saveImage();
                        break;

                    default:
                        console.warn("undefined sketch action", action);
                }
            });
        })
    });

})(jQuery);