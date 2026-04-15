/**
 * XtremeSlider — Admin JavaScript
 */
(function ($) {
    'use strict';

    $(document).ready(function () {

        /* ==================================================================
           1. Layout Radio Cards
           ================================================================== */
        $('.xs-radio-card input[type="radio"]').on('change', function () {
            var $cards = $(this).closest('.xs-radio-cards');
            $cards.find('.xs-radio-card').removeClass('active');
            $(this).closest('.xs-radio-card').addClass('active');

            // Show gradient section only for Simple layout
            var val = $(this).val();
            if (val === 'cool') {
                $('#xs-gradient-section').slideDown(200);
            } else {
                $('#xs-gradient-section').slideUp(200);
            }
        });

        /* ==================================================================
           2. Autoplay Toggle
           ================================================================== */
        $('#xs-autoplay-toggle').on('change', function () {
            if ($(this).is(':checked')) {
                $('.xs-autoplay-speed').slideDown(200);
            } else {
                $('.xs-autoplay-speed').slideUp(200);
            }
        });

        /* ==================================================================
           3. Speed Range Display
           ================================================================== */
        $('#xs-speed-range').on('input', function () {
            var seconds = parseInt($(this).val(), 10) / 1000;
            $('#xs-speed-label').text(seconds);
        });

        /* ==================================================================
           4. Media Uploader — Add Slides
           ================================================================== */
        var mediaFrame;

        $('#xs-add-slides').on('click', function (e) {
            e.preventDefault();

            var currentCount = $('#xs-slides-grid .xs-slide-card').length;
            var maxSlides = parseInt(xsAdmin.maxSlides, 10) || 10;

            if (currentCount >= maxSlides) {
                alert('Maximum ' + maxSlides + ' slides allowed.');
                return;
            }

            if (mediaFrame) {
                mediaFrame.open();
                return;
            }

            mediaFrame = wp.media({
                title: 'Select Slider Images',
                button: { text: 'Add to Slider' },
                multiple: true,
                library: { type: 'image' }
            });

            mediaFrame.on('select', function () {
                var attachments = mediaFrame.state().get('selection').toJSON();
                var remaining = maxSlides - $('#xs-slides-grid .xs-slide-card').length;

                $.each(attachments.slice(0, remaining), function (i, att) {
                    var imgUrl = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
                    var fullUrl = att.url;

                    var card = '<div class="xs-slide-card" data-image-id="' + att.id + '">' +
                        '<div class="xs-slide-img"><img src="' + imgUrl + '" alt=""></div>' +
                        '<div class="xs-slide-fields">' +
                        '<input type="hidden" name="slides[image_id][]" value="' + att.id + '">' +
                        '<input type="hidden" name="slides[image_url][]" value="' + fullUrl + '">' +
                        '<input type="text" name="slides[title][]" value="" placeholder="Title (on image)">' +
                        '<input type="text" name="slides[caption][]" value="" placeholder="Caption (below image)">' +
                        '<input type="text" name="slides[description][]" value="" placeholder="Description (optional)">' +
                        '<input type="text" name="slides[link_url][]" value="" placeholder="Link URL (optional)">' +
                        '</div>' +
                        '<button type="button" class="xs-slide-remove" title="Remove slide">&times;</button>' +
                        '</div>';

                    $('#xs-slides-grid').append(card);
                });

                updateSlideCount();
            });

            mediaFrame.open();
        });

        /* ==================================================================
           5. Remove Slide
           ================================================================== */
        $(document).on('click', '.xs-slide-remove', function () {
            $(this).closest('.xs-slide-card').fadeOut(200, function () {
                $(this).remove();
                updateSlideCount();
            });
        });

        /* ==================================================================
           6. Sortable Slides
           ================================================================== */
        if ($.fn.sortable) {
            $('#xs-slides-grid').sortable({
                items: '.xs-slide-card',
                cursor: 'grabbing',
                tolerance: 'pointer',
                placeholder: 'xs-slide-card ui-sortable-placeholder',
                update: function () {
                    updateSlideCount();
                }
            });
        }

        /* ==================================================================
           7. Slide Count Helper
           ================================================================== */
        function updateSlideCount() {
            var count = $('#xs-slides-grid .xs-slide-card').length;
            var max = parseInt(xsAdmin.maxSlides, 10) || 10;
            $('#xs-slide-count').text(count);

            if (count >= max) {
                $('#xs-add-slides').prop('disabled', true);
            } else {
                $('#xs-add-slides').prop('disabled', false);
            }
        }

        /* ==================================================================
           8. Copy Shortcode
           ================================================================== */
        $(document).on('click', '.xs-copy-btn', function () {
            var $btn = $(this);
            var text = $btn.data('copy');

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function () {
                    showCopied($btn);
                });
            } else {
                // Fallback
                var $temp = $('<textarea>');
                $('body').append($temp);
                $temp.val(text).select();
                document.execCommand('copy');
                $temp.remove();
                showCopied($btn);
            }
        });

        function showCopied($btn) {
            var orig = $btn.html();
            $btn.addClass('copied').html('Copied!');
            setTimeout(function () {
                $btn.removeClass('copied').html(orig);
            }, 1500);
        }

        /* ==================================================================
           9. Color Pickers
           ================================================================== */
        if ($.fn.wpColorPicker) {
            $('.xs-color-picker').wpColorPicker({
                change: function () {
                    updateGradientPreview();
                },
                clear: function () {
                    updateGradientPreview();
                }
            });
        }

        function updateGradientPreview() {
            setTimeout(function () {
                var start = $('input[name="gradient_start"]').val() || '#ec38bc';
                var end = $('input[name="gradient_end"]').val() || '#7303c0';
                $('#xs-gradient-preview').css('background', 'linear-gradient(135deg, ' + start + ', ' + end + ')');
            }, 50);
        }

        /* ==================================================================
           10. Save Button Loading Spinner
           ================================================================== */
        $('form').on('submit', function () {
            var $btn = $(this).find('#xs-save-btn');
            if ($btn.length) {
                $btn.addClass('xs-btn-saving');
            }
        });

    });

})(jQuery);
