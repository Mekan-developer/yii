$(document).ready(function() {
    fancyBox_setup("");
});

function fancyBox_setup(jQsel)
{
    $(jQsel + ".fancybox a").fancybox({
        prevEffect		: 'none', // 'elastic', 'fade'
        nextEffect		: 'none',
        closeBtn		: false,
        helpers : {
            title : {
                type : 'outside' // 'float', 'inside', 'outside', 'over'
            },
            media : {},
            buttons	: {}
        }
    });

    $(jQsel + ".fancybox-button a").fancybox({
        prevEffect		: 'none', // 'elastic', 'fade'
        nextEffect		: 'none',
        closeBtn		: false,
        helpers		: {
            title	: { type : 'over' },
            buttons	: {}
        }
    });

    $(jQsel + ".fancybox-thumb a").fancybox({
        prevEffect	: 'none',
        nextEffect	: 'none',
        helpers	: {
            title	: {
                type: 'over'
            },
            thumbs	: {
                width	: 50,
                height	: 50
            }
        }
    });

    $(jQsel + ".fancybox-media a").fancybox({
        openEffect  : 'none',
        closeEffect : 'none',
        helpers : {
            title	: {
                type: 'over'
            },
            media : {}
        }
    });
}