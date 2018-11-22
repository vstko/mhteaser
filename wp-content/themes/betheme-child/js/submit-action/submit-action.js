(function ($) {
    "use strict";
    $('#mc-embedded-subscribe-form').on('submit', function(e) {
            e.preventDefault();
            var data = $("#mc-embedded-subscribe-form :input").serializeArray();
            console.log(data);
        });

})(jQuery);