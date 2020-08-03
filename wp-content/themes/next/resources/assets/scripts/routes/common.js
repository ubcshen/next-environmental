export default {
  init() {
    // JavaScript to be fired on all pages
    $('.lazyImg').unveil(); 
    var previousScroll = 0;
    $(window).scroll(function () {
      var currentScroll = $(this).scrollTop();
      if (currentScroll < 100) {
        showTopNav();
      } else if (currentScroll >= 100 && currentScroll < $(document).height() - $(window).height()) {
        if (currentScroll > previousScroll) {
          hideNav();
          $('.hamburger').toggleClass('open');
        } else {
          showNav();
        }
        previousScroll = currentScroll;
      }
    });

    function hideNav() {
      $('.header-banner').removeClass('is-visible').addClass('is-hidden fixed-top');
      $('.hide-scroll, .menu-topmenutwo-container').removeClass('is-visible').addClass('is-hidden');
      $('.hide-scroll, .menu-topmenutwo-container').removeClass('d-flex').addClass('d-none');
    }

    function showNav() {
      $('.header-banner').removeClass('is-hidden').addClass('fixed-top').addClass('is-visible');
      //$('.hide-scroll').removeClass('is-hidden').addClass('is-visible').addClass('scrolling');
    }

    function showTopNav() {
      $('.header-banner').removeClass('is-hidden fixed-top').addClass('is-visible').addClass('scrolling');
      $('.hide-scroll, .menu-topmenutwo-container').removeClass('is-hidden').addClass('is-visible');
      $('.hide-scroll, .menu-topmenutwo-container').removeClass('d-none').addClass('is-visible').addClass('d-flex');
    }
    $('.hamburger').on('click', function () {
      $(this).toggleClass('open');
      //$(window).trigger('scroll');
      //$('.casestudy-image').trigger('unveil');
      setTimeout(function () {
      $('.casestudy-image').unveil(200);
      }, 0);
    });
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};
