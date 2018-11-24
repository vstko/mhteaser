
if (window.innerWidth > 1024) {
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
        updateHash: false,
        standardScrollElements: "",
        setHeights: false,
        overflowScroll: true,
        touchScroll:true,
        before: function (index, sections) {},
        after: function () { },
        afterResize: function () { },
        afterRender: function () { },
        offsets: function () { }
    };
    $.scrollify(scrollifyOptions)
})(jQuery);
}

console.log(window.innerWidth);