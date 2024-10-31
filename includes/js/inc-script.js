(function ($) {
    'use strict';
    $(function () {
        disableDownload()
    });
    function disableDownload() {
        $('img').on('contextmenu', function (e) {
            return false;
        });

        $('video').on('contextmenu', function (e) {
            return false;
        });

        $('video').prop("controls", true);
        $('video').attr("controlsList","nodownload");

        $('audio').on('contextmenu', function (e) {
            return false;
        });
        $('audio').prop("controls", true);
        $('audio').attr("controlsList","nodownload");
    }
})(jQuery);



