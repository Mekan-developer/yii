!(function (a) {
    "use strict";

    // Fullscreen >>>
    function exitFullscreen() {
        document.webkitIsFullScreen || document.mozFullScreen || document.msFullscreenElement || (console.log("pressed"), a("body").removeClass("fullscreen-enable"));
    }
    a('[data-bs-toggle="fullscreen"]').on("click", function (e) {
        e.preventDefault(),
            a("body").toggleClass("fullscreen-enable"),
            document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement
                ? document.cancelFullScreen
                ? document.cancelFullScreen()
                : document.mozCancelFullScreen
                    ? document.mozCancelFullScreen()
                    : document.webkitCancelFullScreen && document.webkitCancelFullScreen()
                : document.documentElement.requestFullscreen
                ? document.documentElement.requestFullscreen()
                : document.documentElement.mozRequestFullScreen
                    ? document.documentElement.mozRequestFullScreen()
                    : document.documentElement.webkitRequestFullscreen && document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    });
    document.addEventListener("fullscreenchange", exitFullscreen);
    document.addEventListener("webkitfullscreenchange", exitFullscreen);
    document.addEventListener("mozfullscreenchange", exitFullscreen);
    // Fullscreen <<<

})(jQuery);
