
/**
* Theme:  Zircos - Responsive Bootstrap 4 Admin & Dashboard
* Author: Coderthemes
* File:   Animation
*/

$(function() {
    $('body').on('click', '.demo-animation .btn', function(){
        var animation = $(this).text();
        var cardImg = $(this).closest('.card-box').find('img');
        if (animation === "hinge") {
            animationDuration = 2100;
        }
        else {
            animationDuration = 1200;
        }

        cardImg.removeAttr('class');
        cardImg.addClass('animated '+animation);

        setTimeout(function(){
            cardImg.removeClass(animation);
        }, animationDuration);
    });
});