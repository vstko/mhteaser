(function ($) {
    "use strict";
    var scrollifyOptions = {
        section : ".s-scrollify",
        sectionName : "section-name",
        interstitialSection : "",
        easing: "easeOutExpo",
        scrollSpeed: 1100,
        offset : 0,
        scrollbars: true,
        standardScrollElements: "",
        setHeights: false,
        overflowScroll: true,
        updateHash: true,
        touchScroll:true,
        before: function (index, sections) {},
        after: function () { },
        afterResize: function () { },
        afterRender: function () { },
        offsets: function () { }
    }
    $.scrollify(scrollifyOptions)
})(jQuery);