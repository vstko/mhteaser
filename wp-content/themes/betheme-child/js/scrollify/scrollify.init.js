(function ($) {
    "use strict";
    var timeout,
        delay = 250,
        scrollifyOptions = {
        section : ".s-scrollify",
        sectionName : "section-name",
        interstitialSection : "",
        easing: "easeOutExpo",
        scrollSpeed: 1100,
        offset : 0,
        scrollbars: true,
        updateHash: false,
        standardScrollElements: "",
        setHeights: false,
        overflowScroll: true,
        touchScroll:true,
        before: function (index, sections) {},
        after: function () { },
        afterResize: function () {
            window.innerWidth < 1024 ? $.scrollify.disable() : $.scrollify.enable()
        },
        afterRender: function () { },
        offsets: function () { }
    };

    if (window.innerWidth > 1024) {
        $.scrollify(scrollifyOptions);
    }

    $(window).on('resize', function() {
        clearTimeout(timeout);

        timeout = setTimeout(function() {
            if (window.innerWidth > 1025){
            $.scrollify(scrollifyOptions);
            }
        }, delay);

    });
})(jQuery);
