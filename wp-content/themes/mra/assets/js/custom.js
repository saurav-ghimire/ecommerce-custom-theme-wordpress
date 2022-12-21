// WOW Carousel
// ====================
$(document).ready(function () {
    $("#banner-sliders").owlCarousel({
        navigation: true,
        slideSpeed: 600,
        paginationSpeed: 500,
        singleItem: true,
        autoPlay: true,
        stopOnHover: false,
        navigationText: ["<", ">"],
        transitionStyle: "fade",
    });
    $("#services-slider").owlCarousel({
        navigation: true,
        slideSpeed: 1400,
        paginationSpeed: 1000,
        navigationText: ["", ""],
        autoPlay: 3000,
        items: 3,
        itemsDesktop: [1199, 2],
        itemsDesktopSmall: [979, 1],
        stopOnHover: true,
        autoHeight: true,
    });
    $("#testimonials-slider").owlCarousel({
        navigation: true,
        slideSpeed: 1400,
        paginationSpeed: 1000,
        navigationText: ["", ""],
        autoPlay: 3000,
        items: 1,
        itemsDesktop: [1199, 1],
        itemsDesktopSmall: [979, 1],
        stopOnHover: true,
        autoHeight: true,
        mouseDrag: true,
    });
});

// Fancybox
// ====================
$(document).ready(function () {
    $(".fancybox").fancybox();
});

// Bootstrap Modal
// ====================
$(function () {
    $("#myModal").modal('show');
});

// WOW JS
// ====================
$(document).ready(function () {
    new WOW().init();
});