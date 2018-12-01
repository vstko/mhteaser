(function () {
    "use strict";
    var images = document.querySelectorAll(".blur_img");
    var blur = {
        config: {
            breakpoint: 1025
        },
        blurImg: function (imgs) {
            var screenHeight = window.innerHeight;
                imgs.forEach(function(el) {
                    var percentage;
                    var opacityVal;
                    var pxMid = el.getBoundingClientRect().top + (el.clientHeight/2) - (screenHeight/2);
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
       init: function (images) {
            if(window.innerWidth < this.config.breakpoint) {
                if(images.length > 0){
                    this.blurImg(images);
                }
            }
        }
    };

    window.addEventListener('scroll', function scrolling() {
        blur.init(images);
        window.onresize = function() {
            if(window.innerWidth > 1024) {
                window.removeEventListener('scroll', scrolling);
                for(var i = 0; i < images.length; i++){
                    images[i].style.opacity =  1;
                }
            }else {
                window.addEventListener('scroll', scrolling);
            }
        }
    });
})();
