/* ===================================================================
    Author          : Modina Theme
    Version         : 1.0
* ================================================================= */

(function($) {
    "use strict";

    $(document).ready( function() {

        //>> Mobile Menu Js Start <<//
        $('#mobile-menu').meanmenu({
            meanMenuContainer: '.mobile-menu',
            meanScreenWidth: "1199",
            meanExpand: ['<i class="far fa-plus"></i>'],
        });

        //>> Sidebar Toggle Js Start <<//
        $(".offcanvas__close,.offcanvas__overlay").on("click", function() {
            $(".offcanvas__info").removeClass("info-open");
            $(".offcanvas__overlay").removeClass("overlay-open");
        });
        $(".sidebar__toggle").on("click", function() {
            $(".offcanvas__info").addClass("info-open");
            $(".offcanvas__overlay").addClass("overlay-open");
        });

        //>> Body Overlay Js Start <<//
        $(".body-overlay").on("click", function() {
            $(".offcanvas__area").removeClass("offcanvas-opened");
            $(".df-search-area").removeClass("opened");;
            $(".body-overlay").removeClass("opened");
        });

        //>> Sticky Header Js Start <<//

        $(window).scroll(function() {
            if ($(this).scrollTop() > 250) {
                $("#header-sticky").addClass("sticky");
            } else {
                $("#header-sticky").removeClass("sticky");
            }
        });


        //>> Wow Animation Start <<//
        new WOW().init();

         //>> Video Popup Start <<//
         $(".img-popup").magnificPopup({
            type: "image",
            gallery: {
                enabled: true,
            },
        });

        $('.video-popup').magnificPopup({
            type: 'iframe',
            callbacks: {}
        });


        //>> Wow Animation Start <<//
        new WOW().init();

        //>> Nice Select Start <<//
        $('select').niceSelect();

        $('.odometer').appear(function(e) {
            var odo = $(".odometer");
            odo.each(function() {
                var countNumber = $(this).attr("data-count");
                $(this).html(countNumber);
            });
        });

        //>> Hero-1 Slider Start <<//
        const sliderActive2 = ".hero-slider";
        const sliderInit2 = new Swiper(sliderActive2, {
            loop: true,
            slidesPerView: 1,
            effect: "fade",
            speed: 3000,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".array-prev",
                prevEl: ".array-next",
              },
            pagination: {
                el: ".dot",
                clickable: true,
            },
        });

       function animated_swiper(selector, init) {
           const animated = function animated() {
               $(selector + " [data-animation]").each(function () {
                   let anim = $(this).data("animation");
                   let delay = $(this).data("delay");
                   let duration = $(this).data("duration");
                   $(this)
                       .removeClass("anim" + anim)
                       .addClass(anim + " animated")
                       .css({
                           webkitAnimationDelay: delay,
                           animationDelay: delay,
                           webkitAnimationDuration: duration,
                           animationDuration: duration,
                       })
                       .one("animationend", function () {
                           $(this).removeClass(anim + " animated");
                       });
               });
           };
           animated();
           init.on("slideChange", function () {
               $(sliderActive2 + " [data-animation]").removeClass("animated");
           });
           init.on("slideChange", animated);
       }
       animated_swiper(sliderActive2, sliderInit2);
       //>> Banner Animation <<//
        
        //>> Team Slider Start <<//
        if($('.team-slider').length > 0) {
            const teamSlider = new Swiper(".team-slider", {
                spaceBetween: 0,
                speed: 2000,
                loop: true,

                navigation: {
                    nextEl: ".array-prev",
                    prevEl: ".array-next",
                },
                breakpoints: {
                    1399: {
                        slidesPerView: 5,
                    },
                    1199: {
                        slidesPerView: 4,
                    },
                    991: {
                        slidesPerView: 3,
                    },
                    767: {
                        slidesPerView: 2,
                    },
                    575: {
                        slidesPerView: 2,
                    },
                    475: {
                        slidesPerView: 1,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
            });
        }

        //>> Testimonial Slider Start <<//
        if($('.testimonial-slider').length > 0) {
            const testimonialSlider = new Swiper(".testimonial-slider", {
                spaceBetween: 30,
                speed: 2000,
                loop: true,
                autoplay: {
                    delay: 2000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".dot",
                    clickable: true,
                },
                breakpoints: {
                    1199: {
                        slidesPerView: 2,
                    },
                    991: {
                        slidesPerView: 1,
                    },
                    767: {
                        slidesPerView: 1,
                    },
                    575: {
                        slidesPerView: 1,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
            });
        }

        if($('.testimonial-slider-2').length > 0) {
            const testimonialSlider2 = new Swiper(".testimonial-slider-2", {
                spaceBetween: 30,
                speed: 1500,
                loop: true,
                autoplay: {
                    delay: 1500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".dot",
                    clickable: true,
                },
            });
        }

        if($('.testimonial-slider-3').length > 0) {
            const testimonialSlider3 = new Swiper(".testimonial-slider-3", {
                spaceBetween: 30,
                speed: 2000,
                loop: true,
                autoplay: {
                    delay: 2000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".dot",
                    clickable: true,
                },
                breakpoints: {
                    1199: {
                        slidesPerView: 3,
                    },
                    991: {
                        slidesPerView: 2,
                    },
                    767: {
                        slidesPerView: 1,
                    },
                    575: {
                        slidesPerView: 1,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
            });
        }

        if($('.testimonial-slider-4').length > 0) {
            const testimonialSlider4 = new Swiper(".testimonial-slider-4", {
                spaceBetween: 30,
                speed: 2000,
                loop: true,
                autoplay: {
                    delay: 2000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".dot",
                    clickable: true,
                },
            });
        }

        if($('.testimonial-client-slider').length > 0) {
            const testimonialSlider5Client = new Swiper(".testimonial-client-slider", {
                loop: true,
                spaceBetween: 30,
                speed: 1000,
                watchSlidesProgress: true,
                autoplay: {
                    delay: 2000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".dot2",
                    clickable: true,
                },
                breakpoints: {
                    1199: {
                        slidesPerView: 5,
                    },
                    991: {
                        slidesPerView: 3,
                    },
                    767: {
                        slidesPerView: 2,
                    },
                    575: {
                        slidesPerView: 2,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
    
            });
        }

        if($('.testimonial-slider-5').length > 0) {
            const testimonialSlider5 = new Swiper(".testimonial-slider-5", {
                loop: true,
                spaceBetween: 30,
                speed: 1000,
                autoplay: {
                    delay: 2000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".dot2",
                    clickable: true,
                },
                breakpoints: {
                    991: {
                        slidesPerView: 2,
                    },
                    767: {
                        slidesPerView: 1,
                    },
                    575: {
                        slidesPerView: 1,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
    
            });
        }

        //>> Brand Slider Start <<//
        if($('.brand-slider').length > 0) {
            const brandSlider = new Swiper(".brand-slider", {
                spaceBetween: 30,
                speed: 1500,
                loop: true,
                autoplay: {
                    delay: 1500,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    1399: {
                        slidesPerView: 6,
                    },
                    1199: {
                        slidesPerView: 5,
                    },
                    991: {
                        slidesPerView: 5,
                    },
                    767: {
                        slidesPerView: 4,
                    },
                    575: {
                        slidesPerView: 3,
                    },
                    400: {
                        slidesPerView: 2,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
            });
        }

        //>> Live Courses Slider Start <<//
        if($('.live-courses-slider').length > 0) {
            const liveCoursesSlider = new Swiper(".live-courses-slider", {
                spaceBetween: 30,
                speed: 1500,
                loop: true,
                autoplay: {
                    delay: 1500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".dot",
                    clickable: true,
                },
                breakpoints: {
                    1199: {
                        slidesPerView: 3,
                    },
                    991: {
                        slidesPerView: 2,
                    },
                    767: {
                        slidesPerView: 2,
                    },
                    575: {
                        slidesPerView: 1,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
            });
        }

        //>> Instagram Slider Start <<//
        if($('.instagram-slider').length > 0) {
            const instagramSlider = new Swiper(".instagram-slider", {
                spaceBetween: 0,
                speed: 1500,
                loop: true,
                breakpoints: {
                    1199: {
                        slidesPerView: 5,
                    },
                    991: {
                        slidesPerView: 4,
                    },
                    767: {
                        slidesPerView: 3,
                    },
                    575: {
                        slidesPerView: 2,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
            });
        }

        if($('.instagram-slider-2').length > 0) {
            const instagramSlider2 = new Swiper(".instagram-slider-2", {
                spaceBetween: 10,
                speed: 1500,
                loop: true,
                breakpoints: {
                    1199: {
                        slidesPerView: 5,
                    },
                    991: {
                        slidesPerView: 4,
                    },
                    767: {
                        slidesPerView: 3,
                    },
                    575: {
                        slidesPerView: 2,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
            });
        }

        //>> Courses Slider Start <<//
        if($('.courses-slider').length > 0) {
            const coursesSlider = new Swiper(".courses-slider", {
                spaceBetween: 30,
                speed: 1500,
                loop: true,
                autoplay: {
                    delay: 1500,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    1199: {
                        slidesPerView: 3,
                    },
                    991: {
                        slidesPerView: 2,
                    },
                    767: {
                        slidesPerView: 2,
                    },
                    575: {
                        slidesPerView: 1,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
            });
        }

        if($('.courses-slider-2').length > 0) {
            const coursesSlider2 = new Swiper(".courses-slider-2", {
                spaceBetween: 30,
                speed: 1500,
                loop: true,
                autoplay: {
                    delay: 1500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".dot",
                    clickable: true,
                },
                breakpoints: {
                    1199: {
                        slidesPerView: 4,
                    },
                    991: {
                        slidesPerView: 3,
                    },
                    767: {
                        slidesPerView: 2,
                    },
                    575: {
                        slidesPerView: 1,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
            });
        }

        if($('.related-product-slider').length > 0) {
            const relatedProductSlider = new Swiper(".related-product-slider", {
                spaceBetween: 30,
                speed: 1500,
                loop: true,
                autoplay: {
                    delay: 1500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".dot",
                    clickable: true,
                },
                breakpoints: {
                    1199: {
                        slidesPerView: 4,
                    },
                    991: {
                        slidesPerView: 3,
                    },
                    767: {
                        slidesPerView: 2,
                    },
                    575: {
                        slidesPerView: 1,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
            });
        }

         //>> CountDown Start <<//
         let targetDate = new Date("2024-12-12 00:00:00").getTime();
         const countdownInterval = setInterval(function () {
             let currentDate = new Date().getTime();
             let remainingTime = targetDate - currentDate;
 
             if (remainingTime <= 0) {
                 clearInterval(countdownInterval);
                 // Display a message or perform any action when the countdown timer reaches zero
                 $("#countdown-container").text("Countdown has ended!");
             } else {
                 let days = Math.floor(remainingTime / (1000 * 60 * 60 * 24));
                 let hours = Math.floor(
                     (remainingTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
                 );
                 let minutes = Math.floor(
                     (remainingTime % (1000 * 60 * 60)) / (1000 * 60)
                 );
                 let seconds = Math.floor((remainingTime % (1000 * 60)) / 1000);
 
                 // Pad single-digit values with leading zeros
                 $("#day").text(days.toString().padStart(2, "0"));
                 $("#hour").text(hours.toString().padStart(2, "0"));
                 $("#min").text(minutes.toString().padStart(2, "0"));
                 $("#sec").text(seconds.toString().padStart(2, "0"));
             }
         }, 1000);

        //>> Team Hover Image Show Slider Start <<//
        const teamListItems = document.querySelectorAll(".team-list-items");

        function followImageCursor(event, teamListItems) {
            const contentBox = teamListItems.getBoundingClientRect();
            const dx = event.clientX - contentBox.x;
            const dy = event.clientY - contentBox.y;
            teamListItems.children[2].style.transform = `translate(${dx}px, ${dy}px) rotate(0)`;
        }
        
        teamListItems.forEach((item, i) => {
            item.addEventListener("mousemove", (event) => {
                setInterval(followImageCursor(event, item), 1000);
            });
        });


    //Cart Increment Decriemnt

        // quntity increment and decrement
        const quantityIncrement = document.querySelectorAll(".quantityIncrement");
        const quantityDecrement = document.querySelectorAll(".quantityDecrement");
        if (quantityIncrement && quantityDecrement) {
            quantityIncrement.forEach((increment) => {
                increment.addEventListener("click", function () {
                    const value = parseInt(
                        increment.parentElement.querySelector("input").value
                    );
                    increment.parentElement.querySelector("input").value = value + 1;
                });
            });

            quantityDecrement.forEach((decrement) => {
                decrement.addEventListener("click", function () {
                    const value = parseInt(
                        decrement.parentElement.querySelector("input").value
                    );
                    if (value > 1) {
                        decrement.parentElement.querySelector("input").value = value - 1;
                    }
                });
            });
        }

        //>> PaymentMethod Js Start <<//
        const paymentMethod = $("input[name='pay-method']:checked").val();
        $(".payment").html(paymentMethod);
        $(".checkout-radio-single").on("click", function() {
            let paymentMethod = $("input[name='pay-method']:checked").val();
            $(".payment").html(paymentMethod);
        });

        //Quantity 
        const inputs = document.querySelectorAll('#qty, #qty2, #qty3');
        const btnminus = document.querySelectorAll('.qtyminus');
        const btnplus = document.querySelectorAll('.qtyplus');

        if (inputs.length > 0 && btnminus.length > 0 && btnplus.length > 0) {

            inputs.forEach(function(input, index) {
                const min = Number(input.getAttribute('min'));
                const max = Number(input.getAttribute('max'));
                const step = Number(input.getAttribute('step'));

                function qtyminus(e) {
                    const current = Number(input.value);
                    const newval = (current - step);
                    if (newval < min) {
                        newval = min;
                    } else if (newval > max) {
                        newval = max;
                    }
                    input.value = Number(newval);
                    e.preventDefault();
                }

                function qtyplus(e) {
                    const current = Number(input.value);
                    const newval = (current + step);
                    if (newval > max) newval = max;
                    input.value = Number(newval);
                    e.preventDefault();
                }

                btnminus[index].addEventListener('click', qtyminus);
                btnplus[index].addEventListener('click', qtyplus);
            });
        }

        // Search bar
        $(".search-toggle").on('click', function(){
            $(".header-search-bar").addClass("search-open");
            $(".offcanvas-overlay").addClass("offcanvas-overlay-open");
            });

        $(".search-close,.offcanvas-overlay").on('click', function(){
            $(".header-search-bar").removeClass("search-open");
            $(".offcanvas-overlay").removeClass("offcanvas-overlay-open");
        });

        //>> Active Js Start <<//
        $(".team-list-items, .top-category-items").hover(
            function() {
                $(".team-list-items, .top-category-items").removeClass("active");
                $(this).addClass("active");
            }
        );
        

        //>> Custom Accordion Tabs <<//
		$(".accordion-single .header-area").on("click", function () {
			if ($(this).closest(".accordion-single").hasClass("active")) {
				$(this).closest(".accordion-single").removeClass("active");
				$(this).next(".content-area").slideUp();
			} else {
				$(".accordion-single").removeClass("active");
				$(this).closest(".accordion-single").addClass("active");
				$(".content-area").not($(this).next(".content-area")).slideUp();
				$(this).next(".content-area").slideToggle();
			}
		});

         //>> Back To Top Slider Start <<//

         $(window).on('scroll', function() {
            if ($(this).scrollTop() > 20) {
                $("#back-top").addClass("show");
            } else {
                $("#back-top").removeClass("show");
            }
        });
        
        $(document).on('click', '#back-top', function() {
            $('html, body').animate({ scrollTop: 0 }, 800);
            return false;
        });

        // circle-progress
        $(".circle-bar").loading();

    }); // End Document Ready Function

    $.fn.loading = function() {
        const DEFAULTS = {
            backgroundColor: '#b3cef6',
            progressColor: '#4b86db',
            percent: 75,
            duration: 2000
        };

        $(this).each(function() {
            const $target = $(this);

            const opts = {
                backgroundColor: $target.data('color') ? $target.data('color').split(',')[0] : DEFAULTS.backgroundColor,
                progressColor: $target.data('color') ? $target.data('color').split(',')[1] : DEFAULTS.progressColor,
                percent: $target.data('percent') ? $target.data('percent') : DEFAULTS.percent,
                duration: $target.data('duration') ? $target.data('duration') : DEFAULTS.duration
            };
            // console.log(opts);

            $target.append('<div class="background"></div><div class="rotate"></div><div class="left"></div><div class="right"></div><div class=""><span>' + opts.percent + '%</span></div>');

            $target.find('.background').css('background-color', opts.backgroundColor);
            $target.find('.left').css('background-color', opts.backgroundColor);
            $target.find('.rotate').css('background-color', opts.progressColor);
            $target.find('.right').css('background-color', opts.progressColor);

            const $rotate = $target.find('.rotate');
            setTimeout(function() {
                $rotate.css({
                    'transition': 'transform ' + opts.duration + 'ms linear',
                    'transform': 'rotate(' + opts.percent * 3.6 + 'deg)'
                });
            }, 1);

            if (opts.percent > 50) {
                let animationRight = 'toggle ' + (opts.duration / opts.percent * 50) + 'ms step-end';
                let animationLeft = 'toggle ' + (opts.duration / opts.percent * 50) + 'ms step-start';
                $target.find('.right').css({
                    animation: animationRight,
                    opacity: 1
                });
                $target.find('.left').css({
                    animation: animationLeft,
                    opacity: 0
                });
            }
        });
    }

    //Price Range Slideer
    document.addEventListener("DOMContentLoaded", function () {
        const minSlider = document.getElementById("min-slider");
        const maxSlider = document.getElementById("max-slider");
        const amount = document.getElementById("amount");

        function updateAmount() {
            const minValue = parseInt(minSlider.value, 10);
            const maxValue = parseInt(maxSlider.value, 10);

            // Ensure the minimum value is always lower than the maximum value
            if (minValue > maxValue) {
                minSlider.value = maxValue;
            }

            // Update the displayed price range
            amount.value = "$" + minSlider.value + " - $" + maxSlider.value;

            // Calculate the percentage positions of the sliders
            const minPercent =
                ((minSlider.value - minSlider.min) /
                    (minSlider.max - minSlider.min)) *
                100;
            const maxPercent =
                ((maxSlider.value - maxSlider.min) /
                    (maxSlider.max - maxSlider.min)) *
                100;

            // Update the background gradient to show the active track color
            minSlider.style.background = `linear-gradient(to right, #000 ${minPercent}%, #005BFF ${minPercent}%, #005BFF ${maxPercent}%, #000 ${maxPercent}%)`;
            maxSlider.style.background = `linear-gradient(to right, #000 ${minPercent}%, #005BFF ${minPercent}%, #005BFF ${maxPercent}%, #000 ${maxPercent}%)`;
        }

        // Initialize the sliders and track with default values
        amount && updateAmount();

        // if (minSlider && maxSlider) {

        // Add event listeners for both sliders
        minSlider && minSlider.addEventListener("input", updateAmount);
        maxSlider && maxSlider.addEventListener("input", updateAmount);
        // }
    });

    function loader() {
        $(window).on('load', function() {
            // Animate loader off screen
            $(".preloader").addClass('loaded');
            $(".preloader").delay(600).fadeOut();
        });
    }
    loader();

    
})(jQuery); // End jQuery

