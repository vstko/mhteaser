(function () {
    var blur = {
        cssClasses: {
            imgBlurred: ".blur_img"
        },
        blurImg: function (imgs) {
            window.addEventListener('scroll', function() {
                var screenHeight = window.innerHeight;
                imgs.forEach(function(el) {
                    var percentage;
                    var opacityVal;
                    var pxMid = (el.getBoundingClientRect().top - (el.clientHeight/2));
                    if(el.clientHeight > 600){
                        pxMid += 250
                    }
                    percentage = ((( screenHeight - pxMid ) / screenHeight) * 100)/2 ;
                    opacityVal = pxMid / 200;
                    if(percentage < 38) {
                        el.style.opacity =  1;
                    }
                    else if(percentage > 38 && percentage < 50) {
                        el.style.opacity =  opacityVal;
                    }else if(percentage > 50) {
                        el.style.opacity =  -opacityVal;
                    }
                });
            });
        },
        init: function () {
            var images = document.querySelectorAll(this.cssClasses.imgBlurred);
            if(images.length > 0){
                this.blurImg(images);
            }
        }
    };
    blur.init();
})();
