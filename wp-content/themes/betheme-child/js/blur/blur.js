(function () {
    var blur = {
        cssClasses: {
            imgBlurred: ".blur_img"
        },
        blurImg: function (imgs) {
            window.addEventListener('scroll', function() {
                var screenHeight = window.innerHeight;
                imgs.forEach(function(el) {
                    var pxMid = el.getBoundingClientRect().top - (el.clientHeight/2);
                    console.log(pxMid);
                    var percentage = (( screenHeight - pxMid ) / screenHeight) * 100 ;
                    var opacityVal = pxMid / 200;
                    if(percentage < 80){
                        el.style.opacity =  1;
                    }
                    else if(percentage > 80 && percentage < 97){
                        el.style.opacity =  opacityVal;
                    }else if(percentage > 97){
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