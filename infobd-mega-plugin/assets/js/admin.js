/**
 * Infobd Mega — Admin JS
 */
(function ($) {
    'use strict';
    $(function () {
        // Smooth scroll on tab change
        $('.infobd-tabs a').on('click', function () {
            $('html,body').animate({ scrollTop: 0 }, 250);
        });

        // Bulk toggle helpers
        $('<button type="button" class="button" id="infobd-toggle-all" style="margin-right:8px;">Toggle all</button>')
            .insertBefore('.infobd-save');
        $(document).on('click', '#infobd-toggle-all', function (e) {
            e.preventDefault();
            var $boxes = $('.infobd-fields input[type="checkbox"]');
            var allOn = $boxes.length === $boxes.filter(':checked').length;
            $boxes.prop('checked', !allOn);
        });
    });
})(jQuery);
