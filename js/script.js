$(document).ready(function () {
  $(".cardSlider").owlCarousel({
    items: 4,
    nav: true,
    dots: false,
    margin: 24,
    responsive: {
      0: {
        items: 1,
      },
      480: {
        items: 2,
      },
      768: {
        items: 3,
      },
      960: {
        items: 4,
      },
    },
  });

  // Partner Slider
  $(".partnerSlider").owlCarousel({
    items: 8,
    dots: false,
    autoplay: true,
    responsive: {
      0: {
        items: 1,
      },
      420: {
        items: 2,
      },
      560: {
        items: 3,
      },
      768: {
        items: 4,
      },
      960: {
        items: 6,
      },
      1024: {
        items: 8,
      },
    },
  });

  // Partner Slider
  $(".cta-slider").owlCarousel({
    items: 1,
    dots: true,
  });

  // Select 2
  $(".js-advanceSelect").select2();

  // Calendar
  $(".js-calendar")
    .datepicker({
      format: "dd/mm/yyyy",
      todayHighlight: true,
      autoclose: true,
    })
    .datepicker("setDate", new Date());

  $(".js-calendar-dateTime")
    .datepicker({
      format: "dd/mm/yyyy  hh:mm",
      todayHighlight: true,
      autoclose: true,
    })
    .datepicker("setDate", new Date());
});
