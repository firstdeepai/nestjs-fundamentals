$(document).ready(function() {
    $('.menu').click(function() {
        $('#headlist').slideToggle(300); 

        let currentIcon = $('#menu-icon').attr('src');
        
        if (currentIcon.includes('menu.png')) {
            $('#menu-icon').attr('src', './images/close.png');
        } else {
            $('#menu-icon').attr('src', './images/menu.png');
        }
    });

    $(window).resize(function() {
        if ($(window).width() > 768) {
            $('#headlist').hide();
            $('#menu-icon').attr('src', './images/menu.png');
        }
    });
});