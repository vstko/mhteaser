/*
(function () {
    "use strict";
    var images = document.querySelectorAll(".blur_img"),
        timeout,
        delay = 250,
        blur = {
        config: {
            breakpoint: 1025
        },

        blurImg: function (imgs) {
            var screenHeight = window.innerHeight;
                imgs.forEach(function(el) {
                    var percentage,
                        opacityVal,
                        pxMid = el.getBoundingClientRect().top + (el.clientHeight/2) - (screenHeight/2);

                    percentage = ((( screenHeight - pxMid ) / screenHeight) * 100)/2;
                    opacityVal = pxMid / 200;

                    if(percentage < 38) {
                        el.style.opacity =  1;
                    }
                    else if (percentage > 38 && percentage < 50) {
                        el.style.opacity =  opacityVal;
                    }else if (percentage > 50) {
                        el.style.opacity =  -opacityVal;
                    }
                });
       },

       init: function(images) {
            if(window.innerWidth < this.config.breakpoint) {
                if(images.length > 0){
                    this.blurImg(images);
                }
            }
        }
    };

    function initBlur() {
        blur.init(images);
    }

    function toggleBlur() {
        window.innerWidth > 1024 ? window.removeEventListener('scroll', initBlur) : window.addEventListener('scroll', initBlur)
    }

    window.addEventListener('scroll', initBlur);

    window.addEventListener('resize', function() {
        clearTimeout(timeout);
        timeout = setTimeout(toggleBlur, delay);
    });
})();
*/
